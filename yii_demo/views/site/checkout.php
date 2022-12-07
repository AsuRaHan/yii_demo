<?php

use \yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var app\models\Book $book */
$this->title = 'My Yii Book - ' . $book->name;
$this->registerJsFile(
    'https://unpkg.com/vue@next'
);
?>
<div class="d-flex justify-content-center">
    <div class="card text-center m-3" style="width: 25%;">
        <div class="card-header">
            <?= $book->getAuthorsName() ?>
        </div>
        <img src="<?= $book->image ?>" class="card-img-top" alt="...">
        <div class="card-body">
            <h5 class="card-title"><?= $book->name ?></h5>
            <p class="card-text"><?= $book->description ?></p>

        </div>
    </div>
    <div class="card m-3" style="width: 75%;">
        <div class="card-header">
            Take this book to read
        </div>

        <div class="card-body">
            <div id="counter">
                Счётчик: {{ counter }}
            </div>

        </div>
    </div>
</div>
<?php
$this->registerJs(<<<JS
    const Counter = {
          data() {
                return {
                  counter: 0
                }
          },
          mounted() {
                // setInterval(() => {
                //     this.counter++
                // }, 1000)
          }
        }

Vue.createApp(Counter).mount('#counter')
JS
);
?>