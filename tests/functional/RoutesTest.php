<?php

namespace cetver\RoutesCollector\tests\functional;

use cetver\RoutesCollector\models\Route;
use Codeception\Test\Unit;
use Codeception\Util\Fixtures;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;

class RoutesTest extends Unit
{
    /**
     * @var \cetver\RoutesCollector\tests\FunctionalTester
     */
    protected $tester;

    public function testEnsureOnExistence()
    {
        $model = new Route();
        $rows = $model::find()->asArray()->all();
        $apps = $model::find()->applications()->all();
        $hosts = Fixtures::get('hosts');
        foreach ($apps as $app) {
            $children = $model::getChildren($rows, $app->id);
            $routes = $this->getRoutes($children, $model, $app->id);
            $this->tester->assertNotEmpty($routes);
            $host = $hosts[$app->id];
            $client = new Client(['base_uri' => $host]);
            foreach ($routes as $route) {
                $responseRoute = '';
                $promise = $client->headAsync($route);
                $promise->then(
                    function (ResponseInterface $res) use (&$responseRoute) {
                        $responseRoute = current($res->getHeader('X-Route'));
                    },
                    function (RequestException $e) use (&$responseRoute) {
                        $responseRoute = current($e->getResponse()->getHeader('X-Route'));
                    }
                );
                Promise\settle($promise)->wait();
                $this->tester->assertSame($route, $responseRoute);
            }
        }
    }

    protected function getRoutes($rows, Route &$route, $app)
    {
        $routes = [];
        foreach ($rows as $row) {
            if (empty($row['children'])) {
                if ($row['type'] == $route::TYPE_ACTION) {
                    $route::populateRecord($route, $row);
                    $routes[] = $route->getRoute($app);
                }
            } else {
                foreach ($this->getRoutes($row['children'], $route, $app) as $item) {
                    $routes[] = $item;
                }
            }
        }

        return $routes;
    }
}