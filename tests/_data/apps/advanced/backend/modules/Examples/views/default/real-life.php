<?php

/**
 * @var $this \yii\web\View
 * @var $context \backend\modules\Examples\controllers\DefaultController
 * @var $rows array
 * @var $panelClasses array
 * @var $activeIds array
 * @var $pages \backend\modules\Examples\models\Page[]
 */

use cetver\RoutesCollector\models\Route;
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
    $getTree = function ($items) use (&$getTree, $panelClasses, $activeIds, $pages) {
        $html = '<div class="panel-group">';
        foreach ($items as $item) {
            $id = $item['id'];
            $type = $item['type'];
            $htmlId = str_replace('/', '-', $id);
            /*
             * Usage "Yii::$app->getI18n()->translate()" instead of "Yii::t()" avoids the warning declared in
             * @link https://github.com/yiisoft/yii2/blob/2.0.12/framework/console/controllers/MessageController.php#L555
             */
            $translatedId = Yii::$app->getI18n()->translate('routes-php', $id, [], Yii::$app->language);
            $panelClass = $panelClasses[$type];
            $isActiveId = in_array($id, $activeIds);
            $collapseClass = 'panel-collapse collapse';
            if ($isActiveId) {
                $collapseClass .= ' in';
            }

            $html .= <<<HTML
<div class="$panelClass">
    <div class="panel-heading">
        <h4 class="panel-title">
            <a href="#$htmlId" data-toggle="collapse">
                $translatedId
            </a>
        </h4>
    </div>
    <div id="$htmlId" class="$collapseClass">
        <div class="panel-body">
HTML;
            if (!empty($item['children'])) {
                $html .= $getTree($item['children']);
            } else {
                if (!$isActiveId) {
                    $html .= 'Not active';
                } else {
                    $html .= $this->render('/pages/_form.php', [
                        'page' => $pages[$id],
                    ]);
                }
            }
            $html .= <<<HTML
        </div>
    </div>
</div>

HTML;
        }
        $html .= '</div>';

        return $html;
    };

    foreach ($context->getApps() as $app) {
        $id = $app->id;
        /*
         * Usage "Yii::$app->getI18n()->translate()" instead of "Yii::t()" avoids the warning declared in
         * @link https://github.com/yiisoft/yii2/blob/2.0.12/framework/console/controllers/MessageController.php#L555
         */
        $translatedId = Yii::$app->getI18n()->translate('routes-php', $id, [], Yii::$app->language);
        $root = $app->toArray();
        $root['children'] = Route::getChildren($rows, $id);
        $tree = [$root];
        $content = $getTree($tree);
        echo Html::tag('div', $content, ['style' => 'margin-bottom: 20px;']);
    }

    $css = <<<CSS
.panel-group {
    margin-bottom: 0;
}
CSS;
    $this->registerCss($css);
}
