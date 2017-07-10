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
                    : Html::a(
                        $text,
                        $context->getUrlManager($appId)->createUrl($route->getRoute()),
                        ['target' => '_blank']
                    );
                $html .= Html::tag('li', $content);
            } else {
                $html .= Html::beginTag('li');
                $html .= $text;
                $html .= $getTree($item['children'], $appId);
                $html .= Html::endTag('li');
            }
        }
        $html .= Html::endTag('ol');

        return $html;
    };

    foreach ($context->getApps() as $app) {
        $id = $app->id;
        /*
         * Usage "Yii::$app->getI18n()->translate()" instead of "Yii::t()" avoids the warning declared in
         * @link https://github.com/yiisoft/yii2/blob/2.0.12/framework/console/controllers/MessageController.php#L555
         */
        $translatedId = Yii::$app->getI18n()->translate('routes-php', $id, [], Yii::$app->language);
        $options = ($id === $translatedId) ? [] : ['class' => 'text-danger'];
        echo Html::tag('h2', $translatedId, $options);
        $children = $route::getChildren($rows, $id);
        echo $getTree($children, $id);
    }
}