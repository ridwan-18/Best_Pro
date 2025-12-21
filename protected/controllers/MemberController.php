<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Batch;
use app\models\Member;
use app\models\Personal;
use app\models\Policy;
use app\models\Partner;
use app\models\Quotation;
use app\models\QuotationCommission;
use app\models\QuotationProduct;
use app\models\QuotationRate;
use app\models\ProductEm;
use app\models\Product;
use app\models\Billing;
use app\models\PeriodType;
use app\models\ProductRateType;
use app\models\QuotationTc;
use app\models\QuotationUwLimit;
use app\models\RateType;
use app\models\Utils;
use app\models\Signature;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\UploadedFile;
use Da\QrCode\QrCode;
use yii\helpers\Url;

/**
 * MemberController implements the CRUD actions for Member model.
 */
class MemberController extends Controller
{
	/**
	 * @inheritDoc
	 */
	public function behaviors()
	{
		return array_merge(
			parent::behaviors(),
			[
				'verbs' => [
					'class' => VerbFilter::class,
					'actions' => [
						'delete' => ['POST'],
					],
				],
			]
		);
	}

	/**
	 * Lists all Member models.
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$params = [
			'policy_no' => Yii::$app->request->get('policy_no'),
			'batch_no' => Yii::$app->request->get('batch_no'),
			'status' => Yii::$app->request->get('status'),
		];

		$totalModel = Batch::countAll($params);

		$pagination = new Pagination([
			'totalCount' => $totalModel,
			'pageSize' => Batch::PAGE_SIZE,
			'pageSizeParam' => false,
		]);

		$params = array_merge($params, [
			'offset' => $pagination->offset,
			'limit' => $pagination->limit,
			'sort' => SORT_DESC,
		]);

		 $models = Batch::getAll($params);
		// $user = $app->user->identity->id;
		// $models = Batch::getAll($params, [
			// 'created_by' => $user,
			
		// ]);
		
		// var_dump($models);
		
		$members = Member::getAll([
			'policy_no' => $models->policy_no,
			'batch_no' => $models->batch_no,
		]);
		
		 // var_dump($members);
		
		return $this->render('index', [
			'models' => $models,
			'pagination' => $pagination,
			'members' => $members,
		]);
	}

	public function actionTemplate()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'No');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Branch');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'ID Coas');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Name');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Member No');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Date of Birth');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Entry Age');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'Gender');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', 'Term Month');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', 'Start Date');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', 'End Date');
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'Sum Insured');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', 'PK No');
		$objPHPExcel->getActiveSheet()->setCellValue('N1', 'Account No');
		$objPHPExcel->getActiveSheet()->setCellValue('O1', 'Bank Branch');
		$objPHPExcel->getActiveSheet()->setCellValue('P1', 'Branch Code');
		$objPHPExcel->getActiveSheet()->setCellValue('Q1', 'Identity No');
		$objPHPExcel->getActiveSheet()->setCellValue('R1', 'Email');
		$objPHPExcel->getActiveSheet()->setCellValue('S1', 'Phone');
		$objPHPExcel->getActiveSheet()->setCellValue('T1', 'Address');
		$objPHPExcel->getActiveSheet()->setCellValue('U1', 'City');
		$objPHPExcel->getActiveSheet()->setCellValue('V1', 'Province');

		$objPHPExcel->getActiveSheet()->getComment('D1')->getText()->createTextRun('Name cannot be blank');
		$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getComment('F1')->getText()->createTextRun('Set Format Cell to date');
		$objPHPExcel->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F:F')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
		$objPHPExcel->getActiveSheet()->getComment('J1')->getText()->createTextRun('Set Format Cell to date');
		$objPHPExcel->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('J:J')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
		$objPHPExcel->getActiveSheet()->getComment('K1')->getText()->createTextRun('Set Format Cell to date');
		$objPHPExcel->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('K:K')->getNumberFormat()->setFormatCode('yyyy-mm-dd');
		$objPHPExcel->getActiveSheet()->getComment('L1')->getText()->createTextRun('Sum Insured must be a number');
		$objPHPExcel->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="member-template.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter->save('php://output');
		exit;
	}

	public function actionPrint($id)
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$this->layout = '/print';

		$memberStatus = Yii::$app->request->get('member_status');

		$batch = Batch::findOne(['id' => $id]);
		$policy = Policy::findOne(['policy_no' => $batch->policy_no]);
		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		$partner = Partner::findOne(['id' => $quotation->partner_id]);
		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		$product = Product::findOne(['id' => $quotationProduct->product_id]);

		$members = Member::getAll([
			'policy_no' => $batch->policy_no,
			'batch_no' => $batch->batch_no,
			'member_status' => $memberStatus,
		]);

		$totalMember = Member::countAll([
			'policy_no' => $batch->policy_no,
			'batch_no' => $batch->batch_no,
			'member_status' => $memberStatus,
		]);

		$signature = Signature::findOne(['id' => 1]);

		$qrCodeFilename = 'policy-' . $id . '.png';
		$qrCode = (new QrCode(Url::base(true) . '/member/print-signature/?id=' . $id . '&member_status=' . $memberStatus))
			->setSize(75)
			->setMargin(5);
		$qrCode->writeFile(\Yii::getAlias('@webroot') . '/uploads/signature/' . $qrCodeFilename);
		$qrCodeUrl = Url::base() . Signature::PICTURE_PATH . $qrCodeFilename;

		$page = 'print-pending';
		if (Yii::$app->request->get('member_status') == Member::MEMBER_STATUS_INFORCE) {
			$page = 'print-inforce';
		} else if (Yii::$app->request->get('member_status') == Member::MEMBER_STATUS_DECLINED) {
			$page = 'print-declined';
		}

		return $this->render($page, [
			'batch' => $batch,
			'partner' => $partner,
			'product' => $product,
			'quotation' => $quotation,
			'members' => $members,
			'totalMember' => $totalMember,
			'memberStatus' => $memberStatus,
			'signature' => $signature,
			'qrCodeUrl' => $qrCodeUrl,
		]);
	}

	public function actionPrintSignature($id)
	{
		$this->layout = '/print';

		$memberStatus = Yii::$app->request->get('member_status');

		$batch = Batch::findOne(['id' => $id]);
		$policy = Policy::findOne(['policy_no' => $batch->policy_no]);
		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		$partner = Partner::findOne(['id' => $quotation->partner_id]);
		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		$product = Product::findOne(['id' => $quotationProduct->product_id]);

		$members = Member::getAll([
			'policy_no' => $batch->policy_no,
			'batch_no' => $batch->batch_no,
			'member_status' => $memberStatus,
		]);

		$totalMember = Member::countAll([
			'policy_no' => $batch->policy_no,
			'batch_no' => $batch->batch_no,
			'member_status' => $memberStatus,
		]);

		$signature = Signature::findOne(['id' => 1]);

		$page = 'print-pending-signature';
		if (Yii::$app->request->get('member_status') == Member::MEMBER_STATUS_INFORCE) {
			$page = 'print-inforce-signature';
		} else if (Yii::$app->request->get('member_status') == Member::MEMBER_STATUS_DECLINED) {
			$page = 'print-declined-signature';
		}

		return $this->render($page, [
			'batch' => $batch,
			'partner' => $partner,
			'product' => $product,
			'quotation' => $quotation,
			'members' => $members,
			'totalMember' => $totalMember,
			'memberStatus' => $memberStatus,
			'signature' => $signature,
		]);
	}

	public function actionPrintTest()
	{
		$this->layout = '/print';

		$idLoan = Yii::$app->request->get('id_loan');
		
		$batch = Batch::findOne(['id' => $id]);
		$policy = Policy::findOne(['policy_no' => $batch->policy_no]);
		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		$partner = Partner::findOne(['id' => $quotation->partner_id]);
		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		$product = Product::findOne(['id' => $quotationProduct->product_id]);

		$member = Member::findOne(['id_loan' => $idLoan]);
		$personal = personal :: findOne(['personal_no' => $member->personal_no]);

		$signature = Signature::findOne(['id' => 1]);

		$qrCodeFilename = 'policy-' . $id . '.png';
		$qrCode = (new QrCode(Url::base(true) . '/member/print-signature/?id=' . $id . '&member_status=' . $memberStatus))
			->setSize(75)
			->setMargin(5);
		$qrCode->writeFile(\Yii::getAlias('@webroot') . '/uploads/signature/' . $qrCodeFilename);
		$qrCodeUrl = Url::base() . Signature::PICTURE_PATH . $qrCodeFilename;

		return $this->render('print-test', [
			'member' => $member,
			'signature' => $signature,
			'qrCodeUrl' => $qrCodeUrl,
			'personal' => $personal
		]);
	}
	
	public function actionPrintSertifikat()
	{
		$this->layout = '/print';

		$idLoan = Yii::$app->request->get('id_loan');
		
		$batch = Batch::findOne(['id' => $id]);
		$policy = Policy::findOne(['policy_no' => $batch->policy_no]);
		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		$partner = Partner::findOne(['id' => $quotation->partner_id]);
		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		$product = Product::findOne(['id' => $quotationProduct->product_id]);

		$member = Member::findOne(['id_loan' => $idLoan]);
		$personal = personal :: findOne(['personal_no' => $member->personal_no]);

		$signature = Signature::findOne(['id' => 1]);

		$qrCodeFilename = 'policy-' . $id . '.png';
		$qrCode = (new QrCode(Url::base(true) . '/member/print-signature/?id=' . $id . '&member_status=' . $memberStatus))
			->setSize(75)
			->setMargin(5);
		$qrCode->writeFile(\Yii::getAlias('@webroot') . '/uploads/signature/' . $qrCodeFilename);
		$qrCodeUrl = Url::base() . Signature::PICTURE_PATH . $qrCodeFilename;

		return $this->render('print', [
			'member' => $member,
			'signature' => $signature,
			'qrCodeUrl' => $qrCodeUrl,
			'personal' => $personal
		]);
	}

	public function actionExport($id)
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$memberStatus = Yii::$app->request->get('member_status');

		$batch = Batch::findOne(['id' => $id]);

		$members = Member::getAll([
			'policy_no' => $batch->policy_no,
			'batch_no' => $batch->batch_no,
			'member_status' => $memberStatus,
		]);

		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'NO PESERTA');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'NAMA PESERTA');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'TGL LAHIR');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'USIA');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'TGL MULAI');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'TGL AKHIR');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'UP AS POKOK');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', 'PREMI');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', 'EM');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', 'TOTAL PREMI');

		$baseRow = 2;
		$i = 1;
		foreach ($members as $member) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $baseRow, $i);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $baseRow, $member['member_no']);
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $baseRow, $member['name']);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $baseRow, $member['birth_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $baseRow, $member['age']);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $baseRow, $member['start_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $baseRow, $member['end_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $baseRow, $member['sum_insured']);
			$objPHPExcel->getActiveSheet()->setCellValue('I' . $baseRow, $member['gross_premium']);
			$objPHPExcel->getActiveSheet()->setCellValue('J' . $baseRow, $member['em_premium']);
			$objPHPExcel->getActiveSheet()->setCellValue('K' . $baseRow, $member['nett_premium']);

			$baseRow++;
			$i++;
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="member-export.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter->save('php://output');
		exit;
	}

	public function actionExportAccumulation($id)
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$member = Member::findOne(['id' => $id]);
		$personal = Personal::findOne(['id' => $member->personal_no]);
		if ($member == null || $personal == null) {
			Yii::$app->session->setFlash('error', "Not found");
			return $this->redirect(['index']);
		}

		$params = [
			'policy_no' => $member->policy_no,
			'name' => $personal->name,
			'birth_date' => $personal->birth_date
		];
		$members = Member::getAll($params);

		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'NO');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'NO POLIS');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'NO BATCH');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'NO PESERTA');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'NAMA PESERTA');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'TGL LAHIR');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'USIA');
		$objPHPExcel->getActiveSheet()->setCellValue('H1', 'TGL MULAI');
		$objPHPExcel->getActiveSheet()->setCellValue('I1', 'TGL AKHIR');
		$objPHPExcel->getActiveSheet()->setCellValue('J1', 'UP AS POKOK');
		$objPHPExcel->getActiveSheet()->setCellValue('K1', 'PREMI');
		$objPHPExcel->getActiveSheet()->setCellValue('L1', 'EM');
		$objPHPExcel->getActiveSheet()->setCellValue('M1', 'TOTAL PREMI');

		$baseRow = 2;
		$i = 1;
		foreach ($members as $member) {
			$objPHPExcel->getActiveSheet()->setCellValue('A' . $baseRow, $i);
			$objPHPExcel->getActiveSheet()->setCellValue('B' . $baseRow, $member['policy_no']);
			$objPHPExcel->getActiveSheet()->setCellValue('C' . $baseRow, $member['batch_no']);
			$objPHPExcel->getActiveSheet()->setCellValue('D' . $baseRow, $member['member_no']);
			$objPHPExcel->getActiveSheet()->setCellValue('E' . $baseRow, $member['name']);
			$objPHPExcel->getActiveSheet()->setCellValue('F' . $baseRow, $member['birth_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('G' . $baseRow, $member['age']);
			$objPHPExcel->getActiveSheet()->setCellValue('H' . $baseRow, $member['start_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('I' . $baseRow, $member['end_date']);
			$objPHPExcel->getActiveSheet()->setCellValue('J' . $baseRow, $member['sum_insured']);
			$objPHPExcel->getActiveSheet()->setCellValue('K' . $baseRow, $member['gross_premium']);
			$objPHPExcel->getActiveSheet()->setCellValue('L' . $baseRow, $member['em_premium']);
			$objPHPExcel->getActiveSheet()->setCellValue('M' . $baseRow, $member['nett_premium']);

			$baseRow++;
			$i++;
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="member-accumulation.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * Displays a single Member model.
	 * @param int $id ID
	 * @return string
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id)
	{
		
	
		
		$user= Yii::$app->user->identity->id;
		// echo $user;
		$batch = Batch::findOne(['id' => $id]);
		$policy = Policy::findOne(['policy_no' => $batch->policy_no]);
		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		$product = Product::findOne(['id' => $quotationProduct->product_id]);
		$partner = Partner::findOne(['id' => $policy->partner_id]);
		$personals = $this->findPersonal($batch->policy_no, $batch->batch_no);

		$params = [
			'policy_no' => $batch->policy_no,
			'batch_no' => $batch->batch_no,
			'member_id' => Yii::$app->request->get('member_id'),
			'birth_date' => Yii::$app->request->get('birth_date'),
			'start_date' => Yii::$app->request->get('start_date'),
			'end_date' => Yii::$app->request->get('end_date'),
			'status' => Yii::$app->request->get('status'),
			'member_status' => Yii::$app->request->get('member_status'),
			'reas_status' => Yii::$app->request->get('reas_status'),
			'is_accumulated' => Yii::$app->request->get('is_accumulated'),
			'total_show' => Yii::$app->request->get('total_show'),
		];

		$totalMember = Member::countAll($params);

		$pageSize = ($params['total_show'] > 100) ? Member::PAGE_SIZE : $params['total_show'];
		$pagination = new Pagination([
			'totalCount' => $totalMember,
			'pageSize' => $pageSize,
			'pageSizeParam' => false,
		]);

		$params = array_merge($params, [
			'offset' => $pagination->offset,
			'limit' => $pagination->limit,
			'sort' => SORT_ASC,
		]);

		$members = Member::getAll($params);

		return $this->render('view', [
			'batch' => $batch,
			'product' => $product,
			'members' => $members,
			'partner' => $partner,
			'personals' => $personals,
			'pagination' => $pagination,
			'user' => $user,
		]);
	}

	public function actionAccumulation($id)
	{
		$this->layout = 'clean';

		$member = Member::findOne(['id' => $id]);
		$personal = Personal::findOne(['personal_no' => $member->personal_no]);
		if ($member == null || $personal == null) {
			Yii::$app->session->setFlash('error', "Not found");
			return $this->redirect(['index']);
		}

		$params = [
			'policy_no' => $member->policy_no,
			'name' => $personal->name,
			'birth_date' => $personal->birth_date
		];
		$members = Member::getAccumulation($params);

		return $this->render('accumulation', [
			'member' => $member,
			'personal' => $personal,
			'members' => $members,
		]);
	}

	/**
	 * Creates a new Member model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return string|\yii\web\Response
	 */
	public function actionCreate()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		return $this->render('create');
	}

	public function actionUpload()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$policyNo = Yii::$app->request->post('policy_no');
		$file = UploadedFile::getInstanceByName('file');

		$policy = Policy::findOne(['policy_no' => $policyNo]);
		if ($policy == null) {
			Yii::$app->session->setFlash('error', "Policy not found");
			return $this->redirect(['create']);
		}

		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		if ($quotation == null) {
			Yii::$app->session->setFlash('error', "Quotation not found");
			return $this->redirect(['create']);
		}

		$quotationCommission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
		if ($quotationCommission == null) {
			Yii::$app->session->setFlash('error', "Quotation Commission not found");
			return $this->redirect(['create']);
		}

		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		if ($quotationProduct == null) {
			Yii::$app->session->setFlash('error', "Quotation Product not found");
			return $this->redirect(['create']);
		}

		$currentDate = new \DateTime();
		$createdAt = $currentDate->format('Y-m-d H:i:s');
		$createdBy = Yii::$app->user->identity->id;

		$batch = Batch::find()->where(['policy_no' => $policyNo])->orderBy(['id' => SORT_DESC])->one();
		if ($batch != null) {
			$batchNo = (int)$batch->batch_no + 1;
		} else {
			$batchNo = 1;
		}

		$inputFileType = \PHPExcel_IOFactory::identify($file->tempName);
		$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($file->tempName);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

		$baseRow = 2;
		$members = [];
		$totalMember = 0;
		$totalUp = 0;
		$totalGrossPremium = 0;
		$totalDiscountPremium = 0;
		$totalExtraPremium = 0;
		$totalSavingPremium = 0;
		$totalNettPremium = 0;
		while (!empty($sheetData[$baseRow]['D'])) {
			$birthDate = Utils::trueBirthDate($sheetData[$baseRow]['F']);
			$sumInsured = Utils::removeComma($sheetData[$baseRow]['L']);
			$personal = new Personal();
			$personal->name = $sheetData[$baseRow]['D'];
			$personal->birth_date = $birthDate;
			$personal->personal_no = Personal::generatePersonalNo($personal->name, $personal->birth_date);
			$personal->gender = $sheetData[$baseRow]['H'];
			$personal->id_card_no = $sheetData[$baseRow]['Q'];
			$personal->phone = $sheetData[$baseRow]['S'];
			$personal->email = $sheetData[$baseRow]['R'];
			$personal->address = $sheetData[$baseRow]['T'];
			$personal->province = $sheetData[$baseRow]['V'];
			$personal->city = $sheetData[$baseRow]['U'];
			if ($personal->save(false)) {
				$startDate = Utils::convertDateToYmd($sheetData[$baseRow]['J']);
				$endDate = Utils::convertDateToYmd($sheetData[$baseRow]['K']);

				$age = Member::getAge($quotation->age_calculate, $birthDate, $startDate);
				$term = Member::getTerm($quotation->rate_type, $startDate, $endDate);

				if ($quotation->rate_type == RateType::RATE_ROUND_UP) {
					$termYear = ceil($term / 12);
				} else {
					$termYear = floor($term / 12);
				}
				
				if ($quotationProduct->rate_type == ProductRateType::AGE_TERM) {
					$quotationRate = QuotationRate::findOne([
						'quotation_id' => $policy->quotation_id,
						'age' => $age,
						'term' => $term
					]);
				} else {
					$quotationRate = QuotationRate::findOne([
						'quotation_id' => $policy->quotation_id,
						'term' => ($quotationProduct->period_type == PeriodType::ANNUALLY) ? $termYear : $term
					]);
				}
				
				// var_dump($quotationRate);

				$quotationTc = QuotationTc::findOne([
					'quotation_id' => $policy->quotation_id,
				]);

				$quotationUwLimit = QuotationUwLimit::find()
					->where(['quotation_id' => $policy->quotation_id])
					->andWhere(['<=', 'min_age', $age])
					->andWhere(['>=', 'max_age', $age])
					->andWhere(['<=', 'min_si', $sumInsured])
					->andWhere(['>=', 'max_si', $sumInsured])
					->one();

				$stncDate = Member::getStnc($startDate, $quotationTc->retroactive);
				$totalPremium = $sumInsured * $quotationRate->rate / 1000;
				$grossPremium = $totalPremium;
				$basicPremium = $totalPremium;
				$discount = $totalPremium * $quotationCommission->discount / 100;
				$nettPremium = $totalPremium - $discount;

				$status = Member::MEMBER_STATUS_PENDING;
				if ($quotationUwLimit->medical_code == 'GOA' || $quotationUwLimit->medical_code == 'FC') {
					$status = Member::MEMBER_STATUS_INFORCE;
				}

				$statusReason = '';
				$accStatus = '';

				$otherMember = Personal::find()
					->asArray()
					->select([
						Personal::tableName() . '.id'
					])
					->innerJoin(Member::tableName(), Member::tableName() . '.personal_no = ' . Personal::tableName() . '.personal_no')
					->where([
						Member::tableName() . '.policy_no' => $policyNo,
						Personal::tableName() . '.name' => $personal->name,
						Personal::tableName() . '.birth_date' => $personal->birth_date
					])
					->andWhere(['not', [Member::tableName() . '.member_no' => null]])
					->one();
				if ($otherMember != null) {
					$accStatus .= "Accumulated";
				}

				if ($age < $quotationTc->min_age || $age > $quotationTc->max_age) {
					$statusReason .= "Age does not meet the requirements\n";
				}
				if ($termYear > $quotationTc->max_term) {
					$statusReason .= "Term does not meet the requirements\n";
				}
				if ($sumInsured > $quotationTc->max_si) {
					$statusReason .= "SI does not meet the requirements\n";
				}
				if ($totalPremium < $quotationTc->min_premi) {
					$statusReason .= "Premi does not meet the requirements\n";
				}
				if ($quotationUwLimit->medical_code == '') {
					$statusReason .= "Medical does not meet the requirements\n";
				}
				if ($accStatus != '' || $statusReason != '') {
					$status = Member::MEMBER_STATUS_PENDING;
				}

				$members[] = [
					'member_no' => $sheetData[$baseRow]['E'],
					'policy_no' => $policyNo,
					'batch_no' => str_pad($batchNo, 6, '0', STR_PAD_LEFT),
					'personal_no' => $personal->personal_no,
					'branch' => $sheetData[$baseRow]['B'],
					'age' => $age,
					'branch_code' => $sheetData[$baseRow]['P'],
					'account_no' => $sheetData[$baseRow]['N'],
					'bank_branch' => $sheetData[$baseRow]['O'],
					'term' => $term,
					'start_date' => Utils::convertDateToYmd($sheetData[$baseRow]['J']),
					'end_date' => Utils::convertDateToYmd($sheetData[$baseRow]['K']),
					'sum_insured' => $sumInsured,
					'total_si' => $sumInsured,
					'rate_premi' => $quotationRate->rate,
					'total_premium' => $totalPremium,
					'gross_premium' => $grossPremium,
					'basic_premium' => $basicPremium,
					'nett_premium' => $nettPremium,
					'percentage_discount' => $quotationCommission->discount,
					'discount_premium' => $discount,
					'medical_code' => $quotationUwLimit->medical_code,
					'member_status' => $status,
					'status_reason' => $statusReason,
					'stnc_date' => $stncDate,
					'acc_status' => $accStatus,
					'created_at' => $createdAt,
					'created_by' => $createdBy,
				];

				$totalUp += $sumInsured;
				$totalGrossPremium += $grossPremium;
				$totalDiscountPremium += $discount;
				$totalExtraPremium += 0;
				$totalSavingPremium += 0;
				$totalNettPremium += $nettPremium;
				$totalMember++;
			}

			$baseRow++;
		}

		if (count($members) == 0) {
			Yii::$app->session->setFlash('error', "Member was empty");
			return $this->redirect(['create']);
		}

		$batch = new Batch();
		$batch->batch_no = str_pad($batchNo, 6, '0', STR_PAD_LEFT);
		$batch->policy_no = $policyNo;
		$batch->total_member = $totalMember;
		$batch->total_member_accepted = 0;
		$batch->total_member_pending = $totalMember;
		$batch->total_up = $totalUp;
		$batch->total_gross_premium = $totalGrossPremium;
		$batch->total_discount_premium = $totalDiscountPremium;
		$batch->total_extra_premium = $totalExtraPremium;
		$batch->total_saving_premium = $totalSavingPremium;
		$batch->total_nett_premium = $totalNettPremium;
		$batch->status = Batch::STATUS_OPEN;
		$batch->created_at = $createdAt;
		$batch->created_by = $createdBy;
		if (!$batch->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving Batch");
			return $this->redirect(['create']);
		}

		$attributes = [
			'member_no',
			'policy_no',
			'batch_no',
			'personal_no',
			'branch',
			'age',
			'branch_code',
			'account_no',
			'bank_branch',
			'term',
			'start_date',
			'end_date',
			'sum_insured',
			'total_si',
			'rate_premi',
			'total_premium',
			'gross_premium',
			'basic_premium',
			'nett_premium',
			'percentage_discount',
			'discount_premium',
			'medical_code',
			'member_status',
			'status_reason',
			'stnc_date',
			'acc_status',
			'created_at',
			'created_by'
		];
		$modelSave = Yii::$app->db->createCommand()
			->batchInsert(Member::tableName(), $attributes, $members)
			->execute();
		if (!$modelSave) {
			Yii::$app->session->setFlash('error', "Error while saving Member");
			return $this->redirect(['create']);
		}

		Yii::$app->session->setFlash('success', "Successfully uploaded");
		return $this->redirect([
			'view',
			'id' => $batch->id
		]);
	}

	public function actionUploadExisting()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$batchId = Yii::$app->request->post('batch_id');
		$file = UploadedFile::getInstanceByName('file');

		$currentDate = new \DateTime();
		$createdAt = $currentDate->format('Y-m-d H:i:s');
		$createdBy = Yii::$app->user->identity->id;

		$batch = Batch::findOne(['id' => $batchId]);
		if ($batch == null) {
			Yii::$app->session->setFlash('error', "Not found");
			return $this->redirect(['index']);
		}

		$policy = Policy::findOne(['policy_no' => $batch->policy_no]);
		if ($policy == null) {
			Yii::$app->session->setFlash('error', "Policy not found");
			return $this->redirect(['create']);
		}

		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		if ($quotation == null) {
			Yii::$app->session->setFlash('error', "Quotation not found");
			return $this->redirect(['create']);
		}

		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		if ($quotationProduct == null) {
			Yii::$app->session->setFlash('error', "Quotation Product not found");
			return $this->redirect(['create']);
		}

		$quotationCommission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
		if ($quotationCommission == null) {
			Yii::$app->session->setFlash('error', "Quotation Commission not found");
			return $this->redirect(['create']);
		}

		$inputFileType = \PHPExcel_IOFactory::identify($file->tempName);
		$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($file->tempName);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

		$baseRow = 2;
		$members = [];
		$totalMember = 0;
		$totalUp = 0;
		$totalGrossPremium = 0;
		$totalDiscountPremium = 0;
		$totalExtraPremium = 0;
		$totalSavingPremium = 0;
		$totalNettPremium = 0;
		while (!empty($sheetData[$baseRow]['D'])) {
			$birthDate = Utils::trueBirthDate($sheetData[$baseRow]['F']);
			$sumInsured = Utils::removeComma($sheetData[$baseRow]['L']);
			$personal = new Personal();
			$personal->name = $sheetData[$baseRow]['D'];
			$personal->birth_date = $birthDate;
			$personal->personal_no = Personal::generatePersonalNo($personal->name, $personal->birth_date);
			$personal->gender = $sheetData[$baseRow]['H'];
			$personal->id_card_no = $sheetData[$baseRow]['Q'];
			$personal->phone = $sheetData[$baseRow]['S'];
			$personal->email = $sheetData[$baseRow]['R'];
			$personal->address = $sheetData[$baseRow]['T'];
			$personal->province = $sheetData[$baseRow]['V'];
			$personal->city = $sheetData[$baseRow]['U'];
			if ($personal->save(false)) {
				$startDate = Utils::convertDateToYmd($sheetData[$baseRow]['J']);
				$endDate = Utils::convertDateToYmd($sheetData[$baseRow]['K']);

				$age = Member::getAge($quotation->age_calculate, $birthDate, $startDate);
				$term = Member::getTerm($quotation->rate_type, $startDate, $endDate);

				if ($quotation->rate_type == RateType::RATE_ROUND_UP) {
					$termYear = ceil($term / 12);
				} else {
					$termYear = floor($term / 12);
				}

				if ($quotationProduct->rate_type == ProductRateType::AGE_TERM) {
					if ($quotationProduct->period_type == PeriodType::ANNUALLY) {
						$quotationRate = QuotationRate::findOne([
							'quotation_id' => $policy->quotation_id,
							'age' => $age,
							'term' => $termYear
						]);
					} else {
						$quotationRate = QuotationRate::findOne([
							'quotation_id' => $policy->quotation_id,
							'age' => $age,
							'term' => $term
						]);
					}
				} else {
					$quotationRate = QuotationRate::findOne([
						'quotation_id' => $policy->quotation_id,
						'term' => $termYear
					]);
				}

				$quotationTc = QuotationTc::findOne([
					'quotation_id' => $policy->quotation_id,
				]);

				$quotationUwLimit = QuotationUwLimit::find()
					->where(['quotation_id' => $policy->quotation_id])
					->andWhere(['<=', 'min_age', $age])
					->andWhere(['>=', 'max_age', $age])
					->andWhere(['<=', 'min_si', $sumInsured])
					->andWhere(['>=', 'max_si', $sumInsured])
					->one();

				$stncDate = Member::getStnc($startDate, $quotationTc->retroactive);
				$totalPremium = $sumInsured * $quotationRate->rate / 1000;
				$grossPremium = $totalPremium;
				$basicPremium = $totalPremium;
				$discount = $totalPremium * $quotationCommission->discount / 100;
				$nettPremium = $totalPremium - $discount;

				$status = Member::MEMBER_STATUS_PENDING;
				if ($quotationUwLimit->medical_code == 'GOA' || $quotationUwLimit->medical_code == 'FC') {
					$status = Member::MEMBER_STATUS_INFORCE;
				}

				$statusReason = '';
				$accStatus = '';

				$otherMember = Personal::find()
					->asArray()
					->select([
						Personal::tableName() . '.id'
					])
					->innerJoin(Member::tableName(), Member::tableName() . '.personal_no = ' . Personal::tableName() . '.personal_no')
					->where([
						Member::tableName() . '.policy_no' => $batch->policy_no,
						Personal::tableName() . '.name' => $personal->name,
						Personal::tableName() . '.birth_date' => $personal->birth_date
					])
					->andWhere(['not', [Member::tableName() . '.member_no' => null]])
					->one();
				if ($otherMember != null) {
					$accStatus .= "Accumulated";
				}

				if ($age < $quotationTc->min_age || $age > $quotationTc->max_age) {
					$statusReason .= "Age does not meet the requirements\n";
				}
				if ($termYear > $quotationTc->max_term) {
					$statusReason .= "Term does not meet the requirements\n";
				}
				if ($sumInsured > $quotationTc->max_si) {
					$statusReason .= "SI does not meet the requirements\n";
				}
				if ($totalPremium < $quotationTc->min_premi) {
					$statusReason .= "Premi does not meet the requirements\n";
				}
				if ($quotationUwLimit->medical_code == '') {
					$statusReason .= "Medical does not meet the requirements\n";
				}
				if ($accStatus != '' || $statusReason != '') {
					$status = Member::MEMBER_STATUS_PENDING;
				}

				$members[] = [
					'member_no' => $sheetData[$baseRow]['E'],
					'policy_no' => $batch->policy_no,
					'batch_no' => $batch->batch_no,
					'personal_no' => $personal->personal_no,
					'branch' => $sheetData[$baseRow]['B'],
					'age' => $age,
					'branch_code' => $sheetData[$baseRow]['P'],
					'account_no' => $sheetData[$baseRow]['N'],
					'bank_branch' => $sheetData[$baseRow]['O'],
					'term' => $term,
					'start_date' => Utils::convertDateToYmd($sheetData[$baseRow]['J']),
					'end_date' => Utils::convertDateToYmd($sheetData[$baseRow]['K']),
					'sum_insured' => $sumInsured,
					'total_si' => $sumInsured,
					'rate_premi' => $quotationRate->rate,
					'total_premium' => $totalPremium,
					'gross_premium' => $grossPremium,
					'basic_premium' => $basicPremium,
					'nett_premium' => $nettPremium,
					'percentage_discount' => $quotationCommission->discount,
					'discount_premium' => $discount,
					'medical_code' => $quotationUwLimit->medical_code,
					'member_status' => $status,
					'status_reason' => $statusReason,
					'stnc_date' => $stncDate,
					'acc_status' => $accStatus,
					'created_at' => $createdAt,
					'created_by' => $createdBy,
				];

				$totalUp += $sumInsured;
				$totalGrossPremium += $grossPremium;
				$totalDiscountPremium += $discount;
				$totalExtraPremium += 0;
				$totalSavingPremium += 0;
				$totalNettPremium += $nettPremium;
				$totalMember++;
			}

			$baseRow++;
		}

		if (count($members) == 0) {
			Yii::$app->session->setFlash('error', "Member was empty");
			return $this->redirect(['create']);
		}

		$batch->batch_no = $batch->batch_no;
		$batch->policy_no = $batch->policy_no;
		$batch->total_member = $totalMember;
		$batch->total_member_accepted = 0;
		$batch->total_member_pending = $totalMember;
		$batch->total_up = $totalUp;
		$batch->total_gross_premium = $totalGrossPremium;
		$batch->total_discount_premium = $totalDiscountPremium;
		$batch->total_extra_premium = $totalExtraPremium;
		$batch->total_saving_premium = $totalSavingPremium;
		$batch->total_nett_premium = $totalNettPremium;
		$batch->status = Batch::STATUS_OPEN;
		$batch->created_at = $createdAt;
		$batch->created_by = $createdBy;
		if (!$batch->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving Batch");
			return $this->redirect(['create']);
		}

		$attributes = [
			'member_no',
			'policy_no',
			'batch_no',
			'personal_no',
			'branch',
			'age',
			'branch_code',
			'account_no',
			'bank_branch',
			'term',
			'start_date',
			'end_date',
			'sum_insured',
			'total_si',
			'rate_premi',
			'total_premium',
			'gross_premium',
			'basic_premium',
			'nett_premium',
			'percentage_discount',
			'discount_premium',
			'medical_code',
			'member_status',
			'status_reason',
			'stnc_date',
			'acc_status',
			'created_at',
			'created_by'
		];
		$modelSave = Yii::$app->db->createCommand()
			->batchInsert(Member::tableName(), $attributes, $members)
			->execute();
		if (!$modelSave) {
			Yii::$app->session->setFlash('error', "Error while saving Member");
			return $this->redirect(['create']);
		}

		Yii::$app->session->setFlash('success', "Successfully uploaded");
		return $this->redirect([
			'view',
			'id' => $batchId,
		]);
	}

	/**
	 * Updates an existing Member model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param int $id ID
	 * @return string|\yii\web\Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionUpdate()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$batch = Batch::findOne(['id' => Yii::$app->request->post('batch_id')]);
		if ($batch == null) {
			Yii::$app->session->setFlash('error', "Batch not found");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$policy = Policy::findOne(['policy_no' => Yii::$app->request->post('policy_no')]);
		if ($policy == null) {
			Yii::$app->session->setFlash('error', "Policy not found");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$member = Member::updateAll(['policy_no' => $policy->policy_no], [
			'batch_no' => $batch->batch_no,
			'policy_no' => $batch->policy_no
		]);
		if (!$member) {
			Yii::$app->session->setFlash('error', "Error while saving Member");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$batch->policy_no = $policy->policy_no;
		if (!$batch->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving Batch");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		Yii::$app->session->setFlash('success', "Policy No Successfully saved");
		return $this->redirect([
			'view',
			'id' => Yii::$app->request->post('batch_id'),
		]);
	}

	public function actionUpdateMember()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$model = Member::findOne(['id' => Yii::$app->request->post('id')]);
		if ($model == null) {
			Yii::$app->session->setFlash('error', "Member not found");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$batch = Batch::findOne([
			'batch_no' => $model->batch_no,
			'policy_no' => $model->policy_no
		]);
		if ($batch == null) {
			Yii::$app->session->setFlash('error', "Batch not found");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$personal = Personal::findOne(['personal_no' => $model->personal_no]);
		if ($personal == null) {
			Yii::$app->session->setFlash('error', "Personal not found");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$policy = Policy::findOne(['policy_no' => $model->policy_no]);
		if ($policy == null) {
			Yii::$app->session->setFlash('error', "Policy not found");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		if ($quotation == null) {
			Yii::$app->session->setFlash('error', "Quotation not found");
			return $this->redirect(['create']);
		}

		$quotationCommission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
		if ($quotationCommission == null) {
			Yii::$app->session->setFlash('error', "Quotation not found");
			return $this->redirect(['create']);
		}

		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
		if ($quotationProduct == null) {
			Yii::$app->session->setFlash('error', "Quotation Product not found");
			return $this->redirect(['create']);
		}

		$dateTime = new \DateTime();
		$currentDateTime = $dateTime->format('Y-m-d H:i:s');

		$rateEm = '';
		$emPremium = '';
		if (Yii::$app->request->post('em_type') != null) {
			if (Yii::$app->request->post('em_type') == Member::EM_MANUAL) {
				$emPremium = $model->total_premium * Yii::$app->request->post('percentage_em') / 100;
			} else if (Yii::$app->request->post('em_type') == Member::EM_FROM_PRODUCT) {
				$termYear = floor($model->term / 12);
				$productEm = ProductEm::find()
					->where([
						'product_id' => $quotationProduct->product_id,
						'percentage' => Yii::$app->request->post('percentage_em'),
						'age' => $model->age,
						'term' => $termYear
					])
					->one();
				$rateEm = $productEm->em;
				$emPremium = $model->total_premium * Yii::$app->request->post('percentage_em') / 100 * $rateEm;
			}
		}

		$term = Member::getTerm($quotation->rate_type, Yii::$app->request->post('start_date'), Yii::$app->request->post('end_date'));
		$age = Member::getAge($quotation->age_calculate, Yii::$app->request->post('birth_date'), Yii::$app->request->post('start_date'));

		if ($quotation->rate_type == RateType::RATE_ROUND_UP) {
			$termYear = ceil($term / 12);
		} else {
			$termYear = floor($term / 12);
		}

		if ($quotationProduct->rate_type == ProductRateType::AGE_TERM) {
			$quotationRate = QuotationRate::findOne([
				'quotation_id' => $policy->quotation_id,
				'age' => $age,
				'term' => ($quotationProduct->period_type == PeriodType::ANNUALLY) ? $termYear : $term
			]);
		} else {
			$quotationRate = QuotationRate::findOne([
				'quotation_id' => $policy->quotation_id,
				'term' => ($quotationProduct->period_type == PeriodType::ANNUALLY) ? $termYear : $term
			]);
		}

		$quotationTc = QuotationTc::findOne([
			'quotation_id' => $policy->quotation_id,
		]);

		$quotationUwLimit = QuotationUwLimit::find()
			->where(['quotation_id' => $policy->quotation_id])
			->andWhere(['<=', 'min_age', $age])
			->andWhere(['>=', 'max_age', $age])
			->andWhere(['<=', 'min_si', Yii::$app->request->post('sum_insured')])
			->andWhere(['>=', 'max_si', Yii::$app->request->post('sum_insured')])
			->one();

		$totalPremium = Yii::$app->request->post('sum_insured') * $quotationRate->rate / 1000;
		$grossPremium = $totalPremium;
		$basicPremium = $totalPremium;
		$discount = $totalPremium * $quotationCommission->discount / 100;
		$nettPremium = $totalPremium - $discount;

		$statusReason = '';
		if ($age < $quotationTc->min_age || $age > $quotationTc->max_age) {
			$statusReason .= "Age does not meet the requirements\n";
		}
		if ($termYear > $quotationTc->max_term) {
			$statusReason .= "Term does not meet the requirements\n";
		}
		if (Yii::$app->request->post('sum_insured') > $quotationTc->max_si) {
			$statusReason .= "SI does not meet the requirements\n";
		}
		if ($totalPremium < $quotationTc->min_premi) {
			$statusReason .= "Premi does not meet the requirements\n";
		}
		if ($quotationUwLimit->medical_code == '') {
			$statusReason .= "Medical does not meet the requirements\n";
		}
		if ($statusReason != '') {
			$status = Member::MEMBER_STATUS_PENDING;
		}

		$em = ($emPremium == '') ? 0 : $emPremium;
		$batch->total_up = $batch->total_up - $model->sum_insured + Yii::$app->request->post('sum_insured');
		$batch->total_gross_premium = $batch->total_gross_premium - $model->gross_premium + $grossPremium;
		$batch->total_discount_premium = $batch->total_discount_premium - $model->discount_premium + $discount;
		$batch->total_extra_premium = $batch->total_extra_premium;
		$batch->total_saving_premium = $batch->total_saving_premium;
		$batch->total_nett_premium = $batch->total_nett_premium - $model->nett_premium + $nettPremium + $em;
		if (!$batch->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving Batch");
			return $this->redirect(['create']);
		}

		$model->age = $age;
		$model->start_date = Yii::$app->request->post('start_date');
		$model->end_date = Yii::$app->request->post('end_date');
		$model->sum_insured = Yii::$app->request->post('sum_insured');
		$model->total_si = Yii::$app->request->post('sum_insured');
		$model->total_premium = $totalPremium;
		$model->rate_premi = $quotationRate->rate;
		$model->gross_premium = $grossPremium;
		$model->basic_premium = $basicPremium;
		$model->discount_premium = $discount;
		$model->nett_premium = $nettPremium;
		$model->medical_code = Yii::$app->request->post('medical_code');
		$model->em_type = Yii::$app->request->post('em_type');
		$model->percentage_em = Yii::$app->request->post('percentage_em');
		$model->rate_em = $rateEm;
		$model->em_premium = $emPremium;
		$model->member_status = Yii::$app->request->post('member_status');
		$model->status_reason = $statusReason;
		$model->em_notes = Yii::$app->request->post('em_notes');
		$model->uw_notes = Yii::$app->request->post('uw_notes');
		$model->updated_at = $currentDateTime;
		$model->updated_by = Yii::$app->user->identity->id;
		if (!$model->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		$personal->name = Yii::$app->request->post('name');
		$personal->birth_date = Yii::$app->request->post('birth_date');
		if (!$personal->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving personal");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}

		Yii::$app->session->setFlash('success', "Member Successfully saved");
		return $this->redirect([
			'view',
			'id' => Yii::$app->request->post('batch_id'),
		]);
	}

	public function actionApprove($id)
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$batch = Batch::findOne(['id' => $id]);
		if ($batch == null) {
			Yii::$app->session->setFlash('error', "Batch not found");
			return $this->redirect(['index']);
		}

		$policy = Policy::findOne(['policy_no' => $batch->policy_no]);
		if ($policy == null) {
			Yii::$app->session->setFlash('error', "Policy not found");
			return $this->redirect(['index']);
		}

		$quotation = Quotation::findOne(['id' => $policy->quotation_id]);
		if ($quotation == null) {
			Yii::$app->session->setFlash('error', "Quotation not found");
			return $this->redirect(['index']);
		}

		$tc = QuotationTc::findOne(['quotation_id' => $policy->quotation_id]);
		if ($tc == null) {
			Yii::$app->session->setFlash('error', "TC not found");
			return $this->redirect(['index']);
		}

		$commission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
		if ($commission == null) {
			Yii::$app->session->setFlash('error', "Commission not found");
			return $this->redirect(['index']);
		}

		$totalAccepted = 0;
		$totalUp = 0;
		$totalGrossPremium = 0;
		$totalDiscountPremium = 0;
		$totalExtraPremium = 0;
		$totalSavingPremium = 0;
		$totalNettPremium = 0;
		$members = Member::find()
			->where([
				'batch_no' => $batch->batch_no,
				'policy_no' => $batch->policy_no,
				'member_status' => Member::MEMBER_STATUS_INFORCE
			])
			->all();
		$existingMemberTotal = Member::find()
			->where([
				'and',
				['policy_no' => $batch->policy_no],
				['!=', 'member_no', '']
			])
			->count();
		if ($batch->policy_no == '1032212000464') {
			$runningNo = $existingMemberTotal + 2;
		} else if ($batch->policy_no == '1032210000446') {
			$runningNo = $existingMemberTotal + 3;
		} else {
			$runningNo = $existingMemberTotal + 1;
		}
		foreach ($members as $member) {
			$stncDate = Member::getStnc($member->start_date, $tc->retroactive);

			$member->member_no = Member::generateMemberNo($runningNo, $batch->policy_no);
			$member->status = Member::STATUS_INFORCE;
			$member->stnc_date = $stncDate;
			$member->member_status = Member::MEMBER_STATUS_INFORCE;
			$member->save(false);
			$totalAccepted++;
			$totalUp += $member->sum_insured;
			$totalGrossPremium += $member->gross_premium;
			$totalDiscountPremium += $member->discount_premium;
			$totalExtraPremium += $member->extra_premium;
			$totalSavingPremium += $member->saving_premium;
			$totalNettPremium += $member->nett_premium;
			$runningNo++;
		}

		$currentDate = new \DateTime();
		$updatedAt = $currentDate->format('Y-m-d H:i:s');
		$updatedBy = Yii::$app->user->identity->id;

		$batch->total_member_accepted = $totalAccepted;
		$batch->total_member_pending = $batch->total_member - $totalAccepted;
		$batch->total_up = $totalUp;
		$batch->total_gross_premium = $totalGrossPremium;
		$batch->total_discount_premium = $totalDiscountPremium;
		$batch->total_extra_premium = $totalExtraPremium;
		$batch->total_saving_premium = $totalSavingPremium;
		$batch->total_nett_premium = $totalNettPremium;
		$batch->status = ($batch->total_member_pending > 0) ? Batch::STATUS_PENDING : Batch::STATUS_CLOSED;
		$batch->updated_at = $updatedAt;
		$batch->updated_by = $updatedBy;
		if (!$batch->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving");
			return $this->redirect(['view', 'id' => $id]);
		}

		$newestId = 1;
		$billing = Billing::find()->orderBy(['id' => SORT_DESC])->one();
		if ($billing != null) {
			$newestId = $billing->id + 1;
		}

		$billingCount = Billing::find()->where([
			'policy_no' => $batch->policy_no,
			'YEAR(invoice_date)' => date("Y")
		])->count();

		$administrationCost = $tc->administration_cost;
		$policyCost = $tc->policy_cost;
		$memberCardCost = $tc->member_card_cost;
		$certificateCost = $tc->certificate_cost;
		$stampCost = $tc->stamp_cost;
		$existingBilling = Billing::find()->where([
			'policy_no' => $batch->policy_no
		])->one();
		if ($existingBilling != null) {
			$administrationCost = 0;
			$policyCost = 0;
			$memberCardCost = 0;
			$certificateCost = 0;
			$stampCost = 0;
		}

		$regNoParams = [
			'id' => $newestId,
			'policy_no' => $batch->policy_no,
			'month' => date("n")
		];

		$invoiceNoParams = [
			'id' => $billingCount + 1,
			'policy_no' => $batch->policy_no,
			'month' => date("n")
		];

		$billing = new Billing();
		
		$billing->created_by = Yii::$app->user->identity->id;
		$billing->policy_no = $batch->policy_no;
		$billing->batch_no = $batch->batch_no;
		$billing->reg_no = Billing::generateRegNo($regNoParams);
		$billing->invoice_no = Billing::generateInvoiceNo($invoiceNoParams);
		$billing->invoice_date = date("Y-m-d");
		$billing->due_date = Billing::getDueDate($tc->grace_period);
		$billing->accept_date = date("Y-m-d");
		$billing->total_member = $batch->total_member;
		$billing->gross_premium = $batch->total_gross_premium;
		$billing->extra_premium = $batch->total_extra_premium;
		$billing->discount = $batch->total_gross_premium * $commission->discount / 100;
		$billing->nett_premium = $batch->total_nett_premium;
		$billing->handling_fee = $batch->total_gross_premium * $commission->handling_fee / 100;
		if (
			$batch->policy_no == '1032301000471'
			|| $batch->policy_no == '1032211000456'
		) {
			$billing->pph = $billing->handling_fee * $commission->pph / 100;
		} else {
			$billing->pph = ($billing->discount * $commission->pph / 100) + ($billing->handling_fee * $commission->pph / 100);
		}
		$billing->ppn = ($billing->discount * $commission->ppn / 100) + ($billing->handling_fee * $commission->ppn / 100);
		$billing->admin_cost = $administrationCost;
		$billing->policy_cost = $policyCost;
		$billing->member_card_cost = $memberCardCost;
		$billing->certificate_cost = $certificateCost;
		$billing->stamp_cost = $stampCost;
		$billing->total_billing = $billing->gross_premium -
			$billing->discount -
			$billing->handling_fee +
			$billing->pph -
			$billing->ppn +
			$billing->admin_cost +
			$billing->policy_cost +
			$billing->member_card_cost +
			$billing->certificate_cost +
			$billing->stamp_cost;
		$billing->status = Billing::STATUS_UNVERIFIED;
		if (!$billing->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving billing");
			return $this->redirect(['view', 'id' => $id]);
		}

		Yii::$app->session->setFlash('success', "Successfully Approved");
		return $this->redirect(['view', 'id' => $id]);
	}

	/**
	 * Deletes an existing Member model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param int $id ID
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionDelete($id)
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$batch = Batch::findOne(['id' => $id]);
		if ($batch == null) {
			Yii::$app->session->setFlash('error', "Batch not found");
			return $this->redirect(['view', 'id' => $id]);
		}

		$batch->delete();
		Member::deleteAll([
			'policy_no' => $batch->policy_no,
			'batch_no' => $batch->batch_no,
		]);

		Yii::$app->session->setFlash('success', "Successfully deleted");
		return $this->redirect(['index']);
	}

	public function actionDeleteMember($id)
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$member = Member::findOne(['id' => $id]);
		$batchId = $member->id;
		$member->delete();

		Yii::$app->session->setFlash('success', "Successfully deleted");
		return $this->redirect([
			'view',
			'id' => $batchId,
		]);
	}

	/**
	 * Finds the Member model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param int $id ID
	 * @return Member the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Member::findOne(['id' => $id])) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

	protected function findPersonal($policyNo, $batchNo)
	{
		return Member::find()
			->select([
				Member::tableName() . '.id',
				'CONCAT(`' . Personal::tableName() . '`.`name` , \' - \' , `' . Personal::tableName() . '`.`birth_date`) AS name',
			])
			->asArray()
			->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . Member::tableName() . '.personal_no')
			->where([
				Member::tableName() . '.policy_no' => $policyNo,
				Member::tableName() . '.batch_no' => $batchNo,
			])
			->all();
	}
}
