<?php

namespace backend\modules\FirstModule\modules\FirstNestedModule;

/**
 * first-nested-module module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\FirstModule\modules\FirstNestedModule\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
