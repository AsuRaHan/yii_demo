<?php

namespace app\models;

use app\models\Authors;
use Yii;
use app\models\User;
use yii\data\Pagination;
use yii\db\Query;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property int|null $user_is
 * @property string $name
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $image
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
        if(!$avb){
            return 'Автор неизвестен';
        }
        $allAuthorsName = '';
        foreach (Authors::findAll($avb) as $value) {
            $allAuthorsName .= "$value->name $value->patronymic $value->surname<br>" . PHP_EOL;
        }
        return $allAuthorsName;
    }

    public function getAuthor() {
        return $this->hasMany(Authors::class, ['id' => 'author_id'])->viaTable(AuthorsVsBooks::tableName(), ['book_id' => 'id']); // '{{%authors_vs_books}}'
    }
    public function getAuthorIds() {
        return $this->authors = AuthorsVsBooks::find()->select('author_id')->where(['book_id' => $this->id])->column();
    }
    public function getUserName() {
        $avb = User::find()->where(['id' => $this->user_is])->one();
        return $avb ? $avb->username : 'No';
    }
}
