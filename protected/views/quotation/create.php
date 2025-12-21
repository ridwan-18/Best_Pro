<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\Partner;
use app\models\Province;
use app\models\City;
use app\models\MemberType;
use app\models\BusinessType;
use app\models\Term;
use app\models\DistributionChannel;
use app\models\PaymentMethod;
use app\models\AgeCalculate;
use app\models\RateType;
use app\models\Agent;
use app\models\Quotation;

$partners = Partner::find()->orderBy(['name' => SORT_ASC])->all();
$partners = ArrayHelper::map($partners, 'id', 'name');

$provinces = Province::find()->orderBy(['name' => SORT_ASC])->all();
$provinces = ArrayHelper::map($provinces, 'name', 'name');

$cities = City::find()->orderBy(['name' => SORT_ASC])->all();
$cities = ArrayHelper::map($cities, 'name', 'name');

$agents = Agent::find()->orderBy(['name' => SORT_ASC])->all();
$agents = ArrayHelper::map($agents, 'id', 'name');

$memberTypes = MemberType::memberTypes();
$businessTypes = BusinessType::businessTypes();
$terms = Term::terms();
$distributionChannels = DistributionChannel::distributionChannels();
$paymentMethods = PaymentMethod::paymentMethods();
$ageCalculates = AgeCalculate::ageCalculates();
$rateTypes = RateType::rateTypes();

$this->title = 'Create Quotation - ' . Yii::$app->name;
?>

