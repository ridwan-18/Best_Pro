
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Summary Produksi Konvensional - ' . Yii::$app->name;

$request = Yii::$app->request;


$totalPengajuan = (int) ($summary['total_pengajuan'] ?? 0);
$disetujui = (int) ($summary['Inforce'] ?? 0);
$pending = (int) ($summary['pending'] ?? 0);
$Maturity = (int) ($summary['Maturity'] ?? 0);
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
                        Filter Summary Produksi Konvensional
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

                        <div class="col-md-12">
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
						
						
						<div class="col-md-12">
							<div class="form-group">

								<?= Html::label(
									'Status Kepesertaan',
									'status'
								) ?>

								<?= Html::checkboxList(
									'status',
									$filters['status'],
									[
										'Pending' => 'Pending',
										'Inforce' => 'Inforce',
										'Cancel' => 'Cancel',
										'Maturity' => 'Maturity',
									],
									[
										'separator' => '&nbsp;&nbsp;&nbsp;',
										'itemOptions' => [
											'class' => 'status-checkbox',
										],
									]
								) ?>

							</div>
						</div>

                        <div class="col-md-12">
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

                        <div class="col-md-12">
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

                        <div class="col-md-12">
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

                        <div class="col-md-12">
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

        <div class="col-md-12">

            <h2 class="summary-title">
                <i class="fa fa-bar-chart"></i>
                Summary Produksi Konvensional
            </h2>

            <p class="summary-subtitle">
                Laporan ringkasan produksi dan premi asuransi.
            </p>

        </div>

        <div class="col-md-12 text-right">

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

        <h4>ASURANSI JIWA </h4>

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



        <div class="col-lg-3 col-md-12">

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


        <div class="col-lg-3 col-md-12">

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
		
		<div class="col-lg-3 col-md-12">

            <div class="card-box summary-card">

                <div class="row align-items-center">

                    <div class="col-8">

                        <p class="summary-label">
                            Maturity
                        </p>

                        <h3 class="summary-value">
                            <?= $formatNumber($Maturity) ?>
                        </h3>

                    </div>

                    <div class="col-4 text-right">

                        <i class="fa fa-clock-o summary-icon"></i>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-lg-3 col-md-12">

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



        <div class="col-lg-3 col-md-12">

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
	$persenMaturity = min(100, round(($Maturity / $statusTotal) * 100, 1));

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
						 style="--inforce: <?= $persenInforce ?>;
								--maturity: <?= $persenMaturity ?>;
								--pending: <?= $persenPending ?>;
								--cancel: <?= $persenCancel ?>;">

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
							<span class="legend-dot dot-maturity"></span>
							<span>Maturity</span>
							<strong><?= $formatNumber($Maturity) ?></strong>
							<small><?= $persenMaturity ?>%</small>
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

				<p class="dashboard-chart-note">
					Persentase dihitung dari Total Pengajuan.
				</p>
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



    <?php
    // Data ini harus dikirim dari actionIndex() controller sebagai $monthlySummary.
    // Format tiap baris: bulan, total_pengajuan, Inforce, pending, cancel,
    // total_gross_premium, extra_premium, total_net_premium.
    $monthlyRows = isset($monthlySummary) && is_array($monthlySummary)
        ? $monthlySummary
        : [];

    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $monthlyByNumber = [];
    foreach ($monthlyRows as $row) {
        $bulan = (int) ($row['bulan'] ?? 0);
        if ($bulan >= 1 && $bulan <= 12) {
            $monthlyByNumber[$bulan] = $row;
        }
    }

    $monthlyMaxPengajuan = 1;
    $monthlyMaxPremium = 1;
    for ($i = 1; $i <= 12; $i++) {
        $r = $monthlyByNumber[$i] ?? [];
        $monthlyMaxPengajuan = max($monthlyMaxPengajuan, (int) ($r['total_pengajuan'] ?? 0));
        $monthlyMaxPremium = max(
            $monthlyMaxPremium,
            (float) ($r['total_gross_premium'] ?? 0),
            (float) ($r['extra_premium'] ?? 0),
            (float) ($r['total_net_premium'] ?? 0)
        );
    }
    $tahunDashboard = (int) $request->get('tahun', date('Y'));
    ?>

    <div class="monthly-dashboard card-box">
        <div class="monthly-dashboard-header">
            <div>
                <h4 class="header-title"><i class="fa fa-calendar"></i> Dashboard Produksi Per Bulan</h4>
                <p class="monthly-subtitle">Tren pengajuan dan premi selama satu tahun.</p>
            </div>
            <?= Html::beginForm(['index'], 'get', ['class' => 'monthly-year-filter no-print']) ?>
                <?php
                // Pertahankan filter yang sedang dipakai pada halaman.
                foreach ($filters as $key => $value) {
                    if ($value !== '') {
                        echo Html::hiddenInput($key, $value);
                    }
                }
                ?>
                <label for="tahun-dashboard">Tahun</label>
                <?= Html::dropDownList(
                    'tahun',
                    $tahunDashboard,
                    array_combine(
                        range((int) date('Y') - 5, (int) date('Y') + 1),
                        range((int) date('Y') - 5, (int) date('Y') + 1)
                    ),
                    ['class' => 'form-control', 'id' => 'tahun-dashboard', 'onchange' => 'this.form.submit()']
                ) ?>
            <?= Html::endForm() ?>
        </div>

        <div class="monthly-chart">
           <?php
