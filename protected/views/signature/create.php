<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;

$this->title = 'Create Signature - ' . Yii::$app->name;
?>
<div class="signature-create">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'upload-form']]); ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="p-0 m-0">Create Signature</h2>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <?= $form->field($formModel, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($formModel, 'position')->textInput(['maxlength' => true]) ?>
                <?= $form->field($formModel, 'picture_file')->fileInput(['class' => 'form-control']) ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light', 'id' => 'upload-btn']) ?>
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