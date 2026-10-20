
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Summary Produksi - ' . Yii::$app->name;

$request = Yii::$app->request;

$totalPengajuan = (int) ($summary['total_pengajuan'] ?? 0);
$disetujui = (int) ($summary['disetujui'] ?? 0);
$pending = (int) ($summary['pending'] ?? 0);
$ditolak = (int) ($summary['ditolak'] ?? 0);

$totalGrossPremium = (float) ($summary['total_gross_premium'] ?? 0);
$totalNetPremium = (float) ($summary['total_net_premium'] ?? 0);

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

    <!-- Modal Search -->
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
                        <i class="fa fa-search"></i> Filter Summary Produksi
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
                                <?= Html::label('Nama Peserta', 'nama') ?>

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
                                <?= Html::label('Batch Number', 'batch_no') ?>

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
                                <?= Html::label('Tanggal Awal', 'start_date') ?>

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
                                <?= Html::label('Tanggal Akhir', 'end_date') ?>

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
                                <?= Html::label('Cabang', 'nama_kantor') ?>

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


    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="m-0">Summary Produksi</h2>
            <p class="text-muted mt-1">
                Ringkasan produksi peserta asuransi
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


    <!-- Kartu Summary -->
    <div class="row">

        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <div class="row align-items-center">
                    <div class="col-8">
                        <p class="text-muted mb-1">Total Pengajuan</p>
                        <h3 class="mb-0">
                            <?= $formatNumber($totalPengajuan) ?>
                        </h3>
                    </div>
                    <div class="col-4 text-right">
                        <i class="fa fa-file-text-o text-primary"
                           style="font-size:32px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <div class="row align-items-center">
                    <div class="col-8">
                        <p class="text-muted mb-1">Disetujui</p>
                        <h3 class="mb-0 text-success">
                            <?= $formatNumber($disetujui) ?>
                        </h3>
                    </div>
                    <div class="col-4 text-right">
                        <i class="fa fa-check-circle text-success"
                           style="font-size:32px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <div class="row align-items-center">
                    <div class="col-8">
                        <p class="text-muted mb-1">Pending</p>
                        <h3 class="mb-0 text-warning">
                            <?= $formatNumber($pending) ?>
                        </h3>
                    </div>
                    <div class="col-4 text-right">
                        <i class="fa fa-clock-o text-warning"
                           style="font-size:32px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-box">
                <div class="row align-items-center">
                    <div class="col-8">
                        <p class="text-muted mb-1">Ditolak</p>
                        <h3 class="mb-0 text-danger">
                            <?= $formatNumber($ditolak) ?>
                        </h3>
                    </div>
                    <div class="col-4 text-right">
                        <i class="fa fa-times-circle text-danger"
                           style="font-size:32px;"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- Tabel Summary -->
    <div class="row mt-3">
        <div class="col-12">

            <div class="card-box">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <h4 class="header-title">
                            Ringkasan Produksi dan Premi
                        </h4>

                        <p class="text-muted mb-0">
                            Data berdasarkan filter yang dipilih.
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered nowrap m-0">

                        <thead class="thead-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-right">Total Pengajuan</th>
                                <th class="text-right">Disetujui</th>
                                <th class="text-right">Pending</th>
                                <th class="text-right">Ditolak</th>
                                <th class="text-right">Total Gross Premium</th>
                                <th class="text-right">Total Net Premium</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class="text-center">1</td>

                                <td class="text-right">
                                    <?= $formatNumber($totalPengajuan) ?>
                                </td>

                                <td class="text-right text-success">
                                    <strong>
                                        <?= $formatNumber($disetujui) ?>
                                    </strong>
                                </td>

                                <td class="text-right text-warning">
                                    <strong>
                                        <?= $formatNumber($pending) ?>
                                    </strong>
                                </td>

                                <td class="text-right text-danger">
                                    <strong>
                                        <?= $formatNumber($ditolak) ?>
                                    </strong>
                                </td>

                                <td class="text-right">
                                    <?= $formatRupiah($totalGrossPremium) ?>
                                </td>

                                <td class="text-right">
                                    <?= $formatRupiah($totalNetPremium) ?>
                                </td>

                                <td class="text-center">
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
                                    Total Premi
                                </th>

                                <th class="text-right">
                                    <?= $formatRupiah($totalGrossPremium) ?>
                                </th>

                                <th class="text-right">
                                    <?= $formatRupiah($totalNetPremium) ?>
                                </th>

                                <th></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

            </div>

        </div>
    </div>

</div>
