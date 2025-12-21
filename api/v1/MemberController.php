<?php

namespace app\controllers\api\v1;

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
use FPDF;
use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;


class MemberController extends Controller
{
    public $enableCsrfValidation = false;
    protected $medicalCode = 'CAC';
    protected $createdBy = 1;
	
	const PICTURE_PATH = '/images/e_policy/';
	const PICTURE_PATH_Logo = '/images/img-Reliance-life.jpg';
	const PICTURE_PATH_Ttd = '/images/policy-qr.png';

    public function beforeAction($action)
    {
        $h = Yii::$app->request->headers;
        $k = Utils::sanitize($h->get('x-api-key'));
        $s = Utils::sanitize($h->get('x-api-secret'));
        if (!Api::validate($k, $s)) {
            $this->redirect(['/api/v1/site/header-error']);
            return false;
        }
        return parent::beforeAction($action);
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
	
        $members = Yii::$app->request->post('members');
        if (empty($members)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'
            ];
        }
		
		$produk =  Utils::sanitize($members[0]['produk']);
		
        $policy = Policy::findOne(['produk' =>  $produk ]);
        if ($policy == null) {
            Yii::$app->response->statusCode = 404;
            return [
                'is_success' => 0,
                'message' => 'Policy not found'
            ];
        }
		
        $batch = $this->_createBatch($policy, $members);
		
		if ($batch['member']['isRedundant'] == 1) {
            Yii::$app->response->statusCode = 406;
            return [
                'is_success' => 0,
                'message' => 'Data Peserta  Dalam Kategori Case By Case'
            ];
		}
		
        $billing = $this->_createBilling($policy, $batch['batch']);
        if ($billing['is_success'] == 0) {
            Yii::$app->response->statusCode = $billing['status_code'];
            return [
                'is_success' => 0,
                'message' => $billing['message']
            ];
        }
		
		$id_loan =$batch['member']['idLoan'];
		
		$member_detail = member::findOne([
                'id_loan' => $id_loan,
            ]);
			
			
		$personal_detail = personal::findone([
                'personal_no' => $member_detail-> personal_no,
         ]);	
		 
		$date = Utils::tgl_indo(date("Y-m-d"));
		
		
		
		 // $filename = $this->id_loan;
        // $extension = $this->file_upload->extension;

        // $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . "." . $extension;
        // $this->file_upload->saveAs($path);
		
		// $filePath = 'D:/';
		
		$filePath = \Yii::getAlias('@webroot') . self::PICTURE_PATH;
		$fileName = $batch['member']['idLoan'] . '.pdf';
		// echo("<script>console.log('PHP: " . $batch['member']['idLoan'] . "');</script>");
		$patch= $filePath . $fileName;
	
