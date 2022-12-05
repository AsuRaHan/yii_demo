<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Authors;
use kartik\select2\Select2;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\book $model */
/** @var yii\widgets\ActiveForm $form */
//dd($model->authors);

$url = \yii\helpers\Url::to(['/admin/author/list']);
// Get the initial saved city data (note $model->city is an array of city ids)
$dataList = Authors::find()->andWhere(['id' => $model->authors])->all();
$data = \yii\helpers\ArrayHelper::map($dataList, 'id', 'name');

?>

<div class="book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_is')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?=
    $form->field($model, 'authors')->widget(Select2::classname(), [
        'data' => $data,
        'options' => ['multiple' => true, 'placeholder' => 'Search for a city ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 2,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(city) { return city.text; }'),
            'templateSelection' => new JsExpression('function (city) { return city.text; }'),
        ],
    ]);
    ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'image')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
