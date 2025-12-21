<?php

use yii\helpers\Html;
use app\widgets\Alert;
use app\models\Utils;

$this->title = 'Report Billing - ' . Yii::$app->name;
?>
<div class="report-billing-index">
    <div class="modal fade search-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['report-billing/index'], 'get', ['id' => 'report-billing-search-form']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_no">Invoice No</label>
                                <?= Html::input('text', 'invoice_no', Yii::$app->request->get('invoice_no'), [
                                    'class' => 'form-control',
                                    'id' => 'invoice_no',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reg_no">Reg No</label>
                                <?= Html::input('text', 'reg_no', Yii::$app->request->get('reg_no'), [
                                    'class' => 'form-control',
                                    'id' => 'reg_no',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <?= Html::dropDownList('status', Yii::$app->request->get('status'), $statuses, [
                                    'prompt' => '- Select Status -',
                                    'id' => 'status',
                                    'class' => 'form-control',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="member_status">Member Status</label>
                                <?= Html::dropDownList('member_status', Yii::$app->request->get('member_status'), $memberStatuses, [
                                    'prompt' => '- Select Member Status -',
                                    'id' => 'member_status',
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
            <h2 class="p-0 m-0">Report Billing</h2>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('<i class="fa fa-search"></i> Search', 'javascript:void(0)', [
                'class' => 'btn btn-info waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.search-modal',
            ]); ?>
            <?php
            if (!empty($models)) :
                echo Html::a('<i class="fa fa-file-excel-o"></i> Export', [
                    'export',
                    'invoice_no' => Yii::$app->request->get('invoice_no'),
                    'reg_no' => Yii::$app->request->get('reg_no'),
                    'status' => Yii::$app->request->get('status'),
                    'member_status' => Yii::$app->request->get('member_status'),
                ], ['class' => 'btn btn-success waves-effect waves-light']);
            endif;
            ?>
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
                                <th>Policy No</th>
                                <th>Policy Holder</th>
                                <th>Product</th>
                                <th>Member No</th>
                                <th>Name</th>
                                <th>Birth Date</th>
                                <th>Age</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Term</th>
                                <th>UP</th>
                                <th>Premi</th>
                                <th>Mortalita</th>
                                <th>Extra Premi</th>
                                <th>Total Premi</th>
                                <th>Medical Code</th>
                                <th>Invoice No</th>
                                <th>Reg No</th>
                                <th>Accepted Date</th>
                                <th>Batch No</th>
                                <th>STNC Date</th>
                                <th>Status</th>
                                <th>Member Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($models)) :
                                foreach ($models as $model) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['policy_no']; ?></td>
                                        <td><?= $model['partner']; ?></td>
                                        <td><?= $model['product']; ?></td>
                                        <td><?= $model['member_no']; ?></td>
                                        <td><?= $model['name']; ?></td>
                                        <td><?= Utils::convertDateTodMy($model['birth_date']); ?></td>
                                        <td><?= $model['age']; ?></td>
                                        <td><?= Utils::convertDateTodMy($model['start_date']); ?></td>
                                        <td><?= Utils::convertDateTodMy($model['end_date']); ?></td>
                                        <td><?= $model['term']; ?></td>
                                        <td><?= number_format($model['sum_insured']); ?></td>
                                        <td><?= number_format($model['gross_premium']); ?></td>
                                        <td><?= number_format($model['em_premium']); ?></td>
                                        <td><?= number_format($model['extra_premium']); ?></td>
                                        <td><?= number_format($model['nett_premium']); ?></td>
                                        <td><?= $model['medical_code']; ?></td>
                                        <td><?= $model['invoice_no']; ?></td>
                                        <td><?= $model['reg_no']; ?></td>
                                        <td><?= Utils::convertDateTodMy($model['accept_date']); ?></td>
                                        <td><?= $model['batch_no']; ?></td>
                                        <td><?= Utils::convertDateTodMy($model['stnc_date']); ?></td>
                                        <td><?= $model['status']; ?></td>
                                        <td><?= $model['member_status']; ?></td>
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
            </div>
        </div>
    </div>
</div>