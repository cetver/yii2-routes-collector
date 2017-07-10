<?php

/**
 * @var $this \yii\web\View
 * @var $page \app\modules\Examples\models\Page
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

Pjax::begin([
    'id' => 'id-' . crc32($page->route_id)
]);
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableClientScript' => false,
    'options' => [
        'data-pjax' => ''
    ],
]);
echo $form
    ->field($page, 'route_id', [
        'template' => '{input}',
        'options' => [
            'tag' => false
        ]
    ])
    ->hiddenInput();

echo Html::beginTag('div', ['class' => 'row']);

echo Html::beginTag('div', ['class' => 'col-sm-4']);
echo $form->field($page, 'title');
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'col-sm-4']);
echo $form->field($page, 'meta_keywords')->textarea([]);
echo Html::endTag('div');

echo Html::beginTag('div', ['class' => 'col-sm-4']);
echo $form->field($page, 'meta_description')->textarea([]);
echo Html::endTag('div');

echo Html::endTag('div');

echo Html::submitButton(
    sprintf('%s Save', Html::tag('i', '', ['class' => 'glyphicon glyphicon-ok'])),
    ['class' => 'btn btn-primary']
);
echo ' ';
echo Html::a(
    sprintf('%s View', Html::tag('i', '', ['class' => 'glyphicon glyphicon-eye-open'])),
    [$page->route->getRoute()],
    [
        'class' => 'btn btn-default',
        'target' => '_blank',
        'data-pjax' => 0
    ]
);

ActiveForm::end();
Pjax::end();


$js = <<<JS
$(document).on('pjax:beforeSend', function (xhr) {    
    $(xhr.target).find('[type="submit"]').button('loading');    
});
JS;
$this->registerJs($js);