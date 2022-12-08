<?php

use yii\db\Migration;

/**
 * Class m221206_132510_add_fake_books
 */
class m221206_132510_add_fake_books extends Migration {
    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        if ($this->db->getTableSchema('{{%book}}')) {
            return;
        }
        $this->insertFakeBooks();
    }
    /**
     * {@inheritdoc}
     */
    private function insertFakeBooks() {
        echo "Seeds Table book.\n";
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 150; $i++) {
            $this->insert(
                'book',
                [
                    'name' => $faker->sentence(),
                    'description' => $faker->words(32,true),
                    'isbn' => $faker->isbn13(),
                    'image' => $faker->imageUrl(300, 400, $faker->userName, true, $faker->userName),
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        if ($this->db->getTableSchema('{{%book}}')) {
            return;
        }
        echo "Truncate Table book.\n";
        $this->truncateTable('{{%book}}');
        return false;
    }

}
