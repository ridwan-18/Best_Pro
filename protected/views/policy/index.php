<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\Policy;
use app\models\Utils;

$spaStatuses = Policy::spaStatuses();

$this->title = 'Policy - ' . Yii::$app->name;
?>
<div class="policy-index">
    <div class="modal fade search-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['policy/index'], 'get', ['id' => 'policy-search-form']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="spa_no">SPA No</label>
                                <?= Html::input('text', 'spa_no', Yii::$app->request->get('spa_no'), [
                                    'class' => 'form-control',
                                    'id' => 'spa_no',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="policy_no">Policy No</label>
                                <?= Html::input('text', 'policy_no', Yii::$app->request->get('policy_no'), [
                                    'class' => 'form-control',
                                    'id' => 'policy_no',
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="spa_status">Status</label>
                                <?= Html::dropDownList('spa_status', Yii::$app->request->get('spa_status'), $spaStatuses, [
                                    'prompt' => '- Select Status -',
                                    'id' => 'spa_status',
                                    'class' => 'form-control',
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
            <h2 class="p-0 m-0">Policy</h2>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('<i class="fa fa-plus"></i> Create', ['create'], ['class' => 'btn btn-warning waves-effect waves-light']); ?>
            <?= Html::a('<i class="fa fa-search"></i> Search', 'javascript:void(0)', [
                'class' => 'btn btn-info waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.search-modal',
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
                                <th>SPA No</th>
                                <th>Policy No</th>
                                <th>Policy Holder</th>
                                <th>SPA Date</th>
                                <th>Effective Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = $pagination->offset + 1;
                            if (!empty($models)) :
                                foreach ($models as $model) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['spa_no']; ?></td>
                                        <td><?= $model['policy_no']; ?></td>
                                        <td><?= $model['partner']; ?></td>
                                        <td><?= Utils::convertDateTodMy($model['spa_date']); ?></td>
                                        <td><?= Utils::convertDateTodMy($model['effective_date']); ?></td>
                                        <td><?= Utils::convertDateTodMy($model['end_date']); ?></td>
                                        <td><?= $model['spa_status']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?php
                                                if ($model['policy_no'] != null) {
                                                    echo Html::a(
                                                        '<i class="fa fa-print"></i>',
                                                        [
                                                            'policy/print',
                                                            'id' => $model['id'],
                                                        ],
                                                        [
                                                            'class' => 'btn btn-light btn-sm waves-effect',
                                                            'title' => 'Print',
                                                            'target' => 'blank',
                                                        ]
                                                    );
                                                }
                                                ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-pencil"></i>',
                                                    [
                                                        'policy/update',
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
                                                        'policy/delete',
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