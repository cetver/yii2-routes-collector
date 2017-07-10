<?php

namespace cetver\RoutesCollector\tests\functional\models;

use cetver\RoutesCollector\models\Route;
use Codeception\Test\Unit;
use ReflectionClass;
use UnexpectedValueException;
use Yii;

class RouteTest extends Unit
{
    /**
     * @var \cetver\RoutesCollector\tests\FunctionalTester
     */
    protected $tester;

    public $events;

    public function testGetRoute()
    {
        $modelAction = Route::find()->where(['id' => 'basic/examples/default/tree'])->one();
        $this->tester->assertSame('/examples/default/tree', $modelAction->getRoute());

        $this->tester->assertSame('/examples/default/tree', $modelAction->getRoute('basic'));

        $this->tester->expectException(
            UnexpectedValueException::class,
            function () use ($modelAction) {
                $modelAction->getRoute('app');
            }
        );

        $modelApp = Route::find()->where(['id' => 'basic'])->one();
        $this->tester->expectException(
            new UnexpectedValueException(sprintf(
                'The "type" attribute must equals to "%s".', Route::TYPE_ACTION
            )),
            function () use ($modelApp) {
                $modelApp->getRoute();
            }
        );

        $rc = new ReflectionClass($modelAction);
        $appIds = $rc->getProperty('appIds');
        $appIds->setAccessible(true);
        $appIds->setValue($modelAction, []);
        $this->tester->expectException(
            new UnexpectedValueException(
                'Could not find any application IDs. Ensure that the console command "routes/collect" has been executed.'
            ),
            function () use ($modelAction) {
                $modelAction->getRoute();
            }
        );
        $appIds->setValue($modelAction, $this->getAppIds());

    }

    public function testGetAppIds()
    {
        $model = new Route();
        $db = $model::getDb();
        $tableName = $model::tableName();

        $modelAppIds = $model::getAppIds();
        asort($modelAppIds);
        $this->tester->assertSame($this->getAppIds(), $modelAppIds);

        $db->createCommand()->delete($tableName, ['id' => 'app'])->execute();
        $db
            ->createCommand()
            ->insert($tableName, [
                'id' => 'app',
                'parent_id' => null,
                'type' => $model::TYPE_APPLICATION
            ])
            ->execute();
        $modelAppIds = $model::getAppIds();
        asort($modelAppIds);
        $this->tester->assertSame($this->getAppIds(), $modelAppIds);

        $model::deleteAll();
        $rc = new ReflectionClass($model);
        $appIds = $rc->getProperty('appIds');
        $appIds->setAccessible(true);
        $appIds->setValue($model, null);
        $this->tester->assertSame([], $model::getAppIds());
    }

    protected function getAppIds()
    {
        $ids = [
            'basic',
            'backend',
            'frontend'
        ];
        asort($ids);
        return $ids;
    }
}