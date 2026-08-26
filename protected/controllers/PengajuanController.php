<?php

namespace app\controllers;

use Yii;

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
use yii\web\UploadedFile;
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

class PengajuanController extends Controller
{
    public $enableCsrfValidation = false;
    protected $medicalCode = 'CAC';
    protected $createdBy = 1;
	
	const PICTURE_PATH = '/images/e_policy/';
	const PICTURE_PATH_Logo = '/images/img-Reliance-life.jpg';
	const PICTURE_PATH_Ttd = '/images/policy-qr.png';

    // public function beforeAction($action)
    // {
        // $h = Yii::$app->request->headers;
        // $k = Utils::sanitize($h->get('x-api-key'));
        // $s = Utils::sanitize($h->get('x-api-secret'));
        // if (!Api::validate($k, $s)) {
            // $this->redirect(['/api/v1/site/header-error']);
            // return false;
        // }
        // return parent::beforeAction($action);
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
	
	

    public function actionSubmitPembiayaanBaru()
	{
		 Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		$payload = json_decode($request->getRawBody(), true);

		if (!is_array($payload)) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Payload JSON tidak valid',
				]
			];
		}
	
		$idTransaksi       = $payload['id_transaksi'] ?? null;
		$idPengajuan       = $payload['id_pengajuan'] ?? null;
		$kodeBroker        = $payload['kode_broker'] ?? null;
		$kodeCabang        = $payload['kode_cabang'] ?? null;
		$nomorAkad         = $payload['nomor_akad'] ?? null;
		$nomorRekening     = $payload['nomor_rekening'] ?? null;
		$nama              = $payload['nama'] ?? null;
		$ktp               = $payload['ktp'] ?? null;
		$npwp              = $payload['npwp'] ?? null;
		$jenisKelamin      = $payload['jenis_kelamin'] ?? null;
		$pekerjaan         = $payload['pekerjaan'] ?? null;

		$jenisPembiayaan   = $payload['jenis_pembiayaan'] ?? null;
		$jenisPengajuan    = $payload['jenis_pengajuan'] ?? null;
		$plafond           = (float)($payload['plafond'] ?? 0);
		$bunga             = (float)($payload['bunga'] ?? 0);
		$benefit           = $payload['benefit'] ?? null;
		$coverage          = (float)($payload['coverage'] ?? 0);
		$benefitPembiayaan = $payload['benefit_pembiayaan'] ?? null;

		$tglLahirRaw = (string)($payload['tgl_lahir'] ?? '');

		if (strlen($tglLahirRaw) === 8) {
			$tglLahir = date(
				'Y-m-d',
				strtotime($tglLahirRaw)
			);
		} else {
			$tglLahir = null;
		}

		$tglBukaRaw = (string)($payload['tgl_buka'] ?? '');

		if (strlen($tglBukaRaw) === 8) {
			$tglBuka = date(
				'Y-m-d',
				strtotime($tglBukaRaw)
			);
		} else {
			$tglBuka = null;
		}

		$polisJiwa = $payload['polis_jiwa'] ?? [];

		$plafonPertanggungan = (float)(
			$polisJiwa['plafon_pertanggungan']
			?? $plafond
		);

		$tenorPertanggungan = (int)(
			$polisJiwa['tenor_pertanggungan']
			?? ($payload['tenor'] ?? 0)
		);

		$ratePolis = (float)(
			$polisJiwa['rate_polis']
			?? 0
		);

		$nominalPremi = (float)(
			$polisJiwa['nominal_premi']
			?? 0
		);

		if ($tglBuka && $tenorPertanggungan > 0) {

			$tglAkhir = date(
				'Y-m-d',
				strtotime(
					'+' . $tenorPertanggungan . ' months',
					strtotime($tglBuka)
				)
			);

		} else {

			$tglAkhir = null;

		}

		$age = null;

		if ($tglLahir && $tglBuka) {

			$birth = new \DateTime($tglLahir);
			$start = new \DateTime($tglBuka);

			$age = $birth->diff($start)->y;
		}

		// $produk = 'non pegawai';

		$policybyproduk = Policy::findOne([
			'produk_code' => $pekerjaan,
		]);
			// var_dump($quotationUwLimit);
		if (!$policybyproduk) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '03',
					'message' => 'Policy produk tidak ditemukan',
				]
			];
		}

		$quotation = Quotation::findOne([
			'id' => $policybyproduk->quotation_id,
		]);

		if (!$quotation) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '04',
					'message' => 'Quotation tidak ditemukan',
				]
			];
		}

		$quotationUwLimit = QuotationUwLimit::find()
			->where([
				'quotation_id' => $policybyproduk->quotation_id
			])
			->andWhere(['<=', 'min_age', $age])
			->andWhere(['>=', 'max_age', $age])
			->andWhere(['<=', 'min_si', $plafonPertanggungan])
			->andWhere(['>=', 'max_si', $plafonPertanggungan])
			->one();
		// var_dump($quotationUwLimit);
		if (!$quotationUwLimit) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '05',
					'message' => 'UW limit tidak ditemukan',
				]
			];
		}

		$medicalCode = $quotationUwLimit->medical_code;

		$existingMember = Member::find()
			->where([
				'no_ktp' => $ktp,
				'member_status' => Member::MEMBER_STATUS_INFORCE
			])
			->one();

		$akumulasi = 'False';
		$nilaiAkumulasi = $plafonPertanggungan;

		if ($existingMember) {

			$akumulasi = 'True';

			$upAwal = (float)$existingMember->sum_insured;

			$nilaiAkumulasi =
				$upAwal + $plafonPertanggungan;

			$quotationUwLimit = QuotationUwLimit::find()
				->where([
					'quotation_id' => $policybyproduk->quotation_id
				])
				->andWhere(['<=', 'min_age', $age])
				->andWhere(['>=', 'max_age', $age])
				->andWhere(['<=', 'min_si', $nilaiAkumulasi])
				->andWhere(['>=', 'max_si', $nilaiAkumulasi])
				->one();
			
			if (!$quotationUwLimit) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '06',
						'message' => 'Akumulasi UP melebihi batas underwriting',
						'nilai_akumulasi' => $nilaiAkumulasi,
					]
				];
			}

			$medicalCode = $quotationUwLimit->medical_code;
		}


		$personalNo = Personal::generatePersonalNo(
			$nama,
			$tglLahir
		);

		$batchNo = $idPengajuan;

		$memberNo = '';

		$memberStatus = Member::MEMBER_STATUS_PENDING;

		if ($nominalPremi <= 0) {

			$nominalPremi =
				$plafonPertanggungan *
				$ratePolis /
				1000;
		}

		$nettPremium = round($nominalPremi, 0);

		$polisJiwaJson = json_encode(
			$polisJiwa,
			JSON_UNESCAPED_UNICODE
		);


		// $transaction = Yii::$app->db->beginTransaction();

		try {

			$personal = new Personal();

			$personal->personal_no = $personalNo;
			$personal->name = $nama;
			$personal->birth_date = $tglLahir;
			$personal->id_card_no = $ktp;
			$personal->phone = $ktp;

			if (!$personal->save()) {

				throw new \Exception(
					'Gagal insert Personal: ' .
					json_encode($personal->errors)
				);
			}

			$member = new Member();

			$member->policy_no = $policybyproduk->policy_no;
			$member->batch_no = $batchNo;
			$member->member_no = $memberNo;
			$member->personal_no = $personalNo;

			$member->age = $age;
			$member->term = $tenorPertanggungan;

			$member->start_date = $tglBuka;
			$member->end_date = $tglAkhir;

			$member->sum_insured = $plafonPertanggungan;
			$member->total_si = $plafonPertanggungan;

			$member->total_premium = $nettPremium;
			$member->rate_premi = $ratePolis;
			$member->gross_premium = $nettPremium;
			$member->basic_premium = $nettPremium;
			$member->nett_premium = $nettPremium;

			$member->medical_code = $medicalCode;

			$member->status =
				Member::MEMBER_STATUS_PENDING;

			$member->member_status =
				Member::MEMBER_STATUS_PENDING;

			$member->created_at =
				date('Y-m-d H:i:s');

			$member->created_by =
				$this->createdBy;

			$member->contract_date =
				$tglBuka;

			$member->produk =
				$policybyproduk->produk;

			$member->id_loan =
				$idTransaksi;

			$member->status_uw =
				$medicalCode;

			$member->no_ktp = $ktp;
			$member->pekerjaan = $pekerjaan;

			$member->id_transaksi = $idTransaksi;
			$member->id_pengajuan = $idPengajuan;
			$member->kode_broker = $kodeBroker;
			$member->kode_cabang = $kodeCabang;
			$member->nomor_akad = $nomorAkad;
			$member->nomor_rekening = $nomorRekening;
			$member->nama = $nama;
			$member->ktp = $ktp;
			$member->npwp = $npwp;

			$member->jenis_kelamin =
				$jenisKelamin;

			$member->tgl_lahir =
				$tglLahir;

			$member->tgl_buka =
				$tglBuka;

			$member->tenor =
				$tenorPertanggungan;

			$member->bunga =
				$bunga;

			$member->jenis_pembiayaan =
				$jenisPembiayaan;

			$member->jenis_pengajuan =
				$jenisPengajuan;

			$member->benefit =
				$benefit;

			$member->benefit_pembiayaan =
				$benefitPembiayaan;

			$member->coverage =
				$coverage;

			$member->polis_jiwa =
				$polisJiwaJson;

			if (!$member->save()) {

				throw new \Exception(
					'Gagal insert Member: ' .
					json_encode($member->errors)
				);
			}


		} catch (\Exception $e) {

			// $transaction->rollBack();

			Yii::error(
				'Submit Pembiayaan Baru Error: ' .
				$e->getMessage()
			);

			return [
				'Result' => [
					'status' => '500',
					'kode_response' => '99',
					'message' => 'Gagal menyimpan data',
					'error' => $e->getMessage(),
				]
			];
		}

		$dokument = Dokumen_Medis::getAll([
			'medis' => $medicalCode
		]);

		return [
			'Result' => [
				'status' => '200',
				
				'kode_response' => '00',
				'message' => 'Berhasil kirim pengajuan polis baru',

				'nama' => $nama,

				'nomor_rekening' =>
					$nomorRekening,

				'nomor_akad' =>
					$nomorAkad,

				'jenis_pengajuan' =>
					$jenisPengajuan,

				'jenis_penjaminan' =>
					'Asuransi Jiwa',

				'coverage' =>
					$coverage,

				'polis_jiwa' => [
					'no_polis' =>
						$policybyproduk->policy_no,

					's&k' =>
						'DIISI NOMOR PKS',

					'asuransi' =>
						'DIISI NAMA ASURANSI',

					'periode_awal' =>
						date(
							'Ymd',
							strtotime($tglBuka)
						),

					'periode_akhir' =>
						date(
							'Ymd',
							strtotime($tglAkhir)
						),

					'nilai_penjaminan' =>
						$plafonPertanggungan,

					'tarif_imbal_jasa' =>
						$ratePolis,

					'jumlah_imbal_jasa' =>
						number_format(
							$nettPremium,
							3,
							'.',
							''
						),

					'tarif_extra_premi' =>
						0,

					'jumlah_extra_premi' =>
						0,
						
						
				],
			],
		
		];
	}
	
	
	public function actionSubmitPembiayaanTopup()
	{
		 Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		$payload = json_decode($request->getRawBody(), true);

		if (!is_array($payload)) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Payload JSON tidak valid',
				]
			];
		}
	
		$idTransaksi       = $payload['id_transaksi'] ?? null;
		$idPengajuan       = $payload['id_pengajuan'] ?? null;
		$kodeBroker        = $payload['kode_broker'] ?? null;
		$kodeCabang        = $payload['kode_cabang'] ?? null;
		$nomorAkad         = $payload['nomor_akad'] ?? null;
		$nomorRekening     = $payload['nomor_rekening'] ?? null;
		$nama              = $payload['nama'] ?? null;
		$ktp               = $payload['ktp'] ?? null;
		$npwp              = $payload['npwp'] ?? null;
		$jenisKelamin      = $payload['jenis_kelamin'] ?? null;
		$pekerjaan         = $payload['pekerjaan'] ?? null;

		$jenisPembiayaan   = $payload['jenis_pembiayaan'] ?? null;
		$jenisPengajuan    = $payload['jenis_pengajuan'] ?? null;
		$plafond           = (float)($payload['plafond'] ?? 0);
		$bunga             = (float)($payload['bunga'] ?? 0);
		$benefit           = $payload['benefit'] ?? null;
		$coverage          = (float)($payload['coverage'] ?? 0);
		$benefitPembiayaan = $payload['benefit_pembiayaan'] ?? null;

		$tglLahirRaw = (string)($payload['tgl_lahir'] ?? '');

		if (strlen($tglLahirRaw) === 8) {
			$tglLahir = date(
				'Y-m-d',
				strtotime($tglLahirRaw)
			);
		} else {
			$tglLahir = null;
		}

		$tglBukaRaw = (string)($payload['tgl_buka'] ?? '');

		if (strlen($tglBukaRaw) === 8) {
			$tglBuka = date(
				'Y-m-d',
				strtotime($tglBukaRaw)
			);
		} else {
			$tglBuka = null;
		}

		$polisJiwa = $payload['polis_jiwa'] ?? [];

		$plafonPertanggungan = (float)(
			$polisJiwa['plafon_pertanggungan']
			?? $plafond
		);

		$tenorPertanggungan = (int)(
			$polisJiwa['tenor_pertanggungan']
			?? ($payload['tenor'] ?? 0)
		);

		$ratePolis = (float)(
			$polisJiwa['rate_polis']
			?? 0
		);

		$nominalPremi = (float)(
			$polisJiwa['nominal_premi']
			?? 0
		);

		if ($tglBuka && $tenorPertanggungan > 0) {

			$tglAkhir = date(
				'Y-m-d',
				strtotime(
					'+' . $tenorPertanggungan . ' months',
					strtotime($tglBuka)
				)
			);

		} else {

			$tglAkhir = null;

		}

		$age = null;

		if ($tglLahir && $tglBuka) {

			$birth = new \DateTime($tglLahir);
			$start = new \DateTime($tglBuka);

			$age = $birth->diff($start)->y;
		}

		// $produk = 'non pegawai';

		$policybyproduk = Policy::findOne([
			'produk_code' => $pekerjaan,
		]);
			// var_dump($quotationUwLimit);
		if (!$policybyproduk) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '03',
					'message' => 'Policy produk tidak ditemukan',
				]
			];
		}

		$quotation = Quotation::findOne([
			'id' => $policybyproduk->quotation_id,
		]);

		if (!$quotation) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '04',
					'message' => 'Quotation tidak ditemukan',
				]
			];
		}

		$quotationUwLimit = QuotationUwLimit::find()
			->where([
				'quotation_id' => $policybyproduk->quotation_id
			])
			->andWhere(['<=', 'min_age', $age])
			->andWhere(['>=', 'max_age', $age])
			->andWhere(['<=', 'min_si', $plafonPertanggungan])
			->andWhere(['>=', 'max_si', $plafonPertanggungan])
			->one();
		// var_dump($quotationUwLimit);
		if (!$quotationUwLimit) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '05',
					'message' => 'UW limit tidak ditemukan',
				]
			];
		}

		$medicalCode = $quotationUwLimit->medical_code;

		$existingMember = Member::find()
			->where([
				'no_ktp' => $ktp,
				'member_status' => Member::MEMBER_STATUS_INFORCE
			])
			->one();

		$akumulasi = 'False';
		$nilaiAkumulasi = $plafonPertanggungan;

		if ($existingMember) {

			$akumulasi = 'True';

			$upAwal = (float)$existingMember->sum_insured;

			$nilaiAkumulasi =
				$upAwal + $plafonPertanggungan;

			$quotationUwLimit = QuotationUwLimit::find()
				->where([
					'quotation_id' => $policybyproduk->quotation_id
				])
				->andWhere(['<=', 'min_age', $age])
				->andWhere(['>=', 'max_age', $age])
				->andWhere(['<=', 'min_si', $nilaiAkumulasi])
				->andWhere(['>=', 'max_si', $nilaiAkumulasi])
				->one();
			
			if (!$quotationUwLimit) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '06',
						'message' => 'Akumulasi UP melebihi batas underwriting',
						'nilai_akumulasi' => $nilaiAkumulasi,
					]
				];
			}

			$medicalCode = $quotationUwLimit->medical_code;
		}


		$personalNo = Personal::generatePersonalNo(
			$nama,
			$tglLahir
		);

		$batchNo = $idPengajuan;

		$memberNo = '';

		$memberStatus = Member::MEMBER_STATUS_PENDING;

		if ($nominalPremi <= 0) {

			$nominalPremi =
				$plafonPertanggungan *
				$ratePolis /
				1000;
		}

		$nettPremium = round($nominalPremi, 0);

		$polisJiwaJson = json_encode(
			$polisJiwa,
			JSON_UNESCAPED_UNICODE
		);


		// $transaction = Yii::$app->db->beginTransaction();

		try {

			$personal = new Personal();

			$personal->personal_no = $personalNo;
			$personal->name = $nama;
			$personal->birth_date = $tglLahir;
			$personal->id_card_no = $ktp;
			$personal->phone = $ktp;

			if (!$personal->save()) {

				throw new \Exception(
					'Gagal insert Personal: ' .
					json_encode($personal->errors)
				);
			}

			$member = new Member();

			$member->policy_no = $policybyproduk->policy_no;
			$member->batch_no = $batchNo;
			$member->member_no = $memberNo;
			$member->personal_no = $personalNo;

			$member->age = $age;
			$member->term = $tenorPertanggungan;

			$member->start_date = $tglBuka;
			$member->end_date = $tglAkhir;

			$member->sum_insured = $plafonPertanggungan;
			$member->total_si = $plafonPertanggungan;

			$member->total_premium = $nettPremium;
			$member->rate_premi = $ratePolis;
			$member->gross_premium = $nettPremium;
			$member->basic_premium = $nettPremium;
			$member->nett_premium = $nettPremium;

			$member->medical_code = $medicalCode;

			$member->status =
				Member::MEMBER_STATUS_PENDING;

			$member->member_status =
				Member::MEMBER_STATUS_PENDING;

			$member->created_at =
				date('Y-m-d H:i:s');

			$member->created_by =
				$this->createdBy;

			$member->contract_date =
				$tglBuka;

			$member->produk =
				$policybyproduk->produk;

			$member->id_loan =
				$idTransaksi;

			$member->status_uw =
				$medicalCode;

			$member->no_ktp = $ktp;
			$member->pekerjaan = $pekerjaan;

			$member->id_transaksi = $idTransaksi;
			$member->id_pengajuan = $idPengajuan;
			$member->kode_broker = $kodeBroker;
			$member->kode_cabang = $kodeCabang;
			$member->nomor_akad = $nomorAkad;
			$member->nomor_rekening = $nomorRekening;
			$member->nama = $nama;
			$member->ktp = $ktp;
			$member->npwp = $npwp;

			$member->jenis_kelamin =
				$jenisKelamin;

			$member->tgl_lahir =
				$tglLahir;

			$member->tgl_buka =
				$tglBuka;

			$member->tenor =
				$tenorPertanggungan;

			$member->bunga =
				$bunga;

			$member->jenis_pembiayaan =
				$jenisPembiayaan;

			$member->jenis_pengajuan =
				$jenisPengajuan;

			$member->benefit =
				$benefit;

			$member->benefit_pembiayaan =
				$benefitPembiayaan;

			$member->coverage =
				$coverage;

			$member->polis_jiwa =
				$polisJiwaJson;

			if (!$member->save()) {

				throw new \Exception(
					'Gagal insert Member: ' .
					json_encode($member->errors)
				);
			}


		} catch (\Exception $e) {

			// $transaction->rollBack();

			Yii::error(
				'Submit Pembiayaan Baru Error: ' .
				$e->getMessage()
			);

			return [
				'Result' => [
					'status' => '500',
					'kode_response' => '99',
					'message' => 'Gagal menyimpan data',
					'error' => $e->getMessage(),
				]
			];
		}

		$dokument = Dokumen_Medis::getAll([
			'medis' => $medicalCode
		]);

		return [
			'Result' => [
				'status' => '200',
				
				'kode_response' => '00',
				'message' => 'Berhasil kirim pengajuan polis baru',

				'nama' => $nama,

				'nomor_rekening' =>
					$nomorRekening,

				'nomor_akad' =>
					$nomorAkad,

				'jenis_pengajuan' =>
					$jenisPengajuan,

				'jenis_penjaminan' =>
					'Asuransi Jiwa',

				'coverage' =>
					$coverage,

				'polis_jiwa' => [
					'no_polis' =>
						$policybyproduk->policy_no,

					's&k' =>
						'DIISI NOMOR PKS',

					'asuransi' =>
						'DIISI NAMA ASURANSI',

					'periode_awal' =>
						date(
							'Ymd',
							strtotime($tglBuka)
						),

					'periode_akhir' =>
						date(
							'Ymd',
							strtotime($tglAkhir)
						),

					'nilai_penjaminan' =>
						$plafonPertanggungan,

					'tarif_imbal_jasa' =>
						$ratePolis,

					'jumlah_imbal_jasa' =>
						number_format(
							$nettPremium,
							3,
							'.',
							''
						),

					'tarif_extra_premi' =>
						0,

					'jumlah_extra_premi' =>
						0,
						
						
				],
				 'restitusi_jiwa' => ['status_restitusi' => '1',
				],
				],
		];
	}
	
	
	public function actionSubmitTopupKhusus()
	{
		 Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$request = Yii::$app->request;

		$payload = json_decode($request->getRawBody(), true);

		if (!is_array($payload)) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Payload JSON tidak valid',
				]
			];
		}
	
		$idTransaksi       = $payload['id_transaksi'] ?? null;
		$idPengajuan       = $payload['id_pengajuan'] ?? null;
		$kodeBroker        = $payload['kode_broker'] ?? null;
		$kodeCabang        = $payload['kode_cabang'] ?? null;
		$nomorAkad         = $payload['nomor_akad'] ?? null;
		$nomorRekening     = $payload['nomor_rekening'] ?? null;
		$nama              = $payload['nama'] ?? null;
		$ktp               = $payload['ktp'] ?? null;
		$npwp              = $payload['npwp'] ?? null;
		$jenisKelamin      = $payload['jenis_kelamin'] ?? null;
		$pekerjaan         = $payload['pekerjaan'] ?? null;

		$jenisPembiayaan   = $payload['jenis_pembiayaan'] ?? null;
		$jenisPengajuan    = $payload['jenis_pengajuan'] ?? null;
		$plafond           = (float)($payload['plafond'] ?? 0);
		$bunga             = (float)($payload['bunga'] ?? 0);
		$benefit           = $payload['benefit'] ?? null;
		$coverage          = (float)($payload['coverage'] ?? 0);
		$benefitPembiayaan = $payload['benefit_pembiayaan'] ?? null;

		$tglLahirRaw = (string)($payload['tgl_lahir'] ?? '');

		if (strlen($tglLahirRaw) === 8) {
			$tglLahir = date(
				'Y-m-d',
				strtotime($tglLahirRaw)
			);
		} else {
			$tglLahir = null;
		}

		$tglBukaRaw = (string)($payload['tgl_buka'] ?? '');

		if (strlen($tglBukaRaw) === 8) {
			$tglBuka = date(
				'Y-m-d',
				strtotime($tglBukaRaw)
			);
		} else {
			$tglBuka = null;
		}

		$polisJiwa = $payload['polis_jiwa'] ?? [];

		$plafonPertanggungan = (float)(
			$polisJiwa['plafon_pertanggungan']
			?? $plafond
		);

		$tenorPertanggungan = (int)(
			$polisJiwa['tenor_pertanggungan']
			?? ($payload['tenor'] ?? 0)
		);

		$ratePolis = (float)(
			$polisJiwa['rate_polis']
			?? 0
		);

		$nominalPremi = (float)(
			$polisJiwa['nominal_premi']
			?? 0
		);

		if ($tglBuka && $tenorPertanggungan > 0) {

			$tglAkhir = date(
				'Y-m-d',
				strtotime(
					'+' . $tenorPertanggungan . ' months',
					strtotime($tglBuka)
				)
			);

		} else {

			$tglAkhir = null;

		}

		$age = null;

		if ($tglLahir && $tglBuka) {

			$birth = new \DateTime($tglLahir);
			$start = new \DateTime($tglBuka);

			$age = $birth->diff($start)->y;
		}

		// $produk = 'non pegawai';

		$policybyproduk = Policy::findOne([
			'produk_code' => $pekerjaan,
		]);
			// var_dump($quotationUwLimit);
		if (!$policybyproduk) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '03',
					'message' => 'Policy produk tidak ditemukan',
				]
			];
		}

		$quotation = Quotation::findOne([
			'id' => $policybyproduk->quotation_id,
		]);

		if (!$quotation) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '04',
					'message' => 'Quotation tidak ditemukan',
				]
			];
		}

		$quotationUwLimit = QuotationUwLimit::find()
			->where([
				'quotation_id' => $policybyproduk->quotation_id
			])
			->andWhere(['<=', 'min_age', $age])
			->andWhere(['>=', 'max_age', $age])
			->andWhere(['<=', 'min_si', $plafonPertanggungan])
			->andWhere(['>=', 'max_si', $plafonPertanggungan])
			->one();
		// var_dump($quotationUwLimit);
		if (!$quotationUwLimit) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '05',
					'message' => 'UW limit tidak ditemukan',
				]
			];
		}

		$medicalCode = $quotationUwLimit->medical_code;

		$existingMember = Member::find()
			->where([
				'no_ktp' => $ktp,
				'member_status' => Member::MEMBER_STATUS_INFORCE
			])
			->one();

		$akumulasi = 'False';
		$nilaiAkumulasi = $plafonPertanggungan;

		if ($existingMember) {

			$akumulasi = 'True';

			$upAwal = (float)$existingMember->sum_insured;

			$nilaiAkumulasi =
				$upAwal + $plafonPertanggungan;

			$quotationUwLimit = QuotationUwLimit::find()
				->where([
					'quotation_id' => $policybyproduk->quotation_id
				])
				->andWhere(['<=', 'min_age', $age])
				->andWhere(['>=', 'max_age', $age])
				->andWhere(['<=', 'min_si', $nilaiAkumulasi])
				->andWhere(['>=', 'max_si', $nilaiAkumulasi])
				->one();
			
			if (!$quotationUwLimit) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '06',
						'message' => 'Akumulasi UP melebihi batas underwriting',
						'nilai_akumulasi' => $nilaiAkumulasi,
					]
				];
			}

			$medicalCode = $quotationUwLimit->medical_code;
		}


		$personalNo = Personal::generatePersonalNo(
			$nama,
			$tglLahir
		);

		$batchNo = $idPengajuan;

		$memberNo = '';

		$memberStatus = Member::MEMBER_STATUS_PENDING;

		if ($nominalPremi <= 0) {

			$nominalPremi =
				$plafonPertanggungan *
				$ratePolis /
				1000;
		}

		$nettPremium = round($nominalPremi, 0);

		$polisJiwaJson = json_encode(
			$polisJiwa,
			JSON_UNESCAPED_UNICODE
		);


		// $transaction = Yii::$app->db->beginTransaction();

		try {

			$personal = new Personal();

			$personal->personal_no = $personalNo;
			$personal->name = $nama;
			$personal->birth_date = $tglLahir;
			$personal->id_card_no = $ktp;
			$personal->phone = $ktp;

			if (!$personal->save()) {

				throw new \Exception(
					'Gagal insert Personal: ' .
					json_encode($personal->errors)
				);
			}

			$member = new Member();

			$member->policy_no = $policybyproduk->policy_no;
			$member->batch_no = $batchNo;
			$member->member_no = $memberNo;
			$member->personal_no = $personalNo;

			$member->age = $age;
			$member->term = $tenorPertanggungan;

			$member->start_date = $tglBuka;
			$member->end_date = $tglAkhir;

			$member->sum_insured = $plafonPertanggungan;
			$member->total_si = $plafonPertanggungan;

			$member->total_premium = $nettPremium;
			$member->rate_premi = $ratePolis;
			$member->gross_premium = $nettPremium;
			$member->basic_premium = $nettPremium;
			$member->nett_premium = $nettPremium;

			$member->medical_code = $medicalCode;

			$member->status =
				Member::MEMBER_STATUS_PENDING;

			$member->member_status =
				Member::MEMBER_STATUS_PENDING;

			$member->created_at =
				date('Y-m-d H:i:s');

			$member->created_by =
				$this->createdBy;

			$member->contract_date =
				$tglBuka;

			$member->produk =
				$policybyproduk->produk;

			$member->id_loan =
				$idTransaksi;

			$member->status_uw =
				$medicalCode;

			$member->no_ktp = $ktp;
			$member->pekerjaan = $pekerjaan;

			$member->id_transaksi = $idTransaksi;
			$member->id_pengajuan = $idPengajuan;
			$member->kode_broker = $kodeBroker;
			$member->kode_cabang = $kodeCabang;
			$member->nomor_akad = $nomorAkad;
			$member->nomor_rekening = $nomorRekening;
			$member->nama = $nama;
			$member->ktp = $ktp;
			$member->npwp = $npwp;

			$member->jenis_kelamin =
				$jenisKelamin;

			$member->tgl_lahir =
				$tglLahir;

			$member->tgl_buka =
				$tglBuka;

			$member->tenor =
				$tenorPertanggungan;

			$member->bunga =
				$bunga;

			$member->jenis_pembiayaan =
				$jenisPembiayaan;

			$member->jenis_pengajuan =
				$jenisPengajuan;

			$member->benefit =
				$benefit;

			$member->benefit_pembiayaan =
				$benefitPembiayaan;

			$member->coverage =
				$coverage;

			$member->polis_jiwa =
				$polisJiwaJson;

			if (!$member->save()) {

				throw new \Exception(
					'Gagal insert Member: ' .
					json_encode($member->errors)
				);
			}


		} catch (\Exception $e) {

			// $transaction->rollBack();

			Yii::error(
				'Submit Pembiayaan Baru Error: ' .
				$e->getMessage()
			);

			return [
				'Result' => [
					'status' => '500',
					'kode_response' => '99',
					'message' => 'Gagal menyimpan data',
					'error' => $e->getMessage(),
				]
			];
		}

		$dokument = Dokumen_Medis::getAll([
			'medis' => $medicalCode
		]);

		return [
			'Result' => [
				'status' => '200',
				
				'kode_response' => '00',
				'message' => 'Berhasil kirim pengajuan polis baru',

				'nama' => $nama,

				'nomor_rekening' =>
					$nomorRekening,

				'nomor_akad' =>
					$nomorAkad,

				'jenis_pengajuan' =>
					$jenisPengajuan,

				'jenis_penjaminan' =>
					'Asuransi Jiwa',

				'coverage' =>
					$coverage,

				'polis_jiwa' => [
					'no_polis' =>
						$policybyproduk->policy_no,

					's&k' =>
						'DIISI NOMOR PKS',

					'asuransi' =>
						'DIISI NAMA ASURANSI',

					'periode_awal' =>
						date(
							'Ymd',
							strtotime($tglBuka)
						),

					'periode_akhir' =>
						date(
							'Ymd',
							strtotime($tglAkhir)
						),

					'nilai_penjaminan' =>
						$plafonPertanggungan,

					'tarif_imbal_jasa' =>
						$ratePolis,

					'jumlah_imbal_jasa' =>
						number_format(
							$nettPremium,
							3,
							'.',
							''
						),

					'tarif_extra_premi' =>
						0,

					'jumlah_extra_premi' =>
						0,
						
						
				],
			],
		
		];
	}
	

}
