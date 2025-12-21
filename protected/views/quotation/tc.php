<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\QuotationTc;
use app\models\RateEm;
use app\models\Medical;
use app\models\MedicalCheckup;

$this->registerJsFile(
    '@web/theme/assets/js/easy-number-separator.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

$rateEms = RateEm::rateEms();
$medicalCheckups = MedicalCheckup::medicalCheckups();
$medicals = Medical::find()->orderBy(['name' => SORT_ASC])->all();
$medicals = ArrayHelper::map($medicals, 'code', 'code');

$this->title = 'TC - ' . Yii::$app->name;
?>

<div class="modal fade uw-upload-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload UW Limit</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/upload-uw-limit'], 'post', ['id' => 'uw-upload-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'quotation_id', $quotationModel->id) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="file">File</label>
                            <?= Html::input('file', 'file', null, ['class' => 'form-control', 'required' => true]) ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= Html::submitButton('<i class="fa fa-upload"></i> Upload', ['class' => 'btn btn-primary waves-effect waves-light']) ?>
                        <?= Html::a('<i class="fa fa-file-excel-o"></i> Template', ['uw-template'], ['class' => 'btn btn-success waves-effect waves-light']); ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade uw-create-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Create UW Limit</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/create-uw-limit'], 'post', ['id' => 'uw-create-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'quotation_id', $quotationModel->id) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="min_si">Min SI</label>
                            <?= Html::input('number', 'min_si', null, [
                                'class' => 'form-control',
                                'id' => 'min_si',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="max_si">Max SI</label>
                            <?= Html::input('number', 'max_si', null, [
                                'class' => 'form-control',
                                'id' => 'max_si',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="min_age">Min Age</label>
                            <?= Html::input('number', 'min_age', null, [
                                'class' => 'form-control',
                                'id' => 'min_age',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="max_age">Max Age</label>
                            <?= Html::input('number', 'max_age', null, [
                                'class' => 'form-control',
                                'id' => 'max_age',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="medical_code">Medical Code</label>
                            <?= Html::dropDownList('medical_code', null, $medicals, [
                                'prompt' => '- Select Medical Code -',
                                'id' => 'medical_code',
                                'class' => 'form-control slct2',
                                'required' => 'required',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success waves-effect waves-light']) ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="quotation-tc-create">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">TC</h2>
            <h5 class="p-0 m-0"><?= $quotationModel->proposal_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $quotationModel->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Member</h3>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($formModel, 'min_age')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => $model->min_age,
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($formModel, 'max_age')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => $model->max_age,
                        ]) ?>
                    </div>
                </div>
                <?= $form->field($formModel, 'age_term')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => $model->age_term,
                ]) ?>
                <?= $form->field($formModel, 'max_term')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => $model->max_term,
                ]) ?>
                <?= $form->field($formModel, 'retroactive')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => $model->retroactive,
                ]) ?>
                <div class="form-group">
                    <label for="max_si">Max SI</label>
                    <?= Html::input('text', null, number_format($model->max_si), [
                        'class' => 'form-control',
                        'id' => 'max-si',
                        'step' => 'any',
                        'required' => 'required',
                    ]) ?>
                </div>
                <?= $form->field($formModel, 'max_si')->textInput([
                    'maxlength' => true,
                    'type' => 'hidden',
                    'id' => 'max-si-result',
                    'value' => $model->max_si,
                ])->label(false) ?>
                <div class="form-group">
                    <label for="min_premi">Min Premi</label>
                    <?= Html::input('text', null, number_format($model->min_premi), [
                        'class' => 'form-control',
                        'id' => 'min-premi',
                        'step' => 'any',
                        'required' => 'required',
                    ]) ?>
                </div>
                <?= $form->field($formModel, 'min_premi')->textInput([
                    'maxlength' => true,
                    'type' => 'hidden',
                    'id' => 'min-premi-result',
                    'value' => $model->min_premi,
                ])->label(false) ?>
                <?= $form->field($formModel, 'maturity_age')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => $model->maturity_age,
                ]) ?>
                <?= $form->field($formModel, 'rate_em')
                    ->dropDownList($rateEms, [
                        'prompt' => '- Select Rate EM -',
                        'class' => 'form-control',
                        'value' => $model->rate_em,
                    ]) ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($formModel, 'refund_premium')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => $model->refund_premium,
                        ]) ?>
                    </div>
                    <div class="col-md-8">
                        <?= $form->field($formModel, 'refund_type')
                            ->radioList([
                                QuotationTc::REFUND_TYPE_GROSS => QuotationTc::REFUND_TYPE_GROSS,
                                QuotationTc::REFUND_TYPE_NETT => QuotationTc::REFUND_TYPE_NETT,
                            ], [
                                'value' => $model->refund_type,
                            ]) ?>
                    </div>
                </div>
                <?= $form->field($formModel, 'refund_doc')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => $model->refund_doc,
                ]) ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($formModel, 'grace_period')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => $model->grace_period,
                        ]) ?>
                    </div>
                    <div class="col-md-8">
                        <?= $form->field($formModel, 'grace_type')
                            ->radioList([
                                QuotationTc::GRACE_TYPE_CALENDAR => QuotationTc::GRACE_TYPE_CALENDAR,
                                QuotationTc::GRACE_TYPE_WORK_DAY => QuotationTc::GRACE_TYPE_WORK_DAY,
                            ], [
                                'value' => $model->grace_type,
                            ]) ?>
                    </div>
                </div>
                <?= $form->field($formModel, 'claim_doc')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => $model->claim_doc,
                ]) ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($formModel, 'claim_ratio')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => $model->claim_ratio,
                        ]) ?>
                    </div>
                    <div class="col-md-8">
                        <?= $form->field($formModel, 'claim_type')
                            ->radioList([
                                QuotationTc::CLAIM_TYPE_GROSS => QuotationTc::CLAIM_TYPE_GROSS,
                                QuotationTc::CLAIM_TYPE_NETT => QuotationTc::CLAIM_TYPE_NETT,
                            ], [
                                'value' => $model->claim_type,
                            ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h3>Others</h3>
                <div class="form-group">
                    <label for="administration-cost">Administration Cost</label>
                    <?= Html::input('text', null, number_format($model->administration_cost), [
                        'class' => 'form-control',
                        'id' => 'administration-cost',
                        'step' => 'any',
                        'required' => 'required',
                    ]) ?>
                </div>
                <?= $form->field($formModel, 'administration_cost')->textInput([
                    'maxlength' => true,
                    'type' => 'hidden',
                    'id' => 'administration-cost-result',
                    'value' => $model->administration_cost,
                ])->label(false) ?>
                <div class="form-group">
                    <label for="policy-cost">Policy Cost</label>
                    <?= Html::input('text', null, number_format($model->policy_cost), [
                        'class' => 'form-control',
                        'id' => 'policy-cost',
                        'step' => 'any',
                        'required' => 'required',
                    ]) ?>
                </div>
                <?= $form->field($formModel, 'policy_cost')->textInput([
                    'maxlength' => true,
                    'type' => 'hidden',
                    'id' => 'policy-cost-result',
                    'value' => $model->policy_cost,
                ])->label(false) ?>
                <div class="form-group">
                    <label for="member-card-cost">Member Card Cost</label>
                    <?= Html::input('text', null, number_format($model->member_card_cost), [
                        'class' => 'form-control',
                        'id' => 'member-card-cost',
                        'step' => 'any',
                        'required' => 'required',
                    ]) ?>
                </div>
                <?= $form->field($formModel, 'member_card_cost')->textInput([
                    'maxlength' => true,
                    'type' => 'hidden',
                    'id' => 'member-card-cost-result',
                    'value' => $model->member_card_cost,
                ])->label(false) ?>
                <div class="form-group">
                    <label for="certificate-cost">Certificate Cost</label>
                    <?= Html::input('text', null, number_format($model->certificate_cost), [
                        'class' => 'form-control',
                        'id' => 'certificate-cost',
                        'step' => 'any',
                        'required' => 'required',
                    ]) ?>
                </div>
                <?= $form->field($formModel, 'certificate_cost')->textInput([
                    'maxlength' => true,
                    'type' => 'hidden',
                    'id' => 'certificate-cost-result',
                    'value' => $model->certificate_cost,
                ])->label(false) ?>
                <div class="form-group">
                    <label for="stamp-cost">Stamp Cost</label>
                    <?= Html::input('text', null, number_format($model->stamp_cost), [
                        'class' => 'form-control',
                        'id' => 'stamp-cost',
                        'step' => 'any',
                        'required' => 'required',
                    ]) ?>
                </div>
                <?= $form->field($formModel, 'stamp_cost')->textInput([
                    'maxlength' => true,
                    'type' => 'hidden',
                    'id' => 'stamp-cost-result',
                    'value' => $model->stamp_cost,
                ])->label(false) ?>
                <?= $form->field($formModel, 'medical_checkup')
                    ->dropDownList($medicalCheckups, [
                        'prompt' => '- Select Rate EM -',
                        'class' => 'form-control',
                        'value' => $model->medical_checkup,
                    ]) ?>
                <?= $form->field($formModel, 'remarks')->textarea([
                    'maxlength' => true,
                    'class' => 'form-control',
                    'value' => $model->remarks,
                ]) ?>
                <?= $form->field($formModel, 'release_date')->textInput([
                    'class' => 'form-control dtpckr',
                    'maxlength' => true,
                    'value' => $model->release_date,
                ]) ?>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::a(
                '<i class="fa fa-check"></i> Approve',
                [
                    'quotation/approve-tc',
                    'id' => $quotationModel->id,
                ],
                [
                    'class' => ($quotationModel->is_req_tc == 1 || empty($uwLimits))
                        ? 'btn btn-primary btn-lg waves-effect waves-light disabled'
                        : 'btn btn-primary btn-lg waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to approve this TC?',
                    'data-method' => 'post',
                ]
            ); ?>
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <hr>
    <div class="row mt-4 mb-4">
        <div class="col-md-6 my-auto">
            <h3 class="p-0 m-0">UW Limit</h3>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a('<i class="fa fa-upload"></i> Upload', 'javascript:void(0)', [
                'class' => 'btn btn-primary waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.uw-upload-modal',
            ]); ?>
            <?= Html::a('<i class="fa fa-plus"></i> Create', 'javascript:void(0)', [
                'class' => 'btn btn-warning waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.uw-create-modal',
            ]); ?>
            <?= Html::a(
                '<i class="fa fa-trash"></i> Delete All',
                [
                    'quotation/delete-all-uw-limit',
                    'quotationId' => $quotationModel->id,
                ],
                [
                    'class' => 'btn btn-danger waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to delete all Rate?',
                    'data-method' => 'post',
                ]
            ); ?>
        </div>
    </div>
    <div class="card-box">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover nowrap m-0">
                                <thead>
                                    <tr>
                                        <th width="1">#</th>
                                        <th>Min SI</th>
                                        <th>Max SI</th>
                                        <th>Min Age</th>
                                        <th>Max Age</th>
                                        <th>Medical Code</th>
                                        <th width="1">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = $pagination->offset + 1;
                                    if (!empty($uwLimits)) :
                                        foreach ($uwLimits as $uwLimit) :
                                    ?>
                                            <tr>
                                                <td><?= $i; ?></td>
                                                <td><?= number_format($uwLimit['min_si']); ?></td>
                                                <td><?= number_format($uwLimit['max_si']); ?></td>
                                                <td><?= $uwLimit['min_age']; ?></td>
                                                <td><?= $uwLimit['max_age']; ?></td>
                                                <td><?= $uwLimit['medical_code']; ?></td>
                                                <td>
                                                    <div class="btn-group mb-2">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                            'class' => 'btn btn-light btn-sm waves-effect',
                                                            'title' => 'Update',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#update-uw-modal-' . $uwLimit['id'],
                                                        ]); ?>
                                                        <?= Html::a(
                                                            '<i class="fa fa-trash"></i>',
                                                            [
                                                                'quotation/delete-uw-limit',
                                                                'id' => $uwLimit['id'],
                                                            ],
                                                            [
                                                                'class' => 'btn btn-light btn-sm waves-effect',
                                                                'title' => 'Delete',
                                                                'data-confirm' => 'Are you sure want to delete?',
                                                                'data-method' => 'post',
                                                            ]
                                                        ); ?>
                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="update-uw-modal-<?= $uwLimit['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-md">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h4 class="modal-title" id="myMediumModalLabel">Update UW Limit #<?= $i; ?></h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?= Html::beginForm(['quotation/update-uw-limit'], 'post', ['id' => 'uw-update-form']) ?>
                                                            <?= Html::input('hidden', 'quotation_id', $quotationModel->id) ?>
                                                            <?= Html::input('hidden', 'id', $uwLimit['id'], [
                                                                'id' => 'id',
                                                                'required' => 'required',
                                                            ]) ?>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="min_si">Min SI</label>
                                                                        <?= Html::input('number', 'min_si', $uwLimit['min_si'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'min_si',
                                                                            'required' => 'required',
                                                                            'value' => $uwLimit['min_si'],
                                                                        ]) ?>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="max_si">Max SI</label>
                                                                        <?= Html::input('number', 'max_si', $uwLimit['max_si'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'max_si',
                                                                            'required' => 'required',
                                                                            'value' => $uwLimit['max_si'],
                                                                        ]) ?>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="min_age">Min Age</label>
                                                                        <?= Html::input('number', 'min_age', $uwLimit['min_age'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'min_age',
                                                                            'required' => 'required',
                                                                            'value' => $uwLimit['min_age'],
                                                                        ]) ?>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="max_age">Max Age</label>
                                                                        <?= Html::input('number', 'max_age', $uwLimit['max_age'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'max_age',
                                                                            'required' => 'required',
                                                                            'value' => $uwLimit['max_age'],
                                                                        ]) ?>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label for="medical_code">Medical Code</label>
                                                                        <?= Html::dropDownList('medical_code', $uwLimit['medical_code'], $medicals, [
                                                                            'prompt' => '- Select Medical Code -',
                                                                            'id' => 'medical_code',
                                                                            'class' => 'form-control slct2',
                                                                            'required' => 'required',
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success waves-effect waves-light']) ?>
                                                                </div>
                                                            </div>
                                                            <?= Html::endForm() ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php
                                            $i++;
                                        endforeach;
                                    else :
                                        echo '<tr><td class="text-center" colspan="100">No data</td></tr>';
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$script = <<< JS
    easyNumberSeparator({
      selector: '#max-si',
      separator: ',',
      resultInput: '#max-si-result',
    })
    easyNumberSeparator({
      selector: '#min-premi',
      separator: ',',
      resultInput: '#min-premi-result',
    })
    easyNumberSeparator({
      selector: '#administration-cost',
      separator: ',',
      resultInput: '#administration-cost-result',
    })
    easyNumberSeparator({
      selector: '#policy-cost',
      separator: ',',
      resultInput: '#policy-cost-result',
    })
    easyNumberSeparator({
      selector: '#member-card-cost',
      separator: ',',
      resultInput: '#member-card-cost-result',
    })
    easyNumberSeparator({
      selector: '#certificate-cost',
      separator: ',',
      resultInput: '#certificate-cost-result',
    })
    easyNumberSeparator({
      selector: '#stamp-cost',
      separator: ',',
      resultInput: '#stamp-cost-result',
    })
JS;
$this->registerJs($script);
?>