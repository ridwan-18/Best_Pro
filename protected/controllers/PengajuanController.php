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

class PengajuanController extends Controller
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
	

	public function actionSubmitDokumenCbc()
	{
		Yii::$app->response->format =
			\yii\web\Response::FORMAT_JSON;

		$request = Yii::$app->request;

		$headers = $request->headers;

		$authorization =
			$headers->get('Authorization');

		if (!$authorization) {

			return [
				'is_success' => 0,
				'message' =>
					'Authorization header tidak ditemukan'
			];
		}

		if (
			!preg_match(
				'/^Bearer\s+(.+)$/i',
				$authorization,
				$matches
			)
		) {

			return [
				'is_success' => 0,
				'message' =>
					'Format Authorization harus Bearer Token'
			];
		}

		$token = trim($matches[1]);

		if ($token === '') {

			return [
				'is_success' => 0,
				'message' =>
					'Bearer Token tidak ditemukan'
			];
		}


		$contentType =
			$request->headers->get('Content-Type');

		if (
			$contentType &&
			stripos(
				$contentType,
				'application/json'
			) === false
		) {

			return [
				'is_success' => 0,
				'message' =>
					'Content-Type harus application/json'
			];
		}


		$rawBody =
			$request->getRawBody();

		if (
			$rawBody === null ||
			trim($rawBody) === ''
		) {

			return [
				'is_success' => 0,
				'message' =>
					'Request body kosong'
			];
		}


		$payload =
			json_decode(
				$rawBody,
				true
			);

		if (
			!is_array($payload) ||
			json_last_error() !== JSON_ERROR_NONE
		) {

			return [
				'is_success' => 0,
				'message' =>
					'Payload JSON tidak valid',
				'error' =>
					json_last_error_msg()
			];
		}



		$idTransaksi =
			$payload['id_transaksi'] ?? null;

		$idPengajuan =
			$payload['id_pengajuan'] ?? null;

		$kodeBroker =
			$payload['kode_broker'] ?? null;

		$kodeCabang =
			$payload['kode_cabang'] ?? null;

		$nama =
			$payload['nama'] ?? null;

		$ktp =
			$payload['ktp'] ?? null;

		$jenisKelamin =
			$payload['jenis_kelamin'] ?? null;

		$tglLahirRaw =
			$payload['tgl_lahir'] ?? null;

		$tenor =
			(int)($payload['tenor'] ?? 0);

		$coverage =
			(float)($payload['coverage'] ?? 0);

		$jenisPembiayaan =
			$payload['jenis_pembiayaan'] ?? null;

		$plafond =
			(float)($payload['plafond'] ?? 0);

		$benefit =
			$payload['benefit'] ?? null;

		$pekerjaan =
			$payload['pekerjaan'] ?? null;

		$benefitPembiayaan =
			$payload['benefit_pembiayaan'] ?? null;


		$requiredFields = [
			'id_transaksi' =>
				$idTransaksi,

			'id_pengajuan' =>
				$idPengajuan,

			'kode_broker' =>
				$kodeBroker,

			'kode_cabang' =>
				$kodeCabang,

			'nama' =>
				$nama,

			'ktp' =>
				$ktp,

			'jenis_kelamin' =>
				$jenisKelamin,

			'tgl_lahir' =>
				$tglLahirRaw,

			'tenor' =>
				$tenor,

			'coverage' =>
				$coverage,

			'jenis_pembiayaan' =>
				$jenisPembiayaan,

			'plafond' =>
				$plafond,

			'benefit' =>
				$benefit,

			'pekerjaan' =>
				$pekerjaan,

			'benefit_pembiayaan' =>
				$benefitPembiayaan,
		];


		foreach (
			$requiredFields
			as $field => $value
		) {

			if (
				$value === null ||
				$value === ''
			) {

				return [
					'is_success' => 0,
					'message' =>
						'Field ' .
						$field .
						' wajib diisi'
				];
			}
		}


		$tglLahirRaw =
			(string)$tglLahirRaw;

		if (
			!preg_match(
				'/^\d{8}$/',
				$tglLahirRaw
			)
		) {

			return [
				'is_success' => 0,
				'message' =>
					'Format tgl_lahir harus YYYYMMDD'
			];
		}


		$tglLahirDate =
			\DateTime::createFromFormat(
				'Ymd',
				$tglLahirRaw
			);


		if (!$tglLahirDate) {

			return [
				'is_success' => 0,
				'message' =>
					'Tanggal lahir tidak valid'
			];
		}


		$tglLahir =
			$tglLahirDate->format('Y-m-d');


		$tglBuka =
			date('Y-m-d');


		$birth =
			new \DateTime($tglLahir);

		$start =
			new \DateTime($tglBuka);

		$age =
			$birth->diff($start)->y;


		if ($tenor > 0) {

			$tglAkhir =
				date(
					'Y-m-d',
					strtotime(
						'+' .
						$tenor .
						' months',
						strtotime($tglBuka)
					)
				);

		} else {

			$tglAkhir = null;
		}


		$policybyproduk =
			Policy::findOne([
				'produk_code' =>
					$pekerjaan,
			]);


		if (!$policybyproduk) {

			return [
				'is_success' => 0,
				'message' =>
					'Policy produk tidak ditemukan',
				'produk_code' =>
					$pekerjaan
			];
		}


		$quotation =
			Quotation::findOne([
				'id' =>
					$policybyproduk->quotation_id,
			]);


		if (!$quotation) {

			return [
				'is_success' => 0,
				'message' =>
					'Quotation tidak ditemukan'
			];
		}

		$quotationUwLimit =
			QuotationUwLimit::find()
				->where([
					'quotation_id' =>
						$policybyproduk->quotation_id
				])
				->andWhere([
					'<=',
					'min_age',
					$age
				])
				->andWhere([
					'>=',
					'max_age',
					$age
				])
				->andWhere([
					'<=',
					'min_si',
					$plafond
				])
				->andWhere([
					'>=',
					'max_si',
					$plafond
				])
				->one();


		if (!$quotationUwLimit) {

			return [
				'is_success' => 0,
				'message' =>
					'UW limit tidak ditemukan'
			];
		}


		$medicalCode =$quotationUwLimit->medical_code;


		$existingMember =Member::find()
				->where([
					'no_ktp' =>$ktp,
					'member_status' =>Member::MEMBER_STATUS_INFORCE
				])
				->one();


		$akumulasi = 'False';

		$nilaiAkumulasi =$plafond;


		if ($existingMember) {
			$akumulasi = 'True';
			$upAwal =
				(float)$existingMember->sum_insured;
			$nilaiAkumulasi =
				$upAwal +
				$plafond;


			$quotationUwLimit =
				QuotationUwLimit::find()
					->where([
						'quotation_id' =>
							$policybyproduk->quotation_id
					])
					->andWhere([
						'<=',
						'min_age',
						$age
					])
					->andWhere([
						'>=',
						'max_age',
						$age
					])
					->andWhere([
						'<=',
						'min_si',
						$nilaiAkumulasi
					])
					->andWhere([
						'>=',
						'max_si',
						$nilaiAkumulasi
					])
					->one();


			if (!$quotationUwLimit) {

				return [
					'is_success' => 0,
					'message' =>
						'Akumulasi UP melebihi batas underwriting',

					'nilai_akumulasi' =>
						$nilaiAkumulasi
				];
			}


			$medicalCode =$quotationUwLimit->medical_code;
		}


		$personalNo =Personal::generatePersonalNo(
				$nama,
				$tglLahir
			);

		$batchNo =$idPengajuan;

		$memberNo = '';

		$memberStatus =Member::MEMBER_STATUS_PENDING;
		$ratePolis = QuotationRate::findOne([
					'term' => $tenor,
					'quotation_id' => $policybyproduk->quotation_id
				]);
				
		// var_dump($tenor);
		  // var_dump($policybyproduk);

		$nominalPremi = 0;
		if ($ratePolis > 0) 
		{
			$nominalPremi =
				$plafond *
				$ratePolis->rate /
				1000;
		}

		$nettPremium =
			round($nominalPremi,
				0
			);


		$polisJiwa = [
			'plafon_pertanggungan' =>
				$plafond,
			'tenor_pertanggungan' =>
				$tenor,
			'rate_polis' =>
				$ratePolis->rate,
			'nominal_premi' =>
				$nettPremium,
		];


		$polisJiwaJson =
			json_encode(
				$polisJiwa,
				JSON_UNESCAPED_UNICODE
			);


		$transaction =
			Yii::$app->db->beginTransaction();
		
		$fileNik = $ktp;

		$fileTanggal = date(
			'dmy',
			strtotime($tglBuka)
		);

		$codeDoc = '001';

		$fileBenefit = $benefit;

		$sequence = '01';

		$fileName =
			$fileNik . '_' .
			$fileTanggal . '_' .
			$codeDoc . '_' .
			$fileBenefit . '_' .
			$sequence .
			'.zip';

		try {


			$personal =
				new Personal();

			$personal->personal_no =
				$personalNo;

			$personal->name =
				$nama;

			$personal->birth_date =
				$tglLahir;

			$personal->id_card_no =
				$ktp;

			$personal->phone =
				$ktp;


			if (!$personal->save()) {

				throw new \Exception(
					'Gagal insert Personal: ' .
					json_encode(
						$personal->errors
					)
				);
			}

			$member =new Member();
			$member->policy_no = $policybyproduk->policy_no;
			$member->batch_no =$batchNo;
			$member->member_no =$memberNo;
			$member->personal_no =$personalNo;
			$member->age =$age;
			$member->term =$tenor;
			$member->start_date =$tglBuka;
			$member->end_date =$tglAkhir;
			$member->sum_insured =$plafond;
			$member->total_si =$plafond;
			$member->total_premium =$nettPremium;
			$member->rate_premi =$ratePolis->rate;
			$member->gross_premium =$nettPremium;
			$member->basic_premium =$nettPremium;
			$member->nett_premium =$nettPremium;
			$member->medical_code =$medicalCode;
			$member->status =Member::MEMBER_STATUS_PENDING;
			$member->member_status =Member::MEMBER_STATUS_PENDING;
			$member->created_at =date('Y-m-d H:i:s');
			$member->created_by =$this->createdBy;
			$member->contract_date =$tglBuka;
			$member->produk =$policybyproduk->produk;
			$member->id_loan =$idTransaksi;
			$member->status_uw =$medicalCode;
			$member->no_ktp =$ktp;
			$member->pekerjaan =$pekerjaan;
			$member->id_transaksi =$idTransaksi;
			$member->id_pengajuan =$idPengajuan;
			$member->kode_broker =$kodeBroker;
			$member->kode_cabang =$kodeCabang;
			$member->nama =$nama;
			$member->ktp =$ktp;
			$member->jenis_kelamin =$jenisKelamin;
			$member->tgl_lahir =$tglLahir;
			$member->tgl_buka =$tglBuka;
			$member->tenor =$tenor;
			$member->jenis_pembiayaan =$jenisPembiayaan;
			$member->benefit =$benefit;
			$member->benefit_pembiayaan =$benefitPembiayaan;
			$member->coverage =$coverage;
			$member->polis_jiwa =$polisJiwaJson;


			if (!$member->save()) {

				throw new \Exception(
					'Gagal insert Member: ' .
					json_encode(
						$member->errors
					)
				);
			}
			$transaction->commit();
		} catch (\Exception $e) {

			$transaction->rollBack();

			Yii::error(
				'Submit Dokumen CBC Error: ' .
				$e->getMessage()
			);

			return [
				'is_success' => 0,
				'message' =>
					'Gagal menyimpan data',
				'error' =>
					$e->getMessage()
			];
		}


		$dokument = Dokumen_Medis::getAll([
				'medis' => $medicalCode
			]);


		$sftpResult =
			$this->downloadFileFromBankSftp(
				$fileName
			);


		if (!$sftpResult['success']) {

			return [
				'Result' => [
					'status' => '200',
					'kode_response' => '00',
					'message' =>
						'Pengajuan berhasil, dokumen belum tersedia di SFTP Bank',
					'status_dokumen' => 0,
					'premi_disetujui' => $nettPremium,
					'coverage' => (string)$coverage,
					'keterangan' =>
						$sftpResult['message'],
				]
			];
		}


		return [
			'Result' => [
				'status' => '200',
				'kode_response' => '00',
				'message' =>
					'Berhasil kirim pengajuan dokumen CBC',
				'status_dokumen' => 1,
				'premi_disetujui' => $nettPremium,
				'coverage' => (string)$coverage,
				'keterangan' => '-',

				// 'dokumen' => [
					// 'file_name' =>
						// $sftpResult['file_name'],

					// 'local_path' =>
						// $sftpResult['local_path'],

					// 'remote_path' =>
						// $sftpResult['remote_path'],
				// ]
			]
		];
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
			$member->status =Member::MEMBER_STATUS_PENDING;
			$member->member_status =Member::MEMBER_STATUS_PENDING;
			$member->created_at =date('Y-m-d H:i:s');
			$member->created_by =$this->createdBy;
			$member->contract_date =$tglBuka;
			$member->produk =$policybyproduk->produk;
			$member->id_loan =$idTransaksi;
			$member->status_uw =$medicalCode;
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
			$member->jenis_kelamin =$jenisKelamin;
			$member->tgl_lahir =$tglLahir;
			$member->tgl_buka =$tglBuka;
			$member->tenor =$tenorPertanggungan;
			$member->bunga =$bunga;
			$member->jenis_pembiayaan =$jenisPembiayaan;
			$member->jenis_pengajuan =$jenisPengajuan;
			$member->benefit =$benefit;
			$member->benefit_pembiayaan =$benefitPembiayaan;
			$member->coverage =$coverage;
			$member->polis_jiwa =$polisJiwaJson;

			if (!$member->save()) {

				throw new \Exception(
					'Gagal insert Member: ' .
					json_encode($member->errors)
				);
			}
			
			$sertifikat = $this->generateSertifikat($member,$policybyproduk,$nettPremium);

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
				'nomor_rekening' =>$nomorRekening,
				'nomor_akad' =>$nomorAkad,
				'jenis_pengajuan' =>$jenisPengajuan,
				'jenis_penjaminan' =>'Asuransi Jiwa',
				'coverage' =>$coverage,
				
				 'sertifikat' => [
				'file_name' => $sertifikat['file_name'],
				'file_url' => $sertifikat['file_url'],
				],
					
				
				'polis_jiwa' => [
					'no_polis' =>
						$policybyproduk->policy_no,

					's&k' =>
						'-',

					'asuransi' =>
						'Reliance Life Unit Syariah',

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
						'-',

					'asuransi' =>
						'Reliance Life Unit Syariah',

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
				 'restitusi_jiwa' => [
				 'status_restitusi' => '1',
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

	public function actionDownloadCbc()
	{
		$nik = '1234567890123456';

		try {

			$sftpService = new SftpBankService();

			$result = $sftpService->downloadFilesByNik($nik);

			return $this->asJson([
				'status' => true,
				'data' => $result,
			]);

		} catch (\Throwable $e) {

			Yii::error(
				'DOWNLOAD CBC ERROR: ' . $e->getMessage(),
				'sftp-bank'
			);

			return $this->asJson([
				'status' => false,
				'message' => $e->getMessage(),
			]);
		}
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
	
	public function actionCheckPhp()
	{
		Yii::$app->response->format =
			\yii\web\Response::FORMAT_JSON;

		return [
			'php_version' => PHP_VERSION,
			'php_ini' => php_ini_loaded_file(),
			'extension_dir' => ini_get('extension_dir'),
			'ssh2_connect' => function_exists('ssh2_connect'),
			'loaded_extensions' => get_loaded_extensions(),
		];
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


	private function generateSertifikat($member, $policy, $nettPremium)
	{
		$folder = Yii::getAlias('@webroot/uploads/incoming');

		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}
		
		$norek   = $member->nomor_rekening;
		$noAkad  = $member->nomor_akad;
		$codeDoc = '005';
		$benefit = 2;
		
		$fileName =
		$norek . '_' .
		$noAkad . '_' .
		$codeDoc . '_' .
		$benefit . '.pdf';
		
		$pdfFileName = $fileName;
		
		$pdfPath =$folder . DIRECTORY_SEPARATOR . $pdfFileName;
		
		$zipFileName =
			$norek . '_' .
			$noAkad . '_' .
			$codeDoc . '_' .
			$benefit . '.zip';

		$zipPath =
		$folder . DIRECTORY_SEPARATOR . $zipFileName;
			
		// norek_noakad_codedoc_benefit.zip	

		// $filePath =
			// $folder . DIRECTORY_SEPARATOR . $fileName;

		$pdf = new \FPDF('L', 'mm', 'A4');

		$pdf->SetMargins(0, 0, 0);
		$pdf->SetAutoPageBreak(false);

		$pdf->AddPage();


		$pdf->SetFillColor(255, 255, 255);

		$pdf->Rect(
			0,
			0,
			297,
			210,
			'F'
		);


		$pdf->SetFillColor(42, 46, 111);

		$pdf->Rect(
			0,
			0,
			297,
			25,
			'F'
		);


		$pdf->SetFillColor(235, 52, 35);

		$pdf->Rect(
			0,
			25,
			297,
			2,
			'F'
		);


		$logoReliance =
			Yii::getAlias(
				'@webroot/uploads/assets/reliance.png'
			);

		if (file_exists($logoReliance)) {

			$pdf->Image(
				$logoReliance,
				7,
				4,
				55,
				17
			);

		} else {


			$pdf->SetTextColor(255, 255, 255);

			$pdf->SetFont(
				'Arial',
				'B',
				20
			);

			$pdf->SetXY(
				7,
				4
			);

			$pdf->Cell(
				55,
				8,
				'Reliance',
				0,
				1
			);

			$pdf->SetFont(
				'Arial',
				'',
				8
			);

			$pdf->SetXY(
				8,
				14
			);

			$pdf->Cell(
				55,
				5,
				'Life Unit Syariah',
				0,
				1
			);
		}


		$logoSyariah =
			Yii::getAlias(
				'@webroot/uploads/assets/syariah.png'
			);

		if (file_exists($logoSyariah)) {

			$pdf->Image(
				$logoSyariah,
				255,
				3,
				34,
				19
			);

		}


		$watermark =
			Yii::getAlias(
				'@webroot/uploads/assets/watermark.png'
			);

		if (file_exists($watermark)) {

			$pdf->Image(
				$watermark,
				105,
				48,
				90,
				90
			);

		}

		$pdf->SetTextColor(0, 0, 0);

		$pdf->SetFont(
			'Arial',
			'',
			10
		);

		$pdf->SetXY(
			0,
			32
		);

		$pdf->Cell(
			297,
			5,
			'Bismillahirrahmanirrahim',
			0,
			1,
			'C'
		);


		$pdf->SetFont(
			'Arial',
			'B',
			11
		);

		$pdf->Cell(
			297,
			6,
			'SERTIFIKAT KEPESERTAAN ASURANSI JIWA SYARIAH',
			0,
			1,
			'C'
		);


		$pdf->SetFont(
			'Arial',
			'B',
			9
		);

		$pdf->Cell(
			297,
			5,
			'No Polis: ' . $policy->policy_no,
			0,
			1,
			'C'
		);


		$pdf->SetFont(
			'Arial',
			'',
			9
		);

		$y = 55;


		$pdf->SetXY(33, $y);

		$pdf->Cell(
			35,
			5,
			'Nama',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$pdf->Cell(
			100,
			5,
			strtoupper($member->nama),
			0
		);


		$y += 6;

		$pdf->SetXY(33, $y);

		$pdf->Cell(
			35,
			5,
			'Nomor Sertifikat',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$nomorSertifikat =
			!empty($member->member_no)
				? $member->member_no
				: $member->id;

		$pdf->Cell(
			100,
			5,
			$nomorSertifikat,
			0
		);

		$y += 6;

		$pdf->SetXY(33, $y);

		$pdf->Cell(
			35,
			5,
			'Tanggal Lahir',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$tanggalLahir = '-';

		if (!empty($member->tgl_lahir)) {

			$tanggalLahir = date(
				'd-M-y',
				strtotime($member->tgl_lahir)
			);
		}

		$pdf->Cell(
			100,
			5,
			$tanggalLahir,
			0
		);


		$y += 13;

		$pdf->SetXY(
			33,
			$y
		);

		$pdf->Cell(
			0,
			5,
			'Adalah Peserta dari Pemegang Polis Asuransi Jiwa Syariah:',
			0,
			1
		);


		$pemegangPolis = 'PT. PROTEKSI ANTAR NUSA QQ PT. BANK RIAU KEPRI SYARIAH (PERSERODA)';

		if (!empty($member->pemegang_polis)) {

			$pemegangPolis =
				$member->pemegang_polis;

		} elseif (!empty($policy->policy_holder)) {

			$pemegangPolis =
				$policy->policy_holder;

		} elseif (!empty($policy->nama_pemegang_polis)) {

			$pemegangPolis =
				$policy->nama_pemegang_polis;

		}


		$y += 7;

		$pdf->SetFont(
			'Arial',
			'B',
			9
		);

		$pdf->SetXY(
			25,
			$y
		);

		$pdf->Cell(
			247,
			5,
			strtoupper($pemegangPolis),
			0,
			1,
			'C'
		);


		$pdf->SetFont(
			'Arial',
			'',
			9
		);

		$y += 12;

		$pdf->SetXY(
			33,
			$y
		);

		$pdf->Cell(
			0,
			5,
			'Dengan ketentuan Asuransi sebagai berikut:',
			0,
			1
		);


		$detailY = $y + 8;


		$pdf->SetXY(
			33,
			$detailY
		);

		$pdf->Cell(
			38,
			5,
			'Produk Asuransi',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$produk ='Reliance Pembiayaan Syariah';

		$pdf->Cell(
			95,
			5,
			$produk,
			0
		);


		$detailY += 6;

		$pdf->SetXY(
			33,
			$detailY
		);

		$pdf->Cell(
			38,
			5,
			'Manfaat Asuransi',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$pdf->Cell(
			95,
			5,
			'Menurun',
			0
		);


		$detailY += 6;

		$pdf->SetXY(
			33,
			$detailY
		);

		$pdf->Cell(
			38,
			5,
			'Masa Asuransi',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$term =
			!empty($member->term)
				? $member->term . ' Bulan'
				: '-';

		$pdf->Cell(
			95,
			5,
			$term,
			0
		);


		$detailY += 6;

		$pdf->SetXY(
			33,
			$detailY
		);

		$pdf->Cell(
			38,
			5,
			'Periode Asuransi',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$periode = '-';

		if (
			!empty($member->start_date) &&
			!empty($member->end_date)
		) {

			$periode =
				date(
					'd-M-y',
					strtotime($member->start_date)
				)
				.
				' s/d '
				.
				date(
					'd-M-y',
					strtotime($member->end_date)
				);
		}

		$pdf->Cell(
			95,
			5,
			$periode,
			0
		);

		$detailY += 6;

		$pdf->SetXY(
			33,
			$detailY
		);

		$pdf->Cell(
			38,
			5,
			'Uang Asuransi',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$sumInsured =
			!empty($member->sum_insured)
				? number_format(
					$member->sum_insured,
					0,
					',',
					'.'
				)
				: '0';

		$pdf->Cell(
			95,
			5,
			$sumInsured,
			0
		);


		$rightY = $y + 8;


		$pdf->SetXY(
			160,
			$rightY
		);

		$pdf->Cell(
			38,
			5,
			'Kontribusi Gross',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$grossPremium =
			!empty($member->gross_premium)
				? number_format(
					$member->gross_premium,
					0,
					',',
					'.'
				)
				: '0';

		$pdf->Cell(
			50,
			5,
			$grossPremium,
			0
		);


		$rightY += 6;

		$pdf->SetXY(
			160,
			$rightY
		);

		$pdf->Cell(
			38,
			5,
			'Extra Kontribusi',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$pdf->Cell(
			50,
			5,
			'-',
			0
		);


		$rightY += 6;

		$pdf->SetXY(
			160,
			$rightY
		);

		$pdf->SetFont(
			'Arial',
			'B',
			9
		);

		$totalPremium =
			!empty($member->total_premium)
				? $member->total_premium
				: $nettPremium;

		$totalPremium =
			number_format(
				$totalPremium,
				0,
				',',
				'.'
			);

		$pdf->Cell(
			38,
			5,
			'Total Kontribusi',
			0
		);

		$pdf->Cell(
			5,
			5,
			':',
			0
		);

		$pdf->Cell(
			50,
			5,
			$totalPremium,
			0
		);

		$pdf->SetFont(
			'Arial',
			'',
			5.5
		);

		$pdf->SetXY(
			33,
			174
		);

		$catatan =
			'Sertifikat ini tunduk pada Ketentuan Polis Asuransi serta ketentuan lain yang '
			. 'tercantum di dalam atau melekat pada Polis dan merupakan bagian yang tidak '
			. 'terpisahkan dari Perjanjian Asuransi.';

		$pdf->MultiCell(
			102,
			3,
			$catatan,
			1,
			'L'
		);

		$pdf->SetFont(
			'Arial',
			'I',
			5.5
		);

		$pdf->SetXY(
			33,
			190
		);

		$pdf->Cell(
			150,
			4,
			'*Sertifikat Asuransi ini berlaku apabila pembayaran sudah dilakukan dan efektif masuk ke dalam rekening PT Asuransi Jiwa Reliance Indonesia Unit Syariah',
			0
		);


		$tanggalBuka = date('d-M-y');

		if (!empty($member->tgl_buka)) {

			$tanggalBuka =
				date(
					'd-M-y',
					strtotime($member->tgl_buka)
				);
		}


		$pdf->SetFont(
			'Arial',
			'',
			8
		);

		$pdf->SetXY(
			205,
			145
		);

		$pdf->Cell(
			70,
			5,
			'Jakarta, ' . $tanggalBuka,
			0,
			1,
			'C'
		);


		$pdf->SetFont(
			'Arial',
			'B',
			8
		);

		$pdf->SetX(
			205
		);

		$pdf->Cell(
			70,
			5,
			'PT Asuransi Jiwa Reliance Indonesia Unit Syariah',
			0,
			1,
			'C'
		);


		$signature =
			Yii::getAlias(
				'@webroot/uploads/assets/signature.png'
			);

		if (file_exists($signature)) {

			$pdf->Image(
				$signature,
				228,
				155,
				28,
				20
			);

		} else {

			$pdf->SetXY(
				205,
				158
			);

			$pdf->Cell(
				70,
				20,
				'',
				0,
				1,
				'C'
			);
		}


		$pdf->SetFont(
			'Arial',
			'B',
			8
		);

		$pdf->SetXY(
			205,
			178
		);

		$pdf->Cell(
			70,
			5,
			'Gideon Heru Prasetya',
			0,
			1,
			'C'
		);


		$pdf->SetFont(
			'Arial',
			'',
			8
		);

		$pdf->SetXY(
			205,
			183
		);

		$pdf->Cell(
			70,
			5,
			'Direktur Utama',
			0,
			1,
			'C'
		);


		$pdf->SetDrawColor(
			180,
			180,
			180
		);

		$pdf->Line(
			6,
			198,
			291,
			198
		);


		$pdf->SetFont(
			'Arial',
			'B',
			7
		);

		$pdf->SetXY(
			6,
			200
		);

		$pdf->Cell(
			70,
			4,
			'PT Asuransi Jiwa Reliance Indonesia',
			0,
			1
		);


		$pdf->SetFont(
			'Arial',
			'',
			6.5
		);

		$pdf->SetXY(
			6,
			204
		);

		$pdf->Cell(
			70,
			3,
			'Gedung Soho West Point, Kota Kedoya',
			0,
			1
		);

		$pdf->SetXY(
			6,
			207
		);

		$pdf->Cell(
			70,
			3,
			'Jl. Macan, Kav. 4-5, Kedoya Utara, Kebon Jeruk',
			0,
			1
		);


		$pdf->SetXY(
			78,
			202
		);

		$pdf->Cell(
			55,
			4,
			'Tel. +62 21 2119 9444 (Hotline)',
			0,
			1
		);


		$pdf->SetXY(
			136,
			202
		);

		$pdf->Cell(
			55,
			4,
			'https://reliance-life.co.id',
			0,
			1
		);

		$pdf->SetXY(
			136,
			206
		);

		$pdf->Cell(
			55,
			4,
			'info@reliance-life.co.id',
			0,
			1
		);


		$pdf->SetXY(
			194,
			204
		);

		$pdf->Cell(
			45,
			4,
			'Member of Reliance Group',
			0,
			1
		);


		$pdf->SetFont(
			'Arial',
			'B',
			7
		);

		$pdf->SetXY(
			250,
			204
		);

		$pdf->Cell(
			40,
			4,
			'your reliable partner',
			0,
			1,
			'R'
		);


		$pdf->Output(
			'F',
			$pdfPath
		);
		
		$zip = new \ZipArchive();

		if ($zip->open(
			$zipPath,
			\ZipArchive::CREATE | \ZipArchive::OVERWRITE
		) !== true) {

			throw new \Exception(
				'Gagal membuat file ZIP: ' . $zipPath
			);
		}
		
		if (!$zip->addFile(
			$pdfPath,
			$pdfFileName
		)) {

			$zip->close();

			throw new \Exception(
				'Gagal memasukkan PDF ke dalam ZIP'
			);
		}

		$zip->close();
		
		if (!file_exists($zipPath)) {

			throw new \Exception(
				'File ZIP tidak berhasil dibuat'
			);
		}
		
		if (file_exists($pdfPath)) {
			unlink($pdfPath);
		}


		return [
			'file_name' => $zipFileName,

			'file_path' => $zipPath,

			 'file_url' =>
				Yii::$app->request->hostInfo .
				Yii::$app->request->baseUrl .
				'/uploads/incoming/' .
				$zipFileName,
		];
	}

	
	public function actionSubmitRestitusi()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$request = Yii::$app->request;


		$authorization = $request->headers->get('Authorization');

		if (!$authorization) {
			return [
				'Result' => [
					'status' => '401',
					'kode_response' => '01',
					'message' => 'Authorization header tidak ditemukan'
				]
			];
		}


		$body = $request->getBodyParams();

		if (empty($body)) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Request body tidak boleh kosong'
				]
			];
		}


		$requiredFields = [
			'id_transaksi',
			'id_pengajuan',
			'kode_broker',
			'kode_cabang',
			'nomor_rekening',
			'tanggal_pembiayaan',
			'old_nomor_akad',
			'nomor_akad',
			'plafond_pembiayaan',
			'tenor',
			'benefit',
			'restitusi_jiwa'
		];

		foreach ($requiredFields as $field) {

			if (
				!array_key_exists($field, $body) ||
				$body[$field] === null ||
				$body[$field] === ''
			) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' => "Field {$field} wajib diisi"
					]
				];
			}
		}

		$restitusiJiwa = $body['restitusi_jiwa'];

		if (!is_array($restitusiJiwa)) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Format restitusi_jiwa tidak valid'
				]
			];
		}

		$requiredRestitusiFields = [
			'plafon_penjaminan',
			'tenor_berjalan',
			'sisa_tenor',
			'premi',
			'asuransi',
			'tujuan_pembayaran'
		];

		foreach ($requiredRestitusiFields as $field) {

			if (
				!array_key_exists($field, $restitusiJiwa) ||
				$restitusiJiwa[$field] === null ||
				$restitusiJiwa[$field] === ''
			) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' => "Field restitusi_jiwa.{$field} wajib diisi"
					]
				];
			}
		}

		$tanggalPembiayaan = $body['tanggal_pembiayaan'];

		$date = \DateTime::createFromFormat(
			'Ymd',
			$tanggalPembiayaan
		);

		if (
			!$date ||
			$date->format('Ymd') !== $tanggalPembiayaan
		) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Format tanggal_pembiayaan harus YYYYMMDD'
				]
			];
		}

		$tanggalPembiayaanDb = $date->format('Y-m-d');


		$restitusiJiwaJson = json_encode(
			$restitusiJiwa,
			JSON_UNESCAPED_UNICODE
		);

		if ($restitusiJiwaJson === false) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Gagal memproses data restitusi_jiwa'
				]
			];
		}


		$transaction = Yii::$app->db->beginTransaction();

		try {

			$model = new Restitusi();


			$model->id_transaksi = $body['id_transaksi'];

			$model->id_pengajuan = $body['id_pengajuan'];

			$model->kode_broker = $body['kode_broker'];

			$model->kode_cabang = $body['kode_cabang'];

			$model->nomor_rekening = $body['nomor_rekening'];

			$model->tanggal_pembiayaan = $tanggalPembiayaanDb;

			$model->old_nomor_akad = $body['old_nomor_akad'];

			$model->nomor_akad = $body['nomor_akad'];

			$model->plafon_pembiayaan =
				$body['plafond_pembiayaan'];

			$model->tenor = $body['tenor'];

			$model->benefit = $body['benefit'];

			$model->restitusi_jiwa = $restitusiJiwaJson;

			$model->plafon_penjaminan =
				$restitusiJiwa['plafon_penjaminan'];

			$model->tenor_berjalan =
				$restitusiJiwa['tenor_berjalan'];

			$model->sisa_tenor =
				$restitusiJiwa['sisa_tenor'];

			$model->premi =
				$restitusiJiwa['premi'];

			$model->asuransi =
				$restitusiJiwa['asuransi'];

			$model->tujuan_pembayaran =
				$restitusiJiwa['tujuan_pembayaran'];
			$model->created_at =date('Y-m-d H:i:s');


			$model->status_restitusi = '1';

			if (!$model->save()) {

				$transaction->rollBack();

				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' =>
							'Gagal menyimpan data pengajuan restitusi',
						'jenis_pengajuan' => 'RESTITUSI',
						'restitusi_jiwa' => [
							'status_restitusi' => '0'
						],
						'errors' => $model->getErrors()
					]
				];
			}

			$transaction->commit();

			return [
				'Result' => [
					'status' => '200',
					'kode_response' => '00',
					'message' =>
						'Berhasil kirim data pengajuan restitusi',
					'jenis_pengajuan' => 'RESTITUSI',
					'restitusi_jiwa' => [
						'status_restitusi' => '1'
					]
				]
			];

		} catch (\Exception $e) {

			if ($transaction->getIsActive()) {
				$transaction->rollBack();
			}

			return [
				'Result' => [
					'status' => '500',
					'kode_response' => '99',
					'message' => $e->getMessage(),
					'jenis_pengajuan' => 'RESTITUSI',
					'restitusi_jiwa' => [
						'status_restitusi' => '0'
					]
				]
			];
		}
	}

	public function actionSubmitClaim()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$request = Yii::$app->request;

	
		$authorization = $request->headers->get('Authorization');

		if (!$authorization) {
			return [
				'Result' => [
					'status' => '401',
					'kode_response' => '01',
					'message' => 'Authorization header tidak ditemukan'
				]
			];
		}

		$body = $request->getBodyParams();

		if (empty($body)) {
			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Request body tidak boleh kosong'
				]
			];
		}

		$requiredFields = [
			'id_transaksi',
			'id_pengajuan',
			'kode_broker',
			'ktp',
			'nama',
			'kode_cabang',
			'nomor_rekening',
			'no_akad',
			'tenor',
			'premi',
			'periode_awal',
			'periode_akhir',
			'tenor_berjalan',
			'sisa_tenor',
			'benefit',
			'jenis_klaim',
			'penyebab_klaim',
			'tanggal_kejadian',
			'tempat_kejadian',
			'jumlah_diajukan',
			'tujuan_pembayaran',
			'tanggal_kirim'
		];

		foreach ($requiredFields as $field) {

			if (
				!array_key_exists($field, $body) ||
				$body[$field] === null ||
				$body[$field] === ''
			) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' => "Field {$field} wajib diisi"
					]
				];
			}
		}


		$dateFields = [
			'periode_awal',
			'periode_akhir',
			'tanggal_kejadian',
			'tanggal_kirim'
		];

		$dateValues = [];

		foreach ($dateFields as $field) {

			$value = $body[$field];

			$date = \DateTime::createFromFormat('Ymd', $value);

			if (
				!$date ||
				$date->format('Ymd') !== $value
			) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' => "Format {$field} harus YYYYMMDD"
					]
				];
			}

			$dateValues[$field] = $date->format('Y-m-d');
		}


		$idAgunan = isset($body['id_agunan'])
			? trim($body['id_agunan'])
			: '';

		$nomorBukti = isset($body['nomor_bukti'])
			? trim($body['nomor_bukti'])
			: '';

		if ($body['benefit'] == '4') {

			if ($idAgunan === '') {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' => 'Field id_agunan wajib diisi jika benefit = 4 (Kebakaran)'
					]
				];
			}

			if ($nomorBukti === '') {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' => 'Field nomor_bukti wajib diisi jika benefit = 4 (Kebakaran)'
					]
				];
			}
		}

		$transaction = Yii::$app->db->beginTransaction();

		try {


			$model = new \app\models\claim_riau();

			$model->id_transaksi =
				$body['id_transaksi'];

			$model->id_pengajuan =
				$body['id_pengajuan'];

			$model->kode_broker =
				$body['kode_broker'];

			$model->ktp =
				$body['ktp'];

			$model->nama =
				$body['nama'];

			$model->kode_cabang =
				$body['kode_cabang'];

			$model->nomor_rekening =
				$body['nomor_rekening'];

			$model->no_akad =
				$body['no_akad'];

			$model->tenor =
				$body['tenor'];

			$model->premi =
				$body['premi'];

			$model->periode_awal =
				$dateValues['periode_awal'];

			$model->periode_akhir =
				$dateValues['periode_akhir'];

			$model->tenor_berjalan =
				$body['tenor_berjalan'];

			$model->sisa_tenor =
				$body['sisa_tenor'];

			$model->benefit =
				$body['benefit'];

			$model->id_agunan =
				$idAgunan !== '' ? $idAgunan : null;

			$model->nomor_bukti =
				$nomorBukti !== '' ? $nomorBukti : null;

			$model->jenis_klaim =
				$body['jenis_klaim'];

			$model->penyebab_klaim =
				$body['penyebab_klaim'];

			$model->tanggal_kejadian =
				$dateValues['tanggal_kejadian'];

			$model->tempat_kejadian =
				$body['tempat_kejadian'];

			$model->jumlah_diajukan =
				$body['jumlah_diajukan'];

			$model->tujuan_pembayaran =
				$body['tujuan_pembayaran'];

			$model->tanggal_kirim =
				$dateValues['tanggal_kirim'];
				
			$model->created_at =date('Y-m-d H:i:s');


			if (!$model->save()) {

				$transaction->rollBack();

				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '01',
						'message' => 'Gagal menyimpan data pengajuan claim',
						'status_claim' => '0',
						'errors' => $model->getErrors()
					]
				];
			}


			$transaction->commit();


			return [
				'Result' => [
					'status' => '200',
					'kode_response' => '00',
					'message' => 'Berhasil kirim data pengajuan claim',
					'status_claim' => '1'
				]
			];

		} catch (\Exception $e) {

			if ($transaction->getIsActive()) {
				$transaction->rollBack();
			}

			return [
				'Result' => [
					'status' => '500',
					'kode_response' => '99',
					'message' => $e->getMessage(),
					'status_claim' => '0'
				]
			];
		}
	}


}
