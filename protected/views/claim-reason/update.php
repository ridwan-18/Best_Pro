<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;

$this->title = 'Update Claim Reason - ' . Yii::$app->name;
?>
<div class="claim-reason-update">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="p-0 m-0">Update Claim Reason</h2>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-block waves-effect waves-light']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>