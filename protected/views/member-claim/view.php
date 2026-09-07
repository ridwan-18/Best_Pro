
<?php

use yii\helpers\Html;
use app\widgets\Alert;

$this->title = 'View Claim - ' . Yii::$app->name;

/*
 * ============================================================
 * HELPER
 * ============================================================
 */

$status = $model->status_claim;

if ($status === '1') {
    $statusLabel = 'Approved';
    $statusClass = 'badge-success';
    $statusIcon  = 'fa-check-circle';
} elseif ($status === '2') {
    $statusLabel = 'Ditolak';
    $statusClass = 'badge-danger';
    $statusIcon  = 'fa-times-circle';
} elseif ($status === '0') {
    $statusLabel = 'Analisa';
    $statusClass = 'badge-warning';
    $statusIcon  = 'fa-clock-o';
} else {
    $statusLabel = $status ?: '-';
    $statusClass = 'badge-secondary';
    $statusIcon  = 'fa-question-circle';
}

$formatDate = function ($date) {
    return !empty($date)
        ? Yii::$app->formatter->asDate($date, 'php:d M Y')
        : '-';
};

$formatDateTime = function ($date) {
    return !empty($date)
        ? Yii::$app->formatter->asDatetime($date, 'php:d M Y H:i:s')
        : '-';
};

$formatRupiah = function ($value) {
    return $value !== null
        ? 'Rp ' . number_format($value, 0, ',', '.')
        : '-';
};

$val = function ($value) {
    return Html::encode($value ?: '-');
};

?>

