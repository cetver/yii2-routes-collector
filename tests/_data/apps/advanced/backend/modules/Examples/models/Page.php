<?php

namespace backend\modules\Examples\models;

use cetver\RoutesCollector\models\Route;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "yii2_ext_cetver_routes_collector_pages".
 *
 * @property integer $id
 * @property string $route_id
 * @property string $title
 * @property string $meta_description
 * @property string $meta_keywords
 *
 * @property Route $route
 */
class Page extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yii2_ext_cetver_routes_collector_pages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['route_id'], 'required'],
            [['route_id', 'title', 'meta_description', 'meta_keywords'], 'string'],
            [['route_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Route::className(),
                'targetAttribute' => ['route_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'route_id' => 'Route ID',
            'title' => 'Title',
            'meta_description' => 'Meta Description',
            'meta_keywords' => 'Meta Keywords',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoute()
    {
        return $this->hasOne(Route::className(), ['id' => 'route_id']);
    }
}
