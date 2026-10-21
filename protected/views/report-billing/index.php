
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Summary Produksi - ' . Yii::$app->name;

$request = Yii::$app->request;


$totalPengajuan = (int) ($summary['total_pengajuan'] ?? 0);
$disetujui = (int) ($summary['Inforce'] ?? 0);
$pending = (int) ($summary['pending'] ?? 0);
$ditolak = (int) ($summary['cancel'] ?? 0);

$totalGrossPremium = (float) ($summary['total_gross_premium'] ?? 0);
$totalNetPremium = (float) ($summary['total_net_premium'] ?? 0);
$extra_premium = (float) ($summary['extra_premium'] ?? 0);


$formatNumber = function ($value) {
    return number_format((float) $value, 0, ',', '.');
};

$formatRupiah = function ($value) {
    return 'Rp ' . number_format((float) $value, 0, ',', '.');
};



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
                    'class' => 'btn',
					'style' => 'background-color: #FFF3CD; border-color: #FFE69C; color: #664D03;',
                ]
            ) ?>

        </div>

    </div>


    <?= Alert::widget() ?>

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

    <div class="row summary-cards">



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

    <?php
    $statusTotal = max(1, $totalPengajuan);
    $persenInforce = min(100, round(($disetujui / $statusTotal) * 100, 1));
    $persenPending = min(100, round(($pending / $statusTotal) * 100, 1));
    $persenCancel = min(100, round(($ditolak / $statusTotal) * 100, 1));

    $premiumMax = max(1, $totalGrossPremium, $extra_premium, $totalNetPremium);
    $grossWidth = min(100, round(($totalGrossPremium / $premiumMax) * 100, 1));
    $extraWidth = min(100, round(($extra_premium / $premiumMax) * 100, 1));
    $netWidth = min(100, round(($totalNetPremium / $premiumMax) * 100, 1));
    ?>

    <div class="row dashboard-charts">
        <div class="col-lg-6 col-md-12">
            <div class="card-box dashboard-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <h4 class="header-title"><i class="fa fa-pie-chart"></i> Komposisi Status Pengajuan</h4>
                        <p class="dashboard-muted">Ringkasan status berdasarkan data hasil filter.</p>
                    </div>
                </div>

                <div class="status-chart-layout">
                    <div class="status-donut"
                         style="--inforce: <?= $persenInforce ?>; --pending: <?= $persenPending ?>; --cancel: <?= $persenCancel ?>;">
                        <div class="status-donut-center">
                            <strong><?= $formatNumber($totalPengajuan) ?></strong>
                            <span>Total Pengajuan</span>
                        </div>
                    </div>

                    <div class="status-legend">
                        <div class="legend-item">
                            <span class="legend-dot dot-inforce"></span>
                            <span>Disetujui / Inforce</span>
                            <strong><?= $formatNumber($disetujui) ?></strong>
                            <small><?= $persenInforce ?>%</small>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot dot-pending"></span>
                            <span>Pending</span>
                            <strong><?= $formatNumber($pending) ?></strong>
                            <small><?= $persenPending ?>%</small>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot dot-cancel"></span>
                            <span>Cancel</span>
                            <strong><?= $formatNumber($ditolak) ?></strong>
                            <small><?= $persenCancel ?>%</small>
                        </div>
                    </div>
                </div>
                <p class="dashboard-chart-note">Persentase dihitung dari Total Pengajuan.</p>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card-box dashboard-panel">
                <div class="dashboard-panel-heading">
                    <div>
                        <h4 class="header-title"><i class="fa fa-bar-chart"></i> Perbandingan Premi</h4>
                        <p class="dashboard-muted">Perbandingan nilai premi dalam Rupiah.</p>
                    </div>
                </div>

                <div class="premium-chart">
                    <div class="premium-chart-item">
                        <div class="premium-chart-label">
                            <span><i class="premium-dot gross-dot"></i> Gross Premium</span>
                            <strong><?= $formatRupiah($totalGrossPremium) ?></strong>
                        </div>
                        <div class="premium-track">
                            <div class="premium-bar gross-bar" style="width: <?= $grossWidth ?>%;"></div>
                        </div>
                    </div>

                    <div class="premium-chart-item">
                        <div class="premium-chart-label">
                            <span><i class="premium-dot extra-dot"></i> Extra Premium</span>
                            <strong><?= $formatRupiah($extra_premium) ?></strong>
                        </div>
                        <div class="premium-track">
                            <div class="premium-bar extra-bar" style="width: <?= $extraWidth ?>%;"></div>
                        </div>
                    </div>

                    <div class="premium-chart-item">
                        <div class="premium-chart-label">
                            <span><i class="premium-dot net-dot"></i> Nett Premium </span>
                            <strong><?= $formatRupiah($totalNetPremium) ?></strong>
                        </div>
                        <div class="premium-track">
                            <div class="premium-bar net-bar" style="width: <?= $netWidth ?>%;"></div>
                        </div>
                    </div>
                </div>
                <p class="dashboard-chart-note">Panjang batang dibandingkan dengan nilai premi terbesar pada ringkasan ini.</p>
            </div>
        </div>
    </div>

    <div class="row dashboard-premium-cards">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="premium-summary-card gross-summary">
                <div class="premium-summary-icon"><i class="fa fa-money"></i></div>
                <div class="premium-summary-content">
                    <span>Total Gross Premium</span>
                    <strong><?= $formatRupiah($totalGrossPremium) ?></strong>
                    <small>Premi bruto</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="premium-summary-card extra-summary">
                <div class="premium-summary-icon"><i class="fa fa-plus-circle"></i></div>
                <div class="premium-summary-content">
                    <span>Total Extra Premium</span>
                    <strong><?= $formatRupiah($extra_premium) ?></strong>
                    <small>Premi tambahan</small>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="premium-summary-card net-summary">
                <div class="premium-summary-icon"><i class="fa fa-credit-card"></i></div>
                <div class="premium-summary-content">
                    <span>Total Nett Premium</span>
                    <strong><?= $formatRupiah($totalNetPremium) ?></strong>
                    <small>Premi gross + extra premi</small>
                </div>
            </div>
        </div>
    </div>


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
								<th class="text-right">Total Extra Premium</th>
                                <th class="text-right">Total Nett Premium</th>
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
                                    <?= $formatRupiah($extra_premium) ?>
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
                                    <?= $formatRupiah($extra_premium) ?>
                                </th>

                                <th class="text-right">
                                    <?= $formatRupiah($totalNetPremium) ?>
                                </th>

                                <th class="no-print"></th>

                            </tr>

                        </tfoot>

                    </table>

                </div>


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
                            hasil filter.
                        </li>
                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>


