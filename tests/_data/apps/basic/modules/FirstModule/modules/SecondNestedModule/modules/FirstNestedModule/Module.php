<?php

namespace app\modules\FirstModule\modules\SecondNestedModule\modules\FirstNestedModule;

/**
 * first-nested-module module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
