<?php

use yii\helpers\Html;
use app\widgets\Alert;
use app\models\BatchByPesertaPegadaian;
use app\models\ViewMemberPegadaian;

$this->title = 'View Member pegadaian- ' . Yii::$app->name;
?>

<div class="member-view-pegadaian">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">No Peserta</h2>
            <h5 class="p-0 m-0"><?= $model->nomor_peserta_nd; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a(
                '<i class="fa fa-check"></i> Update Claim',
                [
                    'view-member-pegadaian/approve',
                    'nomor_peserta_nd' => $model->nomor_peserta_nd,
                ],
				[
                    'class' => ($model->status_polis == ViewMemberPegadaian::STATUS_CLAIM)
                        ? 'btn btn-primary waves-effect waves-light disabled'
                        : 'btn btn-primary waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to update status claim?',
                    'data-method' => 'post',
                ]
				
            ); ?>
			
			<?= Html::a(
                '<i class="fa fa-check"></i> Cancel Claim',
                [
                    'view-member-pegadaian/cancel-approve',
                    'nomor_peserta_nd' => $model->nomor_peserta_nd,
                ],
                [
                    'class' => ($model->status_polis == ViewMemberPegadaian::STATUS_CANCEL_CLAIM)
                        ? 'btn btn-primary waves-effect waves-light disabled'
                        : 'btn btn-warning waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to update status cancel claim',
                    'data-method' => 'post',
                ]
            ); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Nama Peserta</td>
                        <td><?= $model->nama_peserta; ?></td>
                    </tr>
					<tr>
                        <td>Nomor peserta</td>
                        <td><?= $model->nomor_peserta_nd; ?></td>
                    </tr>
                    <tr>
                        <td>CIF</td>
                        <td><?= $model->cif; ?></td>
                    </tr>
					<tr>
                        <td>SBG</td>
                        <td><?= $model->sbg; ?></td>
                    </tr>
					<tr>
                        <td>Berkas</td>
                        <td><?= $model->berkas; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Total UP</td>
                        <td><?= number_format($model->total_up); ?></td>
                    </tr>
                    <tr>
                        <td>Premi All</td>
                        <td><?= number_format($model->premi_all); ?></td>
                    </tr>
                    <tr>
                        <td>Premi Share</td>
                        <td><?= number_format($model->premi_share); ?></td>
                    </tr>
					<tr>
                        <td>Status Peserta</td>
                        <td><?= $model->status_polis; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
   
</div>