		require_once('fpdf.php');
		// Create a new FPDF instance
		$pdf = new FPDF();
		$pdf->AddPage();						
		$pdf->SetFont('Arial','B',16);
		// $pdf->Image('PICTURE_PATH_Logo', 10, 10, -200);
		$pdf->Image(\Yii::getAlias('@webroot') . self::PICTURE_PATH_Logo, 10, 10, -200);
		$pdf->SetY(30);
		$pdf->SetX(0);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'SERTIFIKAT ASURANSI', 0, 1, 'C');
		$pdf->SetY($pdf->GetY() + 2);
		$pdf->SetX(0);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'NOMOR : '. $batch['member']['idLoan'], 0, 1, 'C');
		$pdf->SetY($pdf->GetY() + 10);
		$pdf->SetX(10);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(40, 5, 'Nomor Polis', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $batch['member']['policy_no'], 0, 1, 'L');
		$pdf->Cell(40, 5, 'Nama', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $personal_detail->name, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Nomor Peserta', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $member_detail->member_no, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Tanggal Lahir', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5,Utils::tgl_indo($personal_detail->birth_date), 0, 1, 'L');
		$pdf->SetY($pdf->GetY() + 10);
		$pdf->SetX(10);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'Adalah Peserta Polis Dari Asuransi Jiwa (Pemegang Polis)', 0, 0, 'L');
		$pdf->SetY($pdf->GetY() + 8);
		$pdf->SetX(0);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'BANK JAWA TIMUR', 0, 1, 'C');
		$pdf->SetFont('Arial','',8);
		$pdf->SetY($pdf->GetY() + 8);
		$pdf->SetX(10);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'Dengan ketentuan sebagai berikut :', 0, 0, 'L');
		$pdf->SetY($pdf->GetY() + 8);
		$pdf->SetX(10);
		$pdf->Cell(40, 5, 'Jenis Asuransi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $member_detail->produk, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Masa Asuransi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $member_detail->term, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Periode Asuransi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5,Utils::tgl_indo($member_detail->start_date) . ' '. 's/d' . ' ' . Utils::tgl_indo($member_detail->end_date) , 0, 1, 'L');
		$pdf->Cell(40, 5, 'Uang Pertanggungan', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($batch['batch']->total_up), 0, 1, 'L');
		$pdf->Cell(40, 5, 'Premi Gross', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($batch['batch']->total_nett_premium), 0, 1, 'L');
		$pdf->Cell(40, 5, 'Extra Premi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($member_detail->extra_premium), 0, 1, 'L');
		$pdf->Cell(40, 5, 'Total Premi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($batch['batch']->total_nett_premium), 0, 1, 'L');
		$pdf->SetY($pdf->GetY() + 10);
		$pdf->Cell($pdf->GetPageWidth() - 20, 5, 'Jakarta ,' .' ' . $date, 0, 1, 'R');
		// $pdf->Image('http://localhost/ajri-core-system-h2h-bank-jatim/protected/controllers/api/v1/policy-qr.png', $pdf->GetPageWidth() - 40, $pdf->GetY() + 2, -100);
		//$pdf->Image(\Yii::getAlias('@webroot') . self::PICTURE_PATH_Ttd, 10, 10, -200);
		$pdf->Image(\Yii::getAlias('@webroot') . self::PICTURE_PATH_Ttd, $pdf->GetPageWidth() - 40, $pdf->GetY() + 2, -100);
		$pdf->SetY($pdf->GetY() + 22);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell($pdf->GetPageWidth() - 31, 5, 'Aisyah Aini', 0, 1, 'R');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell($pdf->GetPageWidth() - 31, 5, 'Underwriting', 0, 1, 'R');

		$pdf->Output($filePath . $fileName, 'F');

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'message' => 'Succesfully uploaded',
            'total_member' => $batch['batch']->total_member,
            'total_up' => $batch['batch']->total_up,
            'total_nett_premium' => $batch['batch']->total_nett_premium,
            'rate' =>  $batch['member']['rate'],
            'status_uw' => $batch['member']['medicalCode'],
			//'print_url' => 'https://reli.id/dev/h2h-bank-jatim/member/print-sertifikat?id_loan=' . $batch['member']['idLoan'],
			// 'print_url' => 'https://h2h-ajri-staging.reli.id/images/e_policy/' .$fileName,
			'print_url' => 'https://h2h-ajri.reli.id/images/e_policy/' .$fileName,
			'policy_no' =>  $batch['member']['policy_no'],
        ];
    }

    protected function _createBatch($policy, $members)
    {
		
        $batchNo = Batch::generateBatchNo($policy->policy_no);
        $member = $this->_createMember($policy->policy_no, $batchNo, $members);

		$batch = null;
		if($member['isRedundant']== 0)
		{
		$batch = new Batch();
        $batch->policy_no = $member['policy_no'];
        $batch->batch_no = $batchNo;
        $batch->total_member = $member['totalMember'];
        $batch->total_member_accepted = $member['totalMember'];
        $batch->total_member_pending = 0;
        $batch->total_up = $member['totalUp'];
        $batch->total_gross_premium = $member['totalNettPremium'];
        $batch->total_discount_premium = 0;
        $batch->total_extra_premium = 0;
        $batch->total_saving_premium = 0;
        $batch->total_nett_premium = $member['totalNettPremium'];
        $batch->status = Batch::STATUS_CLOSED;
        $batch->created_at = date('Y-m-d H:i:s');
        $batch->created_by = $this->createdBy;
        $batch->save(false);
		}
        return [
            'batch' => $batch,
            'member' => $member
        ];
    }

    protected function _createMember($policyNo, $batchNo, $members)
    {
       $personalCols = ['personal_no', 'name', 'birth_date', 'id_card_no'];
        $memberCols = [
            'policy_no', 'batch_no', 'member_no', 'personal_no', 'age', 'term', 'start_date', 'end_date',
            'sum_insured', 'total_si', 'total_premium', 'rate_premi', 'gross_premium', 'basic_premium', 'nett_premium',
            'medical_code', 'status', 'member_status', 'created_at', 'created_by', 'contract_date', 'produk', 'branch_office_code',
			'id_loan', 'status_uw', 'no_ktp','pekerjaan','acc_status','jenis_transaksi'
        ];
        $personalRows = [];
        $memberRows = [];

        $totalMember = 0;
        $totalUp = 0;
        $totalNettPremium = 0;
        $rate = '';
        $medicalCode = '';
        $idLoan = '';
		$isRedundant = 0;
		// kondisi id_loan//
		$isduplicat = 0;
		// kondisi id_loan//
        foreach ($members as $member) {
            $sumInsured = Utils::sanitize($member['sum_insured']);
            $startDate = Utils::sanitize($member['start_date']);
            $endDate = Utils::sanitize($member['end_date']);
            $produk =  Utils::sanitize($member['produk']);
			$personalNodoubel = Personal::generatePersonalNo($name, $birthDate);

			//kondisi id_loan //
			
			$status = 'Inforce';
			$is_loan_duplicat =  Utils::sanitize($member['id_loan']);
			$id_loan_member = member::findOne([
                'id_loan' => $is_loan_duplicat,
				'status' => $status ,
            ]);
			
			 // var_dump( $id_loan_member);
			
			 if($id_loan_member != 0){
				 $isduplicat = 1;
			 }
			//kondisi_id loan//
			
            $policybyproduk = Policy::findOne([
                'produk' => $produk,
            ]);
		
			$memberTotal = Member::find()
            ->where([
			'and',
				['policy_no' => $policybyproduk->policy_no],
				['!=', 'member_no', '']
			])
            ->count();
			$memberIndexNo = $memberTotal + 1;
	
			
            $quotation = Quotation::findOne([
                'id' => $policybyproduk->quotation_id,
            ]);

            // $quotation = Quotation::findOne(1);
            $age =  Utils::sanitize($member['age']);
            $term = member::getTerm($quotation->rate_type, $startDate, $endDate);
			
			
            $ratepremi = QuotationRate::findOne([
                'term' => $term,
                'quotation_id' => $policybyproduk->quotation_id
            ]);
			
			

			$otherMember = Personal::find()
					->asArray()
					->select([
						Personal::tableName() . '.id',
						'SUM(member.sum_insured) AS sum_insured',
					])
					->innerJoin(Member::tableName(), Member::tableName() . '.personal_no = ' . Personal::tableName() . '.personal_no')
					->where([
						Member::tableName() . '.policy_no' => $policyNo,
						Personal::tableName() . '.name' => $personal->name,
						Personal::tableName() . '.birth_date' => $personal->birth_date
					])
					->andWhere(['not', [Member::tableName() . '.member_no' => null]])
					->one();					
			$accStatus = '';
			if ($otherMember != null) {
				$accStatus = "Accumulated";
				$sumInsured += $otherMember->sum_insured;
			}
				
            $quotationUwLimit = QuotationUwLimit::find()
                ->where(['quotation_id' => $policybyproduk->quotation_id])
                ->andWhere(['<=', 'min_age', $age])
                ->andWhere(['>=', 'max_age', $age])
                ->andWhere(['<=', 'min_si', $sumInsured])
                ->andWhere(['>=', 'max_si', $sumInsured])
                ->one();
				
				
			 $medicalCode = $quotationUwLimit->medical_code;	
			 if($medicalCode != 'CAC'){
				 $isRedundant = 1;
			 }
			 

            $totalPremium = $sumInsured * $ratepremi->rate / 1000;

			 $tgl = Utils::sanitize($member['birth_date']);
			 $dob = str_replace('T00:00:00', '', $tgl);
			
            $name = Utils::sanitize($member['name']);
            // $birthDate = Utils::sanitize($member['birth_date']);
			$birthDate = $dob;
            $age = Utils::sanitize($member['age']);
            $idCardNo = Utils::sanitize($member['no_ktp']);
            $sumInsured = Utils::sanitize($member['sum_insured']);
            $startDate = Utils::sanitize($member['start_date']);;
            $endDate = Utils::sanitize($member['end_date']);
            $term = Utils::sanitize($member['term']);
            $rate = $ratepremi->rate;
            $nettPremium = $totalPremium;
            $personalNo = Personal::generatePersonalNo($name, $birthDate);
            $memberNo = Member::generateMemberNo($memberIndexNo, $policybyproduk->policy_no);
            $contract_date = Utils::sanitize($member['contract_date']);
            $produk = Utils::sanitize($member['produk']);
            $branch_office_code = Utils::sanitize($member['branch_office_code']);
            $id_loan = Utils::sanitize($member['id_loan']);
            $status_uw = $quotationUwLimit->medical_code;
            $no_ktp = Utils::sanitize($member['no_ktp']);
			$pekerjaan = Utils::sanitize($member['pekerjaan']);
			$jenis_transaksi = Utils::sanitize($member['jenis_transaksi']);
		

            $personalRows[] = [$personalNo, $name, $birthDate, $idCardNo];

            $memberRows[] = [
                $policybyproduk->policy_no, $batchNo, $memberNo, $personalNo, $age, $term, $startDate, $endDate,
                $sumInsured, $sumInsured, $nettPremium, $rate, $nettPremium, $nettPremium, $nettPremium,
                $quotationUwLimit->medical_code, Member::STATUS_INFORCE, Member::MEMBER_STATUS_INFORCE, date("Y-m-d H:i:s"), $this->createdBy,
                $contract_date, $produk, $branch_office_code, $id_loan, $status_uw, $no_ktp,$pekerjaan,$accStatus,$jenis_transaksi,
            ];
            $totalMember++;
            $totalUp += $sumInsured;
            $totalNettPremium += $nettPremium;
            $rate = $rate;
            $medicalCode = $quotationUwLimit->medical_code;
            $idLoan = $member['id_loan'];
			$policy_no = $policybyproduk->policy_no;
        }
		
		// tambah kondisi
		if($isRedundant==0 && $isduplicat==0)
		//
		
		{
		Yii::$app->db->createCommand()->batchInsert(Personal::tableName(), $personalCols, $personalRows)->execute();
        Yii::$app->db->createCommand()->batchInsert(Member::tableName(), $memberCols, $memberRows)->execute();
		}
       
		// }
        return [
            'totalUp' => $totalUp,
            'rate' => $rate,
            'totalNettPremium' => $totalNettPremium,
            'medicalCode' => $medicalCode,
            'totalMember' => $totalMember,
            'idLoan' => $idLoan,
			'policy_no' => $policy_no,
			'isRedundant' => $isRedundant,
        ];
		
    }

    protected function _createBilling($policy, $batch)
    {
        $tc = QuotationTc::findOne(['quotation_id' => $policy->quotation_id]);
        if ($tc == null) {
            return [
                'status_code' => 404,
                'is_success' => 0,
                'message' => 'TC not found'
            ];
        }

        $commission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
        if ($commission == null) {
            return [
                'status_code' => 404,
                'is_success' => 0,
                'message' => 'Commission not found'
            ];
        }

        $latestBilling = Billing::find()->orderBy(['id' => SORT_DESC])->one();
        $newId = ($latestBilling != null) ? $latestBilling->id + 1 : 1;

        $newIndex = Billing::find()->where([
            'policy_no' => $batch->policy_no,
            'YEAR(invoice_date)' => date("Y")
        ])->count() + 1;

        $billing = Billing::find()->where([
            'policy_no' => $batch->policy_no
        ])->one();

        $billing = new Billing();
        $billing->batch_no = $batch->batch_no;
        $billing->policy_no = $batch->policy_no;
        $billing->reg_no = Billing::generateRegNo(['id' => $newId, 'policy_no' => $batch->policy_no, 'month' => date('n')]);
        $billing->invoice_no = Billing::generateInvoiceNo(['id' => $newIndex, 'policy_no' => $batch->policy_no, 'month' => date('n')]);
        $billing->invoice_date = date('Y-m-d');
        $billing->due_date = Billing::getDueDate($tc->grace_period);
        $billing->accept_date = date('Y-m-d');
        $billing->total_member = $batch->total_member;
        $billing->gross_premium = $batch->total_nett_premium;
        $billing->nett_premium = $batch->total_nett_premium;
        $billing->status = Billing::STATUS_UNVERIFIED;
        $billing->created_at = date('Y-m-d');
        $billing->created_by = $this->createdBy;
        $billing->discount = $batch->total_gross_premium * $commission->discount / 100;
        $billing->handling_fee = $batch->total_gross_premium * $commission->handling_fee / 100;
        $billing->pph = ($billing->discount * $commission->pph / 100) + ($billing->handling_fee * $commission->pph / 100);
        $billing->ppn = ($billing->discount * $commission->ppn / 100) + ($billing->handling_fee * $commission->ppn / 100);
        $billing->admin_cost = ($billing != null) ? 0 : $tc->admin_cost;
        $billing->policy_cost = ($billing != null) ? 0 : $tc->policy_cost;
        $billing->member_card_cost = ($billing != null) ? 0 : $tc->member_card_cost;
        $billing->certificate_cost = ($billing != null) ? 0 : $tc->certificate_cost;
        $billing->stamp_cost = ($billing != null) ? 0 : $tc->stamp_cost;
        $billing->total_billing = $billing->gross_premium - $billing->discount - $billing->handling_fee +
            $billing->pph - $billing->ppn + $billing->admin_cost + $billing->policy_cost +
            $billing->member_card_cost + $billing->certificate_cost + $billing->stamp_cost;
        if (!$billing->save()) {
            return [
                'status_code' => 500,
                'is_success' => 0,
                'message' => 'Unknown Internal Server Failure, Please retry the process again - ' . $billing->getErrors()
            ];
        }
        return [
            'status_code' => 200,
            'is_success' => 1,
            'message' => 'success'
        ];;
    }
	
	
	
	// echo round_up(3.63333333333, 8);

    public function actionCalculatePremi()
    {
       Yii::$app->response->format = Response::FORMAT_JSON;
		
		
		$Akumulasi_Peserta = '';
		$isRedundant = 0;

        $birthDate = Yii::$app->request->get('birth_date');
        $startDate = Yii::$app->request->get('start_date');
        $endDate = Yii::$app->request->get('end_date');
        $sumInsured = Yii::$app->request->get('sum_insured');
		$produk = Yii::$app->request->get('produk');
		$age = Yii::$app->request->get('age');
		$nik = Yii::$app->request->get('nik');
		// echo $nik;
		
	        $policybyproduk = Policy::findOne([
                'produk' => $produk,
            ]);

            $quotation = Quotation::findOne([
                'id' => $policybyproduk->quotation_id,
            ]);

            $term = member::getTermJatim($quotation->rate_type, $startDate, $endDate);
			
			// echo $term;
            $ratepremi = QuotationRate::findOne([
                'term' => $term,
                'quotation_id' => $policybyproduk->quotation_id
            ]);
			
           
					
				$cekAcumulate = member::findOne([
                'no_ktp' => $nik ]);
				
				if ($cekAcumulate != 0){
					// $Akumulasi_Peserta = 'Peserta sudah pernah terdaftar dengan uang pertanggungan sebelumnya adalah  sebesar'.' ' . number_format($cekAcumulate ->sum_insured);
					$isRedundant = 1;
				}
				
				
								
				$up_awal = ((int)$cekAcumulate ->sum_insured);
					// var_dump($up_awal);
				// var_dump($cekAcumulate);

				if ($cekAcumulate != 0) {
					$sub_total_Acumulate = ((int)$up_awal + $sumInsured);
					
					 // echo($sub_total_Acumulate);
					
					$quotationUwLimit = QuotationUwLimit::find()
						->where(['quotation_id' => $policybyproduk->quotation_id])
						->andWhere(['<=', 'min_age', $age])
						->andWhere(['>=', 'max_age', $age])
						->andWhere(['<=', 'min_si', $sub_total_Acumulate])
						->andWhere(['>=', 'max_si', $sub_total_Acumulate])
						->one();

					// var_dump($sub_total_Acumulate);
				} else {
					$sub_total_Acumulate = 0;
					$quotationUwLimit = QuotationUwLimit::find()
						->where(['quotation_id' => $policybyproduk->quotation_id])
						->andWhere(['<=', 'min_age', $age])
						->andWhere(['>=', 'max_age', $age])
						->andWhere(['<=', 'min_si', $sumInsured])
						->andWhere(['>=', 'max_si', $sumInsured])
						->one();
				}

		//
        $totalPremium = $sumInsured * $ratepremi->rate / 1000;
		
		$roudpremi=(round($totalPremium, 6));
		
		$medicalCode = $quotationUwLimit->medical_code;

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'total_nett_premium' => $roudpremi,
			'medicalCode' => $medicalCode,
			'akumulasi' => $isRedundant ,
			'nilai_akumulasi' => $sub_total_Acumulate ,


        ];

    }
	
	 public function actionAddClaim()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $members = Yii::$app->request->post('members');
        if (empty($members)) {
            Yii::$app->response->statusCode = 404;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'
            ];
        }
		$id_loan = Yii::$app->request->post('id_loan');
		// var_dump($id_loan);
	
        $batch = $this->_memberClaim($members);
		if ($batch['isRedundant'] == 1) {
            Yii::$app->response->statusCode = 409;
            return [
                'is_success' => 0,
                'message' => 'Already exist'
            ];
		}
		
		
        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'message' => 'Succesfully',
			'dokument_klaim' => $batch['dokument'],
			
        ];
    }
	
	protected function _memberClaim($members)
    {
        $memberCols = [
            'id_loan', 'name','tanggal_pengajuan','tanggal_kejadian','jenis_claim','sebab_claim','estimasi_nilai_claim','keterangan','create_at'
        ];
        $memberRows = [];

		$dokument = null;
		$isRedundant = 0;
		$notFound = 0;
		$notAccepted = 0;
		
        foreach ($members as $member) {
			$currentDate = new \DateTime();
			$createAt = $currentDate->format('Y-m-d H:i:s');
			$id_loan = Utils::sanitize($member['id_loan']);
			
			$checkparticipant = claim_bank_jatim::findOne(['id_loan' => $id_loan]);
			if ($checkparticipant != null) {
				$isRedundant = 1;
			}
			
			// $checkparticipantakseptasi = Member::findOne(['id_loan' => $id_loan]);
			// if ($checkparticipantakseptasi == null) {
				// $notFound = 1;
			// }
			
			$checkparticipantnonakseptasi = Member::findOne(['member_no' => $null]);
			if ($checkparticipantnonakseptasi == null) {
				$notFound = 1;
			}
			
            $name = Utils::sanitize($member['name']);
			$tanggal_pengajuan = Utils::sanitize($member['tanggal_pengajuan']);
            $tanggal_kejadian = Utils::sanitize($member['tanggal_kejadian']);
			$jenis_claim = Utils::sanitize($member['jenis_claim']);
			$sebab_claim = Utils::sanitize($member['sebab_claim']);
			$estimasi_nilai_claim = Utils::sanitize($member['estimasi_nilai_claim']);
			$keterangan = Utils::sanitize($member['keterangan']);
			
			$dokument = dokument_claim_jatim::getAll(['jenis_claim' => $jenis_claim]);
			$memberRows[] = [
				 $id_loan,$name,$tanggal_pengajuan,$tanggal_kejadian,$jenis_claim,$sebab_claim,$estimasi_nilai_claim,$keterangan,$createAt
            ];
		
			// var_dump($dokument);
        }
	
		if ($isRedundant == 0) {
			Yii::$app->db->createCommand()->batchInsert(claim_bank_jatim::tableName(), $memberCols, $memberRows)->execute();
		}
		
        return array ('status' => true , 'dokument' => $dokument, 'isRedundant' => $isRedundant , 'notFound' => $notFound);
            
    }
	

	public function actionUploadFile()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		$claimDetail = new claim_bank_jatim_detail();
		
		$currentDate = new \DateTime();
			$createAt = $currentDate->format('Y-m-d H:i:s');
		
        if (Yii::$app->request->ispost) {
            $ktp = UploadedFile::getInstanceByName('files');
            $basePath = \Yii::getAlias('@webroot') . '/images/post_images/';
			
			$id_loan = Yii::$app->request->post('id_loan');
			$kode_dokumen = Yii::$app->request->post('kode_dokumen');
			
			$ktp->saveAs($basePath  . $id_loan .'-'. $kode_dokumen .'-'.$ktp->baseName . '.' . $ktp->extension);
			
			// $claimDetail = new claim_bank_jatim_detail();
			
			$claimDetail->id_loan = $id_loan;
			$claimDetail->files = $id_loan . '-' .$kode_dokumen .'-'. $ktp->baseName . '.' . $ktp->extension;
			$claimDetail->kode_dokumen = $kode_dokumen;
			$claimDetail -> tgl_upload = $createAt;
			$claimDetail->save(false);
			
		}
		return array('status' => true );
    }
	
	  public function actionCreateMemberCbc()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $members = Yii::$app->request->post('members');
        if (empty($members)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'
            ];
        }
		
		$produk =  Utils::sanitize($members[0]['produk']);
		
        $policy = Policy::findOne(['produk' => $produk]);
        if ($policy == null) {
            Yii::$app->response->statusCode = 404;
            return [
                'is_success' => 0,
                'message' => 'Policy not found'
            ];
        }

        $batch = $this->_createBatchCbc($policy, $members);
		
		if ($batch['member']['isRedundant'] == 1) {
            Yii::$app->response->statusCode = 406;
            return [
                'is_success' => 0,
                'message' => 'Data Peserta Tidak Dalam Kategori Case By Case'
            ];
		}

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'message' => 'Succesfully uploaded',
            'total_member' => $batch['batch']->total_member,
            'total_up' => $batch['batch']->total_up,
            'total_nett_premium' => $batch['batch']->total_nett_premium,
            'rate' =>  $batch['member']['rate'],
            'status_uw' => $batch['member']['medicalCode'],
            'print_url' => 'https://reli.id/dev/h2h-bank-jatim/member/print-sertifikat?id_loan=' . $batch['member']['idLoan'],
			'policy_no' =>  $batch['member']['policy_no'],
			'dokument' => $batch['member']['dokument'],
        ];
    }

    protected function _createBatchCbc($policy, $members)
    {
        $batchNo = Batch::generateBatchNo($policy->policy_no);
        $member = $this->_createMemberCbc($policy->policy_no, $batchNo, $members);
		
		$batch = null;
		if($member['isRedundant']== 0)
		{
        $batch = new Batch();
        $batch->policy_no = $member['policy_no'];
        $batch->batch_no = $batchNo;
        $batch->total_member = $member['totalMember'];
        $batch->total_member_accepted = $member['totalMember'];
        $batch->total_member_pending = 0;
        $batch->total_up = $member['totalUp'];
        $batch->total_gross_premium = $member['totalNettPremium'];
        $batch->total_discount_premium = 0;
        $batch->total_extra_premium = 0;
        $batch->total_saving_premium = 0;
        $batch->total_nett_premium = $member['totalNettPremium'];
        $batch->status = 'PENDING';
        $batch->created_at = date('Y-m-d H:i:s');
        $batch->created_by = $this->createdBy;
        $batch->save(false);
		}
        return [
            'batch' => $batch,
            'member' => $member
        ];
    }

    protected function _createMemberCbc($policyNo, $batchNo, $members)
    {
        $dokument = null;
		
        $personalCols = ['personal_no', 'name', 'birth_date', 'id_card_no'];
        $memberCols = [
            'policy_no', 'batch_no', 'member_no', 'personal_no', 'age', 'term', 'start_date', 'end_date',
            'sum_insured', 'total_si', 'total_premium', 'rate_premi', 'gross_premium', 'basic_premium', 'nett_premium',
            'medical_code', 'status', 'member_status', 'created_at', 'created_by', 'contract_date', 'produk', 'branch_office_code',
			'id_loan', 'status_uw', 'no_ktp','pekerjaan','jenis_transaksi',
        ];
        $personalRows = [];
        $memberRows = [];

        $totalMember = 0;
        $totalUp = 0;
        $totalNettPremium = 0;
        $rate = '';
        $medicalCode = '';
        $idLoan = '';
		$isRedundant = 0;
        foreach ($members as $member) {
            $sumInsured = Utils::sanitize($member['sum_insured']);
            $startDate = Utils::sanitize($member['start_date']);
            $endDate = Utils::sanitize($member['end_date']);
            $produk =  Utils::sanitize($member['produk']);
			$personalNodoubel = Personal::generatePersonalNo($name, $birthDate);

            $policybyproduk = Policy::findOne([
                'produk' => $produk,
            ]);

            $quotation = Quotation::findOne([
                'id' => $policybyproduk->quotation_id,
            ]);

            // $quotation = Quotation::findOne(1);
            $age =  Utils::sanitize($member['age']);
            $term = member::getTerm($quotation->rate_type, $startDate, $endDate);
			
			
            $ratepremi = QuotationRate::findOne([
                'term' => $term,
                'quotation_id' => $policybyproduk->quotation_id
            ]);
			
			$otherMember = Personal::find()
					->asArray()
					->select([
						Personal::tableName() . '.id',
						'SUM(member.sum_insured) AS sum_insured',
					])
					->innerJoin(Member::tableName(), Member::tableName() . '.personal_no = ' . Personal::tableName() . '.personal_no')
					->where([
						Member::tableName() . '.policy_no' => $policyNo,
						Personal::tableName() . '.name' => $personal->name,
						Personal::tableName() . '.birth_date' => $personal->birth_date
					])
					->andWhere(['not', [Member::tableName() . '.member_no' => null]])
					->one();					
			$accStatus = '';
			if ($otherMember != null) {
				$accStatus = "Accumulated";
				$sumInsured += $otherMember->sum_insured;
			}
				
            $quotationUwLimit = QuotationUwLimit::find()
                ->where(['quotation_id' => $policybyproduk->quotation_id])
                ->andWhere(['<=', 'min_age', $age])
                ->andWhere(['>=', 'max_age', $age])
                ->andWhere(['<=', 'min_si', $sumInsured])
                ->andWhere(['>=', 'max_si', $sumInsured])
                ->one();
			

			 $medicalCode = $quotationUwLimit->medical_code;	
			 if($medicalCode == 'CAC'){
				 $isRedundant = 1;
			 }

            $totalPremium = $sumInsured * $ratepremi->rate / 1000;
			
			$tgl = Utils::sanitize($member['birth_date']);
			 $dob = str_replace('T00:00:00', '', $tgl);
            // $birthDate = Utils::sanitize($member['birth_date']);
			$birthDate = $dob;

            $name = Utils::sanitize($member['name']);
            $birthDate = Utils::sanitize($member['birth_date']);
            $age = Utils::sanitize($member['age']);
            $idCardNo = Utils::sanitize($member['no_ktp']);
            $sumInsured = Utils::sanitize($member['sum_insured']);
            $startDate = Utils::sanitize($member['start_date']);;
            $endDate = Utils::sanitize($member['end_date']);
            $term = Utils::sanitize($member['term']);
            $rate = $ratepremi->rate;
            $nettPremium = $totalPremium;
            $personalNo = Personal::generatePersonalNo($name, $birthDate);
            $memberNo = '';
            $contract_date = Utils::sanitize($member['contract_date']);
            $produk = Utils::sanitize($member['produk']);
            $branch_office_code = Utils::sanitize($member['branch_office_code']);
            $id_loan = Utils::sanitize($member['id_loan']);
            $status_uw = $quotationUwLimit->medical_code;
            $no_ktp = Utils::sanitize($member['no_ktp']);
			$pekerjaan = Utils::sanitize($member['pekerjaan']);
			$jenis_transaksi = Utils::sanitize($member['jenis_transaksi']);

            $personalRows[] = [$personalNo, $name, $birthDate, $idCardNo];

            $memberRows[] = [
                $policybyproduk->policy_no, $batchNo, $memberNo, $personalNo, $age, $term, $startDate, $endDate,
                $sumInsured, $sumInsured, $nettPremium, $rate, $nettPremium, $nettPremium, $nettPremium,
                $quotationUwLimit->medical_code, Member::MEMBER_STATUS_PENDING, Member::MEMBER_STATUS_PENDING, date("Y-m-d H:i:s"), $this->createdBy,
                $contract_date, $produk, $branch_office_code, $id_loan, $status_uw, $no_ktp,$pekerjaan,$jenis_transaksi,
            ];

            $totalMember++;
            $totalUp += $sumInsured;
            $totalNettPremium += $nettPremium;
            $rate = $rate;
            $medicalCode = $quotationUwLimit->medical_code;
            $idLoan = $member['id_loan'];
			$policy_no = $policybyproduk->policy_no;
        }
		
		$dokument = Dokumen_Medis::getAll(['medis' => $medicalCode]);
			
		if($isRedundant==0)
		{
        Yii::$app->db->createCommand()->batchInsert(Personal::tableName(), $personalCols, $personalRows)->execute();
        Yii::$app->db->createCommand()->batchInsert(Member::tableName(), $memberCols, $memberRows)->execute();
		}
		
        return [
            'totalUp' => $totalUp,
            'rate' => $rate,
            'totalNettPremium' => $totalNettPremium,
            'medicalCode' => $medicalCode,
            'totalMember' => $totalMember,
            'idLoan' => $idLoan,
			'policy_no' => $policy_no,
			'dokument' => $dokument,
			'isRedundant' => $isRedundant,
        ];
		
    }

    protected function _createBillingCbc($policy, $batch)
    {
        $tc = QuotationTc::findOne(['quotation_id' => $policy->quotation_id]);
        if ($tc == null) {
            return [
                'status_code' => 404,
                'is_success' => 0,
                'message' => 'TC not found'
            ];
        }

        $commission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
        if ($commission == null) {
            return [
                'status_code' => 404,
                'is_success' => 0,
                'message' => 'Commission not found'
            ];
        }

        $latestBilling = Billing::find()->orderBy(['id' => SORT_DESC])->one();
        $newId = ($latestBilling != null) ? $latestBilling->id + 1 : 1;

        $newIndex = Billing::find()->where([
            'policy_no' => $batch->policy_no,
            'YEAR(invoice_date)' => date("Y")
        ])->count() + 1;

        $billing = Billing::find()->where([
            'policy_no' => $batch->policy_no
        ])->one();

        $billing = new Billing();
        $billing->batch_no = $batch->batch_no;
        $billing->policy_no = $batch->policy_no;
        $billing->reg_no = Billing::generateRegNo(['id' => $newId, 'policy_no' => $batch->policy_no, 'month' => date('n')]);
        $billing->invoice_no = Billing::generateInvoiceNo(['id' => $newIndex, 'policy_no' => $batch->policy_no, 'month' => date('n')]);
        $billing->invoice_date = date('Y-m-d');
        $billing->due_date = Billing::getDueDate($tc->grace_period);
        $billing->accept_date = date('Y-m-d');
        $billing->total_member = $batch->total_member;
        $billing->gross_premium = $batch->total_nett_premium;
        $billing->nett_premium = $batch->total_nett_premium;
        $billing->status = Billing::STATUS_UNVERIFIED;
        $billing->created_at = date('Y-m-d');
        $billing->created_by = $this->createdBy;
        $billing->discount = $batch->total_gross_premium * $commission->discount / 100;
        $billing->handling_fee = $batch->total_gross_premium * $commission->handling_fee / 100;
        $billing->pph = ($billing->discount * $commission->pph / 100) + ($billing->handling_fee * $commission->pph / 100);
        $billing->ppn = ($billing->discount * $commission->ppn / 100) + ($billing->handling_fee * $commission->ppn / 100);
        $billing->admin_cost = ($billing != null) ? 0 : $tc->admin_cost;
        $billing->policy_cost = ($billing != null) ? 0 : $tc->policy_cost;
        $billing->member_card_cost = ($billing != null) ? 0 : $tc->member_card_cost;
        $billing->certificate_cost = ($billing != null) ? 0 : $tc->certificate_cost;
        $billing->stamp_cost = ($billing != null) ? 0 : $tc->stamp_cost;
        $billing->total_billing = $billing->gross_premium - $billing->discount - $billing->handling_fee +
            $billing->pph - $billing->ppn + $billing->admin_cost + $billing->policy_cost +
            $billing->member_card_cost + $billing->certificate_cost + $billing->stamp_cost;
        if (!$billing->save()) {
            return [
                'status_code' => 500,
                'is_success' => 0,
                'message' => 'Unknown Internal Server Failure, Please retry the process again - ' . $billing->getErrors()
            ];
        }
        return [
            'status_code' => 200,
            'is_success' => 1,
            'message' => 'success'
        ];;
    }
	
	public function actionUploadFileCbc()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		
		$claimDetail = new map_member_medis();
		
        if (Yii::$app->request->ispost) {
            $ktp = UploadedFile::getInstanceByName('files');
            $basePath = \Yii::getAlias('@webroot') . '/images/post_medis/';
			
			$id_loan = Yii::$app->request->post('id_loan');
			$kode_dokumen = Yii::$app->request->post('kode_dokumen');
			
			$ktp->saveAs($basePath  . $id_loan .'-'. $kode_dokumen .'-'.$ktp->baseName . '.' . $ktp->extension);
			
			// $claimDetail = new claim_bank_jatim_detail();
			
			$claimDetail->id_loan = $id_loan;
			$claimDetail->files = $id_loan . '-' .$kode_dokumen .'-'. $ktp->baseName . '.' . $ktp->extension;
			$claimDetail->kode_dokumen = $kode_dokumen;
			$claimDetail->save(false);
			
		}
		return array('status' => true );
    }
	
	public function actionGetPenghantarMedis()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		$id_loan = Yii::$app->request->get('id_loan');
		
		$member = Member::findOne(['id_loan' => $id_loan]);

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'url' => Url::base(true) . Member::PICTURE_PATH . $member->file_medis,

        ];
    }
	
	public function actionGetMemberCac()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		$id_loan = Yii::$app->request->get('id_loan');
		
		$member = Member::findOne(['id_loan' => $id_loan]);

        $personal = personal::findOne([
                'personal_no' => $member->personal_no,
        ]);
		
		// $time = time();
		// $tes = Yii::$app->formatter->asDate($time, 'long')
		
		$Tanggal_Cetak =date("d-M-Y");
		
		// $star_date = Utils::convertDateTodMyPrint($member->star_date);

		
		Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'Id_Loan' => $id_loan,
			'Nomor_Sertifkat' => $id_loan,
			'Nomor_Polis' =>$member-> policy_no,
			'Nama_Peserta' => $personal-> name,
			'Tanggal_Lahir' => $personal-> birth_date,
			'Nomor_Peserta' =>$member-> member_no,
			'Jenis_Produk' => $member->produk,
			'Masa' => $member->term .' '. 'bulan',
			'Periode_Asuransi' => $member->start_date .' sd ' . $member ->end_date,
			// 'Periode_Asuransi' => $star_date .' sd ' . $member ->end_date,
			'Uang_Pertanggungan' => $member->sum_insured,
			'Premi' => $member->gross_premium,
			'Extra_Premi' => $member->em_premium,
			'Total_Premi' => $member->nett_premium,
			'Tanggal_Cetak' => 'Jakarta'.' ' .$Tanggal_Cetak,
			'Nama_TTD' => 'Aisyah Aini',
			'Department' => 'Underwriting',

        ];
	}
		
	public function actionGetMemberCbc()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		$id_loan = Yii::$app->request->get('id_loan');
		
		$member = Member::findOne(['id_loan' => $id_loan]);

        $personal = personal::findOne([
                'personal_no' => $member->personal_no,
        ]);
		
		$Tanggal_Cetak =date("d-M-Y");

		// $currentDate =Utils::convertDateTodMyPrint(date("Y-m-d"));
	
		
		Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'Id_Loan' => $id_loan,
			'Nomor_Sertifkat' => $id_loan,
			'Nomor_Polis' =>$member-> policy_no,
			'Nama_Peserta' => $personal-> name,
			'Tanggal_Lahir' => $personal-> birth_date,
			'Nomor_Peserta' =>$member-> member_no,
			'Jenis_Produk' => $member->produk,
			'Masa' => $member->term .' '. 'bulan',
			'Periode_Asuransi' => $member->start_date .' sd ' . $member ->end_date,
			'Uang_Pertanggungan' => $member->sum_insured,
			'Premi' => $member->gross_premium,
			'Extra_Premi' => $member->em_premium,
			'Total_Premi' => $member->nett_premium,
			'Tanggal_Cetak' => 'Jakarta'.' ' .$Tanggal_Cetak,
			'Nama_TTD' => 'Aisyah Aini',
			'Department' => 'Underwriting',

        ];
	}	
		
	public function actionCalculateRefund()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id_loan = Yii::$app->request->get('id_loan');
        $endDate = Yii::$app->request->get('tanggal_refund');
		
		 $get_data_member = member::findOne([
                'id_loan' => $id_loan,
            ]);
		
		if (empty($get_data_member)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'
            ];
        }	
		
		$termNew = member::getTermRefund($get_data_member->start_date, $endDate);
		// echo $termNew;				
		$sisa_masa = $get_data_member->term - $termNew;
		 // echo $sisa_masa;
			
			// $total_refund_premi = ($get_data_member['gross_premium']) ;
	
		$cek_refund_premi = ($sisa_masa / $get_data_member->term) * (40/100 * $get_data_member->gross_premium) ;
		
		// echo $cek_refund_premi;
		if ($cek_refund_premi <50000)
		{
			$total_refund_premi = 0;
		}
		
		else
		{
			$total_refund_premi = $cek_refund_premi;
		}
		
		$premi_refund=(round($total_refund_premi, 8));

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'premi_refund' => $premi_refund,

        ];
    }	
	
	public function actionPembatalanAkseptasi()
    {
         Yii::$app->response->format = Response::FORMAT_JSON;
		
		 $members = Yii::$app->request->post('members');
		
		$id_loan =  Utils::sanitize($members[0]['id_loan']);
		$endDate =  Utils::sanitize($members[1]['tanggal_refund']);
		$jenis_pembatalan =  Utils::sanitize($members[0]['jenis_pembatalan']);
		
		 $get_data_member = member::findOne([
                'id_loan' => $id_loan,
            ]);
					
		
		 if (empty($get_data_member)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'
            ];
        }
		
		$termNew = member::getTermRefund($get_data_member->start_date, $endDate);
		// var_dump ($termNew);				
		$sisa_masa = $get_data_member->term - $termNew;
			// var_dump ($sisa_masa);
		 $cek_refund_premi = ($sisa_masa / $get_data_member->term) * (40/100 * $get_data_member->gross_premium) ;
		// var_dump ($cek_refund_premi);	
		
		if ($cek_refund_premi <50000)
		 {
		 $total_refund_premi = 0;
		 }
		
		else
		 {
			 $total_refund_premi = $cek_refund_premi;
		 }
		
		$premi_refund=(round($total_refund_premi, 8));
		
		$get_data_member->status = 'Cancel';
		$get_data_member->member_status = 'Cancel';
		$get_data_member->refund_premi = $premi_refund;
		$get_data_member->jenis_pembatalan = $jenis_pembatalan;
		$get_data_member->save(false);
		
		// $premi_refund=(round($total_refund_premi, 8));

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'premi_refund' => $premi_refund,

        ];
    }	
		
	public function actionPembatalanPengajuanAkseptasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		 $members = Yii::$app->request->post('members');
		
		$id_loan =  Utils::sanitize($members[0]['id_loan']);
		$endDate =  Utils::sanitize($members[1]['tanggal_refund']);
		
		$get_data_member = member::findOne([
                'id_loan' => $id_loan,
				'member_status' => 'Pending',
            ]);
		
		 if (empty($get_data_member)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data Tidak Memenuhi Kebutuhan Kriteria'
            ];
        }
		
		
		$get_premi_refund = $get_data_member->gross_premium;
		
		$get_data_member->status = 'Cancel';
		$get_data_member->member_status = 'Cancel';
		$get_data_member->save(false);
		
		$premi_refund=(round($get_premi_refund, 8));

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
           

        ];
    }	
	
	
	public function actionRolbackPeserta()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		 $members = Yii::$app->request->post('members');

		
		$id_loan =  Utils::sanitize($members[0]['id_loan']);
		$endDate =  Utils::sanitize($members[1]['tanggal_refund']);
		
		$get_data_member = member::findOne([
                'id_loan' => $id_loan,
            ]);
		
		 if (empty($get_data_member)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'		
            ];
        }
		
		
		$member = Member::findOne(['id_loan' => $id_loan]);
		$batchId = $member->id;
		$member->delete();

		// Yii::$app->session->setFlash('success', "Successfully deleted");

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
           

        ];
    }

	public function actionCallMutasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		 $members = Yii::$app->request->post('members');

		
		$Id_Loan =  Utils::sanitize($members[0]['Id_Loan']);
		$No_Polis =  Utils::sanitize($members[0]['No_Polis']);
		$Noref_Mutasi =  Utils::sanitize($members[0]['Noref_Mutasi']);
		$Nilai_Mutasi =  Utils::sanitize($members[0]['Nilai_Mutasi']);
		$Rek_Sumber =  Utils::sanitize($members[0]['Rek_Sumber']);
		$Rek_Tujuan =  Utils::sanitize($members[0]['Rek_Tujuan']);
		$Tgl_Mutasi =  Utils::sanitize($members[0]['Tgl_Mutasi']);
		
		$get_data_member = member::findOne([
                'id_loan' => $Id_Loan,
            ]);
		
		 if (empty($get_data_member)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'		
            ];
        }
		
		$get_data_member->noref_mutasi = $Noref_Mutasi;
		$get_data_member->nilai_mutasi = $Nilai_Mutasi;
		$get_data_member->rek_sumber = $Rek_Sumber;
		$get_data_member->rek_tujuan = $Rek_Tujuan;
		$get_data_member->tgl_mutasi = $Tgl_Mutasi;
		$get_data_member->save(false);
		// Yii::$app->session->setFlash('success', "Successfully deleted");

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
           

        ];
    }	
	
	public function actionPesertujuanPesertaCbc()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		 $members = Yii::$app->request->post('members');
	
		
		$id_loan =  Utils::sanitize($members[0]['id_loan']);
		$statusem =  Utils::sanitize($members[0]['status_em']);
		
		$get_data_member = member::findOne([
                'id_loan' => $id_loan,
            ]);
		
		 if (empty($get_data_member)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'		
            ];
        }
		
		$get_data_member->status_em = $statusem;
		$get_data_member->save(false);

		// Yii::$app->session->setFlash('success', "Successfully deleted");

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,

        ];
    }

	 public function actionCreateh2h()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
	
        $members = Yii::$app->request->post('members');
        if (empty($members)) {
            Yii::$app->response->statusCode = 202;
            return [
                'is_success' => 0,
                'message' => 'Data cannot be empty'
            ];
        }
		
		$produk =  Utils::sanitize($members[0]['produk']);
		
        $policy = Policy::findOne(['produk' =>  $produk ]);
        if ($policy == null) {
            Yii::$app->response->statusCode = 404;
            return [
                'is_success' => 0,
                'message' => 'Policy not found'
            ];
        }
		
        $batch = $this->_createBatchh2h($policy, $members);
		
		// if ($batch['member']['isRedundant'] == 1) {
            // Yii::$app->response->statusCode = 406;
            // return [
                // 'is_success' => 0,
                // 'message' => 'Data Peserta  Dalam Kategori Case By Case'
            // ];
		// }
		
        $billing = $this->_createBillingh2h($policy, $batch['batch']);
        if ($billing['is_success'] == 0) {
            Yii::$app->response->statusCode = $billing['status_code'];
            return [
                'is_success' => 0,
                'message' => $billing['message']
            ];
        }
		
		$id_loan =$batch['member']['idLoan'];
		
		$member_detail = member::findOne([
                'id_loan' => $id_loan,
            ]);
			
			
		$personal_detail = personal::findone([
                'personal_no' => $member_detail-> personal_no,
         ]);	
		 
		$date = Utils::tgl_indo(date("Y-m-d"));
		
		
		
		 // $filename = $this->id_loan;
        // $extension = $this->file_upload->extension;

        // $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . "." . $extension;
        // $this->file_upload->saveAs($path);
		
		// $filePath = 'D:/';
		
		$filePath = \Yii::getAlias('@webroot') . self::PICTURE_PATH;
		$fileName = $batch['member']['idLoan'] . '.pdf';
		// echo("<script>console.log('PHP: " . $batch['member']['idLoan'] . "');</script>");
		$patch= $filePath . $fileName;
	
		require_once('fpdf.php');
		// Create a new FPDF instance
		$pdf = new FPDF();
		$pdf->AddPage();						
		$pdf->SetFont('Arial','B',16);
		// $pdf->Image('PICTURE_PATH_Logo', 10, 10, -200);
		$pdf->Image(\Yii::getAlias('@webroot') . self::PICTURE_PATH_Logo, 10, 10, -200);
		$pdf->SetY(30);
		$pdf->SetX(0);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'SERTIFIKAT ASURANSI', 0, 1, 'C');
		$pdf->SetY($pdf->GetY() + 2);
		$pdf->SetX(0);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'NOMOR : '. $batch['member']['idLoan'], 0, 1, 'C');
		$pdf->SetY($pdf->GetY() + 10);
		$pdf->SetX(10);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(40, 5, 'Nomor Polis', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $batch['member']['policy_no'], 0, 1, 'L');
		$pdf->Cell(40, 5, 'Nama', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $personal_detail->name, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Nomor Peserta', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $member_detail->member_no, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Tanggal Lahir', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5,Utils::tgl_indo($personal_detail->birth_date), 0, 1, 'L');
		$pdf->SetY($pdf->GetY() + 10);
		$pdf->SetX(10);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'Adalah Peserta Polis Dari Asuransi Jiwa (Pemegang Polis)', 0, 0, 'L');
		$pdf->SetY($pdf->GetY() + 8);
		$pdf->SetX(0);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'BANK BAPAS 69', 0, 1, 'C');
		$pdf->SetFont('Arial','',8);
		$pdf->SetY($pdf->GetY() + 8);
		$pdf->SetX(10);
		$pdf->Cell($pdf->GetPageWidth(), 5, 'Dengan ketentuan sebagai berikut :', 0, 0, 'L');
		$pdf->SetY($pdf->GetY() + 8);
		$pdf->SetX(10);
		$pdf->Cell(40, 5, 'Jenis Asuransi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $member_detail->produk, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Masa Asuransi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, $member_detail->term, 0, 1, 'L');
		$pdf->Cell(40, 5, 'Periode Asuransi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5,Utils::tgl_indo($member_detail->start_date) . ' '. 's/d' . ' ' . Utils::tgl_indo($member_detail->end_date) , 0, 1, 'L');
		$pdf->Cell(40, 5, 'Uang Pertanggungan', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($batch['batch']->total_up), 0, 1, 'L');
		$pdf->Cell(40, 5, 'Premi Gross', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($batch['batch']->total_nett_premium), 0, 1, 'L');
		$pdf->Cell(40, 5, 'Extra Premi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($member_detail->extra_premium), 0, 1, 'L');
		$pdf->Cell(40, 5, 'Total Premi', 0, 0, 'L');
		$pdf->Cell(5, 5, ':', 0, 0, 'L');
		$pdf->Cell($pdf->GetPageWidth() - 65, 5, number_format($batch['batch']->total_nett_premium), 0, 1, 'L');
		$pdf->SetY($pdf->GetY() + 10);
		$pdf->Cell($pdf->GetPageWidth() - 20, 5, 'Jakarta ,' .' ' . $date, 0, 1, 'R');
		// $pdf->Image('http://localhost/ajri-core-system-h2h-bank-jatim/protected/controllers/api/v1/policy-qr.png', $pdf->GetPageWidth() - 40, $pdf->GetY() + 2, -100);
		//$pdf->Image(\Yii::getAlias('@webroot') . self::PICTURE_PATH_Ttd, 10, 10, -200);
		$pdf->Image(\Yii::getAlias('@webroot') . self::PICTURE_PATH_Ttd, $pdf->GetPageWidth() - 40, $pdf->GetY() + 2, -100);
		$pdf->SetY($pdf->GetY() + 22);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell($pdf->GetPageWidth() - 31, 5, 'Aisyah Aini', 0, 1, 'R');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell($pdf->GetPageWidth() - 31, 5, 'Underwriting', 0, 1, 'R');

		$pdf->Output($filePath . $fileName, 'F');

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'message' => 'Succesfully uploaded',
            'total_member' => $batch['batch']->total_member,
            'total_up' => $batch['batch']->total_up,
            'total_nett_premium' => $batch['batch']->total_nett_premium,
            'rate' =>  $batch['member']['rate'],
            'status_uw' => $batch['member']['medicalCode'],
			//'print_url' => 'https://reli.id/dev/h2h-bank-jatim/member/print-sertifikat?id_loan=' . $batch['member']['idLoan'],
			// 'print_url' => 'https://h2h-ajri-staging.reli.id/images/e_policy/' .$fileName,
			'print_url' => 'https://h2h-ajri.reli.id/images/e_policy/' .$fileName,
			'policy_no' =>  $batch['member']['policy_no'],
        ];
    }

    protected function _createBatchh2h($policy, $members)
    {
		
        $batchNo = Batch::generateBatchNo($policy->policy_no);
        $member = $this->_createMemberh2h($policy->policy_no, $batchNo, $members);

		$batch = null;
		if($member['isRedundant']== 0)
		{
		$batch = new Batch();
        $batch->policy_no = $member['policy_no'];
        $batch->batch_no = $batchNo;
        $batch->total_member = $member['totalMember'];
        $batch->total_member_accepted = $member['totalMember'];
        $batch->total_member_pending = 0;
        $batch->total_up = $member['totalUp'];
        $batch->total_gross_premium = $member['totalNettPremium'];
        $batch->total_discount_premium = 0;
        $batch->total_extra_premium = 0;
        $batch->total_saving_premium = 0;
        $batch->total_nett_premium = $member['totalNettPremium'];
        $batch->status = Batch::STATUS_CLOSED;
        $batch->created_at = date('Y-m-d H:i:s');
        $batch->created_by = $this->createdBy;
        $batch->save(false);
		}
        return [
            'batch' => $batch,
            'member' => $member
        ];
    }

    protected function _createMemberh2h($policyNo, $batchNo, $members)
    {
        $personalCols = ['personal_no', 'name', 'birth_date', 'id_card_no'];
        $memberCols = [
            'policy_no', 'batch_no', 'member_no', 'personal_no', 'age', 'term', 'start_date', 'end_date',
            'sum_insured', 'total_si', 'total_premium', 'rate_premi', 'gross_premium', 'basic_premium', 'nett_premium',
            'medical_code', 'status', 'member_status', 'created_at', 'created_by', 'contract_date', 'produk', 'branch_office_code',
			'id_loan', 'status_uw', 'no_ktp','pekerjaan','acc_status',
        ];
        $personalRows = [];
        $memberRows = [];

        $totalMember = 0;
        $totalUp = 0;
        $totalNettPremium = 0;
        $rate = '';
        $medicalCode = '';
        $idLoan = '';
		$isRedundant = 0;
        foreach ($members as $member) {
            $sumInsured = Utils::sanitize($member['sum_insured']);
            $startDate = Utils::sanitize($member['start_date']);
            $endDate = Utils::sanitize($member['end_date']);
            $produk =  Utils::sanitize($member['produk']);
			$personalNodoubel = Personal::generatePersonalNo($name, $birthDate);

            $policybyproduk = Policy::findOne([
                'produk' => $produk,
            ]);
			
			$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policybyproduk->quotation_id]);
			
			$memberTotal = Member::find()
            ->where([
			'and',
				['policy_no' => $policybyproduk->policy_no],
				['!=', 'member_no', '']
			])
            ->count();
			$memberIndexNo = $memberTotal + 1;
	
			
            $quotation = Quotation::findOne([
                'id' => $policybyproduk->quotation_id,
            ]);

            // $quotation = Quotation::findOne(1);
            $age =  Utils::sanitize($member['age']);
            $term = member::getTerm($quotation->rate_type, $startDate, $endDate);
			
			
            // $ratepremi = QuotationRate::findOne([
                // 'term' => $term,
                // 'quotation_id' => $policybyproduk->quotation_id
            // ]);
			
			
			if ($quotation->rate_type == RateType::RATE_ROUND_UP) {
					$termYear = ceil($term / 12);
				} else {
					$termYear = floor($term / 12);
				}
				// var_dump($termYear);
				
			
					$quotationRate = QuotationRate::findOne([
						'quotation_id' => $policy->quotation_id,
						'term' => $termYear
					]);
					
					// var_dump($quotationRate);
					
			 $ratepremi = QuotationRate::findOne([
                'term' => $termYear,
                'quotation_id' => $policybyproduk->quotation_id
            ]);		
				

			$otherMember = Personal::find()
					->asArray()
					->select([
						Personal::tableName() . '.id',
						'SUM(member.sum_insured) AS sum_insured',
					])
					->innerJoin(Member::tableName(), Member::tableName() . '.personal_no = ' . Personal::tableName() . '.personal_no')
					->where([
						Member::tableName() . '.policy_no' => $policyNo,
						Personal::tableName() . '.name' => $personal->name,
						Personal::tableName() . '.birth_date' => $personal->birth_date
					])
					->andWhere(['not', [Member::tableName() . '.member_no' => null]])
					->one();					
			$accStatus = '';
			if ($otherMember != null) {
				$accStatus = "Accumulated";
				$sumInsured += $otherMember->sum_insured;
			}
				
            $quotationUwLimit = QuotationUwLimit::find()
                ->where(['quotation_id' => $policybyproduk->quotation_id])
                ->andWhere(['<=', 'min_age', $age])
                ->andWhere(['>=', 'max_age', $age])
                ->andWhere(['<=', 'min_si', $sumInsured])
                ->andWhere(['>=', 'max_si', $sumInsured])
                ->one();
				
				
			 // $medicalCode = $quotationUwLimit->medical_code;	
			 // if($medicalCode != 'CAC'){
				 // $isRedundant = 1;
			 // }
			 

            $totalPremium = $sumInsured * $ratepremi->rate / 1000;

			 $tgl = Utils::sanitize($member['birth_date']);
			 $dob = str_replace('T00:00:00', '', $tgl);
			
            $name = Utils::sanitize($member['name']);
            // $birthDate = Utils::sanitize($member['birth_date']);
			$birthDate = $dob;
            $age = Utils::sanitize($member['age']);
            $idCardNo = Utils::sanitize($member['no_ktp']);
            $sumInsured = Utils::sanitize($member['sum_insured']);
            $startDate = Utils::sanitize($member['start_date']);;
            $endDate = Utils::sanitize($member['end_date']);
            $term = Utils::sanitize($member['term']);
            $rate = $ratepremi->rate;
            $nettPremium = $totalPremium;
            $personalNo = Personal::generatePersonalNo($name, $birthDate);
            $memberNo = Member::generateMemberNo($memberIndexNo, $policybyproduk->policy_no);
            $contract_date = Utils::sanitize($member['contract_date']);
            $produk = Utils::sanitize($member['produk']);
            $branch_office_code = " ";
            $id_loan = Utils::sanitize($member['id_loan']);
            $status_uw = $quotationUwLimit->medical_code;
            $no_ktp = Utils::sanitize($member['no_ktp']);
			$pekerjaan = Utils::sanitize($member['pekerjaan']);
		

            $personalRows[] = [$personalNo, $name, $birthDate, $idCardNo];

            $memberRows[] = [
                $policybyproduk->policy_no, $batchNo, $memberNo, $personalNo, $age, $term, $startDate, $endDate,
                $sumInsured, $sumInsured, $nettPremium, $rate, $nettPremium, $nettPremium, $nettPremium,
                $quotationUwLimit->medical_code, Member::STATUS_INFORCE, Member::MEMBER_STATUS_INFORCE, date("Y-m-d H:i:s"), $this->createdBy,
                $contract_date, $produk, $branch_office_code, $id_loan, $status_uw, $no_ktp,$pekerjaan,$accStatus,
            ];
            $totalMember++;
            $totalUp += $sumInsured;
            $totalNettPremium += $nettPremium;
            $rate = $rate;
            $medicalCode = $quotationUwLimit->medical_code;
            $idLoan = $member['id_loan'];
			$policy_no = $policybyproduk->policy_no;
        }
		
		// if($isRedundant==0)
		// {
		Yii::$app->db->createCommand()->batchInsert(Personal::tableName(), $personalCols, $personalRows)->execute();
        Yii::$app->db->createCommand()->batchInsert(Member::tableName(), $memberCols, $memberRows)->execute();
		// }
       
		// }
        return [
            'totalUp' => $totalUp,
            'rate' => $rate,
            'totalNettPremium' => $totalNettPremium,
            'medicalCode' => $medicalCode,
            'totalMember' => $totalMember,
            'idLoan' => $idLoan,
			'policy_no' => $policy_no,
			'isRedundant' => $isRedundant,
        ];
		
    }

    protected function _createBillingh2h($policy, $batch)
    {
        $tc = QuotationTc::findOne(['quotation_id' => $policy->quotation_id]);
        if ($tc == null) {
            return [
                'status_code' => 404,
                'is_success' => 0,
                'message' => 'TC not found'
            ];
        }

        $commission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
        if ($commission == null) {
            return [
                'status_code' => 404,
                'is_success' => 0,
                'message' => 'Commission not found'
            ];
        }

        $latestBilling = Billing::find()->orderBy(['id' => SORT_DESC])->one();
        $newId = ($latestBilling != null) ? $latestBilling->id + 1 : 1;

        $newIndex = Billing::find()->where([
            'policy_no' => $batch->policy_no,
            'YEAR(invoice_date)' => date("Y")
        ])->count() + 1;

        $billing = Billing::find()->where([
            'policy_no' => $batch->policy_no
        ])->one();

        $billing = new Billing();
        $billing->batch_no = $batch->batch_no;
        $billing->policy_no = $batch->policy_no;
        $billing->reg_no = Billing::generateRegNo(['id' => $newId, 'policy_no' => $batch->policy_no, 'month' => date('n')]);
        $billing->invoice_no = Billing::generateInvoiceNo(['id' => $newIndex, 'policy_no' => $batch->policy_no, 'month' => date('n')]);
        $billing->invoice_date = date('Y-m-d');
        $billing->due_date = Billing::getDueDate($tc->grace_period);
        $billing->accept_date = date('Y-m-d');
        $billing->total_member = $batch->total_member;
        $billing->gross_premium = $batch->total_nett_premium;
        $billing->nett_premium = $batch->total_nett_premium;
        $billing->status = Billing::STATUS_UNVERIFIED;
        $billing->created_at = date('Y-m-d');
        $billing->created_by = $this->createdBy;
        $billing->discount = $batch->total_gross_premium * $commission->discount / 100;
        $billing->handling_fee = $batch->total_gross_premium * $commission->handling_fee / 100;
        $billing->pph = ($billing->discount * $commission->pph / 100) + ($billing->handling_fee * $commission->pph / 100);
        $billing->ppn = ($billing->discount * $commission->ppn / 100) + ($billing->handling_fee * $commission->ppn / 100);
        $billing->admin_cost = ($billing != null) ? 0 : $tc->admin_cost;
        $billing->policy_cost = ($billing != null) ? 0 : $tc->policy_cost;
        $billing->member_card_cost = ($billing != null) ? 0 : $tc->member_card_cost;
        $billing->certificate_cost = ($billing != null) ? 0 : $tc->certificate_cost;
        $billing->stamp_cost = ($billing != null) ? 0 : $tc->stamp_cost;
        $billing->total_billing = $billing->gross_premium - $billing->discount - $billing->handling_fee +
            $billing->pph - $billing->ppn + $billing->admin_cost + $billing->policy_cost +
            $billing->member_card_cost + $billing->certificate_cost + $billing->stamp_cost;
        if (!$billing->save()) {
            return [
                'status_code' => 500,
                'is_success' => 0,
                'message' => 'Unknown Internal Server Failure, Please retry the process again - ' . $billing->getErrors()
            ];
        }
        return [
            'status_code' => 200,
            'is_success' => 1,
            'message' => 'success'
        ];;
    }
	
	public function actionCalculatePremih2h()
    {
       Yii::$app->response->format = Response::FORMAT_JSON;
		
		
		$Akumulasi_Peserta = '';
		$isRedundant = 0;

        $birthDate = Yii::$app->request->get('birth_date');
        $startDate = Yii::$app->request->get('start_date');
        $endDate = Yii::$app->request->get('end_date');
        $sumInsured = Yii::$app->request->get('sum_insured');
		$produk = Yii::$app->request->get('produk');
		$age = Yii::$app->request->get('age');
		$nik = Yii::$app->request->get('nik');
		// echo $nik;
		
	        $policybyproduk = Policy::findOne([
                'produk' => $produk,
            ]);

            $quotation = Quotation::findOne([
                'id' => $policybyproduk->quotation_id,
            ]);

            $term = member::getTermJatim($quotation->rate_type, $startDate, $endDate);
			
			// echo $term;
            $ratepremi = QuotationRate::findOne([
                'term' => $term,
                'quotation_id' => $policybyproduk->quotation_id
            ]);
			
           
					
				$cekAcumulate = member::findOne([
                'no_ktp' => $nik ]);
				
				if ($cekAcumulate != 0){
					// $Akumulasi_Peserta = 'Peserta sudah pernah terdaftar dengan uang pertanggungan sebelumnya adalah  sebesar'.' ' . number_format($cekAcumulate ->sum_insured);
					$isRedundant = 1;
				}
				
				
								
				$up_awal = ((int)$cekAcumulate ->sum_insured);
					// var_dump($up_awal);
				// var_dump($cekAcumulate);

				if ($cekAcumulate != 0) {
					$sub_total_Acumulate = ((int)$up_awal + $sumInsured);
					
					 // echo($sub_total_Acumulate);
					
					$quotationUwLimit = QuotationUwLimit::find()
						->where(['quotation_id' => $policybyproduk->quotation_id])
						->andWhere(['<=', 'min_age', $age])
						->andWhere(['>=', 'max_age', $age])
						->andWhere(['<=', 'min_si', $sub_total_Acumulate])
						->andWhere(['>=', 'max_si', $sub_total_Acumulate])
						->one();

					// var_dump($sub_total_Acumulate);
				} else {
					$sub_total_Acumulate = 0;
					$quotationUwLimit = QuotationUwLimit::find()
						->where(['quotation_id' => $policybyproduk->quotation_id])
						->andWhere(['<=', 'min_age', $age])
						->andWhere(['>=', 'max_age', $age])
						->andWhere(['<=', 'min_si', $sumInsured])
						->andWhere(['>=', 'max_si', $sumInsured])
						->one();
				}

		//
        $totalPremium = $sumInsured * $ratepremi->rate / 1000;
		
		$roudpremi=(round($totalPremium, 6));
		
		$medicalCode = $quotationUwLimit->medical_code;

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'total_nett_premium' => $roudpremi,
			'medicalCode' => $medicalCode,
			'akumulasi' => $isRedundant ,
			'nilai_akumulasi' => $sub_total_Acumulate ,


        ];

    }
	
	
	public function actionUploadFileCancel()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;
		// tbl_map_member_cancel
		$doc_member_cancel = new map_member_cancel();
		
        if (Yii::$app->request->ispost) {
            $doc = UploadedFile::getInstanceByName('files');
            $basePath = \Yii::getAlias('@webroot') . '/images/post_cancel/';
			
			$id_loan = Yii::$app->request->post('id_loan');
			// $kode_dokumen = Yii::$app->request->post('kode_dokumen');
			
			$doc->saveAs($basePath  . $id_loan .'-'.$doc->baseName . '.' . $doc->extension);
			
			// $claimDetail = new claim_bank_jatim_detail();
			
			$doc_member_cancel->id_loan = $id_loan;
			$doc_member_cancel->files = $id_loan .'-'. $doc->baseName . '.' . $doc->extension;
			$doc_member_cancel->save(false);
			
		}
		return array('status' => true );
    }
	
}
