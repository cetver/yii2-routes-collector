<?php

namespace cetver\RoutesCollector\commands;

use cetver\RoutesCollector\models\Route;
use DirectoryIterator;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use RegexIterator;
use RuntimeException;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Application;

/**
 * Collects and saves applications routes.
 */
class RoutesController extends Controller
{
    /**
     * The namespace separator.
     */
    const SEPARATOR_NAMESPACE = '\\';
    /**
     * The separator of the action/controller ID.
     *
     * @see Inflector::camel2id()
     */
    const SEPARATOR_ID = '-';
    /**
     * The "controller" word length.
     */
    const LENGTH_WORD_CONTROLLER = 10;
    /**
     * The "action" word length.
     */
    const LENGTH_WORD_ACTION = 6;
    /**
     * @inheritdoc
     */
    public $defaultAction = 'collect';
    /**
     * @var string|Route A class name of the route model.
     */
    public $model = 'cetver\RoutesCollector\models\Route';
    /**
     * @var bool Make actions more talkative.
     */
    public $verbose = true;
    /**
     * @var array Messages to display.
     */
    protected $messages = [];

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'model',
            'verbose'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'm' => 'model',
            'v' => 'verbose',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->model = new $this->model;
        if (!$this->model instanceof Route) {
            throw new InvalidConfigException(sprintf(
                'The "model" option must be a instance of "%s".', Route::className()
            ));
        }

