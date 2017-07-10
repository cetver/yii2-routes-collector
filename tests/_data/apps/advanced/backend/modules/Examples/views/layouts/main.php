<?php

/**
 * @var $this \yii\web\View
 * @var $content string
 */

use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Html;

BootstrapPluginAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?php
    echo Html::csrfMetaTags();
    $this->registerMetaTag(['charset' => Yii::$app->charset]);
    $this->registerMetaTag([
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1',
    ]);
    $this->head();
    ?>
    <title><?= Html::encode($this->title) ?></title>
</head>
<body>
<?php $this->beginBody(); ?>
<div class="container" style="margin-top: 2em;">
    <?= $content ?>
</div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage() ?>
