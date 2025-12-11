<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\Member;
use app\models\Utils;
use app\models\BatchByPesertaPegadaian;
use app\models\ViewMemberPegadaian;


$this->title = 'View Member Pegadaian- ' . Yii::$app->name;
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
                    <?= Html::beginForm(['view-member-pegadaian/index'], 'get', ['id' => 'member-search-form']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_polis_nd">Policy No</label>
                                <?= Html::input('text', 'no_polis_nd', Yii::$app->request->get('no_polis_nd'), [
                                    'class' => 'form-control',
                                    'id' => 'no_polis_nd',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="berkas">Batch No</label>
                                <?= Html::input('text', 'berkas', Yii::$app->request->get('berkas'), [
                                    'class' => 'form-control',
                                    'id' => 'berkas',
                                ]) ?>
                            </div>
                        </div>
						  <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_peserta_nd">Member No</label>
                                <?= Html::input('text', 'nomor_peserta_nd', Yii::$app->request->get('nomor_peserta_nd'), [
                                    'class' => 'form-control',
                                    'id' => 'nomor_peserta_nd',
                                ]) ?>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_peserta">Member Name</label>
                                <?= Html::input('text', 'nama_peserta', Yii::$app->request->get('nama_peserta'), [
                                    'class' => 'form-control',
                                    'id' => 'nama_peserta',
                                ]) ?>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="form-group">
                                <label for="sbg">Sbg</label>
                                <?= Html::input('text', 'sbg', Yii::$app->request->get('sbg'), [
                                    'class' => 'form-control',
                                    'id' => 'sbg',
                                ]) ?>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="form-group">
                                <label for="cif">Cif</label>
                                <?= Html::input('text', 'cif', Yii::$app->request->get('cif'), [
                                    'class' => 'form-control',
                                    'id' => 'cif',
                                ]) ?>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="form-group">
                                <label for="status_polis">Status</label>
                                <?= Html::input('text', 'status_polis', Yii::$app->request->get('status_polis'), [
                                    'class' => 'form-control',
                                    'id' => 'status_polis',
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
            <h2 class="p-0 m-0">View Member Pegadaian</h2>
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
								<th>Nomor Polis</th>
                                <th>Nama</th>
								<th>No Peserta</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Issued Date</th>
								<th>Nama Produk</th>
                                <th>CIF</th>
                                <th>SBG</th>
								<th>Kode Unit</th>
                                <th>Total UP</th>
								<th>Premi All</th>
								<th>Premi Share</th>
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
										<td><?= $model['no_polis_nd']; ?></td>
                                        <td><?= $model['nama_peserta']; ?></td>
                                        <td><?= $model['nomor_peserta_nd']; ?></td>
										<td><?= Utils::convertDateTodMy($model['star_date']); ?></td>
										<td><?= Utils::convertDateTodMy($model['end_date']); ?></td>
										<td><?= Utils::convertDateTodMy($model['accep_date']); ?></td>
										<td><?= $model['jenis_asuransi']; ?></td>
										<td><?= $model['cif']; ?></td>
                                        <td><?= $model['sbg']; ?></td>
										<td><?= $model['kode_unit']; ?></td>
										<td><?= number_format($model['total_up']); ?></td>
										<td><?= number_format($model['premi_all']); ?></td>
										<td><?= number_format($model['premi_share']); ?></td>
										<td><?= $model['status_polis']; ?></td>
										 <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a(
                                                    '<i class="fa fa-search"></i>',
                                                    [
                                                        'view-member-pegadaian/view',
                                                        'nomor_peserta_nd' => $model['nomor_peserta_nd'],
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