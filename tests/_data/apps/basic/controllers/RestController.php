<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;

class RestController extends ActiveController
{
    /**
     * @var string|\cetver\RoutesCollector\models\Route
     */
    public $modelClass = 'cetver\RoutesCollector\models\Route';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);

        return $behaviors;
    }

    public function actionAction()
    {
        $model = $this->modelClass;
        return Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $model::find(),
        ]);
    }
}