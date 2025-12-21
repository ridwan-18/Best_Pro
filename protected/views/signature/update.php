<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\Signature;

$this->title = 'Update Signature - ' . Yii::$app->name;
?>
<div class="signature-update">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form']]); ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Update Signature</h2>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light', 'id' => 'upload-btn']) ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Policy</h3>
            <div class="card-box">
                <?= $form->field($formModel, 'policy_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($formModel, 'policy_position')->textInput(['maxlength' => true]) ?>
                <img src="<?= Url::base() . Signature::PICTURE_PATH . $model->policy_picture; ?>" alt="Pic" class="mb-2 img-thumbnail w-25">
                <?= $form->field($formModel, 'policy_picture_file')->fileInput(['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Member & Billing</h3>
            <div class="card-box">
                <?= $form->field($formModel, 'member_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($formModel, 'member_position')->textInput(['maxlength' => true]) ?>
                <img src="<?= Url::base() . Signature::PICTURE_PATH . $model->member_picture; ?>" alt="Pic" class="mb-2 img-thumbnail w-25">
                <?= $form->field($formModel, 'member_picture_file')->fileInput(['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Claim</h3>
            <div class="card-box">
                <?= $form->field($formModel, 'claim_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($formModel, 'claim_position')->textInput(['maxlength' => true]) ?>
                <img src="<?= Url::base() . Signature::PICTURE_PATH . $model->claim_picture; ?>" alt="Pic" class="mb-2 img-thumbnail w-25">
                <?= $form->field($formModel, 'claim_picture_file')->fileInput(['class' => 'form-control']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<< JS
    $('#upload-form').submit(function() {
        $('#upload-btn').html('<i class="fa fa-spinner"></i> Loading');
        $('#upload-btn').attr('class', 'btn btn-success btn-lg waves-effect waves-light disabled');
    });
JS;
$this->registerJs($script);
?>