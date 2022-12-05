<?php

namespace app\models;

use Yii;
use app\models\Authors;
use app\models\User;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property int|null $user_is
 * @property string $name
 * @property string|null $description
 * @property string|null $isbn
 * @property resource|null $image
 */
class Book extends \yii\db\ActiveRecord {

    public $authors = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_is'], 'integer'],
            [['name'], 'required'],
            [['description', 'image'], 'string'],
            [['name', 'isbn'], 'string', 'max' => 255],
            [['authors'], 'safe'], // !!!
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_is' => Yii::t('app', 'User Is'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'isbn' => Yii::t('app', 'ISBN'),
            'image' => Yii::t('app', 'Image'),
            'authors' => Yii::t('app', 'Authors'), // !!!
        ];
    }

    public function afterSave($insert, $changedAttributes) { // !!!
        $avb = AuthorsVsBooks::find()->where(['book_id' => $this->id])->all();
        foreach ($avb as $book) {
            $book->delete();
        }
        if (!empty($this->authors) && is_array($this->authors)) {
            foreach ($this->authors as $authorsId) {
                $newVs = new AuthorsVsBooks();
                $newVs->author_id = $authorsId;
                $newVs->book_id = $this->id;
                if (!$newVs->save()) {
                    throw new \Exception('автор не сохранен.');
                }
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function getAuthorsName() {
        $avb = AuthorsVsBooks::find()->select('author_id')->where(['book_id' => $this->id])->column();
        $allAuthorsName = '';
        foreach (Authors::findAll($avb) as $value) {
            $allAuthorsName .= "$value->name $value->patronymic $value->surname" . PHP_EOL;
        }
        return $allAuthorsName;
    }

    public function getUserName() {
        $avb = User::find()->where(['id' => $this->user_is])->one();
        return $avb ? $avb->username:'No';
    }

    public function afterFind() {
        $this->authors = AuthorsVsBooks::find()->select('author_id')->where(['book_id' => $this->id])->column();
        parent::afterFind();
    }

}