// Menyiapkan data grafik dari monthlySummary
			$chartLabels = [];
			$chartGross = [];
			$chartExtra = [];
			$chartNet = [];

			for ($i = 1; $i <= 12; $i++) {
				$r = $monthlyByNumber[$i] ?? [];

				$chartLabels[] = substr($namaBulan[$i], 0, 3);

				$chartGross[] = (float) ($r['total_gross_premium'] ?? 0);
				$chartExtra[] = (float) ($r['extra_premium'] ?? 0);
				$chartNet[] = (float) ($r['total_net_premium'] ?? 0);
			}

			$chartLabelsJson = json_encode($chartLabels);
			$chartGrossJson = json_encode($chartGross);
			$chartExtraJson = json_encode($chartExtra);
			$chartNetJson = json_encode($chartNet);
			?>

			<div class="monthly-chart premium-trend-panel">

				<div class="premium-trend-legend">
					<span><i class="trend-dot gross"></i> Gross Premium</span>
					<span><i class="trend-dot extra"></i> Extra Premium</span>
					<span><i class="trend-dot net"></i> Nett Premium</span>
				</div>

				<div class="premium-trend-canvas">
					<canvas id="premiumTrendChart"></canvas>
				</div>

				<p class="monthly-chart-footnote">
					Grafik perkembangan Gross, Extra, dan Nett Premium
					berdasarkan bulan pada tahun <?= $tahunDashboard ?>.
				</p>

			</div>

			<?php
			$this->registerJsFile(
				'https://cdn.jsdelivr.net/npm/chart.js',
				['position' => \yii\web\View::POS_END]
			);

			$js = <<<JS

			(function () {

				var canvas = document.getElementById('premiumTrendChart');

				if (!canvas) {
					return;
				}

				var labels = $chartLabelsJson;

				var grossData = $chartGrossJson;
				var extraData = $chartExtraJson;
				var netData = $chartNetJson;

				if (typeof Chart === 'undefined') {
					console.error('Chart.js belum berhasil dimuat.');
					return;
				}

				var oldChart = Chart.getChart(canvas);

				if (oldChart) {
					oldChart.destroy();
				}

				var ctx = canvas.getContext('2d');

				function formatRupiah(value) {
					return 'Rp ' + new Intl.NumberFormat('id-ID', {
						maximumFractionDigits: 0
					}).format(value);
				}

				new Chart(ctx, {

					type: 'line',

					data: {
						labels: labels,

						datasets: [

							{
								label: 'Gross Premium',
								data: grossData,

								borderColor: '#4285F4',
								backgroundColor: 'rgba(66,133,244,0.10)',

								borderWidth: 3,
								pointRadius: 4,
								pointHoverRadius: 7,

								pointBackgroundColor: '#4285F4',
								tension: 0.35,
								fill: false
							},

							{
								label: 'Extra Premium',
								data: extraData,

								borderColor: '#9B59B6',
								backgroundColor: 'rgba(155,89,182,0.10)',

								borderWidth: 3,
								pointRadius: 4,
								pointHoverRadius: 7,

								pointBackgroundColor: '#9B59B6',
								tension: 0.35,
								fill: false
							},

							{
								label: 'Nett Premium',
								data: netData,

								borderColor: '#20A779',
								backgroundColor: 'rgba(32,167,121,0.10)',

								borderWidth: 3,
								pointRadius: 4,
								pointHoverRadius: 7,

								pointBackgroundColor: '#20A779',
								tension: 0.35,
								fill: false
							}

						]
					},

					options: {

						responsive: true,
						maintainAspectRatio: false,

						animation: {
						duration: 1800,
						easing: 'easeInOutQuart'
						},

						plugins: {

							legend: {
								display: false
							},

							tooltip: {
								backgroundColor: '#172d49',
								padding: 12,

								callbacks: {
									label: function(context) {
										return context.dataset.label + ': ' +
											formatRupiah(context.parsed.y);
									}
								}
							}
						},

						scales: {

							x: {
								grid: {
									display: false
								},

								ticks: {
									color: '#68788d'
								}
							},

							y: {
								beginAtZero: true,

								grid: {
									color: '#edf1f6'
								},

								ticks: {
									color: '#68788d',

									callback: function(value) {
										return new Intl.NumberFormat('id-ID', {
											notation: 'compact',
											maximumFractionDigits: 1
										}).format(value);
									}
								},

								title: {
									display: true,
									text: 'Nilai Premi (Rupiah)',
									color: '#68788d'
								}
							}

						}
					}

				});

			})();

			JS;

			$this->registerJs($js, \yii\web\View::POS_READY);
			?>
        </div>

        <div class="table-responsive monthly-table-wrap">
            <table class="table table-bordered monthly-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th class="text-right">Pengajuan</th>
                        <th class="text-right">Inforce</th>
						<th class="text-right">Maturity</th>
                        <th class="text-right">Pending</th>
                        <th class="text-right">Cancel</th>
                        <th class="text-right">Gross Premium</th>
                        <th class="text-right">Extra Premium</th>
                        <th class="text-right">Nett Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $monthlyTotals = [
                        'total_pengajuan' => 0, 'Inforce' => 0, 'pending' => 0, 'Maturity' => 0,
                        'cancel' => 0, 'total_gross_premium' => 0,
                        'extra_premium' => 0, 'total_net_premium' => 0,
                    ];
                    for ($i = 1; $i <= 12; $i++):
                        $r = $monthlyByNumber[$i] ?? [];
                        foreach ($monthlyTotals as $key => $unused) {
                            $monthlyTotals[$key] += (float) ($r[$key] ?? 0);
                        }
                    ?>
                        <tr>
                            <td><?= Html::encode($namaBulan[$i]) ?></td>
                            <td class="text-right"><?= $formatNumber($r['total_pengajuan'] ?? 0) ?></td>
                            <td class="text-right"><?= $formatNumber($r['Inforce'] ?? 0) ?></td>
							 <td class="text-right"><?= $formatNumber($r['Maturity'] ?? 0) ?></td>
                            <td class="text-right"><?= $formatNumber($r['pending'] ?? 0) ?></td>
                            <td class="text-right"><?= $formatNumber($r['cancel'] ?? 0) ?></td>
                            <td class="text-right"><?= $formatRupiah($r['total_gross_premium'] ?? 0) ?></td>
                            <td class="text-right"><?= $formatRupiah($r['extra_premium'] ?? 0) ?></td>
                            <td class="text-right"><?= $formatRupiah($r['total_net_premium'] ?? 0) ?></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-right"><?= $formatNumber($monthlyTotals['total_pengajuan']) ?></th>
                        <th class="text-right"><?= $formatNumber($monthlyTotals['Inforce']) ?></th>
						 <th class="text-right"><?= $formatNumber($monthlyTotals['Maturity']) ?></th>
                        <th class="text-right"><?= $formatNumber($monthlyTotals['pending']) ?></th>
                        <th class="text-right"><?= $formatNumber($monthlyTotals['cancel']) ?></th>
                        <th class="text-right"><?= $formatRupiah($monthlyTotals['total_gross_premium']) ?></th>
                        <th class="text-right"><?= $formatRupiah($monthlyTotals['extra_premium']) ?></th>
                        <th class="text-right"><?= $formatRupiah($monthlyTotals['total_net_premium']) ?></th>
                    </tr>
                </tfoot>
            </table>
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
								 <th class="text-right">Maturity</th>
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
                                    <?= $formatNumber($Maturity) ?>
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

