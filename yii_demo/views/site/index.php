<?php

use \yii\bootstrap5\LinkPager;
use \yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Book $models */
/** @var yii\data\Pagination $pages */
$this->title = 'My Yii Books';

$this->registerJsFile(
    'https://unpkg.com/vue@next'
);
$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/axios/1.2.1/axios.min.js'
);
?>

<div id="bookList">
    Счётчик: {{ counter }}
</div>

<?php
$this->registerJs(<<<JS
    const bookList = {
          data() {
                return {
                  books: 0
                }
          },
          mounted() {

          }
        }

Vue.createApp(bookList).mount('#bookList')
JS
);
?>