<?php

namespace cetver\RoutesCollector\tests\Helper;

use cetver\RoutesCollector\commands\RoutesController;
use Codeception\Util\Fixtures;
use yii\console\controllers\MigrateController;

class Functional extends \Codeception\Module
{
    public function _initialize()
    {
        $this->runMigrations();
    }

    public function _beforeSuite($settings = [])
    {
        $this->runCommandRoutesCollect();
    }

    public function _afterSuite()
    {
        $this->runCommandRoutesCollect();
    }

    protected function app()
    {
        $config = require realpath(__DIR__ . '/../../_data/apps/basic/config/web.php');

        return new \yii\console\Application($config);
    }

    protected function runMigrations()
    {
        $extAlias = '@cetver/RoutesCollector';
        $controller = new MigrateController('migrate', $this->app(), [
            'interactive' => false,
        ]);

        $controller->migrationPath = $extAlias . '/migrations';
        $controller->run('up');

        $controller->migrationPath = $extAlias . '/tests/_data/apps/basic/modules/Examples/migrations';
        $controller->run('up');
    }

    protected function runCommandRoutesCollect()
    {
        $controller = new RoutesController('routes', $this->app(), [
            'verbose' => false
        ]);
        return $controller->run('collect', [
            Fixtures::get('command_routes_collect_config_dir') . '/config.php'
        ]);
    }
}
