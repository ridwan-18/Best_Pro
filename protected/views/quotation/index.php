<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\Utils;

$this->title = 'Quotation - ' . Yii::$app->name;
?>
<div class="quotation-index">
    <div class="modal fade" id="search-modal" tabindex="-10" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['quotation/index'], 'get', ['id' => 'quotation-search-form']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="proposal_no">Proposal No</label>
                                <?= Html::input('text', 'proposal_no', Yii::$app->request->get('proposal_no'), [
                                    'class' => 'form-control',
                                    'id' => 'proposal_no',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="partner_name">Partner</label>
                                <?= Html::input('text', 'partner_name', Yii::$app->request->get('partner_name'), [
                                    'class' => 'form-control',
                                    'id' => 'partner_name',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary waves-effect waves-light']) ?>
                        </div>
                    </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Quotation</h2>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('<i class="fa fa-plus"></i> Create', ['create'], ['class' => 'btn btn-warning waves-effect waves-light']); ?>
            <?= Html::a('<i class="fa fa-search"></i> Search', 'javascript:void(0)', [
                'class' => 'btn btn-info waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '#search-modal',
            ]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Proposal No</th>
                                <th>Partner</th>
                                <th>Proposed Date</th>
                                <th>Status</th>
                                <th>Request Rate</th>
                                <th>Request TC</th>
                                <th>Request Reins</th>
                                <th>Created At</th>
                                <th>Created By</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = $pagination->offset + 1;
                            if (!empty($models)) :
                                foreach ($models as $model) :
                                    $isReqNewRate = ($model['is_req_new_rate'] == 1)
                                        ? '<i class="fa fa-check-square-o fa-lg"></i>'
                                        : '<i class="fa fa-square-o fa-lg"></i>';
                                    $isReqTc = ($model['is_req_tc'] == 1)
                                        ? '<i class="fa fa-check-square-o fa-lg"></i>'
                                        : '<i class="fa fa-square-o fa-lg"></i>';
                                    $isReqReas = ($model['is_req_reas'] == 1)
                                        ? '<i class="fa fa-check-square-o fa-lg"></i>'
                                        : '<i class="fa fa-square-o fa-lg"></i>';
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['proposal_no']; ?></td>
                                        <td><?= $model['partner']; ?></td>
                                        <td><?= Utils::convertDateTodMy($model['proposed_date']); ?></td>
                                        <td><?= $model['status']; ?></td>
                                        <td class="text-center"><?= $isReqNewRate; ?></td>
                                        <td class="text-center"><?= $isReqTc; ?></td>
                                        <td class="text-center"><?= $isReqReas; ?></td>
                                        <td><?= $model['created_at']; ?></td>
                                        <td><?= $model['created_by']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a(
                                                    '<i class="fa fa-pencil"></i>',
                                                    [
                                                        'quotation/update',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'Update',
                                                    ]
                                                ); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'quotation/delete',
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