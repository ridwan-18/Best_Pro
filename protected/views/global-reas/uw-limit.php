<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\Medical;

$medicals = Medical::find()->orderBy(['name' => SORT_ASC])->all();
$medicals = ArrayHelper::map($medicals, 'code', 'code');

$this->title = 'UW Limit Global Reas - ' . Yii::$app->name;
?>

<div class="modal fade uw-limit-upload-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload UW Limit</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['global-reas/upload-uw-limit'], 'post', ['id' => 'uw-limit-upload-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'global_reas_id', $globalReas->id) ?>
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
                        <?= Html::submitButton('<i class="fa fa-upload"></i> Upload', ['class' => 'btn btn-primary waves-effect waves-light', 'id' => 'upload-btn']) ?>
                        <?= Html::a('<i class="fa fa-file-excel-o"></i> Template', ['uw-limit-template'], ['class' => 'btn btn-success waves-effect waves-light']); ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="global-reas-uw-limit">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">UW Limit</h2>
            <h5 class="p-0 m-0"><?= $globalReas->pks_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $globalReas->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4 mb-4">
        <div class="col-md-6">
            <?= Html::a('<i class="fa fa-upload"></i> Upload', 'javascript:void(0)', [
                'class' => 'btn btn-primary waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.uw-limit-upload-modal',
            ]); ?>
            <?= Html::a(
                '<i class="fa fa-remove"></i> Delete All',
                ['delete-all-uw-limit', 'id' => $globalReas->id],
                [
                    'class' => 'btn btn-danger waves-effect waves-light',
                    'data-method' => 'post',
                    'data-confirm' => 'Are you sure want to Delete All?'
                ]
            ); ?>
        </div>
    </div>
    <div class="card-box">
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
                                                    'data-target' => '#update-uw-limit-modal-' . $uwLimit['id'],
                                                ]); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'global-reas/delete-uw-limit',
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

                                    <div class="modal fade" id="update-uw-limit-modal-<?= $uwLimit['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="myMediumModalLabel">Update UW Limit #<?= $i; ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?= Html::beginForm(['global-reas/update-uw-limit'], 'post', ['id' => 'uw-limit-update-form']) ?>
                                                    <?= Html::input('hidden', 'global_reas_id', $globalReas->id) ?>
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
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'disabledPageCssClass' => 'page-link',
                    'options' => ['class' => 'pagination pagination-split mb-0 mt-4'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<?php
$script = <<< JS
    $('#uw-limit-upload-form').submit(function() {
        $('#upload-btn').html('<i class="fa fa-spinner"></i> Loading');
        $('#upload-btn').attr('class', 'btn btn-primary waves-effect waves-light disabled');
    });
JS;
$this->registerJs($script);
?>