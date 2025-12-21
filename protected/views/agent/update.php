<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;

$this->title = 'Update Agent - ' . Yii::$app->name;
?>
<div class="agent-update">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="p-0 m-0">Update Agent</h2>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'id_card_number')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-block btn-lg waves-effect waves-light']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>