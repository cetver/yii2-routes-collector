<?php

namespace app\modules\FirstModule\modules\FirstNestedModule;

/**
 * first-nested-module module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\FirstModule\modules\FirstNestedModule\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
