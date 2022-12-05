<?php

use yii\db\Migration;

/**
 * Class m221205_085714_add_fk
 */
class m221205_085714_add_fk extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createIndex(
                'idx-authors_vs_books-uii',
                '{{%authors_vs_books}}',
                ['author_id', 'book_id'],
                true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m221205_085714_add_fk cannot be reverted.\n";

        return false;
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m221205_085714_add_fk cannot be reverted.\n";

      return false;
      }
     */
}
