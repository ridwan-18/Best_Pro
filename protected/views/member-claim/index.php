<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $models array */
/* @var $pagination yii\data\Pagination */

$this->title = 'Member Claim';

?>

<div class="member-claim-index">

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Member Claim
            </h3>
        </div>

        <div class="card-body">

            <!-- =====================================================
                 SEARCH
            ====================================================== -->
            <form method="get" action="<?= Yii::$app->urlManager->createUrl(['member-claim/index']) ?>">

                <div class="row">

                    <div class="col-md-3">
                        <label>ID Pengajuan</label>

                        <input
                            type="text"
                            name="id_pengajuan"
                            class="form-control"
                            value="<?= Html::encode(Yii::$app->request->get('id_pengajuan', '')) ?>"
                            placeholder="ID Pengajuan"
                        >
                    </div>

                    <div class="col-md-2">
                        <label>KTP</label>

                        <input
                            type="text"
                            name="ktp"
                            class="form-control"
                            value="<?= Html::encode(Yii::$app->request->get('ktp', '')) ?>"
                            placeholder="KTP"
                        >
                    </div>

                    <div class="col-md-3">
                        <label>Nama</label>

                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            value="<?= Html::encode(Yii::$app->request->get('nama', '')) ?>"
                            placeholder="Nama"
                        >
                    </div>

                    <div class="col-md-2">
                        <label>ID Transaksi</label>

                        <input
                            type="text"
                            name="id_transaksi"
                            class="form-control"
                            value="<?= Html::encode(Yii::$app->request->get('id_transaksi', '')) ?>"
                            placeholder="ID Transaksi"
                        >
                    </div>

                    <div class="col-md-2">
                        <label>Status</label>

                        <select name="status_claim" class="form-control">

                            <option value="">Semua Status</option>

                            <?php foreach (\app\models\MemberClaim::statuses() as $key => $label): ?>

                                <option
                                    value="<?= Html::encode($key) ?>"
                                    <?= Yii::$app->request->get('status_claim') == $key ? 'selected' : '' ?>
                                >
                                    <?= Html::encode($label) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>

                <div class="row mt-3">

                    <div class="col-md-12">

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i>
                            Cari
                        </button>

                        <a
                            href="<?= Yii::$app->urlManager->createUrl(['member-claim/index']) ?>"
                            class="btn btn-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

            <hr>

            <!-- =====================================================
                 TABLE
            ====================================================== -->

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover">

                    <thead>

                        <tr>

                            <th width="50">
                                No
                            </th>

                            <th>
                                ID Transaksi
                            </th>

                            <th>
                                ID Pengajuan
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                KTP
                            </th>

                            <th>
                                No Rekening
                            </th>

                            <th>
                                No Akad
                            </th>

                            <th>
                                Jenis Klaim
                            </th>

                            <th>
                                Tanggal Kejadian
                            </th>

                            <th>
                                Jumlah Diajukan
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Tanggal Kirim
                            </th>

                            <th width="80">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($models)): ?>

                            <?php foreach ($models as $index => $model): ?>

                                <tr>

                                    <!-- NO -->
                                    <td>
                                        <?= $pagination->offset + $index + 1 ?>
                                    </td>

                                    <!-- ID TRANSAKSI -->
                                    <td>
                                        <?= Html::encode(
                                            $model['id_transaksi'] ?? '-'
                                        ) ?>
                                    </td>

                                    <!-- ID PENGAJUAN -->
                                    <td>
                                        <?= Html::encode(
                                            $model['id_pengajuan'] ?? '-'
                                        ) ?>
                                    </td>

                                    <!-- NAMA -->
                                    <td>
                                        <?= Html::encode(
                                            $model['nama'] ?? '-'
                                        ) ?>
                                    </td>

                                    <!-- KTP -->
                                    <td>
                                        <?= Html::encode(
                                            $model['ktp'] ?? '-'
                                        ) ?>
                                    </td>

                                    <!-- NOMOR REKENING -->
                                    <td>
                                        <?= Html::encode(
                                            $model['nomor_rekening'] ?? '-'
                                        ) ?>
                                    </td>

                                    <!-- NO AKAD -->
                                    <td>
                                        <?= Html::encode(
                                            $model['no_akad'] ?? '-'
                                        ) ?>
                                    </td>

                                    <!-- JENIS KLAIM -->
                                    <td>
                                        <?= Html::encode(
                                            $model['jenis_klaim'] ?? '-'
                                        ) ?>
                                    </td>

                                    <!-- TANGGAL KEJADIAN -->
                                    <td>

                                        <?php if (!empty($model['tanggal_kejadian'])): ?>

                                            <?= date(
                                                'd-M-y',
                                                strtotime($model['tanggal_kejadian'])
                                            ) ?>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </td>

                                    <!-- JUMLAH DIAJUKAN -->
                                    <td class="text-right">

                                        <?php if (
                                            isset($model['jumlah_diajukan']) &&
                                            $model['jumlah_diajukan'] !== null &&
                                            $model['jumlah_diajukan'] !== ''
                                        ): ?>

                                            Rp <?= number_format(
                                                (float) $model['jumlah_diajukan'],
                                                0,
                                                ',',
                                                '.'
                                            ) ?>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </td>

                                    <!-- STATUS -->
                                    <td>

                                        <?php
                                        $status = $model['status_claim'] ?? '';

                                        $statusLabel = \app\models\MemberClaim::statusLabel(
                                            $status
                                        );

                                        $statusClass = \app\models\MemberClaim::statusClass(
                                            $status
                                        );
                                        ?>

                                        <span class="badge <?= $statusClass ?>">
                                            <?= Html::encode($statusLabel) ?>
                                        </span>

                                    </td>

                                    <!-- TANGGAL KIRIM -->
                                    <td>

                                        <?php if (!empty($model['tanggal_kirim'])): ?>

                                            <?= date(
                                                'd-M-y',
                                                strtotime($model['tanggal_kirim'])
                                            ) ?>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </td>

                                    <!-- ACTION -->
                                    <td>

                                        <?= Html::a(
                                            '<i class="fa fa-eye"></i>',
                                            [
                                                'member-claim/view',
                                                'id' => $model['id']
                                            ],
                                            [
                                                'class' => 'btn btn-info btn-sm',
                                                'title' => 'View',
                                                'data-pjax' => '0'
                                            ]
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="13"
                                    class="text-center"
                                >
                                    Data tidak ditemukan
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

            <!-- =====================================================
                 PAGINATION
            ====================================================== -->

            <?php if ($pagination->totalCount > 0): ?>

                <div class="row">

                    <div class="col-md-6">

                        <div class="text-muted">

                            Menampilkan
                            <?= $pagination->offset + 1 ?>
                            -
                            <?= min(
                                $pagination->offset + $pagination->limit,
                                $pagination->totalCount
                            ) ?>

                            dari
                            <?= $pagination->totalCount ?>
                            data

                        </div>

                    </div>

                    <div class="col-md-6">

                        <?= LinkPager::widget([
                            'pagination' => $pagination,
                        ]) ?>

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>