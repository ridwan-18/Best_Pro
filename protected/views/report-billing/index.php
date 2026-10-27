
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



<style>
/* Reporting Dashboard - clean, professional light theme */
.summary-produksi-index {
    font-family: "Roboto", "Segoe UI", Arial, sans-serif;
    color: #243247;
    background: #f4f7fb;
    padding: 18px;
    border-radius: 16px;
}
.summary-produksi-index > .row.mb-4.no-print {
    margin-left: 0;
    margin-right: 0;
    padding: 24px 26px;
    margin-bottom: 24px !important;
    border-radius: 16px;
    background: linear-gradient(120deg, #142b4a 0%, #214b75 68%, #2d668b 100%);
    box-shadow: 0 8px 22px rgba(22, 49, 79, .16);
    align-items: center;
}
.summary-produksi-index .summary-title {
    color: #fff !important;
    font-size: 27px;
    font-weight: 750;
    letter-spacing: -.4px;
    margin: 0 0 7px;
}
.summary-produksi-index .summary-subtitle {
    color: #dce8f5 !important;
    font-size: 13px;
    margin: 0;
}
.summary-produksi-index .card-box,
.summary-produksi-index .dashboard-panel,
.summary-produksi-index .premium-summary-card,
.summary-produksi-index .reporting-box {
    background: #fff !important;
    border: 1px solid #e3eaf2 !important;
    border-radius: 14px !important;
    box-shadow: 0 4px 16px rgba(30, 55, 90, .055) !important;
    color: #243247;
}
.summary-produksi-index .card-box {
    padding: 22px;
    margin-bottom: 20px;
}
.summary-produksi-index .summary-card {
    min-height: 132px;
    position: relative;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
}
.summary-produksi-index .summary-card:after {
    content: "";
    position: absolute;
    right: -22px;
    top: -28px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(42, 102, 161, .07);
}
.summary-produksi-index .summary-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(30, 55, 90, .11) !important;
}
.summary-produksi-index .summary-label {
    color: #68788d !important;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .35px;
    text-transform: uppercase;
    margin-bottom: 12px;
}
.summary-produksi-index .summary-value {
    color: #172d49 !important;
    font-size: 30px;
    font-weight: 750;
}
.summary-produksi-index .summary-icon {
    color: #3977b9 !important;
    background: #edf5ff;
    padding: 13px;
    border-radius: 13px;
    font-size: 25px !important;
}
.summary-produksi-index .header-title {
    color: #1c3554 !important;
    font-size: 17px;
    font-weight: 750;
}
.summary-produksi-index p,
.summary-produksi-index label,
.summary-produksi-index th,
.summary-produksi-index td,
.summary-produksi-index strong,
.summary-produksi-index .text-muted,
.summary-produksi-index span {
    color: #33445b !important;
}
.summary-produksi-index .dashboard-panel {
    min-height: 340px;
    padding: 23px;
}
.summary-produksi-index .dashboard-muted,
.summary-produksi-index .dashboard-chart-note {
    color: #78879a !important;
}
.summary-produksi-index .status-donut:before {
    background: #fff;
}
.summary-produksi-index .status-donut-center strong {
    color: #1b3452 !important;
}
.summary-produksi-index .status-donut-center span,
.summary-produksi-index .legend-item small {
    color: #78879a !important;
}
.summary-produksi-index .legend-item {
    border-bottom-color: #edf1f6;
}
.summary-produksi-index .premium-track {
    background: #edf1f6;
}
.summary-produksi-index .premium-summary-card {
    min-height: 120px;
    padding: 22px;
}
.summary-produksi-index .premium-summary-icon {
    background: #f0f5fb;
}
.summary-produksi-index .premium-summary-content span,
.summary-produksi-index .premium-summary-content small {
    color: #77869a !important;
}
.summary-produksi-index .premium-summary-content strong {
    color: #1b3452 !important;
    font-size: 19px;
}
.summary-produksi-index .reporting-box {
    padding: 24px;
}
.summary-produksi-index .reporting-table {
    border: 1px solid #e0e7ef !important;
    border-collapse: separate !important;
    border-spacing: 0;
    border-radius: 10px;
    overflow: hidden;
}
.summary-produksi-index .reporting-table thead th {
    background: #f0f5fa !important;
    color: #344b66 !important;
    border: 1px solid #e0e7ef !important;
    font-size: 11px;
    letter-spacing: .25px;
    text-transform: uppercase;
}
.summary-produksi-index .reporting-table tbody td {
    background: #fff !important;
    color: #34445a !important;
    border: 1px solid #e8edf3 !important;
    font-size: 12px;
}
.summary-produksi-index .reporting-table tbody tr:hover td {
    background: #f7faff !important;
    color: #1b3452 !important;
}
.summary-produksi-index .reporting-table tfoot th {
    background: #f0f5fa !important;
    color: #1b3452 !important;
    border: 1px solid #e0e7ef !important;
}
.summary-produksi-index .report-badge {
    background: #edf5ff;
    border: 1px solid #d5e6fa;
    color: #245c91 !important;
    border-radius: 20px;
    font-size: 11px;
    letter-spacing: .3px;
}
.summary-produksi-index .report-note {
    background: #f8fafc;
    border: 1px solid #e3eaf2;
    border-radius: 10px;
}
.summary-produksi-index .report-note li {
    color: #586a80 !important;
}
.summary-produksi-index .btn {
    border-radius: 8px;
    padding: 9px 14px;
    font-size: 12px;
    font-weight: 700;
    box-shadow: none;
}
.summary-produksi-index .btn-info {
    background: #e8f2ff !important;
    border-color: #cfe2fa !important;
    color: #245b91 !important;
}
.summary-produksi-index .btn-success {
    background: #e6f7ef !important;
    border-color: #c5ead8 !important;
    color: #18794e !important;
}
.summary-produksi-index .btn-primary {
    background: #245b91 !important;
    border-color: #245b91 !important;
    color: #fff !important;
}
.summary-produksi-index .btn-secondary,
.summary-produksi-index .btn-light {
    background: #fff3cd !important;
    border-color: #ffe69c !important;
    color: #664d03 !important;
}
.summary-produksi-index .btn-info:hover,
.summary-produksi-index .btn-success:hover {
    filter: brightness(.97);
    box-shadow: 0 4px 12px rgba(30, 55, 90, .10);
}
#search-modal .modal-content {
    background: #fff !important;
    border: 1px solid #e0e7ef !important;
    border-radius: 14px;
    box-shadow: 0 18px 50px rgba(20, 43, 74, .18);
}
#search-modal .modal-header,
#search-modal .modal-body {
    border-color: #e5ebf2 !important;
}
#search-modal .modal-title,
#search-modal .modal-body label,
#search-modal .modal-header .close {
    color: #1c3554 !important;
}
#search-modal .form-control {
    background: #fff !important;
    color: #243247 !important;
    border: 1px solid #d7e0ea !important;
    border-radius: 8px;
}
#search-modal .form-control::placeholder {
    color: #94a1b2 !important;
}
#search-modal select option {
    color: #243247;
    background: #fff;
}
@media screen and (max-width: 767px) {
    .summary-produksi-index { padding: 10px; }
    .summary-produksi-index > .row.mb-4.no-print { padding: 20px 16px; }
    .summary-produksi-index .summary-title { font-size: 22px; }
    .summary-produksi-index .reporting-box { padding: 15px; }
}
@media print {
    .summary-produksi-index { background: #fff !important; padding: 0; }
    .summary-produksi-index > .row.mb-4.no-print { display: none !important; }
}
</style>

<!-- REDESIGN SUMMARY PRODUKSI: visual layer only -->
<style>
.summary-produksi-index {
  --sp-ink:#eaf1ff; --sp-muted:#9eacc4; --sp-panel:#111d31; --sp-panel2:#15243b;
  --sp-line:rgba(151,174,211,.17); --sp-blue:#55a6ff; --sp-green:#30d6a0;
  --sp-purple:#ad91ff; --sp-orange:#ffbd69;
  background:#081222 !important; color:var(--sp-ink) !important; padding:26px !important;
  border-radius:22px !important; font-family:Inter,"Segoe UI",Arial,sans-serif !important;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.035);
}
.summary-produksi-index > .row.mb-4.no-print {
  position:relative; overflow:hidden; margin:0 0 25px !important; padding:30px 32px !important;
  border:1px solid rgba(110,166,255,.28); border-radius:22px !important;
  background:radial-gradient(circle at 88% 0%,rgba(85,166,255,.28),transparent 34%),
             linear-gradient(115deg,#142747 0%,#10203a 58%,#172d4b 100%) !important;
  box-shadow:0 18px 45px rgba(0,0,0,.23) !important;
}
.summary-produksi-index > .row.mb-4.no-print:after {
  content:"PRODUCTION ANALYTICS"; position:absolute; right:28px; top:20px;
  color:rgba(215,230,255,.23); font-size:10px; letter-spacing:3px; font-weight:800;
}
.summary-produksi-index .summary-title {color:#fff !important;font-size:30px !important;font-weight:800 !important;letter-spacing:-.8px;margin-bottom:8px !important}
.summary-produksi-index .summary-subtitle {color:#b7c9e5 !important;font-size:13px !important;letter-spacing:.15px}
.summary-produksi-index .summary-cards {display:grid !important;grid-template-columns:repeat(4,minmax(0,1fr));gap:17px;margin:0 0 22px !important}
.summary-produksi-index .summary-cards > [class*="col-"] {width:auto !important;max-width:none !important;flex:none !important;padding:0 !important}
.summary-produksi-index .summary-card {
  position:relative; min-height:158px !important; padding:23px !important; overflow:hidden;
  background:linear-gradient(145deg,#172740,#101b2e) !important; border:1px solid var(--sp-line) !important;
  border-radius:18px !important; box-shadow:0 10px 24px rgba(0,0,0,.16) !important;
}
.summary-produksi-index .summary-card:before {content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--sp-blue)}
.summary-produksi-index .summary-cards > div:nth-child(2) .summary-card:before {background:var(--sp-green)}
.summary-produksi-index .summary-cards > div:nth-child(3) .summary-card:before {background:var(--sp-orange)}
.summary-produksi-index .summary-cards > div:nth-child(4) .summary-card:before {background:#ff7e93}
.summary-produksi-index .summary-card:after {content:"";position:absolute;right:-34px;bottom:-52px;width:135px;height:135px;border:1px solid rgba(125,174,255,.13);border-radius:50%;box-shadow:0 0 0 17px rgba(125,174,255,.025),0 0 0 35px rgba(125,174,255,.02)}
.summary-produksi-index .summary-label {color:#9eacc4 !important;font-size:11px !important;font-weight:800 !important;letter-spacing:1.1px !important;text-transform:uppercase;margin-bottom:17px !important}
.summary-produksi-index .summary-value {color:#f5f8ff !important;font-size:34px !important;font-weight:800 !important;letter-spacing:-1px;line-height:1.1}
.summary-produksi-index .summary-icon {display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:rgba(85,166,255,.12);border:1px solid rgba(85,166,255,.2);border-radius:15px;color:var(--sp-blue) !important;font-size:22px !important}
.summary-produksi-index .card-box,
.summary-produksi-index .dashboard-panel,
.summary-produksi-index .premium-summary-card,
.summary-produksi-index .reporting-box {
  color:var(--sp-ink) !important;background:linear-gradient(145deg,#121f34,#0f1a2c) !important;
  border:1px solid var(--sp-line) !important;border-radius:19px !important;
  box-shadow:0 12px 30px rgba(0,0,0,.16) !important;
}
.summary-produksi-index .dashboard-panel {min-height:350px;padding:26px !important;margin-bottom:20px}
.summary-produksi-index .dashboard-panel-heading {padding-bottom:16px;border-bottom:1px solid var(--sp-line);margin-bottom:23px !important}
.summary-produksi-index .header-title {color:#edf4ff !important;font-size:16px !important;font-weight:750 !important;letter-spacing:-.2px}
.summary-produksi-index .dashboard-muted,.summary-produksi-index .dashboard-chart-note {color:var(--sp-muted) !important}
.summary-produksi-index .status-donut:before {background:#111d31 !important}
.summary-produksi-index .status-donut-center strong {color:#fff !important;font-size:27px}
.summary-produksi-index .status-donut-center span,.summary-produksi-index .legend-item small {color:var(--sp-muted) !important}
.summary-produksi-index .legend-item {border-bottom:1px solid var(--sp-line) !important;padding:11px 0}
.summary-produksi-index .premium-track {background:#26364e !important;height:11px !important;border-radius:20px}
.summary-produksi-index .premium-bar {border-radius:20px !important}
.summary-produksi-index .gross-bar {background:linear-gradient(90deg,#3989ff,#67bcff) !important}
.summary-produksi-index .extra-bar {background:linear-gradient(90deg,#8b70ff,#c0aaff) !important}
.summary-produksi-index .net-bar {background:linear-gradient(90deg,#16b987,#5be5b7) !important}
.summary-produksi-index .premium-summary-card {padding:23px !important;min-height:126px;margin-bottom:18px !important;gap:17px !important}
.summary-produksi-index .premium-summary-icon {width:54px !important;height:54px !important;flex:0 0 54px !important;border-radius:16px !important;background:#1d304b !important;font-size:23px !important}
.summary-produksi-index .gross-summary .premium-summary-icon {color:var(--sp-blue) !important}
.summary-produksi-index .extra-summary .premium-summary-icon {color:var(--sp-purple) !important}
.summary-produksi-index .net-summary .premium-summary-icon {color:var(--sp-green) !important}
.summary-produksi-index .premium-summary-content span,.summary-produksi-index .premium-summary-content small {color:var(--sp-muted) !important}
.summary-produksi-index .premium-summary-content strong {color:#f4f7ff !important;font-size:21px !important;font-weight:800}
.summary-produksi-index .reporting-box {padding:25px !important;margin-bottom:22px}
.summary-produksi-index .reporting-box .header-title {font-size:18px !important}
.summary-produksi-index .reporting-table {border-collapse:separate !important;border-spacing:0 !important;border:1px solid var(--sp-line) !important;border-radius:12px !important;overflow:hidden;background:#0d192b !important}
.summary-produksi-index .reporting-table thead th {background:#1b2d47 !important;color:#b9cce8 !important;border:0 !important;border-bottom:1px solid #344763 !important;padding:15px 13px !important;font-size:10px !important;letter-spacing:.8px;text-transform:uppercase;font-weight:800 !important}
.summary-produksi-index .reporting-table tbody td {background:#101d30 !important;color:#dce6f6 !important;border:0 !important;border-bottom:1px solid rgba(151,174,211,.1) !important;padding:13px !important;font-size:12px !important;vertical-align:middle}
.summary-produksi-index .reporting-table tbody tr:nth-child(even) td {background:#132239 !important}
.summary-produksi-index .reporting-table tbody tr:hover td {background:#1b304d !important}
.summary-produksi-index .reporting-table tfoot th {background:#1b2d47 !important;color:#fff !important;border:0 !important;padding:14px 13px !important}
.summary-produksi-index p,.summary-produksi-index label,.summary-produksi-index th,.summary-produksi-index td,.summary-produksi-index strong,.summary-produksi-index .text-muted,.summary-produksi-index span {color:inherit}
.summary-produksi-index .text-muted {color:var(--sp-muted) !important}
.summary-produksi-index .btn {border-radius:10px !important;font-weight:700 !important;padding:10px 15px !important;box-shadow:none !important;transition:transform .18s ease,filter .18s ease}
.summary-produksi-index .btn:hover {transform:translateY(-2px);filter:brightness(1.08)}
.summary-produksi-index .btn-primary,.summary-produksi-index .btn-info {background:linear-gradient(120deg,#2879e8,#4a9dff) !important;border:0 !important;color:#fff !important}
.summary-produksi-index .btn-success {background:linear-gradient(120deg,#119b70,#2fd4a0) !important;border:0 !important;color:#061b16 !important}
.summary-produksi-index .btn-light,.summary-produksi-index .btn-default {background:#22344d !important;border:1px solid #344966 !important;color:#e5eefc !important}
#search-modal .modal-content {background:#111d31 !important;color:#eaf1ff !important;border:1px solid #344966 !important;border-radius:18px !important;overflow:hidden}
#search-modal .modal-header {background:#172943 !important;border-bottom:1px solid #344966 !important}
#search-modal .modal-title,#search-modal .modal-body label,#search-modal .modal-header .close {color:#eef4ff !important}
#search-modal .form-control {background:#0b1728 !important;color:#edf4ff !important;border:1px solid #344966 !important;border-radius:9px !important;min-height:42px}
#search-modal .form-control::placeholder {color:#788ba8 !important}
#search-modal select option {background:#0b1728;color:#edf4ff}
@media(max-width:1100px){.summary-produksi-index .summary-cards{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:767px){.summary-produksi-index{padding:12px !important;border-radius:14px !important}.summary-produksi-index > .row.mb-4.no-print{padding:23px 19px !important}.summary-produksi-index > .row.mb-4.no-print:after{display:none}.summary-produksi-index .summary-title{font-size:24px !important}.summary-produksi-index .summary-cards{grid-template-columns:1fr;gap:12px}.summary-produksi-index .summary-card{min-height:125px !important}.summary-produksi-index .summary-value{font-size:29px !important}.summary-produksi-index .dashboard-panel{padding:17px !important}.summary-produksi-index .reporting-box{padding:14px !important}.summary-produksi-index .table-responsive{overflow-x:auto}}
@media print{.summary-produksi-index{background:#fff !important;color:#000 !important;padding:0 !important}.summary-produksi-index > .row.mb-4.no-print{display:none !important}.summary-produksi-index .card-box,.summary-produksi-index .dashboard-panel,.summary-produksi-index .premium-summary-card,.summary-produksi-index .reporting-box{background:#fff !important;color:#000 !important;box-shadow:none !important;border:1px solid #999 !important}.summary-produksi-index .summary-title,.summary-produksi-index .summary-value,.summary-produksi-index .header-title,.summary-produksi-index p,.summary-produksi-index strong,.summary-produksi-index span,.summary-produksi-index th,.summary-produksi-index td{color:#000 !important}.summary-produksi-index .reporting-table td,.summary-produksi-index .reporting-table th{background:#fff !important;color:#000 !important;border:1px solid #999 !important}}
</style>

