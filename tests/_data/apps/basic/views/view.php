<?php

/**
 * @var $this \yii\web\View
 * @var $context \app\controllers\Controller
 */

$context = $this->context;
$this->title = $context->getRoute();
echo $this->title;