<style>


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



.report-print-header {
    display: none;
}


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


.summary-produksi-index .dashboard-panel {
    background: #292e33;
    border: 1px solid #454d55;
    border-radius: 12px;
    min-height: 340px;
    padding: 22px;
}
.summary-produksi-index .dashboard-panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 22px;
}
.summary-produksi-index .dashboard-panel .header-title {
    margin: 0 0 8px;
    font-size: 17px;
}
.summary-produksi-index .dashboard-muted,
.summary-produksi-index .dashboard-chart-note {
    color: #c2c8ce !important;
    font-size: 12px;
    margin: 0;
}
.summary-produksi-index .status-chart-layout {
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 24px;
    flex-wrap: wrap;
}
.summary-produksi-index .status-donut {
    width: 190px;
    height: 190px;
    flex: 0 0 190px;
    border-radius: 50%;
    background: conic-gradient(
        #25b887 0 calc(var(--inforce) * 1%),
        #f2b84b calc(var(--inforce) * 1%) calc((var(--inforce) + var(--pending)) * 1%),
        #e35d6a calc((var(--inforce) + var(--pending)) * 1%) 100%
    );
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.summary-produksi-index .status-donut:before {
    content: "";
    position: absolute;
    width: 128px;
    height: 128px;
    border-radius: 50%;
    background: #292e33;
}
.summary-produksi-index .status-donut-center {
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.summary-produksi-index .status-donut-center strong {
    font-size: 25px;
    line-height: 1.3;
}
.summary-produksi-index .status-donut-center span {
    color: #c2c8ce !important;
    font-size: 11px;
}
.summary-produksi-index .status-legend {
    flex: 1;
    min-width: 190px;
}
.summary-produksi-index .legend-item {
    display: grid;
    grid-template-columns: 12px minmax(100px, 1fr) auto auto;
    align-items: center;
    gap: 8px;
    padding: 12px 0;
    border-bottom: 1px solid #454d55;
    font-size: 12px;
}
.summary-produksi-index .legend-item:last-child { border-bottom: 0; }
.summary-produksi-index .legend-item strong { font-size: 13px; }
.summary-produksi-index .legend-item small {
    color: #c2c8ce !important;
    min-width: 38px;
    text-align: right;
}
.summary-produksi-index .legend-dot,
.summary-produksi-index .premium-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.summary-produksi-index .dot-inforce { background: #25b887; }
.summary-produksi-index .dot-pending { background: #f2b84b; }
.summary-produksi-index .dot-cancel { background: #e35d6a; }
.summary-produksi-index .dashboard-chart-note { margin-top: 20px; }
.summary-produksi-index .premium-chart { padding-top: 5px; }
.summary-produksi-index .premium-chart-item { margin-bottom: 26px; }
.summary-produksi-index .premium-chart-label {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 10px;
    font-size: 12px;
}
.summary-produksi-index .premium-chart-label span { white-space: normal; }
.summary-produksi-index .premium-chart-label strong {
    text-align: right;
    font-size: 12px;
    overflow-wrap: anywhere;
}
.summary-produksi-index .premium-dot { margin-right: 7px; }
.summary-produksi-index .gross-dot { background: #4c9aff; }
.summary-produksi-index .extra-dot { background: #a78bfa; }
.summary-produksi-index .net-dot { background: #25b887; }
.summary-produksi-index .premium-track {
    height: 12px;
    border-radius: 20px;
    background: #41474e;
    overflow: hidden;
}
.summary-produksi-index .premium-bar {
    height: 100%;
    min-width: 0;
    border-radius: 20px;
}
.summary-produksi-index .gross-bar { background: #4c9aff; }
.summary-produksi-index .extra-bar { background: #a78bfa; }
.summary-produksi-index .net-bar { background: #25b887; }
.summary-produksi-index .dashboard-premium-cards { margin-top: 0; }
.summary-produksi-index .premium-summary-card {
    display: flex;
    align-items: center;
    gap: 16px;
    min-height: 115px;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
    border: 1px solid #454d55;
    background: #292e33;
}
.summary-produksi-index .premium-summary-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    border-radius: 12px;
    font-size: 22px;
    background: #343a40;
}
.summary-produksi-index .gross-summary .premium-summary-icon { color: #4c9aff; }
.summary-produksi-index .extra-summary .premium-summary-icon { color: #a78bfa; }
.summary-produksi-index .net-summary .premium-summary-icon { color: #25b887; }
.summary-produksi-index .premium-summary-content {
    display: flex;
    flex-direction: column;
    min-width: 0;
    gap: 5px;
}
.summary-produksi-index .premium-summary-content span {
    color: #c2c8ce !important;
    font-size: 12px;
}
.summary-produksi-index .premium-summary-content strong {
    font-size: 18px;
    overflow-wrap: anywhere;
}
.summary-produksi-index .premium-summary-content small {
    color: #aeb6be !important;
    font-size: 11px;
}
@media screen and (max-width: 767px) {
    .summary-produksi-index .dashboard-panel { padding: 16px; }
    .summary-produksi-index .status-donut {
        width: 160px; height: 160px; flex-basis: 160px;
    }
    .summary-produksi-index .status-donut:before {
        width: 108px; height: 108px;
    }
    .summary-produksi-index .premium-chart-label { flex-direction: column; gap: 5px; }
    .summary-produksi-index .premium-chart-label strong { text-align: left; }
}

.summary-produksi-index .card-box {
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.summary-produksi-index .summary-card:hover {
    box-shadow: 0 6px 16px rgba(32, 201, 151, 0.4);
    transform: translateY(-3px);
    transition: all 0.3s ease;
}

.summary-produksi-index .btn-info:hover {
    background-color: #17a2b8;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.5);
    transition: background-color 0.3s ease;
}

.status-donut {
    transition: background 1s ease;
}

.premium-bar {
    transition: width 1s ease;
}

@media screen and (max-width: 767px) {
    .summary-produksi-index .summary-cards > div {
        width: 100%;
        flex: unset;
        margin-bottom: 20px;
    }
}


</style>
