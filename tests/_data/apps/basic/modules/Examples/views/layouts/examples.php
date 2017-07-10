<?php

/**
 * @var $this \yii\web\View
 * @var $content string
 */

use yii\bootstrap\Nav;
use yii\helpers\Html;

$this->beginContent('@app/modules/Examples/views/layouts/main.php');

echo Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [
        ['label' => 'Tree', 'url' => ['/examples/default/tree']],
        ['label' => 'Ordered Tree', 'url' => ['/examples/default/ordered-tree']],
        ['label' => 'Aliases (i18n)', 'url' => ['/examples/default/aliases-i18n']],
        ['label' => 'Real Life Example', 'url' => ['/examples/default/real-life']],
    ],
]);

echo Html::tag('div', $content, ['style' => 'margin: 20px 0 10px']);

$this->endContent();

