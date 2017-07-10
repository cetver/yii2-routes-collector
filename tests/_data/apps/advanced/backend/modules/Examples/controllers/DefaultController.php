<?php

namespace backend\modules\Examples\controllers;

use backend\modules\Examples\models\Page;
use cetver\RoutesCollector\models\Route;
use yii;
use yii\helpers\ArrayHelper;

class DefaultController extends Controller
{
    public $layout = 'examples';

    /**
     * @return array|Route[]
     */
    public function getApps()
    {
        return Route::find()->applications()->orderBy('id')->where(['IN', 'id', ['backend', 'frontend']])->all();
    }

    /**
     * @param string $app
     *
     * @return yii\web\UrlManager
     */
    public function getUrlManager($app)
    {
        return ($app === 'backend') ? Yii::$app->urlManager : Yii::$app->urlManagerFrontend;
    }

    public function actionTree()
    {
        $route = new Route();
        $rows = $route::find()->asArray()->all();

        return $this->render('tree', compact(
            'route',
            'rows'
        ));
    }

    public function actionOrderedTree()
    {
        $route = new Route();
        $rows = $route::find()->asArray()->all();
        ArrayHelper::multisort($rows, ['type', 'id'], [SORT_ASC, SORT_ASC]);

        return $this->render('tree', compact(
            'route',
            'rows'
        ));
    }

    public function actionAliasesI18n()
    {
        $route = new Route();
        $rows = $route::find()->asArray()->all();
        ArrayHelper::multisort($rows, ['type', 'id'], [SORT_ASC, SORT_ASC]);

        return $this->render('aliases-i18n', compact(
            'route',
            'rows'
        ));
    }

    public function actionRealLife()
    {
        /**
         * @var $page Page
         */
        $rows = ArrayHelper::index(Route::find()->asArray()->all(), 'id');
        $activeIds = [
            'backend',
            'backend/examples',
            'backend/examples/pages',
            'backend/examples/pages/first',
            'backend/examples/pages/second',
            'backend/examples/pages/third',
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
        $panelClasses = [
            Route::TYPE_APPLICATION => 'panel panel-primary',
            Route::TYPE_MODULE => 'panel panel-success',
            Route::TYPE_CONTROLLER => 'panel panel-info',
            Route::TYPE_ACTION => 'panel panel-default',
        ];
        return $this->render('real-life', compact(
            'rows',
            'panelClasses',
            'activeIds',
            'pages'
        ));
    }
}