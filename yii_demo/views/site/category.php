<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Category';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['site/category']];
$this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], $crumbs);

function renderCat($cats,$sort = false){
//    echo '<pre>';
//    var_dump($cats[0]);
//    echo '</pre>';
    if($sort){
    usort($cats, function ($a, $b) {
        return $a['sorted'] - $b['sorted'];
    });}
    echo '<ul>';
    foreach ($cats as $cat) {
        if($cat->children){
            echo '<li>' . Html::a($cat->name, $cat->url, ['class' => 'profile-link']);
            renderCat($cat->children,true);
            echo '</li>';
        }else{
            echo '<li>' . Html::a($cat->name, $cat->url, ['class' => 'profile-link']) . '</li>';
        }
    }
    echo '</ul>';
}
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

        <?php renderCat($cats,$firstSort);  ?>
    
    <code><?= __FILE__ ?></code>
</div>
