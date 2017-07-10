<?php

$db = require(realpath(__DIR__ . '/../../../../config/db.php'));

return [
    'vendorPath' => realpath(__DIR__ . '/../../../../../../vendor'),
    'on beforeAction' => function (yii\base\ActionEvent $event) {
        $app = $event->sender;
        $response = $app->response;
        $controller = $app->controller;
        if ($app instanceof yii\web\Application &&
            $response instanceof \yii\web\Response &&
            $controller instanceof \yii\web\Controller
        ) {
            $response->headers->set('X-Route', '/' . $controller->route);
        }
    },
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'rest'],
            ],
        ],
        'db' => $db,
        'i18n' => [
            'translations' => [
                'routes-php' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@cetver/RoutesCollector/tests/_data/messages',
                    'forceTranslation' => true,
                ],
            ],
        ],
    ],
];
