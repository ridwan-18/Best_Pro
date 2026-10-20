<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\BatchByPeserta;
use app\models\Member;
use app\models\Utils;



$this->title = 'Data Produksi - ' . Yii::$app->name;
?>
<div class="member-index">
    <div class="modal fade" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['data-produksi/index'], 'get', ['id' => 'member-search-form']) ?>
                    <div class="row">
					
					
					<div class="col-md-6">
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <?= Html::input('text', 'nama', Yii::$app->request->get('nama'), [
                                    'class' => 'form-control',
                                    'id' => 'nama',
                                ]) ?>
                            </div>
                        </div>
					
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status Kepesertaan</label>
                                <?= Html::input('text', 'status', Yii::$app->request->get('status'), [
                                    'class' => 'form-control',
                                    'id' => 'status',
                                ]) ?>
                            </div>
                        </div>
						
						 <div class="col-md-6">
                            <div class="form-group">
                                <label for="approve">Status Dokument</label>
                                <?= Html::input('text', 'approve', Yii::$app->request->get('approve'), [
                                    'class' => 'form-control',
                                    'id' => 'approve',
                                ]) ?>
                            </div>
                        </div>
						
						
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="batch_no">Batch No</label>
                                <?= Html::input('text', 'batch_no', Yii::$app->request->get('batch_no'), [
                                    'class' => 'form-control',
                                    'id' => 'batch_no',
                                ]) ?>
                            </div>
                        </div>
						
						
						<div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <?= Html::input('text', 'start_date', Yii::$app->request->get('updated_at'), [
                                'class' => 'form-control dtpckr',
                                'id' => 'start_date',
                            ]) ?>
                        </div>
                    </div>
					
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <?= Html::input('text', 'end_date', Yii::$app->request->get('updated_at'), [
                                'class' => 'form-control dtpckr',
                                'id' => 'end_date',
                            ]) ?>
                        </div>
                    </div>
						
						
					 <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_kantor">Cabang</label>
                                <?= Html::input('text', 'nama_kantor', Yii::$app->request->get('nama_kantor'), [
                                    'class' => 'form-control',
                                    'id' => 'nama_kantor',
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
            <h2 class="p-0 m-0">Summary Produksi</h2>
        </div>
        <div class="col-md-6 text-right">
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
                                <th width="1">No</th>
								<th>Total Pengajuan</th>
								<th>Disetujui</th>
								<th>Pending</th>
								<th>Ditolak</th>
                                <th>Total Gross Premium</th>
								  <th>Total net premium</th>
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
										  <td><?= $model['produk']; ?></td>
                                        <td><?= $model['batch_no']; ?></td>
										<td><?= $model['nama']; ?></td>
										<td><?= $model['tgl_lahir']; ?></td>
										 <td><?= $model['member_no']; ?></td>
										 	 <td><?= $model['member_no']; ?></td>
										
										 <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a(
                                                    '<i class="fa fa-search"></i>',
                                                    [
                                                        'data-produksi/view',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'View',
                                                    ]
                                                ); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'member/delete',
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