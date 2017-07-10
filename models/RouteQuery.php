<?php

namespace cetver\RoutesCollector\models;

use yii;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Route]].
 *
 * @see Route
 */
class RouteQuery extends ActiveQuery
{
    /**
     * Adds WHERE condition to the columns: "id", "type".
     *
     * @param string $route The route of the current request.
     *
     * @see \yii\base\Controller::$route
     *
     * @param null|string $appId The application ID. If the argument equals to null Yii::$app->id will used.
     *
     * @see \yii\web\Application::$id
     * @return $this
     */
    public function routeEquals($route, $appId = null)
    {
        $tableName = Route::tableName();
        if ($appId === null) {
            $appId = Yii::$app->id;
        }
        $id = implode(Route::SEPARATOR_ROUTE, [
            $appId,
            $route,
        ]);

        return $this->andWhere([
            $tableName . '.id' => $id,
            $tableName . '.type' => Route::TYPE_ACTION,
        ]);
    }

    /**
     * Adds WHERE condition to the "type" column.
     *
     * @return $this
     */
    public function applications()
    {
        return $this->andWhere([
            Route::tableName() . '.type' => Route::TYPE_APPLICATION,
        ]);
    }

    /**
     * @inheritdoc
     * @return Route[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Route|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