        return parent::beforeAction($action);
    }

    /**
     * Creates a file with multiple calls of the translator function and executes the "message/extract" command over it.
     * Example:
     *
     * ```
     * # The result of the command below:
     * # Creating a file "/tmp/source/cetver-routes-collector-commands-routes-controller-action-extract-messages.php"
     * # with the following content:
     * # Yii::t("routes", "first route");
     * # Yii::t("routes", "second route");
     * # Creating a file "/tmp/en-US/routes.php" with the following content:
     * # return [
     * #     'first route' => '',
     * #     'second route' => '',
     * # ];
     * ./yii routes/extract-messages '{"sourcePath":"/tmp/source","messagePath":"/tmp"}' routes
     * ```
     *
     * @param string $commandMessageExtractOptions The "message/extract" command options in JSON format.
     * @see \yii\console\controllers\MessageController::options()
     * @param string $translatorCategory The first argument of the translator function.
     * @see Yii::t()
     *
     * @return int CLI exit code
     * @throws InvalidArgumentException
     */
    public function actionExtractMessages($commandMessageExtractOptions = '', $translatorCategory = 'routes')
    {
        if (!is_string($commandMessageExtractOptions)) {
            throw new InvalidArgumentException('The "commandMessageExtractOptions" argument must be a string.');
        }
        if (!is_string($translatorCategory)) {
            throw new InvalidArgumentException('The "translatorCategory" argument must be a string.');
        }
        $commandMessageExtractOptions = array_merge(
            [
                'sourcePath' => Yii::getAlias('@runtime/vendor'),
                'messagePath' => '@app/messages',
                'languages' => [Yii::$app->sourceLanguage],
                'translator' => 'Yii::t',
                'except' => [],
            ],
            (array)Json::decode($commandMessageExtractOptions)
        );
        FileHelper::createDirectory($commandMessageExtractOptions['sourcePath']);
        foreach ($commandMessageExtractOptions as &$commandMessageExtractOption) {
            if (is_bool($commandMessageExtractOption)) {
                $commandMessageExtractOption = ($commandMessageExtractOption) ? 'true' : 'false';
            }
            if (is_array($commandMessageExtractOption)) {
                $commandMessageExtractOption = implode(',', $commandMessageExtractOption);
            }
        }
        $sourceFileName = Inflector::camel2id(Inflector::camelize(__METHOD__)) . '.php';
        $sourceFilePath = implode(DIRECTORY_SEPARATOR, [
            $commandMessageExtractOptions['sourcePath'],
            $sourceFileName
        ]);
        $sourceFileContent = <<<EOD
<?php

/**
 * This file is automatically generated by 'yii {$this->getRoute()}' command.
 * It contains calls of the translator function for source code messages extraction.
 */


EOD;
        $model = $this->model;
        $ids = $model::find()->select('id')->orderBy('id')->column();
        foreach ($ids as $id) {
            $sourceFileContent .= sprintf(
                '%s("%s", "%s");%s',
                $commandMessageExtractOptions['translator'],
                $translatorCategory,
                $id,
                PHP_EOL
            );
        }
        file_put_contents($sourceFilePath, $sourceFileContent, LOCK_EX);
        $commandMessageExtractRoute = 'message/extract';
        try {
            $exitCode = $this->run($commandMessageExtractRoute, $commandMessageExtractOptions);
        } catch (Exception $e) {
            $exitCode = self::EXIT_CODE_ERROR;
            $command = [$commandMessageExtractRoute];
            foreach ($commandMessageExtractOptions as $key => $value) {
                $command[] = sprintf('    --%s=%s', $key, $value);
            }
            $command = implode(' \\' . PHP_EOL, $command);
            $this->messages = [
                Console::ansiFormat('Command failed:', [Console::FG_RED]),
                Console::ansiFormat($command, [Console::FG_RED]),
                Console::ansiFormat(sprintf('Error: %s', $e->getMessage()), [Console::FG_RED])
            ];
            $this->displayMessages();
        }
        return $exitCode;
    }

    /**
     * Parses applications for collecting and saving routes.
     * @see Route::tableName()
     *
     * @param string $configFile The path or alias of the configuration file.
     * @link http://www.yiiframework.com/doc-2.0/guide-concept-configurations.html#application-configurations
     * The format of the configuration file is below:
     *
     * ```php
     * <?php
     * return [
     *     [], // the configuration of the first web-app
     *     [], // the configuration of the second web-app
     * ];
     * ```
     *
     * @return int CLI exit code
     * @throws InvalidArgumentException
     */
    public function actionCollect($configFile)
    {
        $configFile = Yii::getAlias($configFile);
        if (!is_file($configFile)) {
            throw new InvalidArgumentException(sprintf(
                'The "%s" configuration file does not exist.',
                $configFile
            ));
        }
        $rows = [];
        $configs = require $configFile;
        foreach ($configs as $config) {
            $app = new Application($config);
            $app->getErrorHandler()->unregister();
            $appRows = $this->getRows($app);
            $this->refillRows($appRows, $app->id);
            $rows = array_merge($rows, $appRows);
        }

        return $this->executeQueries($rows);
    }

    /**
     * Displays the messages, when the verbose mode is on.
     * @see RoutesController::$silent
     * @see RoutesController::$this->messages
     */
    protected function displayMessages()
    {
        if ($this->verbose) {
            $this->messages[] = '';
            $this->stdout(implode(PHP_EOL, $this->messages));
        }
    }

    /**
     * Executes queries in a transaction in the following order:
     * 1. Create the temporary table.
     * 2. Insert the data from an argument of this method into the temporary table.
     * 3. Update the data of the original table from the temporary table, if necessary.
     * 4. Insert the data from the temporary table into the original table, if necessary.
     * 5. Delete the obsolete data from the source table, if necessary.
     * 6. Drop the temporary table.
     * @see Route::tableName()
     *
     * @param array $rows The data to insert into the temporary table.
     *
     * @return int CLI exit code
     * @throws RuntimeException
     */
    protected function executeQueries($rows)
    {
        $exitCode = self::EXIT_CODE_NORMAL;        
        $db = Yii::$app->getDb();
        $dbSchema = $db->getSchema();
        $transaction = $db->beginTransaction();
        try {
            $model = $this->model;
            $table = $model::tableName();
            $tempTable = uniqid(sprintf('yii2_ext_cetver_routes_collector_%s_', $table));
            $sqlReplacePairs = [
                '<table>' => $table,
                '<tempTable>' => $tempTable,
            ];
            $tempTableColumns = [
                'id' => $dbSchema->createColumnSchemaBuilder($dbSchema::TYPE_TEXT)->notNull(),
                'parent_id' => $dbSchema->createColumnSchemaBuilder($dbSchema::TYPE_TEXT),
                'type' => $dbSchema->createColumnSchemaBuilder($dbSchema::TYPE_SMALLINT)->notNull(),
                'PRIMARY KEY ({{id}})',
            ];
            $tempTableOptions = null;
            if ($db->driverName === 'mysql') {
                // for avoiding case insensitive collation, using BINARY
                $tempTableColumns['id'] = $dbSchema::TYPE_STRING . '(255) BINARY NOT NULL';
                $tempTableColumns['parent_id'] = $dbSchema::TYPE_STRING . '(255) BINARY';
                $tempTableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }
            $db->createCommand()->createTable($tempTable, $tempTableColumns, $tempTableOptions)->execute();

            foreach ($rows as $attributes) {
                $model->attributes = $attributes;
                if (!$model->validate()) {
                    throw new RuntimeException(implode(PHP_EOL, [
                        'The data presented below',
                        VarDumper::dumpAsString($attributes),
                        'Did not pass validation',
                        'Errors occurred',
                        VarDumper::dumpAsString($model->errors),
                    ]));
                }
            }

            $db->createCommand()->batchInsert($tempTable, ['parent_id', 'type', 'id'], $rows)->execute();

            $this->messages[] = 'Table ' . Console::ansiFormat($table, [Console::FG_YELLOW]);

            $sql = strtr(
                "
                UPDATE {{<table>}}
                SET
                  {{parent_id}} = (
                    SELECT {{parent_id}} FROM {{<tempTable>}} WHERE {{id}} = {{<table>}}.{{id}}
                  ),
                  {{type}} = (
                    SELECT {{type}} FROM {{<tempTable>}} WHERE {{id}} = {{<table>}}.{{id}}
                  )
                WHERE {{id}} IN (
                  SELECT {{q}}.{{id}}
                  FROM (
                    SELECT {{<table>}}.{{id}}
                    FROM {{<table>}}
                    INNER JOIN {{<tempTable>}}
                    ON {{<tempTable>}}.{{id}} = {{<table>}}.{{id}} 
                    AND (
                      COALESCE({{<tempTable>}}.{{parent_id}}, '') <> COALESCE({{<table>}}.{{parent_id}}, '') OR 
                      {{<tempTable>}}.{{type}} <> {{<table>}}.{{type}} 
                    )
                  ) {{q}}                  
                )                                                         
                ",
                $sqlReplacePairs
            );
            $numRows = $db->createCommand($sql)->execute();
            $this->messages[] = '    Number of updated rows: ' . Console::ansiFormat($numRows, [Console::FG_GREEN]);

            $sql = strtr(
                "
                INSERT INTO {{<table>}} (
                  {{id}},
                  {{parent_id}},
                  {{type}}
                )
                SELECT
                  {{<tempTable>}}.{{id}},
                  {{<tempTable>}}.{{parent_id}},
                  {{<tempTable>}}.{{type}}
                FROM {{<tempTable>}}
                LEFT JOIN {{<table>}}
                ON {{<table>}}.{{id}} = {{<tempTable>}}.{{id}}
                AND COALESCE({{<table>}}.{{parent_id}}, '') = COALESCE({{<tempTable>}}.{{parent_id}}, '')
                AND {{<table>}}.{{type}} = {{<tempTable>}}.{{type}}
                WHERE {{<table>}}.{{id}} IS NULL
                ",
                $sqlReplacePairs
            );
            $numRows = $db->createCommand($sql)->execute();
            $this->messages[] = '    Number of inserted rows: ' . Console::ansiFormat($numRows, [Console::FG_GREEN]);

            $sql = strtr(
                '
                DELETE FROM {{<table>}}
                WHERE {{id}} NOT IN (
                  SELECT {{id}}
                  FROM {{<tempTable>}}
                )
                ',
                $sqlReplacePairs
            );
            $numRows = $db->createCommand($sql)->execute();
            $this->messages[] = '    Number of deleted rows: ' . Console::ansiFormat($numRows, [Console::FG_GREEN]);

            $db->createCommand()->dropTable($tempTable)->execute();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            $exitCode = self::EXIT_CODE_ERROR;
            $this->messages[] = Console::ansiFormat('Error:', [Console::FG_RED]);
            $this->messages[] = Console::ansiFormat($e->getMessage(), [Console::FG_RED]);
        }
        $this->displayMessages();

        return $exitCode;
    }

    /**
     * Refills the rows to insert into "routes" table.
     * @see Route::tableName()
     *
     * @param array $rows The rows to refill.
     * @param string $appId The application ID.
     * @see \yii\web\Application::$id
     */
    protected function refillRows(&$rows, $appId)
    {
        foreach ($rows as &$row) {
            $id = implode(Route::SEPARATOR_ROUTE, [
                $appId,
                $row['unique_id']
            ]);
            $parentId = implode(Route::SEPARATOR_ROUTE, array_filter([
                $appId,
                $row['parent_id']
            ]));
            $row['id'] = $id;
            $row['parent_id'] = $parentId;

            unset($row['unique_id']);
        }
        array_unshift($rows, [
            'parent_id' => null,
            'type' => Route::TYPE_APPLICATION,
            'id' => $appId,
        ]);
    }

    /**
     * Returns the rows to insert into "routes" table.
     * @see Route::tableName()
     *
     * @param \yii\base\Module $module The module instance.
     *
     * @return array
     */
    protected function getRows(\yii\base\Module $module)
    {
        /**
         * @var $controller \yii\web\Controller
         */
        $rows = [];
        $moduleUniqueId = $module->getUniqueId();
        foreach ($module->getModules() as $moduleId => $item) {
            $childModule = $module->getModule($moduleId);
            if ($childModule === null) {
                continue;
            }
            $rows[] = [
                'parent_id' => $moduleUniqueId,
                'unique_id' => $childModule->getUniqueId(),
                'type' => Route::TYPE_MODULE,
            ];
            foreach ($this->getRows($childModule) as $row) {
                $rows[] = $row;
            }
        }

        $controllersRoutes = [];
        foreach ($this->getControllersBaseNames($module->controllerPath) as $controllersBaseName) {
            $controllerClass = implode(self::SEPARATOR_NAMESPACE, [
                $module->controllerNamespace,
                $controllersBaseName,
            ]);
            if ($this->isValidController($controllerClass)) {
                $controllerRoute = Inflector::camel2id(
                    substr($controllersBaseName, 0, -self::LENGTH_WORD_CONTROLLER),
                    self::SEPARATOR_ID,
                    true
                );
                $controllersRoutes[$controllerClass] = $controllerRoute;
            }
        }
        foreach ($this->parseControllerMap($module->controllerMap) as $item) {
            $controllerClass = $item['controllerClass'];
            if ($this->isValidController($controllerClass)) {
                $controllersRoutes[$controllerClass] = $item['controllerRoute'];
            }
        }
        foreach ($controllersRoutes as $controllerClass => $controllerRoute) {
            $createdController = $module->createController($controllerRoute);
            if ($createdController === false) {
                throw new RuntimeException(sprintf(
                    'Failed to create the controller by the "%s" route',
                    $controllerRoute
                ));
            }
            $controller = $createdController[0];
            $actionsIds = $this->getActionsIds($controller);
            if (!empty($actionsIds)) {
                $controllerUniqueId = $controller->getUniqueId();
                $rows[] = [
                    'parent_id' => $moduleUniqueId,
                    'unique_id' => $controllerUniqueId,
                    'type' => Route::TYPE_CONTROLLER,
                ];
                foreach ($actionsIds as $actionId) {
                    $actionUniqueId = implode(Route::SEPARATOR_ROUTE, [
                        $controllerUniqueId,
                        $actionId
                    ]);
                    $rows[] = [
                        'parent_id' => $controllerUniqueId,
                        'unique_id' => $actionUniqueId,
                        'type' => Route::TYPE_ACTION,
                    ];
                }
            }
        }

        return $rows;
    }

    /**
     * Returns valid actions IDs of the specified controller.
     * @see \yii\web\Controller::actions()
     * @see \yii\base\Action::$id
     *
     * @param \yii\web\Controller $controller The controller instance.
     *
     * @return array
     */
    protected function getActionsIds(\yii\web\Controller $controller)
    {
        $actions = array_keys($controller->actions());
        $rc = new ReflectionClass($controller);
        foreach ($rc->getMethods() as $method) {
            $methodName = $method->getName();
            if (strpos($methodName, 'action') === 0 &&
                $methodName !== 'actions' &&
                $method->isPublic() &&
                !$method->isStatic()
            ) {
                $actions[] = Inflector::camel2id(
                    substr($methodName, self::LENGTH_WORD_ACTION),
                    self::SEPARATOR_ID,
                    true
                );
            }
        }

        return array_unique($actions);
    }

    /**
     * Yields an array with keys: "controllerRoute", "controllerClass", where
     * "controllerRoute" is a controller ID from controller mapping
     * "controllerClass" is a controller class from controller mapping
     * @see \yii\base\Module::$controllerMap
     *
     * @param array $controllerMap The controller mapping.
     * @see \yii\base\Module::$controllerMap
     *
     * @return \Generator
     */
    protected function parseControllerMap($controllerMap)
    {
        foreach ($controllerMap as $controllerRoute => $params) {
            $controllerClass = (is_string($params)) ? $params : $params['class'];
            yield compact('controllerRoute', 'controllerClass');
        }
    }

    /**
     * Checks whether the controller is valid.
     *
     * @param string $controllerClass The controller class to check.
     *
     * @return bool
     */
    protected function isValidController($controllerClass)
    {
        if (class_exists($controllerClass)) {
            $rc = new ReflectionClass($controllerClass);

            return !$rc->isAbstract() && $rc->isSubclassOf('yii\web\Controller');
        }

        return false;
    }

    /**
     * Yields the controller base names if the directory is not empty, otherwise null.
     *
     * @param string $directory The controllers directory.
     *
     * @return \Generator
     */
    protected function getControllersBaseNames($directory)
    {
        /**
         * @var $directoryIterator DirectoryIterator
         */
        if (!is_dir($directory)) {
            yield;
        } else {
            $di = new DirectoryIterator($directory);
            $ri = new RegexIterator($di, '/^.+Controller\.php/');
            foreach ($ri as $directoryIterator) {
                yield $directoryIterator->getFileInfo()->getBasename('.php');
            }
        }
    }
}
