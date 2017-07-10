<?php

/**
 * @var $this \yii\web\View
 * @var $context \backend\modules\Examples\controllers\DefaultController
 * @var $route \cetver\RoutesCollector\models\Route
 * @var $rows array
 */

use yii\bootstrap\Alert;
use yii\bootstrap\Html;

$context = $this->context;

if (empty($rows)) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-warning',
        ],
        'closeButton' => [
            'tag' => false,
            'label' => false,
        ],
        'body' => 'There is no data, ensure that the tests were executed.',
    ]);
} else {
    $getTree = function ($items, $appId) use (&$getTree, &$route, $context) {
        $html = Html::beginTag('ol');
        foreach ($items as $item) {
            $id = $item['id'];
            if (empty($item['children'])) {
                $route::populateRecord($route, $item);
                $content = ($route->type !== $route::TYPE_ACTION)
                    ? $id
                    : Html::a(
                        $id,
                        $context->getUrlManager($appId)->createUrl($route->getRoute()),
                        ['target' => '_blank']
                    );
                $html .= Html::tag('li', $content);
            } else {
                $html .= Html::beginTag('li');
                $html .= $id;
                $html .= $getTree($item['children'], $appId);
                $html .= Html::endTag('li');
            }
        }
        $html .= Html::endTag('ol');

        return $html;
    };

    foreach ($context->getApps() as $app) {
        $id = $app->id;
        echo Html::tag('h2', $id);
        $children = $route::getChildren($rows, $id);
        echo $getTree($children, $id);
    }
}