<div class="modal fade create-partner-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Create Partner</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/create-partner'], 'post', ['id' => 'quotation-partner-form']) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <?= Html::input('text', 'name', null, [
                                'class' => 'form-control',
                                'id' => 'name',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="province">Province</label>
                            <?= Html::dropDownList('province', null, $provinces, [
                                'prompt' => '- Select Province -',
                                'id' => 'province',
                                'class' => 'form-control slct2',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <?= Html::dropDownList('city', null, $cities, [
                                'prompt' => '- Select City -',
                                'id' => 'city',
                                'class' => 'form-control slct2',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <?= Html::textarea('address', null, [
                                'id' => 'address',
                                'class' => 'form-control',
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

<div class="quotation-create">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="p-0 m-0">Create Quotation</h2>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($quotationFormModel, 'partner_id')
                    ->dropDownList($partners, [
                        'prompt' => '- Select Partner -',
                        'class' => 'form-control slct2',
                    ]) ?>
                <?= Html::a('<i class="fa fa-plus"></i> Create Partner', 'javascript:void(0)', [
                    'class' => 'btn btn-warning waves-effect waves-light mb-4',
                    'data-toggle' => 'modal',
                    'data-target' => '.create-partner-modal',
                ]); ?>

                <?= $form->field($quotationFormModel, 'member_type')
                    ->dropDownList($memberTypes, [
                        'prompt' => '- Select Member Type -',
                        'class' => 'form-control',
                    ]) ?>
                <?= $form->field($quotationFormModel, 'member_qty')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => '0',
                ]) ?>
                <?= $form->field($quotationFormModel, 'last_insurance')->textInput([
                    'maxlength' => true,
                ]) ?>
                <?= $form->field($quotationFormModel, 'business_type')
                    ->dropDownList($businessTypes, [
                        'prompt' => '- Select Business Type -',
                        'class' => 'form-control',
                    ]) ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($quotationFormModel, 'proposed_date')->textInput([
                            'class' => 'form-control dtpckr',
                            'maxlength' => true,
                            'value' => Quotation::generateProposedDate(),
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($quotationFormModel, 'expired_date')->textInput([
                            'class' => 'form-control dtpckr',
                            'maxlength' => true,
                            'value' => Quotation::generateExpiredDate(),
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <?= $form->field($quotationFormModel, 'term')
                    ->dropDownList($terms, [
                        'prompt' => '- Select Term -',
                        'class' => 'form-control',
                    ]) ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($quotationFormModel, 'member_card')
                            ->radioList([
                                1 => 'Yes',
                                0 => 'No',
                            ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($quotationFormModel, 'certificate_card')
                            ->radioList([
                                1 => 'Yes',
                                0 => 'No',
                            ]) ?>
                    </div>
                </div>
                <?= $form->field($quotationFormModel, 'distribution_channel')
                    ->dropDownList($distributionChannels, [
                        'prompt' => '- Select Distribution Channel -',
                        'class' => 'form-control',
                    ]) ?>
                <?= $form->field($quotationFormModel, 'payment_method')
                    ->dropDownList($paymentMethods, [
                        'prompt' => '- Select Payment Method -',
                        'class' => 'form-control',
                    ]) ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($quotationFormModel, 'min_age')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => '0',
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($quotationFormModel, 'max_age')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => '0',
                        ]) ?>
                    </div>
                </div>
                <?= $form->field($quotationFormModel, 'age_calculate')
                    ->dropDownList($ageCalculates, [
                        'prompt' => '- Select Age Calculate -',
                        'class' => 'form-control',
                    ]) ?>
                <?= $form->field($quotationFormModel, 'effective_policy')
                    ->radioList([
                        Quotation::EFFECTIVE_POLICY_DTD => Quotation::EFFECTIVE_POLICY_DTD,
                        Quotation::EFFECTIVE_POLICY_DTDM => Quotation::EFFECTIVE_POLICY_DTDM,
                    ]) ?>
                <?= $form->field($quotationFormModel, 'rate_type')
                    ->dropDownList($rateTypes, [
                        'prompt' => '- Select Rate Type -',
                        'class' => 'form-control',
                    ]) ?>
                <?= $form->field($quotationFormModel, 'notes')->textarea([
                    'maxlength' => true,
                    'class' => 'form-control',
                ]) ?>
                <?= $form->field($quotationFormModel, 'agent_id')
                    ->dropDownList($agents, [
                        'prompt' => '- Select PIC -',
                        'class' => 'form-control slct2',
                    ]) ?>
            </div>
        </div>
    </div>
    <h3>Commission</h3>
    <div class="card-box">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($quotationCommissionFormModel, 'discount')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => '0',
                ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'maintenance_agent_id')
                    ->dropDownList($agents, [
                        'prompt' => '- Select Maintenance -',
                        'class' => 'form-control slct2',
                    ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'maintenance_fee')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => '0',
                ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'admin_agent_id')
                    ->dropDownList($agents, [
                        'prompt' => '- Select Admin Ref -',
                        'class' => 'form-control slct2',
                    ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'admin_fee')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => '0',
                ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'handling_agent_id')
                    ->dropDownList($agents, [
                        'prompt' => '- Select Handling Ref -',
                        'class' => 'form-control slct2',
                    ]) ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($quotationCommissionFormModel, 'handling_fee')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => '0',
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($quotationCommissionFormModel, 'pph')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => '0',
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($quotationCommissionFormModel, 'ppn')->textInput([
                            'maxlength' => true,
                            'type' => 'number',
                            'step' => 'any',
                            'value' => '0',
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <?= $form->field($quotationCommissionFormModel, 'refferal_agent_id')
                    ->dropDownList($agents, [
                        'prompt' => '- Select Refferal Ref -',
                        'class' => 'form-control slct2',
                    ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'refferal_fee')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => '0',
                ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'closing_agent_id')
                    ->dropDownList($agents, [
                        'prompt' => '- Select Closing Agent -',
                        'class' => 'form-control slct2',
                    ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'closing_fee')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => '0',
                ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'fee_based_agent_id')
                    ->dropDownList($agents, [
                        'prompt' => '- Select Fee Based Ref -',
                        'class' => 'form-control slct2',
                    ]) ?>
                <?= $form->field($quotationCommissionFormModel, 'fee_based')->textInput([
                    'maxlength' => true,
                    'type' => 'number',
                    'step' => 'any',
                    'value' => '0',
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
</div>