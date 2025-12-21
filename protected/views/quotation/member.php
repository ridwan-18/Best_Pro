<?php

use yii\helpers\Html;
use app\widgets\Alert;

$this->title = 'Quotation Member - ' . Yii::$app->name;
?>

<div class="modal fade upload-member-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload Member</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/upload-member'], 'post', ['id' => 'member-form']) ?>
                <?= Html::input('hidden', 'quotation_id', $quotationModel->id, [
                    'id' => 'quotation_id',
                    'required' => 'required',
                ]) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?= Html::input('file', 'import_file', null, [
                                'id' => 'import_file',
                                'required' => 'required',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= Html::submitButton('<i class="fa fa-upload"></i> Upload', ['class' => 'btn btn-warning waves-effect waves-light']) ?>
                        <?= Html::a('<i class="fa fa-download"></i> Download Sample', ['download-sample'], ['class' => 'btn btn-primary waves-effect waves-light']); ?>
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
            <h2 class="p-0 m-0">Quotation Member</h2>
            <h5 class="p-0 m-0"><?= $quotationModel->proposal_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $quotationModel->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-12">
                <?= Html::a('<i class="fa fa-upload"></i> Upload', 'javascript:void(0)', [
                    'class' => 'btn btn-warning waves-effect waves-light mb-4',
                    'data-toggle' => 'modal',
                    'data-target' => '.upload-member-modal',
                ]); ?>
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Product</th>
                                <th>Premium Type</th>
                                <th>Rate Type</th>
                                <th>Period Type</th>
                                <th>Sum Insured</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- <?php
                                    $i = 1;
                                    if (!empty($quotationPicModels)) :
                                        foreach ($quotationPicModels as $model) :
                                    ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['product']; ?></td>
                                        <td><?= $model['premium_type']; ?></td>
                                        <td><?= $model['rate_type']; ?></td>
                                        <td><?= $model['period_type']; ?></td>
                                        <td><?= $model['si_type']; ?></td>
                                        <td><?= $model['created_at']; ?></td>
                                        <td><?= $model['created_by']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                    'class' => 'btn btn-light btn-sm waves-effect',
                                                    'title' => 'Update',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#update-product-modal-' . $model['id'],
                                                ]); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'quotation/delete-product',
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

                                    <div class="modal fade" id="update-product-modal-<?= $model['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="myMediumModalLabel">Update Product</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?= Html::beginForm(['quotation/update-product'], 'post', ['id' => 'pic-form']) ?>
                                                    <?= Html::input('hidden', 'quotation_id', $quotationModel->id, [
                                                        'id' => 'quotation_id',
                                                        'required' => 'required',
                                                    ]) ?>
                                                    <?= Html::input('hidden', 'id', $model['id'], [
                                                        'id' => 'id',
                                                        'required' => 'required',
                                                    ]) ?>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="product_id">Product</label>
                                                                <?= Html::dropDownList('product_id', $model['product_id'], $products, [
                                                                    'prompt' => '- Select Product -',
                                                                    'id' => 'product_id',
                                                                    'class' => 'form-control slct2',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="premium_type">Premium Type</label>
                                                                <?= Html::dropDownList('premium_type', $model['premium_type'], $premiumTypes, [
                                                                    'prompt' => '- Select Premium Type -',
                                                                    'id' => 'premium_type',
                                                                    'class' => 'form-control',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="rate_type">Rate Type</label>
                                                                <?= Html::dropDownList('rate_type', $model['rate_type'], $productRateTypes, [
                                                                    'prompt' => '- Select Rate Type -',
                                                                    'id' => 'rate_type',
                                                                    'class' => 'form-control',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="period_type">Period Type</label>
                                                                <?= Html::dropDownList('period_type', $model['period_type'], $periodTypes, [
                                                                    'prompt' => '- Select Period Type -',
                                                                    'id' => 'period_type',
                                                                    'class' => 'form-control',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="si_type">Sum Insured</label>
                                                                <?= Html::dropDownList('si_type', $model['si_type'], $siTypes, [
                                                                    'prompt' => '- Select Sum Insured -',
                                                                    'id' => 'si_type',
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
                            <?php
                                            $i++;
                                        endforeach;
                                    else :
                                        echo '<tr><td class="text-center" colspan="100">No data</td></tr>';
                                    endif;
                            ?> -->

                            <?= '<tr><td class="text-center" colspan="100">No data</td></tr>'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>