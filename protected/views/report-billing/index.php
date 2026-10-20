
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Summary Produksi - ' . Yii::$app->name;

$request = Yii::$app->request;

/*
|--------------------------------------------------------------------------
| DATA SUMMARY
|--------------------------------------------------------------------------
*/

$totalPengajuan = (int) ($summary['total_pengajuan'] ?? 0);
$disetujui = (int) ($summary['Inforce'] ?? 0);
$pending = (int) ($summary['pending'] ?? 0);
$ditolak = (int) ($summary['cancel'] ?? 0);

$totalGrossPremium = (float) ($summary['total_gross_premium'] ?? 0);
$totalNetPremium = (float) ($summary['total_net_premium'] ?? 0);

/*
|--------------------------------------------------------------------------
| FORMAT ANGKA
|--------------------------------------------------------------------------
*/

$formatNumber = function ($value) {
    return number_format((float) $value, 0, ',', '.');
};

$formatRupiah = function ($value) {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$filters = [
    'nama' => $request->get('nama', ''),
    'status' => $request->get('status', ''),
    'approve' => $request->get('approve', ''),
    'batch_no' => $request->get('batch_no', ''),
    'start_date' => $request->get('start_date', ''),
    'end_date' => $request->get('end_date', ''),
    'nama_kantor' => $request->get('nama_kantor', ''),
];

?>

<div class="summary-produksi-index">

    <!-- ======================================================
         MODAL FILTER
    ======================================================= -->

    <div
        class="modal fade"
        id="search-modal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="searchModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h4 class="modal-title" id="searchModalLabel">
                        <i class="fa fa-search"></i>
                        Filter Summary Produksi
                    </h4>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">

                    <?= Html::beginForm(
                        ['index'],
                        'get',
                        ['id' => 'summary-search-form']
                    ) ?>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">

                                <?= Html::label(
                                    'Nama Peserta',
                                    'nama'
                                ) ?>

                                <?= Html::textInput(
                                    'nama',
                                    $filters['nama'],
                                    [
                                        'class' => 'form-control',
                                        'id' => 'nama',
                                        'placeholder' => 'Masukkan nama peserta',
                                    ]
                                ) ?>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <?= Html::label(
                                    'Status Kepesertaan',
                                    'status'
                                ) ?>

                                <?= Html::textInput(
                                    'status',
                                    $filters['status'],
                                    [
                                        'class' => 'form-control',
                                        'id' => 'status',
                                        'placeholder' => 'Masukkan status',
                                    ]
                                ) ?>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <?= Html::label(
                                    'Status Dokumen',
                                    'approve'
                                ) ?>

                                <?= Html::dropDownList(
                                    'approve',
                                    $filters['approve'],
                                    [
                                        '' => 'Semua Status Dokumen',
                                        'DISETUJUI' => 'DISETUJUI',
                                        'DITOLAK' => 'DITOLAK',
                                        'DIPROSES' => 'DIPROSES',
                                        'Menunggu' => 'Menunggu',
                                    ],
                                    [
                                        'class' => 'form-control',
                                        'id' => 'approve',
                                    ]
                                ) ?>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <?= Html::label(
                                    'Batch Number',
                                    'batch_no'
                                ) ?>

                                <?= Html::textInput(
                                    'batch_no',
                                    $filters['batch_no'],
                                    [
                                        'class' => 'form-control',
                                        'id' => 'batch_no',
                                        'placeholder' => 'Masukkan batch number',
                                    ]
                                ) ?>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <?= Html::label(
                                    'Tanggal Awal',
                                    'start_date'
                                ) ?>

                                <?= Html::textInput(
                                    'start_date',
                                    $filters['start_date'],
                                    [
                                        'class' => 'form-control dtpckr',
                                        'id' => 'start_date',
                                        'placeholder' => 'YYYY-MM-DD',
                                        'autocomplete' => 'off',
                                    ]
                                ) ?>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                <?= Html::label(
                                    'Tanggal Akhir',
                                    'end_date'
                                ) ?>

                                <?= Html::textInput(
                                    'end_date',
                                    $filters['end_date'],
                                    [
                                        'class' => 'form-control dtpckr',
                                        'id' => 'end_date',
                                        'placeholder' => 'YYYY-MM-DD',
                                        'autocomplete' => 'off',
                                    ]
                                ) ?>

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">

                                <?= Html::label(
                                    'Cabang',
                                    'nama_kantor'
                                ) ?>

                                <?= Html::textInput(
                                    'nama_kantor',
                                    $filters['nama_kantor'],
                                    [
                                        'class' => 'form-control',
                                        'id' => 'nama_kantor',
                                        'placeholder' => 'Masukkan nama cabang',
                                    ]
                                ) ?>

                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12 text-right">

                            <?= Html::submitButton(
                                '<i class="fa fa-search"></i> Terapkan Filter',
                                [
                                    'class' => 'btn btn-primary',
                                ]
                            ) ?>

                            <?= Html::a(
                                '<i class="fa fa-refresh"></i> Reset',
                                ['index'],
                                [
                                    'class' => 'btn btn-secondary',
                                ]
                            ) ?>

                        </div>

                    </div>

                    <?= Html::endForm() ?>

                </div>

            </div>

        </div>
    </div>


    <!-- ======================================================
         HEADER REPORTING
    ======================================================= -->

    <div class="row mb-4 no-print">

        <div class="col-md-6">

            <h2 class="summary-title">
                <i class="fa fa-bar-chart"></i>
                Summary Produksi
            </h2>

            <p class="summary-subtitle">
                Laporan ringkasan produksi dan premi asuransi.
            </p>

        </div>

        <div class="col-md-6 text-right">

            <?= Html::button(
                '<i class="fa fa-search"></i> Filter Data',
                [
                    'class' => 'btn btn-info',
                    'data-toggle' => 'modal',
                    'data-target' => '#search-modal',
                ]
            ) ?>

            <?= Html::button(
                '<i class="fa fa-print"></i> Cetak Laporan',
                [
                    'class' => 'btn btn-success',
                    'onclick' => 'window.print();',
                ]
            ) ?>

            <?= Html::a(
                '<i class="fa fa-refresh"></i> Reset',
                ['index'],
                [
                    'class' => 'btn btn-light',
                ]
            ) ?>

        </div>

    </div>


    <?= Alert::widget() ?>


    <!-- ======================================================
         JUDUL CETAK
    ======================================================= -->

    <div class="report-print-header">

        <h2>SUMMARY PRODUKSI</h2>

        <h4>ASURANSI JIWA SYARIAH</h4>

        <p>
            Periode:
            <?= Html::encode(
                $filters['start_date'] !== ''
                    ? $filters['start_date']
                    : 'Semua Tanggal'
            ) ?>

            s/d

            <?= Html::encode(
                $filters['end_date'] !== ''
                    ? $filters['end_date']
                    : 'Sekarang'
            ) ?>
        </p>

        <?php if ($filters['nama_kantor'] !== ''): ?>
            <p>
                Cabang:
                <?= Html::encode($filters['nama_kantor']) ?>
            </p>
        <?php endif; ?>

        <p>
            Dicetak pada:
            <?= date('d-m-Y H:i:s') ?>
        </p>

        <hr>

    </div>


    <!-- ======================================================
         KARTU RINGKASAN
    ======================================================= -->

    <div class="row summary-cards">

        <!-- TOTAL PENGAJUAN -->

        <div class="col-lg-3 col-md-6">

            <div class="card-box summary-card">

                <div class="row align-items-center">

                    <div class="col-8">

                        <p class="summary-label">
                            Total Pengajuan
                        </p>

                        <h3 class="summary-value">
                            <?= $formatNumber($totalPengajuan) ?>
                        </h3>

                    </div>

                    <div class="col-4 text-right">

                        <i class="fa fa-file-text-o summary-icon"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- DISETUJUI -->

        <div class="col-lg-3 col-md-6">

            <div class="card-box summary-card">

                <div class="row align-items-center">

                    <div class="col-8">

                        <p class="summary-label">
                            Disetujui / Inforce
                        </p>

                        <h3 class="summary-value">
                            <?= $formatNumber($disetujui) ?>
                        </h3>

                    </div>

                    <div class="col-4 text-right">

                        <i class="fa fa-check-circle summary-icon"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- PENDING -->

        <div class="col-lg-3 col-md-6">

            <div class="card-box summary-card">

                <div class="row align-items-center">

                    <div class="col-8">

                        <p class="summary-label">
                            Pending
                        </p>

                        <h3 class="summary-value">
                            <?= $formatNumber($pending) ?>
                        </h3>

                    </div>

                    <div class="col-4 text-right">

                        <i class="fa fa-clock-o summary-icon"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- CANCEL -->

        <div class="col-lg-3 col-md-6">

            <div class="card-box summary-card">

                <div class="row align-items-center">

                    <div class="col-8">

                        <p class="summary-label">
                            Cancel
                        </p>

                        <h3 class="summary-value">
                            <?= $formatNumber($ditolak) ?>
                        </h3>

                    </div>

                    <div class="col-4 text-right">

                        <i class="fa fa-times-circle summary-icon"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ======================================================
         TABEL REPORTING
    ======================================================= -->

    <div class="row mt-3">

        <div class="col-12">

            <div class="card-box reporting-box">

                <div class="row mb-3">

                    <div class="col-md-8">

                        <h4 class="header-title">
                            Ringkasan Produksi dan Premi
                        </h4>

                        <p class="text-muted mb-0">
                            Data berdasarkan filter yang dipilih.
                        </p>

                    </div>

                    <div class="col-md-4 text-right no-print">

                        <span class="report-badge">
                            Laporan Produksi
                        </span>

                    </div>

                </div>


                <div class="table-responsive">

                    <table class="table table-hover table-bordered reporting-table">

                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-right">Total Pengajuan</th>
                                <th class="text-right">Disetujui</th>
                                <th class="text-right">Pending</th>
                                <th class="text-right">Cancel</th>
                                <th class="text-right">Total Gross Premium</th>
                                <th class="text-right">Total Net Premium</th>
                                <th class="text-center no-print">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>

                                <td class="text-center">
                                    1
                                </td>

                                <td class="text-right">
                                    <?= $formatNumber($totalPengajuan) ?>
                                </td>

                                <td class="text-right">
                                    <?= $formatNumber($disetujui) ?>
                                </td>

                                <td class="text-right">
                                    <?= $formatNumber($pending) ?>
                                </td>

                                <td class="text-right">
                                    <?= $formatNumber($ditolak) ?>
                                </td>

                                <td class="text-right">
                                    <?= $formatRupiah($totalGrossPremium) ?>
                                </td>

                                <td class="text-right">
                                    <?= $formatRupiah($totalNetPremium) ?>
                                </td>

                                <td class="text-center no-print">

                                    <?= Html::a(
                                        '<i class="fa fa-list"></i> Detail',
                                        array_merge(
                                            ['/data-produksi/index'],
                                            $filters
                                        ),
                                        [
                                            'class' => 'btn btn-info btn-sm',
                                            'title' => 'Lihat detail produksi',
                                        ]
                                    ) ?>

                                </td>

                            </tr>

                        </tbody>

                        <tfoot>

                            <tr>

                                <th colspan="5" class="text-right">
                                    TOTAL PREMI
                                </th>

                                <th class="text-right">
                                    <?= $formatRupiah($totalGrossPremium) ?>
                                </th>

                                <th class="text-right">
                                    <?= $formatRupiah($totalNetPremium) ?>
                                </th>

                                <th class="no-print"></th>

                            </tr>

                        </tfoot>

                    </table>

                </div>


                <!-- KETERANGAN -->

                <div class="report-note">

                    <strong>Keterangan:</strong>

                    <ul>
                        <li>
                            Total Pengajuan merupakan jumlah pengajuan
                            berdasarkan hasil filter.
                        </li>
                        <li>
                            Disetujui / Inforce menunjukkan jumlah peserta
                            berstatus Inforce berdasarkan data summary.
                        </li>
                        <li>
                            Gross Premium dan Net Premium mengikuti
                            hasil perhitungan pada controller.
                        </li>
                    </ul>

                </div>


                <!-- TANDA TANGAN CETAK -->

                <div class="signature-section">

                    <div class="signature-box">

                        <p>Mengetahui,</p>

                        <br><br><br>

                        <p>
                            <strong>
                                __________________________
                            </strong>
                        </p>

                        <p>Penanggung Jawab</p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ======================================================
     CSS REPORTING
======================================================= -->

<style>

/* ======================================================
   TAMPILAN UTAMA
======================================================= */

.summary-produksi-index {
    font-family: "Roboto", Arial, sans-serif;
}

.summary-produksi-index .summary-title {
    color: #ffffff !important;
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 8px;
}

.summary-produksi-index .summary-subtitle {
    color: #ffffff !important;
    font-size: 14px;
    font-weight: 500;
}

.summary-produksi-index .card-box {
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #292e33;
    border: 1px solid #555b61;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}

.summary-produksi-index .summary-card {
    min-height: 120px;
}

.summary-produksi-index .summary-label {
    color: #ffffff !important;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 10px;
}

.summary-produksi-index .summary-value {
    color: #ffffff !important;
    font-size: 27px;
    font-weight: 700;
    margin: 0;
    overflow-wrap: anywhere;
}

.summary-produksi-index .summary-icon {
    color: #ffffff !important;
    font-size: 34px;
}

.summary-produksi-index .header-title {
    color: #ffffff !important;
    font-size: 18px;
    font-weight: 700;
}

.summary-produksi-index p,
.summary-produksi-index label,
.summary-produksi-index th,
.summary-produksi-index td,
.summary-produksi-index strong,
.summary-produksi-index .text-muted,
.summary-produksi-index span {
    color: #ffffff !important;
}


/* ======================================================
   TOMBOL
======================================================= */

.summary-produksi-index .btn {
    font-weight: 700;
    margin-bottom: 4px;
}

.summary-produksi-index .btn-info,
.summary-produksi-index .btn-success,
.summary-produksi-index .btn-primary,
.summary-produksi-index .btn-secondary,
.summary-produksi-index .btn-light {
    color: #ffffff !important;
}


/* ======================================================
   TABEL REPORTING
======================================================= */

.summary-produksi-index .reporting-table {
    width: 100%;
    border-collapse: collapse !important;
    border: 2px solid #ffffff !important;
    margin-bottom: 0;
}

.summary-produksi-index .reporting-table thead th {
    background-color: #343a40 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    text-align: center;
    vertical-align: middle;
    padding: 12px 10px;
    border: 2px solid #ffffff !important;
    white-space: nowrap;
}

.summary-produksi-index .reporting-table tbody td {
    background-color: transparent !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    vertical-align: middle;
    padding: 12px 10px;
    border: 2px solid #ffffff !important;
}

.summary-produksi-index .reporting-table tbody tr:hover td {
    background-color: #3b4249 !important;
    color: #ffffff !important;
    border-color: #ffffff !important;
}

.summary-produksi-index .reporting-table tfoot th {
    background-color: #343a40 !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    padding: 12px 10px;
    border: 2px solid #ffffff !important;
}

.summary-produksi-index .report-badge {
    display: inline-block;
    padding: 7px 12px;
    background-color: #343a40;
    border: 1px solid #ffffff;
    border-radius: 5px;
    color: #ffffff !important;
    font-weight: 700;
}


/* ======================================================
   MODAL FILTER
======================================================= */

#search-modal .modal-content {
    background-color: #292e33;
    border: 1px solid #777777;
}

#search-modal .modal-header,
#search-modal .modal-body {
    border-color: #666666;
}

