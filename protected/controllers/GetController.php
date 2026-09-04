<?php

namespace app\controllers;

use Yii;
use yii\web\UploadedFile;
use phpseclib3\Net\SFTP;
use app\components\SftpBankService;
use app\models\Partner;
use yii\web\Controller;
use yii\web\Response;
use app\models\Api;
use app\models\Batch;
use app\models\Billing;
use app\models\Member;
use app\models\Personal;
use app\models\Policy;
use app\models\QuotationCommission;
use app\models\QuotationTc;
use app\models\Utils;
use app\models\ProductRateType;
use app\models\QuotationRate;
use app\models\Quotation;
use app\models\QuotationProduct;
use app\models\Product;
use app\models\PeriodType;
use app\models\QuotationUwLimit;
use app\models\RateType;
use app\models\Signature;
use yii\web\NotFoundHttpException;

use Da\QrCode\QrCode;
use yii\helpers\Url;
use app\models\claim_bank_jatim;
use app\models\claim_bank_jatim_detail;
use app\models\dokument_claim_jatim;
use app\models\Dokumen_Medis;
use app\models\map_member_medis;
use app\models\map_member_cancel;
use app\models\MemberClaim;
use FPDF;
use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\claim_banding;
use app\models\User;
use app\models\Restitusi;
use app\models\claim_riau;

require_once __DIR__ . '/fpdf.php';

class GetController extends Controller
{
    public $enableCsrfValidation = false;
    protected $medicalCode = 'CAC';
    protected $createdBy = 1;
	
	const PICTURE_PATH = '/images/e_policy/';
	const PICTURE_PATH_Logo = '/images/img-Reliance-life.jpg';
	const PICTURE_PATH_Ttd = '/images/policy-qr.png';


    // }
	
	public function beforeAction($action)
	{
    $headers = Yii::$app->request->headers;

    $authorization = $headers->get('Authorization');

    if (empty($authorization)) {
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        Yii::$app->response->data = [
            'is_success' => 0,
            'message' => 'Authorization header tidak ditemukan'
        ];

        return false;
    }

    if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        Yii::$app->response->data = [
            'is_success' => 0,
            'message' => 'Format Authorization harus Bearer Token'
        ];

        return false;
    }

    $token = trim($matches[1]);

    if (!$this->validateToken($token)) {
        Yii::$app->response->statusCode = 401;
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        Yii::$app->response->data = [
            'is_success' => 0,
            'message' => 'Token tidak valid atau sudah expired'
        ];

        return false;
    }

