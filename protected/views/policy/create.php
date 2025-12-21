<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\Bank;
use app\models\InsurancePurpose;
use app\models\Partner;
use app\models\Quotation;

$getEndDateUrl = Url::to(['policy/get-end-date']);

$quotations = Quotation::find()
    ->asArray()
    ->select([
        Quotation::tableName() . '.id',
        Quotation::tableName() . '.proposal_no',
        Partner::tableName() . '.name AS partner'
    ])
    ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' .  Quotation::tableName() . '.partner_id')
    ->orderBy([Quotation::tableName() . '.id' => SORT_ASC])
    ->all();

$options = [];
foreach ($quotations as $quotation) {
    $items = [];
    $items['value'] = $quotation['id'];
    $items['label'] = $quotation['proposal_no'] . ' - ' . $quotation['partner'];
    $options[] = $items;
}

$quotations = ArrayHelper::map($options, 'value', 'label');

$banks = Bank::find()->orderBy(['name' => SORT_ASC])->all();
$banks = ArrayHelper::map($banks, 'id', 'name');

$insurancePurposes = InsurancePurpose::insurancePurposes();

$this->title = 'Create Policy - ' . Yii::$app->name;
?>

<div class="policy-create">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Create Policy</h2>
        </div>
        <div class="col-md-6 my-auto">
            <?= Html::beginForm(['policy/create'], 'post', ['id' => 'policy-form', 'class' => 'form-inline']) ?>
            <?= Html::dropDownList('quotation_id', Yii::$app->request->post('quotation_id'), $quotations, [
                'prompt' => '- Select Proposal -',
                'id' => 'quotation_id',
                'class' => 'form-control slct2',
                'required' => 'required',
                'onchange' => 'submit()',
            ]) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <?php
    if (Yii::$app->request->post('quotation_id') != '') :
        $quotation = Quotation::findOne(['id' => Yii::$app->request->post('quotation_id')]);
        $partner = Partner::findOne(['id' => $quotation->partner_id]);

        $policyFormModel->partner_insurance_purpose = $partner->insurance_purpose;
    ?>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($policyFormModel, 'quotation_id')->hiddenInput(['value' => Yii::$app->request->post('quotation_id'), 'id' => 'quotation-id'])->label(false); ?>
        <?= $form->field($policyFormModel, 'spa_date')->hiddenInput(['value' => date("Y-m-d")])->label(false); ?>

        <?= $form->field($policyFormModel, 'status')
            ->radioList([
                'New' => 'New',
                'Renewal' => 'Renewal',
            ])->label(false) ?>
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
                    ]) ?>
                    <?= $form->field($policyFormModel, 'pic_title')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'pic_id_card_no')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'pic_phone')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'pic_email')->textInput([
                        'maxlength' => true,
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
                        ]) ?>
                    <?= $form->field($policyFormModel, 'bank_branch')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'bank_account_name')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'bank_account_no')->textInput([
                        'maxlength' => true,
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
                                'id' => 'effective-date',
                                'maxlength' => true,
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($policyFormModel, 'end_date')->textInput([
                                'class' => 'form-control dtpckr',
                                'id' => 'end-date',
                                'maxlength' => true,
                            ]) ?>
                        </div>
                    </div>
                    <?= $form->field($policyFormModel, 'insurance_period')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'payment_period')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'member_insured')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'notes')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'work_location')->textInput([
                        'maxlength' => true,
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($policyFormModel, 'sign_location')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'sign_date')->textInput([
                        'class' => 'form-control dtpckr',
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'sign_by')->textInput([
                        'maxlength' => true,
                    ]) ?>
                    <?= $form->field($policyFormModel, 'sign_title')->textInput([
                        'maxlength' => true,
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12 text-right">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    <?php
    endif;
    ?>
</div>

<?php
$script = <<< JS
    $( "#effective-date" ).change(function() {
        $.ajax({
            url: "{$getEndDateUrl}",
            type: "POST",            
            data: "quotation_id=" + $("#quotation-id").val() + "&effective_date=" + $("#effective-date").val(),
            dataType: "json",
            success: function(data){       
                console.log($data);  
                if (data.end_date != null) {
                    $('#end-date').val(data.end_date);
                }
            }
        });
    }); 
JS;
$this->registerJs($script);
?>