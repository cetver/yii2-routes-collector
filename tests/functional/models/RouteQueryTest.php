<?php

namespace cetver\RoutesCollector\tests\functional\models;

use cetver\RoutesCollector\models\Route;
use cetver\RoutesCollector\models\RouteQuery;
use Codeception\Test\Unit;

class RouteQueryTest extends Unit
{
    /**
     * @var \cetver\RoutesCollector\tests\FunctionalTester
     */
    protected $tester;

    public function testAll()
    {
        $modelClass = Route::className();

        $query = new RouteQuery($modelClass);
        $result = $query->emulateExecution()->all();
        $this->tester->assertSame([], $result);

        $query = new RouteQuery($modelClass);
        $result = $query->limit(1)->all();
        $this->tester->assertInstanceOf($modelClass, $result[0]);
    }

    public function testOne()
    {
        $modelClass = Route::className();

        $query = new RouteQuery($modelClass);
        $result = $query->emulateExecution()->one();
        $this->tester->assertSame(null, $result);

        $query = new RouteQuery($modelClass);
        $result = $query->one();
        $this->tester->assertInstanceOf($modelClass, $result);

        $query = new RouteQuery($modelClass);
        $result = $query->asArray()->one();
        $this->tester->assertInternalType('array', $result);
    }
}