<style>
    /* =========================================================
       CLAIM VIEW
       ========================================================= */

    .claim-page {
        padding-bottom: 30px;
    }

    .claim-header {
        margin-bottom: 25px;
    }

    .claim-header h2 {
        font-size: 25px;
        font-weight: 600;
        margin-bottom: 6px !important;
    }

    .claim-header h2 i {
        margin-right: 8px;
    }

    .claim-subtitle {
        color: #98a6ad;
        font-size: 14px;
    }

    .claim-card {
        background: #fff;
        border: 1px solid #e7eaf0;
        border-radius: 8px;
        margin-bottom: 20px;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .03);
        overflow: hidden;
    }

    .claim-card-header {
        padding: 18px 22px;
        border-bottom: 1px solid #edf0f2;
        background: #fafbfc;
    }

    .claim-card-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #343a40;
    }

    .claim-card-header h4 i {
        width: 22px;
        margin-right: 5px;
    }

    .claim-card-body {
        padding: 10px 22px 18px;
    }

    .claim-info {
        width: 100%;
        margin-bottom: 0;
    }

    .claim-info tr {
        border-bottom: 1px solid #f1f3f5;
    }

    .claim-info tr:last-child {
        border-bottom: none;
    }

    .claim-info th {
        width: 200px;
        padding: 11px 10px 11px 0;
        color: #7a8793;
        font-weight: 500;
        font-size: 13px;
        vertical-align: top;
    }

    .claim-info td {
        padding: 11px 0;
        color: #343a40;
        font-size: 13px;
        font-weight: 500;
        vertical-align: top;
    }

    .claim-value-highlight {
        font-size: 16px;
        font-weight: 700;
    }

    .claim-id-box {
        display: inline-block;
        background: #f1f3f5;
        border-radius: 5px;
        padding: 5px 10px;
        font-family: monospace;
        font-size: 13px;
    }

    .claim-status {
        display: inline-block;
        padding: 7px 13px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .claim-status i {
        margin-right: 5px;
    }

    .claim-section-space {
        margin-top: 5px;
    }

    .claim-danger {
        margin: 5px 0 10px;
        border-radius: 6px;
    }

    .claim-form-label {
        font-weight: 600;
        font-size: 13px;
        color: #495057;
        margin-bottom: 8px;
    }

    .claim-form .form-control {
        height: 42px;
        border-radius: 5px;
    }

    .claim-actions {
        border-top: 1px solid #edf0f2;
        margin-top: 20px;
        padding-top: 20px;
    }

    .btn-back {
        margin-right: 5px;
    }

    @media (max-width: 767px) {

        .claim-header .text-right {
            text-align: left !important;
            margin-top: 15px;
        }

        .claim-info th {
            width: 45%;
        }

        .claim-card-body {
            padding: 5px 15px 15px;
        }

        .claim-card-header {
            padding: 15px;
        }

        .claim-header h2 {
            font-size: 21px;
        }
    }
</style>


<div class="member-view claim-page">

    <!-- =========================================================
         HEADER
         ========================================================= -->

    <div class="row claim-header">

        <div class="col-md-8">

            <h2>
                <i class="fa fa-file-text-o"></i>
                Detail Claim
            </h2>

            <div class="claim-subtitle">
                ID Pengajuan:
                <strong>
                    <?= $val($model->id_pengajuan) ?>
                </strong>
            </div>

        </div>

        <div class="col-md-4 text-right">

            <?= Html::a(
                '<i class="fa fa-arrow-left"></i> Kembali',
                ['index'],
                [
                    'class' => 'btn btn-secondary btn-back waves-effect waves-light'
                ]
            ) ?>

        </div>

    </div>


    <?= Alert::widget() ?>


    <!-- =========================================================
         INFORMASI TRANSAKSI
         ========================================================= -->

    <div class="claim-card">

        <div class="claim-card-header">

            <h4>
                <i class="fa fa-info-circle"></i>
                Informasi Transaksi
            </h4>

        </div>

        <div class="claim-card-body">

            <div class="row">

                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>ID</th>
                            <td>
                                <span class="claim-id-box">
                                    <?= $val($model->id) ?>
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>ID Transaksi</th>
                            <td>
                                <?= $val($model->id_transaksi) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>ID Pengajuan</th>
                            <td>
                                <?= $val($model->id_pengajuan) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Kode Broker</th>
                            <td>
                                <?= $val($model->kode_broker) ?>
                            </td>
                        </tr>

                    </table>

                </div>


                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>Kode Cabang</th>
                            <td>
                                <?= $val($model->kode_cabang) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>ID Pengajuan Klaim Riau</th>
                            <td>
                                <?= $val($model->id_pengajuan_klaim_riau) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Nomor Bukti</th>
                            <td>
                                <?= $val($model->nomor_bukti) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Created At</th>
                            <td>
                                <?= $formatDateTime($model->created_at) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         DATA DEBITUR
         ========================================================= -->

    <div class="claim-card">

        <div class="claim-card-header">

            <h4>
                <i class="fa fa-user"></i>
                Data Debitur
            </h4>

        </div>

        <div class="claim-card-body">

            <div class="row">

                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>Nama</th>
                            <td>
                                <?= $val($model->nama) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>KTP</th>
                            <td>
                                <?= $val($model->ktp) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Nomor Rekening</th>
                            <td>
                                <?= $val($model->nomor_rekening) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Benefit</th>
                            <td>
                                <?= $val($model->benefit) ?>
                            </td>
                        </tr>

                    </table>

                </div>


                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>Nomor Akad</th>
                            <td>
                                <?= $val($model->no_akad) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>ID Agunan</th>
                            <td>
                                <?= $val($model->id_agunan) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Tenor</th>
                            <td>
                                <?= $val($model->tenor) ?>

                                <?php if (!empty($model->tenor)): ?>
                                    Bulan
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Tenor Berjalan</th>
                            <td>
                                <?= $val($model->tenor_berjalan) ?>

                                <?php if (!empty($model->tenor_berjalan)): ?>
                                    Bulan
                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Sisa Tenor</th>
                            <td>
                                <?= $val($model->sisa_tenor) ?>

                                <?php if (!empty($model->sisa_tenor)): ?>
                                    Bulan
                                <?php endif; ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         DATA PEMBIAYAAN
         ========================================================= -->

    <div class="claim-card">

        <div class="claim-card-header">

            <h4>
                <i class="fa fa-credit-card"></i>
                Data Pembiayaan
            </h4>

        </div>

        <div class="claim-card-body">

            <div class="row">

                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>Premi</th>
                            <td>
                                <?= $model->premi !== null
                                    ? '<span class="claim-value-highlight">' .
                                      Html::encode($formatRupiah($model->premi)) .
                                      '</span>'
                                    : '-'
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Periode Awal</th>
                            <td>
                                <?= $formatDate($model->periode_awal) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Periode Akhir</th>
                            <td>
                                <?= $formatDate($model->periode_akhir) ?>
                            </td>
                        </tr>

                    </table>

                </div>


                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>Jumlah Diajukan</th>
                            <td>
                                <?php if ($model->jumlah_diajukan !== null): ?>

                                    <span class="claim-value-highlight">
                                        <?= Html::encode(
                                            $formatRupiah(
                                                $model->jumlah_diajukan
                                            )
                                        ) ?>
                                    </span>

                                <?php else: ?>

                                    -

                                <?php endif; ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Tujuan Pembayaran</th>
                            <td>
                                <?= $val($model->tujuan_pembayaran) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Nomor Rekening</th>
                            <td>
                                <?= $val($model->nomor_rekening) ?>
                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         INFORMASI CLAIM
         ========================================================= -->

    <div class="claim-card">

        <div class="claim-card-header">

            <h4>
                <i class="fa fa-file-text"></i>
                Informasi Claim
            </h4>

        </div>

        <div class="claim-card-body">

            <div class="row">

                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>Jenis Klaim</th>
                            <td>
                                <?= $val($model->jenis_klaim) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Penyebab Klaim</th>
                            <td>
                                <?= $val($model->penyebab_klaim) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Tanggal Kejadian</th>
                            <td>
                                <?= $formatDate($model->tanggal_kejadian) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Tempat Kejadian</th>
                            <td>
                                <?= $val($model->tempat_kejadian) ?>
                            </td>
                        </tr>

                    </table>

                </div>


                <div class="col-md-6">

                    <table class="claim-info">

                        <tr>
                            <th>Tanggal Kirim</th>
                            <td>
                                <?= $formatDate($model->tanggal_kirim) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Nomor Bukti</th>
                            <td>
                                <?= $val($model->nomor_bukti) ?>
                            </td>
                        </tr>

                        <tr>
                            <th>Status Claim</th>
                            <td>

                                <span class="badge <?= $statusClass ?> claim-status">

                                    <i class="fa <?= $statusIcon ?>"></i>

                                    <?= Html::encode($statusLabel) ?>

                                </span>

                            </td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         ALASAN BATAL
         ========================================================= -->

    <?php if (!empty($model->alasan_batal_klaim)): ?>

        <div class="claim-card">

            <div class="claim-card-header">

                <h4>
                    <i class="fa fa-exclamation-triangle"></i>
                    Alasan Batal Klaim
                </h4>

            </div>

            <div class="claim-card-body">

                <div class="alert alert-danger claim-danger">

                    <i class="fa fa-warning"></i>

                    <?= nl2br(
                        Html::encode($model->alasan_batal_klaim)
                    ) ?>

                </div>

            </div>

        </div>

    <?php endif; ?>


    <!-- =========================================================
         UPDATE STATUS
         ========================================================= -->

    <div class="claim-card">

        <div class="claim-card-header">

            <h4>
                <i class="fa fa-check-circle"></i>
                Update Status Claim
            </h4>

        </div>

        <div class="claim-card-body">

            <?= Html::beginForm(
                ['update', 'id' => $model->id],
                'post',
                [
                    'id' => 'claim-form',
                    'class' => 'claim-form'
                ]
            ) ?>

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label
                            for="status_claim"
                            class="claim-form-label"
                        >
                            Status Claim
                        </label>

                        <?= Html::dropDownList(
                            'status_claim',
                            $model->status_claim,
                            [
                                '0' => 'Analisa',
                                '1' => 'Approved',
                                '2' => 'Ditolak',
                            ],
                            [
                                'class' => 'form-control',
                                'id' => 'status_claim',
                                'prompt' => 'Pilih Status'
                            ]
                        ) ?>

                    </div>

                </div>

            </div>


            <div class="claim-actions">

                <div class="text-right">

                    <?= Html::submitButton(
                        '<i class="fa fa-save"></i> Simpan Perubahan',
                        [
                            'class' =>
                                'btn btn-success btn-lg waves-effect waves-light',
                        ]
                    ) ?>

                </div>

            </div>

            <?= Html::endForm() ?>

        </div>

    </div>

</div>
