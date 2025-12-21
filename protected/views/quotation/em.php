<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;

$this->title = 'EM - ' . Yii::$app->name;
?>

<div class="modal fade em-upload-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload EM</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/upload-em'], 'post', ['id' => 'uw-upload-form', 'enctype' => 'multipart/form-data']) ?>
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
                        <?= Html::a('<i class="fa fa-file-excel-o"></i> Template', ['em-template'], ['class' => 'btn btn-success waves-effect waves-light']); ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="quotation-tc-create">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">EM</h2>
            <h5 class="p-0 m-0"><?= $quotationModel->proposal_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $quotationModel->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4 mb-4">
        <div class="col-md-6">
            <?= Html::a('<i class="fa fa-upload"></i> Upload', 'javascript:void(0)', [
                'class' => 'btn btn-primary waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.em-upload-modal',
            ]); ?>
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
                                <th>Percentage</th>
                                <th>Age</th>
                                <th>Term</th>
                                <th>EM</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = $pagination->offset + 1;
                            if (!empty($quotationEms)) :
                                foreach ($quotationEms as $quotationEm) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $quotationEm['percentage']; ?></td>
                                        <td><?= $quotationEm['age']; ?></td>
                                        <td><?= $quotationEm['term']; ?></td>
                                        <td><?= $quotationEm['em']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                    'class' => 'btn btn-light btn-sm waves-effect',
                                                    'title' => 'Update',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#update-em-modal-' . $quotationEm['id'],
                                                ]); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'quotation/delete-em',
                                                        'id' => $quotationEm['id'],
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

                                    <div class="modal fade" id="update-em-modal-<?= $quotationEm['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="myMediumModalLabel">Update EM #<?= $i; ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?= Html::beginForm(['quotation/update-uw-limit'], 'post', ['id' => 'uw-update-form']) ?>
                                                    <?= Html::input('hidden', 'quotation_id', $quotationModel->id) ?>
                                                    <?= Html::input('hidden', 'id', $quotationEm['id'], [
                                                        'id' => 'id',
                                                        'required' => 'required',
                                                    ]) ?>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="percentage">Percentage</label>
                                                                <?= Html::input('number', 'percentage', $quotationEm['percentage'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'percentage',
                                                                    'step' => 'any',
                                                                    'required' => 'required',
                                                                    'value' => $quotationEm['percentage'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="age">Age</label>
                                                                <?= Html::input('number', 'age', $quotationEm['age'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'age',
                                                                    'required' => 'required',
                                                                    'value' => $quotationEm['age'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="term">Term</label>
                                                                <?= Html::input('number', 'term', $quotationEm['term'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'term',
                                                                    'required' => 'required',
                                                                    'value' => $quotationEm['term'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="em">EM</label>
                                                                <?= Html::input('number', 'em', $quotationEm['em'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'em',
                                                                    'step' => 'any',
                                                                    'required' => 'required',
                                                                    'value' => $quotationEm['em'],
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