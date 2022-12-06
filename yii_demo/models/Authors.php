<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $surname
 * @property string $name
 * @property string|null $patronymic
 * @property string|null $description
 * @property string|null $biography
 * @property string|null $birthday
 * @property resource|null $image
 *
 * @property AuthorsVsBooks[] $authorsVsBooks
 */
class Authors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'authors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['surname', 'name'], 'required'],
            [['description', 'biography', 'image'], 'string'],
            [['birthday'], 'safe'],
            [['surname', 'name', 'patronymic'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'surname' => Yii::t('app', 'Surname'),
            'name' => Yii::t('app', 'Name'),
            'patronymic' => Yii::t('app', 'Patronymic'),
            'description' => Yii::t('app', 'Description'),
            'biography' => Yii::t('app', 'Biography'),
            'birthday' => Yii::t('app', 'Birthday'),
            'image' => Yii::t('app', 'Image'),
        ];
    }

    /**
     * Gets query for [[AuthorsVsBooks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorsVsBooks()
    {
        return $this->hasMany(AuthorsVsBooks::class, ['author_id' => 'id']);
    }

    public function getBooks()
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])->viaTable(AuthorsVsBooks::tableName(), ['author_id' => 'id']);
    }

    public static function getAllAuthors() {
        return self::find()->select(['name'])->indexBy('id')->column();
    }
}
