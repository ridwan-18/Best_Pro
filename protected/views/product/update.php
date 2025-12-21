<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;

$this->title = 'Update Product - ' . Yii::$app->name;
?>
<div class="product-update">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Update Product</h2>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a(
                '<i class="fa fa-file-text"></i> EM',
                ['em', 'id' => $model->id],
                ['class' => 'btn btn-lg btn-purple waves-effect waves-light']
            ); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>