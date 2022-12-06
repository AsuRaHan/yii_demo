<?php

use app\models\book;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Authors;

/** @var yii\web\View $this */
/** @var app\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = Yii::t('app', 'Books');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Book'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            'id',
//            'user_is',
            [
                'label' => 'Book in user',
//                'attribute' => 'user_is',
                'value' => function($data) {
                    return $data->getUserName();
                },
            ],
            'name',
            'description:ntext',
            'isbn',
//            [
//                'attribute' => 'image',
//                'format' => 'raw',
//                'value' => function ($data) {
//                    $temp = Html::img($data->getImageBlob());
////                    dd($temp);composer require --prefer-dist yiisoft/yii2-imagine "*"
//                    return $temp;
//                },
//            ],
            [
                'label' => 'Authors',
                'attribute' => 'authors',
                'value' => function($data) {
                    return $data->getAuthorsName();
                }
            ],

            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, book $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
        'pager' => [
            'class' => 'yii\bootstrap5\LinkPager'
        ]
    ]);
    ?>

    <?php Pjax::end(); ?>

</div>
