<?php

namespace cetver\RoutesCollector\models;

use UnexpectedValueException;
use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "routes".
 *
 * @property string $id The string in the following format: "App ID[/Module ID][/Controller ID][/Action ID]", where
 * App ID           @see \yii\web\Application::$id
 * Module ID        @see \yii\base\Module::$id
 * Controller ID    @see \yii\web\Controller::$id
 * Action ID        @see \yii\base\Action::$id
 * /                @see Route::SEPARATOR_ROUTE
 * []               Optional value
 * @property string|null $parent_id The string in the following format: "App ID[/Module ID][/Controller ID][/Action ID]", where
 * App ID           @see \yii\web\Application::$id
 * Module ID        @see \yii\base\Module::$id
 * Controller ID    @see \yii\web\Controller::$id
 * Action ID        @see \yii\base\Action::$id
 * /                @see Route::SEPARATOR_ROUTE
 * []               Optional value
 * @property integer $type The flag representing the route type.
 * @see Route::TYPE_APPLICATION
 * @see Route::TYPE_MODULE
 * @see Route::TYPE_CONTROLLER
 * @see Route::TYPE_ACTION
 */
class Route extends ActiveRecord
{
    /**
     * The route separator.
     */
    const SEPARATOR_ROUTE = '/';
    /**
     * The length of the route separator.
     */
    const LENGTH_SEPARATOR_ROUTE = 1;
    /**
     * Represents the route type as an application.
     * @see \yii\web\Application
     */
    const TYPE_APPLICATION = 1;
    /**
     * Represents the route type as a module.
     * @see \yii\base\Module
     */
    const TYPE_MODULE = 2;
    /**
     * Represents the route type as a controller.
     * @see \yii\web\Controller
     */
    const TYPE_CONTROLLER = 3;
    /**
     * Represents the route type as an action.
     * @see \yii\base\Action
     */
    const TYPE_ACTION = 4;

    /**
     * @var null|array The variable for caching.
     * @see Route::getAppIds()
     */
    protected static $appIds;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'routes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type'], 'required'],
            [['id', 'parent_id'], 'string'],
            [['type'], 'in', 'range' => [
                self::TYPE_APPLICATION,
                self::TYPE_MODULE,
                self::TYPE_CONTROLLER,
                self::TYPE_ACTION,
            ]],
        ];
    }

    /**
     * @inheritdoc
     * @return RouteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RouteQuery(get_called_class());
    }

    /**
     * Returns the rows of the table as a tree. Example:
     *
     * ```php
     * [
     *     [
     *         'id' => '',
     *         'parent_id' => '',
     *         'type' => '',
     *     ],
     *     [
     *         'id' => '',
     *         'parent_id' => '',
     *         'type' => '',
     *         'children' => [
     *             [
     *                 'id' => '',
     *                 'parent_id' => '',
     *                 'type' => '',
     *             ],
     *             [
     *                 'id' => '',
     *                 'parent_id' => '',
     *                 'type' => '',
     *                 'children' => [
     *                     [
     *                         'id' => '',
     *                         'parent_id' => '',
     *                         'type' => '',
     *                     ],
     *                 ],
     *             ],
     *         ],
     *     ],
     * ]
     * ```
     *
     * @param array $rows The result returned by the method Route::find()->asArray(true)->all()
     * @param string $parentId
     * @see Route::$parent_id
     *
     * @return array
     */
    public static function getChildren($rows, $parentId)
    {
        $result = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] === $parentId) {
                $children = static::getChildren($rows, $row['id']);
                if (!empty($children)) {
                    $row['children'] = $children;
                }
                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Returns a list of application IDs.
     *
     * @return array
     */
    public static function getAppIds()
    {
        if (static::$appIds === null) {
            static::$appIds = static::find()->applications()->select('id')->column();
        }

        return static::$appIds;
    }

    /**
     * Returns the route for the specified application ID.
     *
     * @see \yii\web\Application::$id
     * @see \yii\base\Controller::$route
     *
     * @param null|string $appId The application ID. If the argument equals to null Yii::$app->id will used.
     *
     * @throws UnexpectedValueException
     * @return string
     */
    public function getRoute($appId = null)
    {
        if ($this->type != self::TYPE_ACTION) {
            throw new UnexpectedValueException(sprintf(
                'The "type" attribute must equals to "%s".', self::TYPE_ACTION
            ));
        }
        if ($appId === null) {
            $appId = Yii::$app->id;
        }
        $appIds = static::getAppIds();
        if (empty($appIds)) {
            throw new UnexpectedValueException(
                'Could not find any application IDs. Ensure that the console command "routes/collect" has been executed.'
            );
        }
        if (!in_array($appId, $appIds)) {
            throw new UnexpectedValueException(sprintf(
                'The application ID must be one of: "%s".', implode('", "', $appIds)
            ));
        }

        return self::SEPARATOR_ROUTE . substr($this->id, strlen($appId) + self::LENGTH_SEPARATOR_ROUTE);
    }
}
