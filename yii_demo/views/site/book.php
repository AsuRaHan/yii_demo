<?php

use \yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\Book $book */
$this->title = 'My Yii Book';
//dump($book);
?>
<div class="d-flex justify-content-center">
    <div class="card text-center" style="width: 25rem;">
        <div class="card-header">
            <?= $book->getAuthorsName() ?>
        </div>
        <img src="<?= $book->image ?>" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><?= $book->name ?></h5>
            <p class="card-text"><?= $book->description ?></p>

        </div>
        <div class="card-footer text-muted">
            <a href="#" class="btn btn-primary">Go somewhere</a>
        </div>
    </div>
</div>
