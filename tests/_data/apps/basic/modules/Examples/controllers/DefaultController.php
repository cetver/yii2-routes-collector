<?php

namespace app\modules\Examples\controllers;

use app\modules\Examples\models\Page;
use cetver\RoutesCollector\models\Route;
use yii;
use yii\helpers\ArrayHelper;

class DefaultController extends Controller
{
    public $layout = 'examples';

    public function actionTree()
    {
        $route = new Route();
        $rows = $route::find()->asArray()->all();
        $children = $route::getChildren($rows, Yii::$app->id);

        return $this->render('tree', compact(
            'route',
            'children'
        ));
    }

    public function actionOrderedTree()
    {
        $route = new Route();
        $rows = $route::find()->asArray()->all();
        ArrayHelper::multisort($rows, ['type', 'id'], [SORT_ASC, SORT_ASC]);
        $children = $route::getChildren($rows, Yii::$app->id);

        return $this->render('tree', compact(
            'route',
            'children'
        ));
    }

    public function actionAliasesI18n()
    {
        $route = new Route();
        $rows = $route::find()->asArray()->all();
        ArrayHelper::multisort($rows, ['type', 'id'], [SORT_ASC, SORT_ASC]);
        $children = $route::getChildren($rows, Yii::$app->id);

        return $this->render('aliases-i18n', compact(
            'route',
            'children'
        ));
    }

    public function actionRealLife()
    {
        /**
         * @var $page Page
         */
        $rows = ArrayHelper::index(Route::find()->asArray()->all(), 'id');
        $activeIds = [
            'basic/examples',
            'basic/examples/pages',
            'basic/examples/pages/first',
            'basic/examples/pages/second',
            'basic/examples/pages/third',
        ];
        $pages = ArrayHelper::index(
            Page::find()->with('route')->where(['in', 'route_id', $activeIds])->all(),
            'route_id'
        );
        foreach ($activeIds as $activeId) {
            if (!isset($pages[$activeId]) && isset($rows[$activeId])) {
                $route = new Route();
                $route::populateRecord($route, $rows[$activeId]);
                $page = new Page();
                $page->route_id = $route->id;
                $page->populateRelation('route', $route);
                $pages[$activeId] = $page;
            }
        }
        $request = Yii::$app->getRequest();
        if ($request->isPjax) {
            $pageAttributes = $request->post('Page');
            $routeId = ArrayHelper::getValue($pageAttributes, 'route_id');
            $page = $pages[$routeId];
            $page->attributes = $pageAttributes;
            $page->save();

            return $this->renderAjax('/pages/_form.php', compact('page'));
        }
        ArrayHelper::multisort($rows, ['type', 'id'], [SORT_ASC, SORT_ASC]);
        $children = Route::getChildren($rows, Yii::$app->id);
        $panelClasses = [
            Route::TYPE_MODULE => 'panel panel-success',
            Route::TYPE_CONTROLLER => 'panel panel-info',
            Route::TYPE_ACTION => 'panel panel-default',
        ];

        return $this->render('real-life', compact(
            'children',
            'panelClasses',
            'activeIds',
            'pages'
        ));
    }
}