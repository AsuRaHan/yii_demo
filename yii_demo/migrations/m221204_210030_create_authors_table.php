<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%authors}}`.
 */
class m221204_210030_create_authors_table extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        if ($this->db->getTableSchema('{{%authors}}')) {
            return;
        }
        $this->createTable('{{%authors}}', [
            'id' => $this->primaryKey(),
            'surname' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'patronymic' => $this->string(),
            'description' => $this->text(),
            'biography' => $this->text(),
            'birthday' => $this->date(),
            'image' => $this->binary(),
        ]);

        $this->createTable('{{%authors_vs_books}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'book_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('authors_vs_books_author_id_fk', 'authors_vs_books', 'author_id', 'authors', 'id');
        $this->addForeignKey('authors_vs_books_book_id_fk', 'authors_vs_books', 'book_id', 'books', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        if ($this->db->getTableSchema('{{%authors}}')) {
            return;
        }
        $this->dropTable('{{%authors}}');
    }

}
