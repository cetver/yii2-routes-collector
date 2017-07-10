<?php

require_once realpath(__DIR__ . '/../../functional/_bootstrap.php');

$db = [
    'class' => 'yii\db\Connection',
    'attributes' => [
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ],
];

switch (getenv('DB')) {
    default:
        $db = array_merge($db, \Codeception\Util\Fixtures::get('db'));
        break;
    case 'pgsql':
        $db['dsn'] = 'pgsql:host=127.0.0.1;dbname=cetver-yii2-routes-collector;';
        $db['username'] = 'postgres';
        $db['password'] = '';
        break;
    case 'mysql':
        $db['dsn'] = 'mysql:host=127.0.0.1;dbname=cetver-yii2-routes-collector;';
        $db['username'] = 'root';
        $db['password'] = '';
        break;
    case 'sqlite':
        $db['dsn'] = 'sqlite:cetver-yii2-routes-collector.sqlite';
        $db['on afterOpen'] = function (\yii\base\Event $event) {
            /**
             * @var $db yii\db\Connection
             */
            $db = $event->sender;
            $db->createCommand('PRAGMA foreign_keys = ON')->execute();
        };
        break;
}

return $db;
