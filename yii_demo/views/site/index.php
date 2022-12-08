<?php

$this->title = 'My Yii Books';

/**
 * Такой интересный кастыль чтоб передать в SPA токен :)
 * это просто эксперемент чтоб попробывать как быдет работать SPA скрещенная с обычным приложением
 * такое лучше не делать так как токен по сути передается и его могут увить все кому не лень
 * но для эксперемента пойдет
 */

if (Yii::$app->user->isGuest) {
    $access_token = '';
} else {
    $access_token = '?access-token='.yii::$app->user->identity->access_token;
}
$js = <<<JS
    window.user_access_token = "$access_token";
JS;
$this->registerJs(
    $js,
    \yii\web\View::POS_HEAD
);

$this->registerJsFile(
    '/app.js'
);
?>

<div id="app">

</div>

