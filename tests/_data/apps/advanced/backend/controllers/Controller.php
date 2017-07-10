<?php

namespace backend\controllers;

class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        $this->viewPath = '@app/views';
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'view' => 'view',
            ],
            'standaloneAction' => [
                'class' => 'yii\web\ViewAction',
                'defaultView' => 'view',
                'viewPrefix' => '',
            ],
            'StandaloneAction' => [
                'class' => 'yii\web\ViewAction',
                'defaultView' => 'view',
                'viewPrefix' => '',
            ],
            'standalone-action' => [
                'class' => 'yii\web\ViewAction',
                'defaultView' => 'view',
                'viewPrefix' => '',
            ],

        ];
    }
}