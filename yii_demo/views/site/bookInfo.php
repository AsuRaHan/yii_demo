<?php

use \yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\Book $book */
$this->title = 'My Yii Book - ' . $book->name;
//dump($book);
?>
<div class="d-flex justify-content-center">
    <div class="card text-center" style="width: 25rem;">
        <div class="card-header">
            <?= $book->getAuthorsName() ?>
        </div>
        <img src="<?= $book->image ?:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Noimage.svg/739px-Noimage.svg.png' ?>" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><?= $book->name ?></h5>
            <p class="card-text"><?= $book->description ?></p>

        </div>
        <div class="card-footer text-muted">
            <a href="<?= \yii\helpers\Url::to(['/site/checkout', 'id' => $book->id]) ?>" class="btn btn-primary">Get this book</a>
        </div>
    </div>
</div>