    return parent::beforeAction($action);
	}
	
	
	private function base64UrlDecode($data)
	{
		$remainder = strlen($data) % 4;

		if ($remainder) {
			$data .= str_repeat('=', 4 - $remainder);
		}

		return base64_decode(
			strtr($data, '-_', '+/')
		);
	}
	
	
	private function validateToken($token)
	{
		$parts = explode('.', $token);

		if (count($parts) !== 3) {
			return false;
		}

		list($encodedHeader, $encodedPayload, $encodedSignature) = $parts;

		$secret = Yii::$app->params['jwtSecret'];

		$signature = hash_hmac(
			'sha256',
			$encodedHeader . '.' . $encodedPayload,
			$secret,
			true
		);

		$expectedSignature = rtrim(
			strtr(
				base64_encode($signature),
				'+/',
				'-_'
			),
			'='
		);

		if (!hash_equals($expectedSignature, $encodedSignature)) {
			return false;
		}

		$payloadJson = $this->base64UrlDecode($encodedPayload);

		if ($payloadJson === false) {
			return false;
		}

		$payload = json_decode($payloadJson, true);

		if (!is_array($payload)) {
			return false;
		}

		if (empty($payload['exp'])) {
			return false;
		}

		if (time() >= (int)$payload['exp']) {
			return false;
		}

		return true;
	}
	

	
	
	
	private function getBankToken()
	{
		$config = Yii::$app->params['bankApi'];

		$payload = [
			'client_id' => $config['client_id'],
			'client_secret' => $config['client_secret'],
			'username' => $config['username'],
			'password' => $config['password'],
			'grant_type' => $config['grant_type'],
		];

		$ch = curl_init($config['tokenUrl']);

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($payload),
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Accept: application/json',
			],
			CURLOPT_TIMEOUT => 60,
		]);

		$response = curl_exec($ch);

		$httpCode = curl_getinfo(
			$ch,
			CURLINFO_HTTP_CODE
		);

		$curlError = curl_error($ch);

		curl_close($ch);

		if ($response === false) {
			throw new \Exception(
				'Gagal koneksi Token Bank: ' .
				$curlError
			);
		}

		if ($httpCode < 200 || $httpCode >= 300) {
			throw new \Exception(
				'Generate token gagal. HTTP ' .
				$httpCode .
				' Response: ' .
				$response
			);
		}

		$result = json_decode(
			$response,
			true
		);

		if (!is_array($result)) {
			throw new \Exception(
				'Response token Bank tidak valid'
			);
		}

		/*
		 * Sesuaikan nama field ini dengan
		 * response sebenarnya dari Bank.
		 */
		if (isset($result['access_token'])) {
			return $result['access_token'];
		}

		if (isset($result['token'])) {
			return $result['token'];
		}

		throw new \Exception(
			'Token tidak ditemukan pada response Bank: ' .
			$response
		);
	}
	
	public function actionCallbackDocument()
	{
		Yii::$app->response->format =
			\yii\web\Response::FORMAT_JSON;

		$request = Yii::$app->request;

		$rawBody = $request->getRawBody();

		if (!$rawBody) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Request body kosong',
				]
			];
		}

		$payload = json_decode(
			$rawBody,
			true
		);

		if (!is_array($payload)) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Payload JSON tidak valid',
				]
			];
		}

		Yii::info(
			'Callback Document Bank: ' .
			json_encode($payload),
			'bank.callback'
		);

		/*
		 * Proses callback Bank di sini
		 */

		return [
			'Result' => [
				'status' => '200',
				'kode_response' => '00',
				'message' => 'Callback dokumen berhasil diterima',
			]
		];
	}
	
	
	private function getCbcFileFromSftp($fileName)
	{
		$config = Yii::$app->params['bankSftp'];

		$connection = ssh2_connect(
			$config['host'],
			$config['port']
		);

		if (!$connection) {
			throw new \Exception(
				'Gagal koneksi SFTP Bank'
			);
		}

		if (!ssh2_auth_password(
			$connection,
			$config['username'],
			$config['password']
		)) {
			throw new \Exception(
				'Login SFTP Bank gagal'
			);
		}

		$sftp = ssh2_sftp($connection);

		if (!$sftp) {
			throw new \Exception(
				'SFTP subsystem gagal'
			);
		}

		$remoteFile =
			'ssh2.sftp://' .
			intval($sftp) .
			'/' .
			trim($config['outgoing'], '/') .
			'/' .
			$fileName;

		$localDir =
			Yii::getAlias('@runtime/cbc');

		if (!is_dir($localDir)) {
			mkdir($localDir, 0777, true);
		}

		$localFile =
			$localDir .
			DIRECTORY_SEPARATOR .
			$fileName;

		if (!file_exists($remoteFile)) {
			throw new \Exception(
				'File CBC belum tersedia: ' .
				$fileName
			);
		}

		if (!copy($remoteFile, $localFile)) {
			throw new \Exception(
				'Gagal download file CBC'
			);
		}

		return $localFile;
	}
	
	

	public function actionTestSftp()
	{
		require Yii::getAlias('@app/sftp-lib/vendor/autoload.php');

		$sftp = new \phpseclib3\Net\SFTP(
			'202.152.22.234',
			22,
			30
		);

		if (!$sftp->login('reliance', 'reliance@brks2026')) {
			return $this->asJson([
				'status' => false,
				'message' => 'Login SFTP gagal'
			]);
		}

		if (!$sftp->chdir('/outgoing')) {
			return $this->asJson([
				'status' => false,
				'message' => 'Folder /outgoing tidak ditemukan'
			]);
		}

		$files = $sftp->nlist();

		return $this->asJson([
			'status' => true,
			'message' => 'SFTP berhasil',
			'files' => $files
		]);
	}


	private function downloadFileFromBankSftp($fileName)
	{
		$host = '202.152.22.234';
		$port = 22;
		$username = 'reliance';
		$password = 'reliance@brks2026';

		$remotePath = '/outgoing/' . $fileName;

		// Folder penyimpanan lokal
		$localDir = Yii::getAlias('@runtime/cbc');

		if (!is_dir($localDir)) {
			if (!mkdir($localDir, 0777, true)) {
				return [
					'success' => false,
					'message' => 'Gagal membuat folder penyimpanan dokumen CBC'
				];
			}
		}

		$localPath =
			$localDir .
			DIRECTORY_SEPARATOR .
			$fileName;

		try {

			// Pastikan extension SSH2 tersedia
			if (!function_exists('ssh2_connect')) {
				return [
					'success' => false,
					'message' => 'Extension PHP SSH2 tidak tersedia'
				];
			}

			// Connect ke SFTP Bank
			$connection = \ssh2_connect(
				$host,
				$port
			);

			if (!$connection) {
				return [
					'success' => false,
					'message' => 'Gagal koneksi ke SFTP Bank'
				];
			}

			// Authentication
			if (
				!\ssh2_auth_password(
					$connection,
					$username,
					$password
				)
			) {
				return [
					'success' => false,
					'message' => 'Gagal authentication SFTP Bank'
				];
			}

			// Buat SFTP resource
			$sftp = \ssh2_sftp($connection);

			if (!$sftp) {
				return [
					'success' => false,
					'message' => 'Gagal membuat koneksi SFTP'
				];
			}

			// Path file remote
			$remoteFile =
				'ssh2.sftp://' .
				intval($sftp) .
				$remotePath;

			// Cek file
			if (!file_exists($remoteFile)) {
				return [
					'success' => false,
					'message' =>
						'File belum tersedia di SFTP Bank: ' .
						$remotePath
				];
			}

			// Ambil isi file
			$content = file_get_contents($remoteFile);

			if ($content === false) {
				return [
					'success' => false,
					'message' =>
						'Gagal membaca file dari SFTP Bank'
				];
			}

			// Simpan file lokal
			$saved = file_put_contents(
				$localPath,
				$content
			);

			if ($saved === false) {
				return [
					'success' => false,
					'message' =>
						'Gagal menyimpan file CBC ke lokal'
				];
			}

			Yii::info(
				'File CBC berhasil didownload: ' .
				$remotePath .
				' -> ' .
				$localPath,
				'cbc-sftp'
			);

			return [
				'success' => true,
				'file_name' => $fileName,
				'remote_path' => $remotePath,
				'local_path' => $localPath,
				'size' => $saved
			];

		} catch (\Throwable $e) {

			Yii::error(
				'SFTP CBC Error: ' .
				$e->getMessage(),
				'cbc-sftp'
			);

			return [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
	}
	
	

	
	public function actionListOutgoingFiles()
	{
		Yii::$app->response->format =
			\yii\web\Response::FORMAT_JSON;

		$host = '202.152.22.234';
		$port = 22;
		$username = 'reliance';
		$password = 'reliance@brks2026';

		try {

			// =========================
			// CEK SSH2
			// =========================
			if (!function_exists('ssh2_connect')) {
				return [
					'success' => false,
					'step' => 'check_ssh2',
					'message' => 'Extension PHP SSH2 tidak tersedia'
				];
			}

			// =========================
			// CONNECT
			// =========================
			$connection = \ssh2_connect(
				$host,
				$port
			);

			if (!$connection) {
				return [
					'success' => false,
					'step' => 'connect',
					'message' => 'Gagal koneksi ke SFTP Bank'
				];
			}

			// =========================
			// LOGIN
			// =========================
			if (!\ssh2_auth_password(
				$connection,
				$username,
				$password
			)) {
				return [
					'success' => false,
					'step' => 'login',
					'message' => 'Gagal authentication SFTP Bank'
				];
			}

			// =========================
			// SFTP
			// =========================
			$sftp = \ssh2_sftp($connection);

			if (!$sftp) {
				return [
					'success' => false,
					'step' => 'sftp',
					'message' => 'Gagal membuat koneksi SFTP'
				];
			}

			// =========================
			// ROOT SFTP
			// =========================
			$rootPath =
				'ssh2.sftp://' .
				intval($sftp) .
				'/';

			// =========================
			// LIST ROOT
			// =========================
			$rootFiles = scandir($rootPath);

			if ($rootFiles === false) {
				return [
					'success' => false,
					'step' => 'list_root',
					'message' => 'Gagal membaca root SFTP',
					'root_path' => $rootPath
				];
			}

			$rootFiles = array_values(
				array_filter(
					$rootFiles,
					function ($file) {
						return $file !== '.' &&
							   $file !== '..';
					}
				)
			);

			// =========================
			// CEK OUTGOING
			// =========================
			$outgoingPath =
				'ssh2.sftp://' .
				intval($sftp) .
				'/outgoing';

			$outgoingExists = is_dir($outgoingPath);

			// =========================
			// JIKA OUTGOING ADA
			// =========================
			$outgoingFiles = [];

			if ($outgoingExists) {

				$outgoingFiles = scandir(
					$outgoingPath
				);

				if ($outgoingFiles === false) {
					$outgoingFiles = [];
				}

				$outgoingFiles = array_values(
					array_filter(
						$outgoingFiles,
						function ($file) {
							return $file !== '.' &&
								   $file !== '..';
						}
					)
				);
			}

			return [
				'success' => true,
				'message' => 'SFTP berhasil diakses',
				'root_path' => $rootPath,
				'root_files' => $rootFiles,
				'outgoing_path' => $outgoingPath,
				'outgoing_exists' => $outgoingExists,
				'total_outgoing_file' =>
					count($outgoingFiles),
				'outgoing_files' => $outgoingFiles
			];

		} catch (\Throwable $e) {

			Yii::error(
				'List SFTP Outgoing Error: ' .
				$e->getMessage(),
				'cbc-sftp'
			);

			return [
				'success' => false,
				'step' => 'exception',
				'message' => $e->getMessage()
			];
		}
	}


	
	public function actionCbc()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$request = Yii::$app->request->post();

		// ==============================
		// Ambil payload
		// ==============================
		$id_transaksi     = $request['id_transaksi'] ?? null;
		$id_pengajuan     = $request['id_pengajuan'] ?? null;
		$id_pengajuan_cbc = $request['id_pengajuan_cbc'] ?? null;
		$kode_broker      = $request['kode_broker'] ?? null;
		$ktp               = $request['ktp'] ?? null;
		$alasan_batal_cbc = $request['alasan_batal_cbc'] ?? null;
		if (
			empty($id_transaksi) ||
			empty($id_pengajuan) ||
			empty($id_pengajuan_cbc) ||
			empty($kode_broker) ||
			empty($ktp) ||
			empty($alasan_batal_cbc)
		) {

			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Parameter tidak lengkap'
				]
			];
		}

		$member = member::findOne([
			'id_loan' => $id_transaksi
		]);

		if (empty($member)) {

			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '200',
					'kode_response' => '89',
					'message' => 'Peserta tidak ditemukan'
				]
			];
		}
		
		
		$member_cancel = member::findOne([
			'id_loan' => $id_transaksi,
			'status' => 'cancel'
		]);
		
		// var_dump($member_cancel);

		if ($member_cancel != null) {

			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '200',
					'kode_response' => '25',
					'message' => 'CBC sudah disetujui - proses pembatalan CBC tidak dapat dilakukan'
				]
			];
		}
		
		
		$batch_status = Batch::findOne([
			'batch_no' => $member->batch_no,
			'policy_no' => $member->policy_no
		]);

		if (!empty($batch_status)) {

			$batch_status->status = 'Cancel';

			$batch_status->save(false);
		}

		$member->jenis_pembatalan = $id_pengajuan_cbc;
		$member->description = $alasan_batal_cbc;
		$member->status = 'Cancel';
		$member->member_status ='Cancel';
		$member->save(false);
		Yii::$app->response->statusCode = 200;

		return [
			'Result' => [
				'status' => '200',
				'kode_response' => '00',
				'message' => 'Pengajuan Pembatalan cbc Disetujui'
			]
		];
	}

	public function actionStatusPolis()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$request = Yii::$app->request;

		$authorization = $request->headers->get('Authorization');

		if (!$authorization) {
			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '401',
					'kode_response' => '01',
					'message' => 'Authorization header tidak ditemukan'
				]
			];
		}

		$data = json_decode($request->getRawBody(), true);

		if (!is_array($data)) {
			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Format JSON tidak valid'
				]
			];
		}

		$id_transaksi   = $data['id_transaksi'] ?? null;
		$kode_broker    = $data['kode_broker'] ?? null;
		$kode_cabang    = $data['kode_cabang'] ?? null;
		$nomor_akad     = $data['nomor_akad'] ?? null;
		$nomor_rekening = $data['nomor_rekening'] ?? null;

		if (
			$id_transaksi === null || $id_transaksi === '' ||
			$kode_broker === null || $kode_broker === '' ||
			$kode_cabang === null || $kode_cabang === '' ||
			$nomor_akad === null || trim($nomor_akad) === '' ||
			$nomor_rekening === null || $nomor_rekening === ''
		) {
			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Parameter tidak lengkap'
				]
			];
		}

		$member = member::findOne([
			'nomor_akad' =>$nomor_akad
		]);

		if ($member === null) {

			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '200',
					'kode_response' => '99',
					'message' => 'Polis dengan nomor akad dan nomor rekening tidak ditemukan',
					'nomor_rekening' => $nomor_rekening,
					'nomor_akad' => trim($nomor_akad)
				]
			];
		}

		$benefit  = $member->benefit ?? '';
		$coverage = $member->coverage ?? 0;

		$polisJiwa = [
			'no_polis'         => $member->policy_no ?? '',
			'asuransi'         => 'Reliance Pembiayaan Syariah',
			'periode_awal'     => $member->start_date ?? null,
			'periode_akhir'    => $member->end_date ?? null,
			'tenor'            => $member->term ?? 0,
			'tenor_berjalan'   => $member->tenor_berjalan ?? 0,
			'sisa_tenor'      => $member->sisa_tenor ?? 0,
			'nilai_penjaminan' => $member->total_si ?? 0,
			'jumlah_premi'     => $member->gross_premium ?? 0
		];

		Yii::$app->response->statusCode = 200;

		return [
			'Result' => [
				'status' => '200',
				'kode_response' => '00',
				'message' => 'Berhasil kirim pengajuan cek polis',
				'nomor_rekening' => $nomor_rekening,
				'nomor_akad' => trim($nomor_akad),
				'benefit' => $benefit,
				'coverage' => $coverage,
				'polis_jiwa' => $polisJiwa
			]
		];
	}
	
	public function actionStatusKlaim()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$request = Yii::$app->request;

		$authorization = $request->headers->get('Authorization');

		if (empty($authorization)) {
			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '401',
					'kode_response' => '01',
					'message' => 'Authorization header tidak ditemukan'
				]
			];
		}

		$rawBody = $request->getRawBody();

		$data = json_decode($rawBody, true);

		if (!is_array($data)) {
			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Format JSON tidak valid'
				]
			];
		}

		$id_transaksi       = $data['id_transaksi'] ?? null;
		$id_pengajuan_klaim_riau = $data['id_pengajuan_klaim'] ?? null;
		$kode_broker        = $data['kode_broker'] ?? null;
		$benefit            = $data['benefit'] ?? null;
		$nomor_rekening     = $data['nomor_rekening'] ?? null;

		if (
			empty($id_transaksi) ||
			empty($id_pengajuan_klaim_riau) ||
			empty($kode_broker) ||
			empty($benefit) ||
			empty($nomor_rekening)
		) {
			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Parameter tidak lengkap'
				]
			];
		}

		$member = claim_riau::find()
			->where([
				'id_pengajuan_klaim_riau' => $id_pengajuan_klaim_riau,
				'nomor_rekening'     => $nomor_rekening,
				'benefit'            => $benefit
			])
			->one();

		if ($member === null) {

			Yii::$app->response->statusCode = 200;

			return [
				'Result' => [
					'status' => '200',
					'kode_response' => '99',
					'message' => 'Data klaim tidak ditemukan',
					'id_transaksi' => $id_transaksi,
					'id_pengajuan' => $id_pengajuan_klaim_riau,
					'nomor_rekening' => $nomor_rekening
				]
			];
		}

		$statusKlaim = $member->status_claim ?? 0;
		$statusBayar = $member->status_bayar ?? 0;

		$klaimDibayarkan = $member->klaim_dibayarkan ?? 0;

		Yii::$app->response->statusCode = 200;

		return [
			'Result' => [
				'status'            => '200',
				'kode_response'     => '00',
				'message'           => 'Berhasil kirim data cek status klaim',
				'id_transaksi_bank' => $member->id_transaksi_bank ?? $id_transaksi,
				'id_pengajuan'      => $member->id_pengajuan_klaim_riau ?? $id_pengajuan_klaim_riau,
				'status_klaim'      => (string) $statusKlaim,
				'status_bayar'      => (string) $statusBayar,
				'klaim_dibayarkan'  => $klaimDibayarkan,
				'asuransi'          => 'Reliance Pembiayaan Syariah',
				'keterangan'        => $member->keterangan ?? '-'
			]
		];
	}

}
