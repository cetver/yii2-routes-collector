<?php

return [
    'id' => 'frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\controllers',
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
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'dzAaKl-qcf1q_ZVXvCQ_8KGkAw5IPXys',
            'csrfParam' => '_csrf-frontend',
        ],
    ],
];
