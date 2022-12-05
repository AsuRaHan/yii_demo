<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m221204_205909_create_book_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        if ($this->db->getTableSchema('{{%book}}')) {
            return;
        }
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'user_is' => $this->integer()->defaultValue(0),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'isbn' => $this->string(),
            'image' => $this->binary()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        if ($this->db->getTableSchema('{{%book}}')) {
            return;
        }
        $this->dropTable('{{%book}}');
    }

}
