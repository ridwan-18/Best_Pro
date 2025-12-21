<?php

use yii\helpers\Html;
use app\widgets\Alert;

$this->title = 'Quotation PIC - ' . Yii::$app->name;
?>

<div class="modal fade create-pic-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Create PIC</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/create-pic'], 'post', ['id' => 'pic-form']) ?>
                <?= Html::input('hidden', 'quotation_id', $quotationModel->id, [
                    'id' => 'quotation_id',
                    'required' => 'required',
                ]) ?>
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
                            <label for="phone">Phone</label>
                            <?= Html::input('text', 'phone', null, [
                                'class' => 'form-control',
                                'id' => 'phone',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <?= Html::input('text', 'email', null, [
                                'class' => 'form-control',
                                'id' => 'email',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="job_position">Job</label>
                            <?= Html::input('text', 'job_position', null, [
                                'class' => 'form-control',
                                'id' => 'job_position',
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
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Quotation PIC</h2>
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
                <?= Html::a('<i class="fa fa-plus"></i> Create', 'javascript:void(0)', [
                    'class' => 'btn btn-warning waves-effect waves-light mb-4',
                    'data-toggle' => 'modal',
                    'data-target' => '.create-pic-modal',
                ]); ?>
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Job</th>
                                <th>Created At</th>
                                <th>Created By</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($quotationPicModels)) :
                                foreach ($quotationPicModels as $model) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['name']; ?></td>
                                        <td><?= $model['phone']; ?></td>
                                        <td><?= $model['email']; ?></td>
                                        <td><?= $model['job_position']; ?></td>
                                        <td><?= $model['created_at']; ?></td>
                                        <td><?= $model['created_by']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                    'class' => 'btn btn-light btn-sm waves-effect',
                                                    'title' => 'Update',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#update-pic-modal-' . $model['id'],
                                                ]); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'quotation/delete-pic',
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

                                    <div class="modal fade" id="update-pic-modal-<?= $model['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="myMediumModalLabel">Update PIC</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?= Html::beginForm(['quotation/update-pic'], 'post', ['id' => 'pic-form']) ?>
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
                                                                <label for="name">Name</label>
                                                                <?= Html::input('text', 'name', $model['name'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'name',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="phone">Phone</label>
                                                                <?= Html::input('text', 'phone',  $model['phone'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'phone',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="email">Email</label>
                                                                <?= Html::input('text', 'email',  $model['email'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'email',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="job_position">Job</label>
                                                                <?= Html::input('text', 'job_position',  $model['job_position'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'job_position',
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