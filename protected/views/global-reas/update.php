<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\Reassuradur;
use app\models\GlobalReas;

$reassuradurs = Reassuradur::find()->orderBy(['name' => SORT_ASC])->all();
$reassuradurs = ArrayHelper::map($reassuradurs, 'id', 'name');

$reasTypes = GlobalReas::reasTypes();
$reasMethods = GlobalReas::reasMethods();
$ratePeriods = GlobalReas::ratePeriods();
$rateTypes = GlobalReas::rateTypes();
$prorateTypes = GlobalReas::prorateTypes();

$this->title = 'Update Global Reas - ' . Yii::$app->name;
?>
<div class="global-reas-update">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Update Global Reas</h2>
            <b><?= $model->pks_no; ?></b>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $model->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'reassuradur_id')
                            ->dropDownList($reassuradurs, [
                                'prompt' => '- Select Reassuradur -',
                                'class' => 'form-control slct2',
                            ]) ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'effective_date')->textInput([
                                    'class' => 'form-control dtpckr',
                                    'maxlength' => true,
                                    'value' => date("Y-m-d"),
                                ]) ?>
                                <?= $form->field($model, 'is_unlimited')->checkbox(); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'expired_date')->textInput([
                                    'class' => 'form-control dtpckr',
                                    'maxlength' => true,
                                    'value' => date("Y-m-d"),
                                ]) ?>
                            </div>
                        </div>
                        <?= $form->field($model, 'reas_type')->radioList($reasTypes) ?>
                        <?= $form->field($model, 'reas_method')
                            ->dropDownList($reasMethods, [
                                'prompt' => '- Select Reas Method -',
                                'class' => 'form-control',
                            ]) ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'ceding_share')->textInput([
                                    'maxlength' => true,
                                    'type' => 'number',
                                    'step' => 'any',
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'reas_share')->textInput([
                                    'maxlength' => true,
                                    'type' => 'number',
                                    'step' => 'any',
                                ]) ?>
                            </div>
                        </div>
                        <?= $form->field($model, 'rate_period')
                            ->dropDownList($ratePeriods, [
                                'prompt' => '- Select Rate Period -',
                                'class' => 'form-control',
                            ]) ?>
                        <?= $form->field($model, 'rate_type')
                            ->dropDownList($rateTypes, [
                                'prompt' => '- Select Rate Type -',
                                'class' => 'form-control',
                            ]) ?>
                        <?= $form->field($model, 'prorate_type')
                            ->dropDownList($prorateTypes, [
                                'prompt' => '- Select Prorate Type -',
                                'class' => 'form-control',
                            ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'cover_note')->textInput([
                            'maxlength' => true,
                        ]) ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'retroactive')->textInput([
                                    'maxlength' => true,
                                    'type' => 'number',
                                    'step' => 'any',
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'claim_expired')->textInput([
                                    'maxlength' => true,
                                    'type' => 'number',
                                    'step' => 'any',
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'commission')->textInput([
                                    'maxlength' => true,
                                    'type' => 'number',
                                    'step' => 'any',
                                ]) ?>
                            </div>
                        </div>
                        <?= $form->field($model, 'remarks')->textarea([
                            'maxlength' => true,
                            'class' => 'form-control',
                        ]) ?>
                    </div>
                </div>
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