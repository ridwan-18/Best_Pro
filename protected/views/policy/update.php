<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\Bank;
use app\models\InsurancePurpose;
use app\models\Policy;

$banks = Bank::find()->orderBy(['name' => SORT_ASC])->all();
$banks = ArrayHelper::map($banks, 'id', 'name');

$insurancePurposes = InsurancePurpose::insurancePurposes();

$this->title = 'Update Policy - ' . Yii::$app->name;
?>

<div class="policy-update">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Update Policy - <?= $policyModel->status; ?></h2>
            <h5 class="p-0 m-0"><?= $policyModel->spa_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <p class="m-0 p-0"><?= $partner->name; ?></p>
            <p class="m-0 p-0"><?= $quotation->proposal_no; ?></p>
        </div>
    </div>
    <?= Alert::widget() ?>
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($policyFormModel, 'quotation_id')->hiddenInput(['value' => $policyModel->quotation_id])->label(false); ?>
    <?= $form->field($policyFormModel, 'spa_date')->hiddenInput(['value' => $policyModel->spa_date])->label(false); ?>
    <?= $form->field($policyFormModel, 'spa_status')->hiddenInput(['value' => $policyModel->spa_status])->label(false); ?>
    <?= $form->field($policyFormModel, 'status')->hiddenInput(['value' => $policyModel->status])->label(false); ?>
    <?php
    if ($policyModel->policy_no != '') :
    ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card-box">
                    <?= $form->field($policyFormModel, 'policy_no')->textInput([
                        'maxlength' => true,
                        'value' => $policyModel->policy_no,
                    ]) ?>
                </div>
            </div>
        </div>
    <?php
    endif;
    ?>
    <div class="card-box">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($policyFormModel, 'partner_zip_code')->textInput([
                    'maxlength' => true,
                    'value' => $partner->zip_code,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_phone')->textInput([
                    'maxlength' => true,
                    'value' => $partner->phone,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_fax')->textInput([
                    'maxlength' => true,
                    'value' => $partner->fax,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_email')->textInput([
                    'maxlength' => true,
                    'value' => $partner->email,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_established_date')->textInput([
                    'class' => 'form-control dtpckr',
                    'maxlength' => true,
                    'value' => $partner->established_date,
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($policyFormModel, 'partner_npwp')->textInput([
                    'maxlength' => true,
                    'value' => $partner->npwp,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_certificate_no')->textInput([
                    'maxlength' => true,
                    'value' => $partner->certificate_no,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_siup')->textInput([
                    'maxlength' => true,
                    'value' => $partner->siup,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_fund_source')->textInput([
                    'maxlength' => true,
                    'value' => $partner->fund_source,
                ]) ?>
                <?= $form->field($policyFormModel, 'partner_insurance_purpose')
                    ->dropDownList($insurancePurposes, [
                        'prompt' => '- Select Insurance Purpose -',
                        'class' => 'form-control',
                        'value' => $partner->insurance_purpose,
                    ]) ?>
                <?= $form->field($policyFormModel, 'partner_insurance_purpose_description')
                    ->textArea([
                        'maxlength' => true,
                        'value' => $partner->insurance_purpose_description,
                    ])
                    ->label(false) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h3>Contact Person</h3>
            <div class="card-box">
                <?= $form->field($policyFormModel, 'pic_name')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->pic_name,
                ]) ?>
                <?= $form->field($policyFormModel, 'pic_title')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->pic_title,
                ]) ?>
                <?= $form->field($policyFormModel, 'pic_id_card_no')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->pic_id_card_no,
                ]) ?>
                <?= $form->field($policyFormModel, 'pic_phone')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->pic_phone,
                ]) ?>
                <?= $form->field($policyFormModel, 'pic_email')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->pic_email,
                ]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <h3>Policy Holder Bank Account</h3>
            <div class="card-box">
                <?= $form->field($policyFormModel, 'bank_id')
                    ->dropDownList($banks, [
                        'prompt' => '- Select Bank -',
                        'class' => 'form-control slct2',
                        'value' => $policyModel->bank_id,
                    ]) ?>
                <?= $form->field($policyFormModel, 'bank_branch')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->bank_branch,
                ]) ?>
                <?= $form->field($policyFormModel, 'bank_account_name')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->bank_account_name,
                ]) ?>
                <?= $form->field($policyFormModel, 'bank_account_no')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->bank_account_no,
                ]) ?>
            </div>
        </div>
    </div>
    <h3>Insurance Info</h3>
    <div class="card-box">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($policyFormModel, 'effective_date')->textInput([
                            'class' => 'form-control dtpckr',
                            'maxlength' => true,
                            'value' => $policyModel->effective_date,
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($policyFormModel, 'end_date')->textInput([
                            'class' => 'form-control dtpckr',
                            'maxlength' => true,
                            'value' => $policyModel->end_date,
                        ]) ?>
                    </div>
                </div>
                <?= $form->field($policyFormModel, 'insurance_period')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->insurance_period,
                ]) ?>
                <?= $form->field($policyFormModel, 'payment_period')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->payment_period,
                ]) ?>
                <?= $form->field($policyFormModel, 'member_insured')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->member_insured,
                ]) ?>
                <?= $form->field($policyFormModel, 'notes')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->notes,
                ]) ?>
                <?= $form->field($policyFormModel, 'work_location')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->work_location,
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($policyFormModel, 'sign_location')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->sign_location,
                ]) ?>
                <?= $form->field($policyFormModel, 'sign_date')->textInput([
                    'class' => 'form-control dtpckr',
                    'maxlength' => true,
                    'value' => $policyModel->sign_date,
                ]) ?>
                <?= $form->field($policyFormModel, 'sign_by')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->sign_by,
                ]) ?>
                <?= $form->field($policyFormModel, 'sign_title')->textInput([
                    'maxlength' => true,
                    'value' => $policyModel->sign_title,
                ]) ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::a(
                '<i class="fa fa-check"></i> Issued Policy',
                [
                    'policy/issue',
                    'id' => $policyModel->id,
                ],
                [
                    'class' => ($policyModel->spa_status == Policy::SPA_STATUS_ISSUED)
                        ? 'btn btn-primary btn-lg waves-effect waves-light disabled'
                        : 'btn btn-primary btn-lg waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to issue this policy?',
                    'data-method' => 'post',
                ]
            ); ?>
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>