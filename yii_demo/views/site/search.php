<?php
/*
 * Страница результатов поиска по каталогу, файл views/catalog/search.php
 */

use yii\helpers\Html;
use yii\helpers\Url;
use \yii\bootstrap5\LinkPager;

?>


<?=$this->render('searchform',['query'=>$query]);?>


<?php if (!empty($books)): ?>
    <h2>Результаты поиска</h2>
    <div class="d-flex flex-wrap mb-3">

        <?php foreach ($books as $product): ?>

            <div class="card m-2" style="width: 19rem;">
                <img src="<?= $product['image'] ?>" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title"><?= $product['name'] ?></h5>
                    <p class="card-text"><?= $product['description'] ?></p>

                </div>
                <div class="card-footer">
                    <a href="<?= Url::to(['/site/book', 'id' => $product['id']]) ?>"
                       class="btn btn-success">Go</a>
                </div>
            </div>

        <?php endforeach; ?>

    </div>
    <div class="d-flex justify-content-center">
        <?= LinkPager::widget(['pagination' => $pages]); /* постраничная навигация */ ?>
    </div>
<?php else: ?>
    <h2>Hичего не найдено.</h2>
<?php endif; ?>

