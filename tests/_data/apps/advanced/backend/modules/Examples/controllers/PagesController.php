<?php

namespace backend\modules\Examples\controllers;

use backend\modules\Examples\models\Page;
use cetver\RoutesCollector\models\Route;

class PagesController extends Controller
{
    public function beforeAction($action)
    {
        /**
         * @var $page Page
         */
        $result = parent::beforeAction($action);

        $route = Route::find()->routeEquals($this->getRoute())->one();
        if ($route !== null) {
            $page = Page::find()->where(['route_id' => $route->id])->one();
            if ($page !== null) {
                $this->view->title = $page->title;
                $this->view->registerMetaTag(
                    [
                        'name' => 'keywords',
                        'content' => $page->meta_keywords,
                    ],
                    'keywords'
                );
                $this->view->registerMetaTag(
                    [
                        'name' => 'description',
                        'content' => $page->meta_description,
                    ],
                    'description'
                );
            }
        }

        return $result;
    }

    public function actions()
    {
        return [
            'first' => [
                'class' => 'yii\web\ViewAction',
                'viewPrefix' => '',
            ],
            'second' => [
                'class' => 'yii\web\ViewAction',
                'viewPrefix' => '',

            ],
            'third' => [
                'class' => 'yii\web\ViewAction',
                'viewPrefix' => '',
            ],
        ];
    }
}