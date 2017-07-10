<?php

use Codeception\Util\Fixtures;

Fixtures::add('db', [
    'dsn' => 'pgsql:dbname=cetver-yii2-routes-collector;port=5434',
    'username' => 'postgres',
    'password' => 'root',
]);

Fixtures::add('hosts', [
    'basic' => 'http://basic.cetver-yii2-routes-collector',
    'frontend' => 'http://frontend.cetver-yii2-routes-collector',
    'backend' => 'http://backend.cetver-yii2-routes-collector',
]);

Fixtures::add('command_routes_collect_config_dir', realpath(__DIR__ . '/../_data/config/command-routes-collect'));
