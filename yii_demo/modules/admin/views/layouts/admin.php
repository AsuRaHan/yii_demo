<?php
/** @var yii\web\View $this */

/** @var string $content */
use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <title>Admin | <?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="d-flex flex-column h-100">
        <?php $this->beginBody() ?>

        
            <?php
            NavBar::begin([
                'brandLabel' => Yii::$app->name,
                'brandUrl' => Yii::$app->homeUrl,
                'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark sticky-top px-2'],
                'renderInnerContainer' => false,
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav'],
                'items' => [
                    ['label' => 'Gii', 'url' => ['/gii']],
                    Yii::$app->user->isGuest ? ['label' => 'Login', 'url' => ['/site/login']] : '<li class="nav-item">'
                            . Html::beginForm(['/site/logout'])
                            . Html::submitButton(
                                    'Logout (' . Yii::$app->user->identity->username . ')',
                                    ['class' => 'nav-link btn btn-link logout']
                            )
                            . Html::endForm()
                            . '</li>'
                ]
            ]);
            NavBar::end();
            ?>
       

        <div class="d-flex flex-wrap">
            <aside style="width: 15rem;">
                <ul class="list-group  rounded-0 widget widget-menu unstyled sticky-top">
                    <li class="list-group-item"><?= Html::a('Users managment', '/admin/user') ?></li>
                    <li class="list-group-item"><?= Html::a('Books managment', '/admin/book') ?></li>
                    <li class="list-group-item"><?= Html::a('Authors managment', '/admin/author') ?></li>
                </ul>
            </aside>
            <main style="width:calc(100% - 15rem);" class="px-2">
<?= Alert::widget() ?>
<?= $content ?>
            </main>
        </div>

<?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
