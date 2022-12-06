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
            [['author'], 'safe'], // !!!
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
            'author' => Yii::t('app', 'Author'), // !!!
        ];
    }

    public function getImageBlob() {
        return 'data:image/jpeg;base64,' . base64_encode($this->image);
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

    public function getUserName() {
        $avb = User::find()->where(['id' => $this->user_is])->one();
        return $avb ? $avb->username : 'No';
    }

    public function afterFind() {
        $this->authors = AuthorsVsBooks::find()->select('author_id')->where(['book_id' => $this->id])->column();
        parent::afterFind();
    }
    /**
     * Результаты поиска по каталогу товаров
     */
    public function getSearchResult($search, $page) {
        $search = $this->cleanSearchString($search);
        if (empty($search)) {
            return [null, null];
        }

        // пробуем извлечь данные из кеша
        $key = 'search-'.md5($search).'-page-'.$page;
        $data = Yii::$app->cache->get($key);

        if ($data === false) { // данных нет в кеше, получаем их заново
            // разбиваем поисковый запрос на отдельные слова
            $words = explode(' ', $search);
            // рассчитываем релевантность для каждого товара
            $relevance = "IF (`name` LIKE '%" . $words[0] . "%', 2, 0)";
            $relevance .= " + IF (`description` LIKE '%" . $words[0] . "%', 1, 0)";
            for ($i = 1; $i < count($words); $i++) {
                $relevance .= " + IF (`name` LIKE '%" . $words[$i] . "%', 2, 0)";
                $relevance .= " + IF (`description` LIKE '%" . $words[$i] . "%', 1, 0)";
            }
            $query = (new Query())
                ->select(['*', 'relevance' => $relevance])
                ->from('book')
                ->where(['like', 'name', $words[0]])
                ->orWhere(['like', 'description', $words[0]]);
            for ($i = 1; $i < count($words); $i++) {
                $query = $query->orWhere(['like', 'name', $words[$i]]);
                $query = $query->orWhere(['like', 'description', $words[$i]]);
            }
            // сортируем разультаты по убыванию релевантности
            $query = $query->orderBy(['relevance' => SORT_DESC]);
            // постраничная навигация
            $pages = new Pagination([
                'totalCount' => $query->count(),
                'pageSize' => 20, //Yii::$app->params['pageSize'],
                'forcePageParam' => false,
                'pageSizeParam' => false
            ]);
            $products = $query
                ->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
            // сохраняем полученные данные в кеше
            $data = [$products, $pages];
            Yii::$app->cache->set($key, $data);
        }

        return $data;
    }

    /**
     * Вспомогательная функция, очищает строку поискового запроса с сайта
     * от всякого мусора
     */
    protected function cleanSearchString($search) {
        $search = iconv_substr($search, 0, 64);
        // удаляем все, кроме букв и цифр
        $search = preg_replace('#[^0-9a-zA-ZА-Яа-яёЁ]#u', ' ', $search);
        // сжимаем двойные пробелы
        $search = preg_replace('#\s+#u', ' ', $search);
        $search = trim($search);
        return $search;
    }
}
