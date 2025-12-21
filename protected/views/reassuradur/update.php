<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;

$this->title = 'Update Reassuradur - ' . Yii::$app->name;
?>
<div class="reassuradur-update">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="p-0 m-0">Update Reassuradur</h2>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'fax')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'address')->textarea([
                            'maxlength' => true,
                            'class' => 'form-control',
                        ]) ?>
                        <?= $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'established_year')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'value' => '0',
                        ]) ?>
                        <?= $form->field($model, 'tax_payer_identification')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'trade_business_license')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'company_deed')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'pic_name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'payment_due_date')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'value' => '0',
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <h3>Reassuradur Bank</h3>
                        <?= $form->field($model, 'bank_name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'bank_branch')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'bank_account_name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'bank_account_number')->textInput(['maxlength' => true]) ?>
                        <hr>
                        <h3>Payment Bank</h3>
                        <?= $form->field($model, 'payment_bank_name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'payment_bank_branch')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'payment_bank_account_name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'payment_bank_account_number')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
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