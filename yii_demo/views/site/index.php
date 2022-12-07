<?php

use \yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\Book $models */
/** @var yii\data\Pagination $pages */
$this->title = 'My Yii Books';

?>


<?=$this->render('searchform',['query'=>'']);?>

<?php if(Yii::$app->session->hasFlash('id_book_error')):?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> <?= Yii::$app->session->getFlash('id_book_error'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="d-flex flex-wrap mb-3">

    <?php foreach ($models as $model) : ?>

        <div class="card m-2" style="width: 19rem;">
            <img src="<?= $model->image ?:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Noimage.svg/739px-Noimage.svg.png' ?>" class="card-img-top" alt="...">
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