#search-modal .modal-title,
#search-modal .modal-body label,
#search-modal .modal-header .close {
    color: #ffffff !important;
    font-weight: 700;
}

#search-modal .form-control {
    color: #ffffff !important;
    background-color: #343a40 !important;
    border: 1px solid #777777 !important;
    font-weight: 600;
}

#search-modal .form-control::placeholder {
    color: #cccccc !important;
    opacity: 1;
}

#search-modal select option {
    color: #ffffff;
    background-color: #343a40;
}

#search-modal .close {
    opacity: 1;
    text-shadow: none;
}


/* ======================================================
   KETERANGAN DAN TANDA TANGAN
======================================================= */

.summary-produksi-index .report-note {
    margin-top: 22px;
    padding: 15px;
    border: 1px solid #666666;
    border-radius: 5px;
}

.summary-produksi-index .report-note ul {
    margin-top: 10px;
    padding-left: 22px;
}

.summary-produksi-index .report-note li {
    color: #ffffff;
    margin-bottom: 5px;
}

.summary-produksi-index .signature-section {
    display: flex;
    justify-content: flex-end;
    margin-top: 30px;
}

.summary-produksi-index .signature-box {
    width: 260px;
    text-align: center;
}

.summary-produksi-index .signature-box p {
    color: #ffffff;
}


