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

class CalculateController extends Controller
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
	
	
	public function actionPremi()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$request = Yii::$app->request;
		$body = $request->getBodyParams();
		if (empty($body)) {
			$rawBody = $request->getRawBody();
			$body = json_decode($rawBody, true);
		}

		$idTransaksi       = $body['id_transaksi'] ?? null;
		$kodeBroker        = $body['kode_broker'] ?? null;
		$pekerjaan         = $body['pekerjaan'] ?? null;
		$tanggalLahir      = $body['tgl_lahir'] ?? null;
		$jenisPembiayaan   = $body['jenis_pembiayaan'] ?? null;
		$benefit           = $body['benefit'] ?? null;
		$benefitPembiayaan = $body['benefit_pembiayaan'] ?? null;
		$coverage          = $body['coverage'] ?? null;

		$premiJiwa = $body['premi_jiwa'] ?? [];

		$uangPertanggungan = $premiJiwa['plafon_pertanggungan'] ?? null;
		$tenor             = $premiJiwa['tenor_pertanggungan'] ?? null;

		if (
			empty($idTransaksi) ||
			empty($kodeBroker) ||
			empty($pekerjaan) ||
			empty($tanggalLahir) ||
			empty($jenisPembiayaan) ||
			empty($benefit) ||
			empty($coverage) ||
			empty($uangPertanggungan) ||
			empty($tenor)
		) {
			Yii::$app->response->statusCode = 400;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '01',
					'message' => 'Parameter tidak lengkap.'
				]
			];
		}

		$tanggalLahirFormatted = \DateTime::createFromFormat(
			'Ymd',
			$tanggalLahir
		);

		if (!$tanggalLahirFormatted) {
			Yii::$app->response->statusCode = 400;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '02',
					'message' => 'Format tgl_lahir tidak valid. Gunakan YYYYMMDD.'
				]
			];
		}

		$tanggalLahir = $tanggalLahirFormatted->format('Y-m-d');

		// if ($pekerjaan == '01') {
			// $produk = 'Karyawan Kepri';
		// } else {
			// $produk = 'pegawai aktif';
		// }

		$policybyproduk = Policy::findOne([
			'produk_code' => $pekerjaan,
		]);

		if ($policybyproduk === null) {
			Yii::$app->response->statusCode = 400;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '03',
					'message' => 'Produk/policy tidak ditemukan.'
				]
			];
		}
		$quotation = Quotation::findOne([
			'id' => $policybyproduk->quotation_id,
		]);

		if ($quotation === null) {
			Yii::$app->response->statusCode = 400;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '03',
					'message' => 'Quotation tidak ditemukan.'
				]
			];
		}
		
		$age = Member::getAge(
			$quotation->age_calculate,
			$tanggalLahir,
			date('Y-m-d')
		);
		
		$quotationUwLimit = QuotationUwLimit::find()
						->where(['quotation_id' => $policybyproduk->quotation_id])
						->andWhere(['<=', 'min_age', $age])
						->andWhere(['>=', 'max_age', $age])
						->andWhere(['<=', 'min_si', $uangPertanggungan])
						->andWhere(['>=', 'max_si', $uangPertanggungan])
						->one();
		$medicalCode = $quotationUwLimit->medical_code;				

		$term = (int)$tenor;

		$termYear = $term / 12;

		$endAge = $age + $termYear;
		
		if ($endAge > 65) {

			Yii::$app->response->statusCode = 400;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '04',
					'message' => 'Usia pada akhir masa pertanggungan tidak boleh melebihi 65 tahun.'
				]
			];
		}

		$ratepremi = QuotationRate::findOne([
			'term' => $term,
			'quotation_id' => $policybyproduk->quotation_id
		]);
		
		// var_dump($ratepremi);

		if ($ratepremi === null) {

			Yii::$app->response->statusCode = 400;

			return [
				'Result' => [
					'status' => '400',
					'kode_response' => '05',
					'message' => 'Rate premi untuk tenor tersebut tidak ditemukan.'
				]
			];
		}

		$uangPertanggungan = (int)$uangPertanggungan;
		$rate = (float)$ratepremi->rate;
		$totalPremium = $uangPertanggungan * $rate / 1000;
		$roundPremi = round($totalPremium, 0);
		Yii::$app->response->statusCode = 200;

		return [
			'Result' => [
				'status' => '200',
				'kode_response' => '00',
				'message' => 'Berhasil',
				'kode_broker' => (string)$kodeBroker,
				'jenis_pengajuan' => '2',
				'coverage' => (string)$coverage,
				'asuransi_jiwa' => [
					'premi' => (string)$roundPremi,
					'rate' => $rate,
					'medicalCode' => $medicalCode
				]
			]
		];
	}
}

