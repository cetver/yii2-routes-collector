<?php

use yii\db\Migration;

/**
 * Handles the creation of table `yii2_ext_cetver_routes_collector_pages`.
 */
class m170613_165235_create_pages_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $table = 'yii2_ext_cetver_routes_collector_pages';
        $columns = [
            'id' => $this->primaryKey(),
            'route_id' => $this->text()->notNull(),
            'title' => $this->text(),
            'meta_description' => $this->text(),
            'meta_keywords' => $this->text(),
            'FOREIGN KEY({{route_id}}) REFERENCES {{routes}}({{id}}) ON UPDATE CASCADE ON DELETE CASCADE',
        ];
        $options = null;
        $db = $this->db;
        if ($db->driverName === 'mysql') {
            $schema = $db->getSchema();
            $columns['route_id'] = $schema::TYPE_STRING . '(255) BINARY NOT NULL';
            $options = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($table, $columns, $options);
        $this->createIndex(
            'yii2_ext_cetver_routes_collector_pages_route_id_idx',
            'yii2_ext_cetver_routes_collector_pages',
            'route_id'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('yii2_ext_cetver_routes_collector_pages');
    }
}