/* Dashboard produksi bulanan */
.summary-produksi-index .monthly-dashboard {
    padding: 24px;
    margin: 8px 0 24px;
    background: #fff !important;
    border: 1px solid #e3eaf2 !important;
    border-radius: 14px !important;
    box-shadow: 0 4px 16px rgba(30, 55, 90, .055) !important;
}
.summary-produksi-index .monthly-dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 22px;
}
.summary-produksi-index .monthly-dashboard-header .header-title {
    margin: 0 0 7px;
    color: #1c3554 !important;
}
.summary-produksi-index .monthly-subtitle,
.summary-produksi-index .monthly-chart-footnote {
    color: #78879a !important;
    font-size: 12px;
    margin: 0;
}
.summary-produksi-index .monthly-year-filter {
    display: flex;
    align-items: center;
    gap: 10px;
}
.summary-produksi-index .monthly-year-filter label {
    color: #344b66 !important;
    margin: 0;
}
.summary-produksi-index .monthly-year-filter .form-control {
    min-width: 105px;
    border: 1px solid #d7e0ea;
    border-radius: 8px;
    color: #243247;
    background: #fff;
}
.summary-produksi-index .monthly-chart {
    padding: 18px 18px 14px;
    border: 1px solid #e8edf3;
    border-radius: 12px;
    background: #fbfcfe;
    margin-bottom: 22px;
}
.summary-produksi-index .monthly-chart-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    margin-bottom: 20px;
    font-size: 12px;
}
.summary-produksi-index .monthly-chart-legend span {
    display: inline-flex;
    align-items: center;
    gap: 7px;
}
.summary-produksi-index .monthly-legend-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 3px;
}
.summary-produksi-index .monthly-count-dot { background: #3977b9; }
.summary-produksi-index .monthly-gross-dot { background: #a78bfa; }
.summary-produksi-index .monthly-net-dot { background: #25b887; }
.summary-produksi-index .monthly-bars {
    display: grid;
    grid-template-columns: repeat(12, minmax(30px, 1fr));
    gap: 10px;
    align-items: end;
    min-width: 540px;
}
.summary-produksi-index .monthly-bar-column {
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.summary-produksi-index .monthly-bar-values {
    color: #344b66;
    font-size: 10px;
    margin-bottom: 5px;
    min-height: 14px;
}
.summary-produksi-index .monthly-bar-area {
    height: 170px;
    width: 100%;
    display: flex;
    align-items: end;
    justify-content: center;
    gap: 3px;
    border-bottom: 1px solid #dfe7f0;
    background: repeating-linear-gradient(to top, transparent 0, transparent 41px, #e9eef5 42px, transparent 43px);
}
.summary-produksi-index .monthly-bar {
    width: 23%;
    max-width: 15px;
    min-height: 2px;
    border-radius: 4px 4px 0 0;
    transition: height .35s ease;
}
.summary-produksi-index .monthly-count-bar { background: #3977b9; }
.summary-produksi-index .monthly-gross-bar { background: #a78bfa; }
.summary-produksi-index .monthly-net-bar { background: #25b887; }
.summary-produksi-index .monthly-month-label {
    color: #52647a !important;
    font-size: 11px;
    margin-top: 8px;
}
.summary-produksi-index .monthly-chart-footnote { margin-top: 14px; }
.summary-produksi-index .monthly-table {
    margin-bottom: 0;
    border: 1px solid #e0e7ef;
}
.summary-produksi-index .monthly-table th {
    background: #f0f5fa !important;
    color: #344b66 !important;
    font-size: 11px;
    white-space: nowrap;
}
.summary-produksi-index .monthly-table td {
    color: #34445a !important;
    font-size: 12px;
    white-space: nowrap;
}
.summary-produksi-index .monthly-table th,
.summary-produksi-index .monthly-table td {
    border: 1px solid #e0e7ef !important;
    padding: 10px 9px;
}
.summary-produksi-index .monthly-table tfoot th {
    font-weight: 750;
    background: #eaf1f8 !important;
}
@media print {
    .summary-produksi-index .monthly-year-filter { display: none !important; }
    .summary-produksi-index .monthly-chart,
    .summary-produksi-index .monthly-dashboard { break-inside: avoid; }
}
@media screen and (max-width: 767px) {
    .summary-produksi-index .monthly-dashboard { padding: 15px; }
    .summary-produksi-index .monthly-chart { overflow-x: auto; }
    .summary-produksi-index .monthly-dashboard-header { flex-direction: column; }
}

.dot-maturity {
    background: #6c757d;
}

/* Donut chart dengan 4 status */
.status-donut {
    background: conic-gradient(
        #28a745 0 calc(var(--inforce) * 1%),
        #6c757d calc(var(--inforce) * 1%) calc((var(--inforce) + var(--maturity)) * 1%),
        #f0ad4e calc((var(--inforce) + var(--maturity)) * 1%) calc((var(--inforce) + var(--maturity) + var(--pending)) * 1%),
        #dc3545 calc((var(--inforce) + var(--maturity) + var(--pending)) * 1%) 100%
    );
}


/* Grafik tren premi */
.summary-produksi-index .premium-trend-panel {
    width: 100%;
    padding: 20px 10px 5px;
}

.summary-produksi-index .premium-trend-canvas {
    position: relative;
    width: 100%;
    height: 350px;
}

.summary-produksi-index .premium-trend-legend {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 25px;
    margin-bottom: 25px;
}

.summary-produksi-index .premium-trend-legend span {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 600;
}

.summary-produksi-index .trend-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    display: inline-block;
}

.summary-produksi-index .trend-dot.gross {
    background: #4285F4;
}

.summary-produksi-index .trend-dot.extra {
    background: #9B59B6;
}

.summary-produksi-index .trend-dot.net {
    background: #20A779;
}

@media (max-width: 767px) {
    .summary-produksi-index .premium-trend-canvas {
        height: 280px;
    }

    .summary-produksi-index .premium-trend-legend {
        justify-content: flex-start;
        gap: 12px;
    }
}

/* =====================================================
   EXECUTIVE DASHBOARD ANIMATION
===================================================== */

.summary-produksi-index .summary-card,
.summary-produksi-index .dashboard-panel,
.summary-produksi-index .premium-summary-card,
.summary-produksi-index .monthly-dashboard,
.summary-produksi-index .reporting-box {
    will-change: opacity, transform;
}

/* Efek hover kartu KPI */
.summary-produksi-index .summary-card {
    transition:
        transform .35s ease,
        box-shadow .35s ease,
        border-color .35s ease;
}

.summary-produksi-index .summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(28, 53, 84, .12) !important;
    border-color: #c8d9ec !important;
}

/* Efek hover panel */
.summary-produksi-index .dashboard-panel,
.summary-produksi-index .monthly-dashboard,
.summary-produksi-index .reporting-box {
    transition:
        transform .35s ease,
        box-shadow .35s ease;
}

.summary-produksi-index .dashboard-panel:hover,
.summary-produksi-index .monthly-dashboard:hover,
.summary-produksi-index .reporting-box:hover {
    box-shadow: 0 10px 28px rgba(28, 53, 84, .09) !important;
}

/* Kartu premi */
.summary-produksi-index .premium-summary-card {
    transition:
        transform .35s ease,
        box-shadow .35s ease;
}

.summary-produksi-index .premium-summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(28, 53, 84, .10) !important;
}

/* Ikon kartu */
.summary-produksi-index .summary-icon,
.summary-produksi-index .premium-summary-icon {
    transition: transform .35s ease;
}

.summary-produksi-index .summary-card:hover .summary-icon,
.summary-produksi-index .premium-summary-card:hover .premium-summary-icon {
    transform: scale(1.10);
}

/* Donut */
.summary-produksi-index .status-donut {
    transition: transform .5s ease;
}

.summary-produksi-index .status-donut:hover {
    transform: scale(1.04);
}

/* Batang premi */
.summary-produksi-index .premium-bar {
    transition: width 1.4s cubic-bezier(.22, 1, .36, 1);
}

/* Batang grafik bulanan */
.summary-produksi-index .monthly-bar {
    transition: height 1.2s cubic-bezier(.22, 1, .36, 1);
}

/* Baris tabel */
.summary-produksi-index .monthly-table tbody tr,
.summary-produksi-index .reporting-table tbody tr {
    transition: background-color .25s ease;
}

/* Tombol */
.summary-produksi-index .btn {
    transition:
        transform .25s ease,
        box-shadow .25s ease,
        background-color .25s ease;
}

.summary-produksi-index .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 14px rgba(28, 53, 84, .12);
}

/* Animasi saat halaman sedang dimuat */
.summary-produksi-index.dashboard-animate-ready
.summary-card,
.summary-produksi-index.dashboard-animate-ready
.dashboard-panel,
.summary-produksi-index.dashboard-animate-ready
.premium-summary-card,
.summary-produksi-index.dashboard-animate-ready
.monthly-dashboard,
.summary-produksi-index.dashboard-animate-ready
.reporting-box {
    opacity: 0;
    transform: translateY(18px);
}

/* Aksesibilitas: matikan gerakan jika diminta sistem */
@media (prefers-reduced-motion: reduce) {
    .summary-produksi-index *,
    .summary-produksi-index *::before,
    .summary-produksi-index *::after {
        animation: none !important;
        transition: none !important;
    }
}

</style>

<?php

$dashboardAnimationJs = <<<'JS'

(function () {

    'use strict';

    var root = document.querySelector('.summary-produksi-index');

    if (!root) {
        return;
    }

    // Hindari animasi dijalankan dua kali.
    if (root.getAttribute('data-animation-initialized') === '1') {
        return;
    }

    root.setAttribute('data-animation-initialized', '1');
    root.classList.add('dashboard-animate-ready');

    var reduceMotion = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // =================================================
    // 1. ANIMASI PANEL DASHBOARD
    // =================================================

    var revealElements = root.querySelectorAll(
        '.summary-card, ' +
        '.dashboard-panel, ' +
        '.premium-summary-card, ' +
        '.monthly-dashboard, ' +
        '.reporting-box'
    );

    revealElements.forEach(function (element, index) {

        if (reduceMotion) {
            element.style.opacity = 1;
            element.style.transform = 'none';
            return;
        }

        element.style.transition =
            'opacity 700ms ease, transform 700ms cubic-bezier(.22,1,.36,1)';

        setTimeout(function () {

            element.style.opacity = 1;
            element.style.transform = 'translateY(0)';

        }, 100 + (index * 90));

    });


    // =================================================
    // 2. ANIMASI ANGKA KPI DAN TOTAL PREMI
    // =================================================

    function animateNumber(element, delay) {

        var originalText = element.textContent.trim();

        var match = originalText.match(/[0-9][0-9.,]*/);

        if (!match) {
            return;
        }

        var target = parseInt(
            match[0].replace(/[^0-9]/g, ''),
            10
        );

        if (isNaN(target)) {
            return;
        }

        var prefix = originalText.substring(0, match.index);
        var suffix = originalText.substring(match.index + match[0].length);

        if (reduceMotion) {
            return;
        }

        var duration = 1400;
        var startTime = null;

        function update(timestamp) {

            if (!startTime) {
                startTime = timestamp;
            }

            var progress = Math.min(
                (timestamp - startTime) / duration,
                1
            );

            // Efek easing agar angka bergerak halus.
            var eased = 1 - Math.pow(1 - progress, 3);

            var current = Math.floor(target * eased);

            element.textContent =
                prefix +
                current.toLocaleString('id-ID') +
                suffix;

            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent =
                    prefix +
                    target.toLocaleString('id-ID') +
                    suffix;
            }

        }

        setTimeout(function () {
            requestAnimationFrame(update);
        }, delay || 0);

    }

    var numberElements = root.querySelectorAll(
        '.summary-value, .premium-summary-content strong'
    );

    numberElements.forEach(function (element, index) {
        animateNumber(element, index * 100);
    });


    // =================================================
    // 3. ANIMASI DONUT STATUS
    // =================================================

    var donut = root.querySelector('.status-donut');

    if (donut && !reduceMotion) {

        var statusVariables = [
            '--inforce',
            '--maturity',
            '--pending',
            '--cancel'
        ];

        var statusTargets = {};

        statusVariables.forEach(function (variable) {

            statusTargets[variable] = parseFloat(
                donut.style.getPropertyValue(variable)
            ) || 0;

            donut.style.setProperty(variable, '0');

        });

        var donutStart = null;
        var donutDuration = 1400;

        function animateDonut(timestamp) {

            if (!donutStart) {
                donutStart = timestamp;
            }

            var progress = Math.min(
                (timestamp - donutStart) / donutDuration,
                1
            );

            var eased = 1 - Math.pow(1 - progress, 3);

            statusVariables.forEach(function (variable) {

                var value = statusTargets[variable] * eased;

                donut.style.setProperty(
                    variable,
                    value.toFixed(2)
                );

            });

            if (progress < 1) {
                requestAnimationFrame(animateDonut);
            } else {

                statusVariables.forEach(function (variable) {
                    donut.style.setProperty(
                        variable,
                        statusTargets[variable]
                    );
                });

            }

        }

        setTimeout(function () {
            requestAnimationFrame(animateDonut);
        }, 250);

    }


    // =================================================
    // 4. ANIMASI BATANG PERBANDINGAN PREMI
    // =================================================

    var premiumBars = root.querySelectorAll('.premium-bar');

    premiumBars.forEach(function (bar, index) {

        var targetWidth = bar.style.width;

        if (reduceMotion) {
            return;
        }

        bar.style.width = '0%';

        setTimeout(function () {
            bar.style.width = targetWidth;
        }, 350 + (index * 180));

    });


    // =================================================
    // 5. ANIMASI BATANG PRODUKSI BULANAN
    // =================================================

    var monthlyBars = root.querySelectorAll('.monthly-bar');

    monthlyBars.forEach(function (bar, index) {

        var targetHeight = bar.style.height;

        if (reduceMotion) {
            return;
        }

        bar.style.height = '0%';

        setTimeout(function () {
            bar.style.height = targetHeight;
        }, 500 + (index * 35));

    });


    // =================================================
    // 6. ANIMASI GRAFIK CHART.JS JIKA TERSEDIA
    // =================================================

    if (window.premiumTrendChart && !reduceMotion) {

        window.premiumTrendChart.options.animation = {
            duration: 1600,
            easing: 'easeInOutQuart'
        };

        window.premiumTrendChart.update();

    }

})();

JS;

$this->registerJs(
    $dashboardAnimationJs,
    \yii\web\View::POS_READY,
    'executive-dashboard-animation'
);

?>
