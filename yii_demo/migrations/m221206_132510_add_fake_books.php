<?php

use yii\db\Migration;

/**
 * Class m221206_132510_add_fake_books
 */
class m221206_132510_add_fake_books extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $faker = \Faker\Factory::create();

        for ($i = 1; $i <= 100; $i++) {
            $b = new \app\models\Book();
//            $b->setIsNewRecord(true);
            $b->name = $faker->userName;
            $b->isbn = $faker->isbn13();
            $b->description = $faker->text;
            $b->image = $faker->imageUrl(360, 480, $faker->userName, true, $faker->userName);;
            $b->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221206_132510_add_fake_books cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221206_132510_add_fake_books cannot be reverted.\n";

        return false;
    }
    */
}
