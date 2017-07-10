<?php

/**
 * @var $this \yii\web\View
 * @var $content string
 */

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <?php
        $this->registerMetaTag(['charset' => Yii::$app->charset]);
        $this->head();
        ?>
        <title><?= $this->title ?></title>
    </head>
    <body>
    <?php
    $this->beginBody();
    echo $content;
    $this->endBody();
    ?>
    </body>
    </html>
<?php $this->endPage() ?>