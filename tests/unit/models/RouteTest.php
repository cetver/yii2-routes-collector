<?php

namespace cetver\RoutesCollector\tests\unit\models;

use cetver\RoutesCollector\models\Route;
use cetver\RoutesCollector\models\RouteQuery;
use Codeception\Test\Unit;

class RouteTest extends Unit
{
    /**
     * @var \cetver\RoutesCollector\tests\UnitTester
     */
    protected $tester;

    public function testGetChildren()
    {
        $rows = [];
        $parentId = 'parent-id';
        $this->tester->assertSame([], Route::getChildren($rows, $parentId));

        $rows = [
            [
                'id' => 'basic',
                'parent_id' => null,
                'type' => 1,
            ],
            [
                'id' => 'basic/examples',
                'parent_id' => 'basic',
                'type' => 2,
            ],
            [
                'id' => 'basic/examples/default',
                'parent_id' => 'basic/examples',
                'type' => 3,
            ],
            [
                'id' => 'basic/examples/default/aliases-i18n',
                'parent_id' => 'basic/examples/default',
                'type' => 4,
            ],
            [
                'id' => 'basic/examples/default/ordered-tree',
                'parent_id' => 'basic/examples/default',
                'type' => 4,
            ],
            [
                'id' => 'basic/examples/default/real-life',
                'parent_id' => 'basic/examples/default',
                'type' => 4,
            ],
            [
                'id' => 'basic/examples/default/tree',
                'parent_id' => 'basic/examples/default',
                'type' => 4,
            ],
            [
                'id' => 'basic/basic',
                'parent_id' => 'basic',
                'type' => 3,
            ],
            [
                'id' => 'basic/basic/action',
                'parent_id' => 'basic/basic',
                'type' => 4,
            ],
        ];
        $this->tester->assertSame([], Route::getChildren($rows, $parentId));

        $parentId = 'basic';
        $this->tester->assertSame(
            [
                [
                    'id' => 'basic/examples',
                    'parent_id' => 'basic',
                    'type' => 2,
                    'children' => [
                        [
                            'id' => 'basic/examples/default',
                            'parent_id' => 'basic/examples',
                            'type' => 3,
                            'children' => [
                                [
                                    'id' => 'basic/examples/default/aliases-i18n',
                                    'parent_id' => 'basic/examples/default',
                                    'type' => 4,
                                ],
                                [
                                    'id' => 'basic/examples/default/ordered-tree',
                                    'parent_id' => 'basic/examples/default',
                                    'type' => 4,
                                ],
                                [
                                    'id' => 'basic/examples/default/real-life',
                                    'parent_id' => 'basic/examples/default',
                                    'type' => 4,
                                ],
                                [
                                    'id' => 'basic/examples/default/tree',
                                    'parent_id' => 'basic/examples/default',
                                    'type' => 4,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'basic/basic',
                    'parent_id' => 'basic',
                    'type' => 3,
                    'children' => [
                        [
                            'id' => 'basic/basic/action',
                            'parent_id' => 'basic/basic',
                            'type' => 4,
                        ],
                    ],
                ],
            ],
            Route::getChildren($rows, $parentId)
        );
    }

    public function testFind()
    {
        $this->tester->assertInstanceOf(RouteQuery::className(), Route::find());
    }

    public function testRules()
    {
        $route = new Route();
        $this->tester->assertSame(
            [
                [['id', 'type'], 'required'],
                [['id', 'parent_id'], 'string'],
                [['type'], 'in', 'range' => [
                    $route::TYPE_APPLICATION,
                    $route::TYPE_MODULE,
                    $route::TYPE_CONTROLLER,
                    $route::TYPE_ACTION,
                ]],
            ],
            $route->rules()
        );
    }

    public function testTableName()
    {
        $this->tester->assertSame('routes', Route::tableName());
    }
}