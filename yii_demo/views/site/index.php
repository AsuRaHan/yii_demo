<?php

use \yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\Book $models */
/** @var yii\data\Pagination $pages */
$this->title = 'My Yii Books';

?>


<?=$this->render('searchform',['query'=>'']);?>

<div class="d-flex flex-wrap mb-3">

    <?php foreach ($models as $model) : ?>

        <div class="card m-2" style="width: 19rem;">
            <img src="<?= $model->image ?>" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title"><?= $model->name ?></h5>
                <p class="card-text"><?= $model->description ?></p>

            </div>
            <div class="card-footer">
                <a href="<?= \yii\helpers\Url::to(['/site/book', 'id' => $model->id]) ?>" class="btn btn-success">Go</a>
            </div>
        </div>

    <?php endforeach; ?>

</div>
<div class="d-flex justify-content-center">
    <?= LinkPager::widget([
        'pagination' => $pages,

    ]); ?>
</div>
