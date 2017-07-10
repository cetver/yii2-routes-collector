<?php

Yii::setAlias('@cetver/RoutesCollector', realpath(__DIR__ . '/../../../../../'));

$db = require(realpath(__DIR__ . '/../../../config/db.php'));

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'vendorPath' => realpath(__DIR__ . '/../../../../../vendor'),
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
    'controllerMap' => [
        'second' => 'app\controllers\RenamedToSecondController',
        '3' => [
            'class' => 'app\controllers\RenamedToThirdController',
        ],
        'invalid' => 'app\controllers\RandomClass',
    ],
    'modules' => [
        'first-module' => [
            'class' => 'app\modules\FirstModule\Module',
            'controllerMap' => [
                'second' => 'app\controllers\RenamedToSecondController',
                3 => [
                    'class' => 'app\controllers\RenamedToThirdController',
                ],
                'invalid' => 'app\controllers\RandomClass',
            ],
            'modules' => [
                'no-controllers' => 'app\modules\FirstModule\modules\FirstNestedModule\Module',
                '2-nested-module' => [
                    'class' => 'app\modules\FirstModule\modules\SecondNestedModule\Module',
                    'controllerMap' => [
                        'second' => 'app\controllers\RenamedToSecondController',
                        3 => [
                            'class' => 'app\controllers\RenamedToThirdController',
                        ],
                        'invalid' => 'app\controllers\RandomClass',
                    ],
                    'modules' => [
                        'FirstNestedModule' => [
                            'class' => 'app\modules\FirstModule\modules\SecondNestedModule\modules\FirstNestedModule\Module',
                            'controllerMap' => [
                                'second' => 'app\controllers\RenamedToSecondController',
                                3 => [
                                    'class' => 'app\controllers\RenamedToThirdController',
                                ],
                                'invalid' => 'app\controllers\RandomClass',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'examples' => 'app\modules\Examples\Module',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '6tctDlcIZ3BDn_TgCqRT1725nP0YFC2D',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'rest'],
            ],
        ],
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

return $config;
