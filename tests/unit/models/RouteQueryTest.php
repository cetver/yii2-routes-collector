<?php

namespace cetver\RoutesCollector\tests\unit\models;

use cetver\RoutesCollector\models\Route;
use cetver\RoutesCollector\models\RouteQuery;
use Codeception\Test\Unit;
use Yii;

class RouteQueryTest extends Unit
{
    /**
     * @var \cetver\RoutesCollector\tests\UnitTester
     */
    protected $tester;

    public function testRouteEquals()
    {
        $route = 'route';
        $this->tester->assertSame(
            [
                'routes.id' => implode(Route::SEPARATOR_ROUTE, [Yii::$app->id, $route]),
                'routes.type' => Route::TYPE_ACTION,
            ],
            Route::find()->routeEquals($route)->where
        );

        $appId = 'app';
        $this->tester->assertSame(
            [
                'routes.id' => implode(Route::SEPARATOR_ROUTE, [$appId, $route]),
                'routes.type' => Route::TYPE_ACTION,
            ],
            Route::find()->routeEquals($route, $appId)->where
        );
    }

    public function testApplications()
    {
        $this->tester->assertSame(
            ['routes.type' => Route::TYPE_APPLICATION],
            Route::find()->applications()->where
        );
    }
}