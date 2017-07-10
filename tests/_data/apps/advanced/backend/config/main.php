<?php

require_once realpath(__DIR__  . '/../../../../../functional/_bootstrap.php');

return [
    'id' => 'backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'controllerMap' => [
        'second' => 'backend\controllers\RenamedToSecondController',
        '3' => [
            'class' => 'backend\controllers\RenamedToThirdController',
        ],
        'invalid' => 'backend\controllers\RandomClass',
    ],
    'modules' => [
        'first-module' => [
            'class' => 'backend\modules\FirstModule\Module',
            'controllerMap' => [
                'second' => 'backend\controllers\RenamedToSecondController',
                3 => [
                    'class' => 'backend\controllers\RenamedToThirdController',
                ],
                'invalid' => 'backend\controllers\RandomClass',
            ],
            'modules' => [
                'no-controllers' => 'backend\modules\FirstModule\modules\FirstNestedModule\Module',
                '2-nested-module' => [
                    'class' => 'backend\modules\FirstModule\modules\SecondNestedModule\Module',
                    'controllerMap' => [
                        'second' => 'backend\controllers\RenamedToSecondController',
                        3 => [
                            'class' => 'backend\controllers\RenamedToThirdController',
                        ],
                        'invalid' => 'backend\controllers\RandomClass',
                    ],
                    'modules' => [
                        'FirstNestedModule' => [
                            'class' => 'backend\modules\FirstModule\modules\SecondNestedModule\modules\FirstNestedModule\Module',
                            'controllerMap' => [
                                'second' => 'backend\controllers\RenamedToSecondController',
                                3 => [
                                    'class' => 'backend\controllers\RenamedToThirdController',
                                ],
                                'invalid' => 'backend\controllers\RandomClass',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'examples' => 'backend\modules\Examples\Module',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '--GSIWAeYUViPkMq9vdUQgsqDaqJVGFu',
            'csrfParam' => '_csrf-backend',
        ],
        'urlManagerFrontend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => Codeception\Util\Fixtures::get('hosts')['frontend'],
            'hostInfo' => Codeception\Util\Fixtures::get('hosts')['frontend'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'rest'],
            ],
        ],
    ],
];
