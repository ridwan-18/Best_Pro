<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\Utils;

$this->title = 'Member Claim - ' . Yii::$app->name;
?>
<div class="member-claim-index">
    <div class="modal fade search-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['member-claim/index'], 'get', ['id_loan' => 'member-claim-search-form']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="policy_no">Policy No</label>
                                <?= Html::input('text', 'policy_no', Yii::$app->request->get('policy_no'), [
                                    'class' => 'form-control',
                                    'id' => 'policy_no',
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
            <h2 class="p-0 m-0">Member Claim</h2>
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
                                <th width="1">No</th>
                                <th>ID Loan</th>
                                <th>Nama</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Tanggal Kejadian</th>
                                <th>Jenis Claim</th>
								<th>Sebab Claim</th>
								<th>Estimasi Nilai Claim</th>
								<th>Keterangan</th>
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
                                        <td><?= $model['id_loan']; ?></td>
                                        <td><?= $model['name']; ?></td>
										<td><?= Utils::convertDateTodMy($model['tanggal_pengajuan']); ?></td>
										<td><?= Utils::convertDateTodMy($model['tanggal_kejadian']); ?></td>
                                        <td><?= $model['jenis_claim']; ?></td>
										<td><?= $model['sebab_claim']; ?></td>
                                        <td><?= number_format($model['estimasi_nilai_claim']); ?></td>
										<td><?= $model['keterangan']; ?></td>
										<td><?= $model['status']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a(
                                                    '<i class="fa fa-search"></i>',
                                                    [
                                                        'member-claim/view',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'View',
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