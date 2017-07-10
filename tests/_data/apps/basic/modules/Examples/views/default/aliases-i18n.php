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
            /*
             * Usage "Yii::$app->getI18n()->translate()" instead of "Yii::t()" avoids the warning declared in
             * @link https://github.com/yiisoft/yii2/blob/2.0.12/framework/console/controllers/MessageController.php#L555
             */
            $translatedId = Yii::$app->getI18n()->translate('routes-php', $id, [], Yii::$app->language);
            $text = ($id === $translatedId)
                ? $translatedId
                : Html::tag('span', $translatedId, ['class' => 'text-danger']);
            if (empty($item['children'])) {
                $route::populateRecord($route, $item);
                $content = ($route->type !== $route::TYPE_ACTION)
                    ? $text
                    : Html::a($text, [$route->getRoute()], ['target' => '_blank']);
                $html .= Html::tag('li', $content);
            } else {
                $html .= Html::beginTag('li');
                $html .= $text;
                $html .= $getTree($item['children']);
                $html .= Html::endTag('li');
            }
        }
        $html .= Html::endTag('ol');

        return $html;
    };

    echo $getTree($children);
}