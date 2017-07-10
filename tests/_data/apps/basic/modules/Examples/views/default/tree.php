<?php

/**
 * @var $this \yii\web\View
 * @var $route \cetver\RoutesCollector\models\Route
 * @var $children array
 */

use yii\bootstrap\Alert;
use yii\bootstrap\Html;

if (empty($children)) {
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
    $getTree = function ($items) use (&$getTree, &$route) {
        $html = Html::beginTag('ol');
        foreach ($items as $item) {
            $id = $item['id'];
            if (empty($item['children'])) {
                $route::populateRecord($route, $item);
                $content = ($route->type !== $route::TYPE_ACTION)
                    ? $id
                    : Html::a($id, [$route->getRoute()], ['target' => '_blank']);
                $html .= Html::tag('li', $content);
            } else {
                $html .= Html::beginTag('li');
                $html .= $id;
                $html .= $getTree($item['children']);
                $html .= Html::endTag('li');
            }
        }
        $html .= Html::endTag('ol');

        return $html;
    };

    echo $getTree($children);
}
