<?php

use yii\db\Migration;

/**
 * Handles the creation of table `routes`.
 */
class m170607_193626_create_routes_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $table = 'routes';
        $columns = [
            'id' => $this->text()->notNull(),
            'parent_id' => $this->text(),
            'type' => $this->smallInteger()->notNull(),
            'PRIMARY KEY ({{id}})'
        ];
        $options = null;
        $db = $this->db;
        if ($db->driverName === 'mysql') {
            $schema = $db->getSchema();
            // for avoiding case insensitive collation, using BINARY
            $columns['id'] = $schema::TYPE_STRING . '(255) BINARY NOT NULL';
            $columns['parent_id'] = $schema::TYPE_STRING . '(255) BINARY';
            $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($table, $columns, $options);
        $this->createIndex('routes_id_type_idx', 'routes', [
            'id',
            'type'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('routes');
    }
}
