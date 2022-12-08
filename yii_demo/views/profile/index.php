<?php
/** @var yii\web\View $this */
/** @var app\models\User $model */
?>
<h1>profile/index</h1>

<?php $form = \kartik\form\ActiveForm::begin([]); ?>

<?= $form->field($model, 'email')->input('email', ['maxlength' => true]) ?>

<div class="form-group">
    <?= \kartik\helpers\Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
</div>

<?php \kartik\form\ActiveForm::end(); ?>

<p>
    You may change the content of this page by modifying
    the file <code><?= __FILE__; ?></code>.
</p>
