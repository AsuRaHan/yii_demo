<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\book;
use yii\data\Pagination;
use yii\db\Query;

/**
 * BookSearch represents the model behind the search form of `app\models\book`.
 */
class BookSearch extends book
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_is'], 'integer'],
            [['name', 'description', 'isbn', 'image'], 'safe'],
//            [['authors'], 'safe'], // !!!
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = book::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_is' => $this->user_is,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'isbn', $this->isbn])
            ->andFilterWhere(['like', 'image', $this->image]);

        return $dataProvider;
    }

    public function getSearchResult($search, $page) {
        $search = $this->cleanSearchString($search);
        if (empty($search)) {
            return [null, null];
        }
        $key = 'search-'.md5($search).'-page-'.$page;
        $data = \Yii::$app->cache->get($key);

        if ($data === false) {
            $query = $this->getQuerySearchResult();
            $pages = new Pagination([
                'totalCount' => $query->count(),
                'pageSize' => 20,
                'forcePageParam' => false,
                'pageSizeParam' => false
            ]);
            $books = $query
                ->offset($pages->offset)
                ->limit($pages->limit)
                ->all();
            $data = [$books, $pages];
            \Yii::$app->cache->set($key, $data);
        }

        return $data;
    }

    public function cleanSearchString($search) {
        $search = iconv_substr($search, 0, 64);
        $search = preg_replace('#[^0-9a-zA-ZА-Яа-яёЁ]#u', ' ', $search);
        $search = preg_replace('#\s+#u', ' ', $search);
        $search = trim($search);
        return $search;
    }
    public function getQuerySearchResult($search) {
        $words = explode(' ', $search);
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
        return $query;
    }
}
