<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;

$this->title = 'View Rate - ' . Yii::$app->name;
?>

<div class="modal fade rate-upload-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload Rate</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/upload-rate'], 'post', ['id' => 'rate-upload-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'quotation_id', $quotationModel->id) ?>
                <?= Html::input('hidden', 'quotation_product_id', $quotationProduct->id) ?>
                <?= Html::input('hidden', 'product_id', $product->id) ?>
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
                        <?= Html::a('<i class="fa fa-file-excel-o"></i> Template', ['rate-template', 'id' => $quotationModel->id], ['class' => 'btn btn-success waves-effect waves-light']); ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade rate-create-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Create Rate</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/create-rate'], 'post', ['id' => 'rate-create-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'quotation_id', $quotationModel->id) ?>
                <?= Html::input('hidden', 'product_id', $product->id) ?>
                <?= Html::input('hidden', 'quotation_product_id', $quotationProduct->id) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="min_si">Type</label>
                            <?= Html::input('text', 'type', null, [
                                'class' => 'form-control',
                                'id' => 'type',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <?= Html::input('number', 'age', null, [
                                'class' => 'form-control',
                                'id' => 'age',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="term">Term</label>
                            <?= Html::input('number', 'term', null, [
                                'class' => 'form-control',
                                'id' => 'term',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="unit">Unit</label>
                            <?= Html::input('number', 'unit', null, [
                                'class' => 'form-control',
                                'id' => 'unit',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="rate">Rate</label>
                            <?= Html::input('number', 'rate', null, [
                                'class' => 'form-control',
                                'id' => 'rate',
                                'required' => 'required',
                                'step' => 'any',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="interest">Interest</label>
                            <?= Html::input('number', 'interest', null, [
                                'class' => 'form-control',
                                'id' => 'interest',
                                'required' => 'required',
                                'step' => 'any',
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
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">View Rate</h2>
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
                <table class="table">
                    <tr>
                        <td>Product</td>
                        <td><?= $product->name; ?></td>
                    </tr>
                    <tr>
                        <td>Premium Type</td>
                        <td><?= $quotationProduct->premium_type; ?></td>
                    </tr>
                    <tr>
                        <td>Rate Type</td>
                        <td><?= $quotationProduct->rate_type; ?></td>
                    </tr>
                    <tr>
                        <td>Period Type</td>
                        <td><?= $quotationProduct->period_type; ?></td>
                    </tr>
                    <tr>
                        <td>SI Type</td>
                        <td><?= $quotationProduct->si_type; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row mt-4 mb-4">
        <div class="col-md-6 my-auto">
            <h3 class="p-0 m-0">Rate</h3>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a('<i class="fa fa-upload"></i> Upload', 'javascript:void(0)', [
                'class' => 'btn btn-primary waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.rate-upload-modal',
            ]); ?>
            <?= Html::a('<i class="fa fa-plus"></i> Create', 'javascript:void(0)', [
                'class' => 'btn btn-warning waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.rate-create-modal',
            ]); ?>
            <?= Html::a(
                '<i class="fa fa-trash"></i> Delete All',
                [
                    'quotation/delete-all-rate',
                    'quotationId' => $quotationModel->id,
                    'productId' => $product->id,
                ],
                [
                    'class' => 'btn btn-danger waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to delete all Rate?',
                    'data-method' => 'post',
                ]
            ); ?>
        </div>
    </div>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Type</th>
                                <th>Age</th>
                                <th>Term</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>Interest</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = $pagination->offset + 1;
                            if (!empty($quotationRates)) :
                                foreach ($quotationRates as $model) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['type']; ?></td>
                                        <td><?= $model['age']; ?></td>
                                        <td><?= $model['term']; ?></td>
                                        <td><?= $model['unit']; ?></td>
                                        <td><?= $model['rate']; ?></td>
                                        <td><?= $model['interest']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                    'class' => 'btn btn-light btn-sm waves-effect',
                                                    'title' => 'Update',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#update-rate-modal-' . $model['id'],
                                                ]); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'quotation/delete-rate',
                                                        'id' => $model['id'],
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

                                    <div class="modal fade" id="update-rate-modal-<?= $model['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="myMediumModalLabel">Update Rate #<?= $i; ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?= Html::beginForm(['quotation/update-rate'], 'post', ['id' => 'rate-update-form']) ?>
                                                    <?= Html::input('hidden', 'quotation_id', $quotationModel->id) ?>
                                                    <?= Html::input('hidden', 'quotation_product_id', $quotationProduct->id) ?>
                                                    <?= Html::input('hidden', 'id', $model['id'], [
                                                        'id' => 'id',
                                                        'required' => 'required',
                                                    ]) ?>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="min_si">Type</label>
                                                                <?= Html::input('text', 'type', $model['type'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'type',
                                                                    'required' => 'required',
                                                                    'value' => $model['type'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="age">Age</label>
                                                                <?= Html::input('number', 'age', $model['age'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'age',
                                                                    'required' => 'required',
                                                                    'value' => $model['age'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="term">Term</label>
                                                                <?= Html::input('number', 'term', $model['term'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'term',
                                                                    'required' => 'required',
                                                                    'value' => $model['term'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="unit">Unit</label>
                                                                <?= Html::input('number', 'unit', $model['unit'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'unit',
                                                                    'required' => 'required',
                                                                    'value' => $model['unit'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="rate">Rate</label>
                                                                <?= Html::input('number', 'rate', $model['rate'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'rate',
                                                                    'required' => 'required',
                                                                    'step' => 'any',
                                                                    'value' => $model['rate'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="interest">Interest</label>
                                                                <?= Html::input('number', 'interest', $model['interest'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'interest',
                                                                    'required' => 'required',
                                                                    'step' => 'any',
                                                                    'value' => $model['interest'],
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