/* ======================================================
   HEADER CETAK
======================================================= */

.report-print-header {
    display: none;
}


/* ======================================================
   RESPONSIVE
======================================================= */

@media screen and (max-width: 767px) {

    .summary-produksi-index .summary-title {
        font-size: 22px;
    }

    .summary-produksi-index .summary-value {
        font-size: 23px;
    }

    .summary-produksi-index .reporting-table {
        min-width: 1000px;
    }

    .summary-produksi-index .signature-section {
        justify-content: center;
    }

}


/* ======================================================
   PRINT REPORT
======================================================= */

@media print {

    @page {
        size: landscape;
        margin: 12mm;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    body * {
        visibility: hidden;
    }

    .summary-produksi-index,
    .summary-produksi-index * {
        visibility: visible;
    }

    .summary-produksi-index {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        font-family: Arial, sans-serif;
        color: #000000 !important;
    }

    .summary-produksi-index .no-print,
    .summary-produksi-index .btn,
    .summary-produksi-index #search-modal,
    .summary-produksi-index .alert,
    .modal-backdrop {
        display: none !important;
    }

    .report-print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
    }

    .report-print-header h2,
    .report-print-header h4,
    .report-print-header p {
        color: #000000 !important;
        font-weight: 700;
    }

    .summary-produksi-index .card-box {
        background: #ffffff !important;
        border: 1px solid #000000 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 12px;
    }

    .summary-produksi-index .summary-title,
    .summary-produksi-index .summary-subtitle,
    .summary-produksi-index .summary-label,
    .summary-produksi-index .summary-value,
    .summary-produksi-index .summary-icon,
    .summary-produksi-index .header-title,
    .summary-produksi-index p,
    .summary-produksi-index strong,
    .summary-produksi-index li {
        color: #000000 !important;
    }

    .summary-produksi-index .reporting-table {
        width: 100% !important;
        border-collapse: collapse !important;
        border: 1px solid #000000 !important;
    }

    .summary-produksi-index .reporting-table th,
    .summary-produksi-index .reporting-table td {
        color: #000000 !important;
        background: #ffffff !important;
        border: 1px solid #000000 !important;
        padding: 8px 6px !important;
        font-size: 10px;
    }

    .summary-produksi-index .reporting-table thead th,
    .summary-produksi-index .reporting-table tfoot th {
        background: #eeeeee !important;
        font-weight: 700 !important;
    }

    .summary-produksi-index .table-responsive {
        overflow: visible !important;
    }

    .summary-produksi-index .report-note {
        border: 1px solid #000000 !important;
    }

    .summary-produksi-index .signature-box p {
        color: #000000 !important;
    }

    .summary-produksi-index .summary-cards {
        display: flex;
        flex-wrap: nowrap;
    }

    .summary-produksi-index .summary-cards > div {
        width: 25%;
        flex: 0 0 25%;
    }

    .summary-produksi-index .reporting-box {
        page-break-inside: avoid;
    }

}
</style>
