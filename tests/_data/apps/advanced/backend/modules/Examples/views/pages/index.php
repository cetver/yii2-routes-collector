<?php

/**
 * @var $this \yii\web\View
 */

use yii\widgets\DetailView;

?>

<div style="display: table; margin: 25% auto 0;">
    <?php
    echo DetailView::widget([
        'model' => [],
        'options' => [
            'tag' => 'dl',
            'class' => 'dl-horizontal',
        ],
        'template' => '<dt>{captionOptions}{label}</dt><dd>{contentOptions}{value}</dd>',
        'attributes' => [
            [
                'label' => 'Title',
                'value' => $this->title,
            ],
            [
                'label' => 'Meta keywords',
                'value' => @$this->metaTags['keywords'],
            ],
            [
                'label' => 'Meta description',
                'value' => @$this->metaTags['description'],
            ],
        ],
    ]);
    ?>
</div>
