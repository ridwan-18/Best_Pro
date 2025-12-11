<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\AlterationRefund;
use app\models\AlterationRefundMember;
use app\models\Policy;
use app\models\Member;
use app\models\Personal;
use app\models\Partner;
use app\models\Quotation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Product;
use app\models\QuotationProduct;
use app\models\Signature;
use Da\QrCode\QrCode;
use yii\helpers\Url;
use app\models\Utils;
use app\models\Billing;

/**
 * AlterationRefundController implements the CRUD actions for AlterationRefund model.
 */
class AlterationRefundController extends Controller
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
     * Lists all AlterationRefund models.
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
        ];

        $totalModel = AlterationRefund::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => AlterationRefund::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = AlterationRefund::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    public function actionGetMemberData()
    {
        $member = Member::findOne(['member_no' => Yii::$app->request->post('member_no')]);
        $personal = Personal::findOne(['personal_no' => $member->personal_no]);
        $data = [];
        $data['member_no'] = $member->member_no;
        $data['name'] = $personal->name;
        $data['birth_date'] = $personal->birth_date;
        $data['age'] = $member->age;
        $data['start_date'] = $member->start_date;
        $data['end_date'] = $member->end_date;
        $data['sum_insured'] = number_format($member->total_si);
        $data['premi'] = number_format($member->total_premium);
        $data['extra_premi'] = number_format($member->extra_premium);
        $data['cancelled_premi'] = number_format($member->total_premium);
        echo json_encode($data);
    }

    public function actionGetBatchData()
    {
        $members = Member::find()
            ->asArray()
            ->select([
                Member::tableName() . '.member_no',
                Member::tableName() . '.age',
                Member::tableName() . '.start_date',
                Member::tableName() . '.end_date',
                Member::tableName() . '.total_si',
                Member::tableName() . '.total_premium',
                Member::tableName() . '.extra_premium',
                Personal::tableName() . '.name',
                Personal::tableName() . '.birth_date'
            ])
            ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . Member::tableName() . '.personal_no')
            ->where([
                'policy_no' => Yii::$app->request->post('policy_no'),
                'batch_no' => Yii::$app->request->post('batch_no')
            ])
            ->all();

        $data = [];
        foreach ($members as $member) {
            $data[] = [
                'member_no' => $member['member_no'],
                'name' => $member['name'],
                'birth_date' => $member['birth_date'],
                'age' => $member['age'],
                'start_date' => $member['start_date'],
                'end_date' => $member['end_date'],
                'sum_insured' => number_format($member['total_si']),
                'premi' => number_format($member['total_premium']),
                'extra_premi' => number_format($member['extra_premium']),
                'cancelled_premi' => number_format($member['total_premium'])
            ];
        }
        echo json_encode($data);
    }

    /**
     * Displays a single AlterationRefund model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);

        $members = AlterationRefundMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);

        return $this->render('view', [
            'model' => $model,
            'partner' => $partner,
            'members' => $members,
        ]);
    }

    /**
     * Creates a new AlterationRefund model.
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

        if (!Yii::$app->request->post('members_no')) {
            return $this->render('create');
        }

        $membersNo = Yii::$app->request->post('members_no');
        $newEndDates = Yii::$app->request->post('new_end_dates');
        $policy = Policy::findOne(['policy_no' => Yii::$app->request->post('policy_no')]);
        if ($policy == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['create']);
        }

        $quotation = Quotation::findOne(['id' => $policy->quotation_id]);
        if ($quotation == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['create']);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $alteration = AlterationRefund::find()->orderBy(['id' => SORT_DESC])->one();
        if ($alteration != null) {
            $newestId = $alteration->id + 1;
        } else {
            $newestId = 1;
        }

        $model = new AlterationRefund();
        $model->alteration_no = AlterationRefund::generateAlterationNo(['id' => $newestId]);
        $model->alteration_date = date("Y-m-d");
        $model->policy_no = $policy->policy_no;
        $model->total_si = 0;
        $model->total_premium = 0;
        $model->total_premium_refund = 0;
        $model->status = AlterationRefund::STATUS_PENDING;
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $members = [];
        $totalSi = 0;
        $totalPremium = 0;
        $totalPremiumRefund = 0;
        foreach ($membersNo as $key => $value) {
            $member = Member::findOne(['member_no' => $value]);
            $personal = Personal::findOne(['personal_no' => $member->personal_no]);
            $remainingTerm = Member::getTerm($quotation->rate_type, $newEndDates[$key], $member->end_date);
            $premiRefund = round(($remainingTerm / $member->term) * (0.5 * $member->total_premium));
            $members[] = [
                'alteration_no' => $model->alteration_no,
                'member_no' => $member->member_no,
                'name' => $personal->name,
                'birth_date' => $personal->birth_date,
                'age' => $member->age,
                'start_date' => $member->start_date,
                'end_date' => $member->end_date,
                'new_end_date' => $newEndDates[$key],
                'term' => $member->term,
                'remaining_term' => $remainingTerm,
                'sum_insured' => $member->sum_insured,
                'premi' => $member->total_premium,
                'extra_premi' => $member->extra_premium,
                'premi_refund' => $premiRefund
            ];

            $totalSi += $member->sum_insured;
            $totalPremium += $member->total_premium;
            $totalPremiumRefund += $premiRefund;
        }

        if (count($members) == 0) {
            Yii::$app->session->setFlash('error', "Member was empty");
            return $this->redirect(['create']);
        }

        $model->total_si = $totalSi;
        $model->total_premium = $totalPremium;
        $model->total_premium_refund = $totalPremiumRefund;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $attributes = [
            'alteration_no',
            'member_no',
            'name',
            'birth_date',
            'age',
            'start_date',
            'end_date',
            'new_end_date',
            'term',
            'remaining_term',
            'sum_insured',
            'premi',
            'extra_premi',
            'premi_refund',
        ];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(AlterationRefundMember::tableName(), $attributes, $members)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving Member");
            return $this->redirect(['create']);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing AlterationRefund model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
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

        $model = $this->findModel($id);

        $members = AlterationRefundMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);
        foreach ($members as $member) {
            $membership = Member::findOne(['member_no' => $member['member_no']]);
            $membership->status = Member::STATUS_SURRENDER;
            $membership->save(false);
        }
		
		
        $refundMember = AlterationRefundMember::find()
            ->select([
                'COUNT(alteration_no) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(premi_refund) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'alteration_no' => $model->alteration_no
            ])
            ->one();
		
        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->status = AlterationRefund::STATUS_APPROVED;
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => $id]);
        }
		
		$billing = new Billing();
		$billing->policy_no = $model->policy_no;
		$billing->batch_no = 000000;
		$billing->reg_no = $model->reg_no;
		$billing->invoice_no = $model->alteration_no;
		$billing->invoice_date = date("Y-m-d");
		$billing->due_date = date("Y-m-d");
		$billing->accept_date = date("Y-m-d");
		$billing->total_member = $refundMember['total_member'];
		$billing->gross_premium = $refundMember['total_gross_premium'];
		$billing->extra_premium = 0;
		$billing->discount = 0;
		$billing->nett_premium = $refundMember['total_gross_premium'];
	
		$billing->ppn = 0;
		$billing->admin_cost = 0;
		$billing->policy_cost = 0;
		$billing->member_card_cost = 0;
		$billing->certificate_cost = 0;
		$billing->stamp_cost = 0;
		$billing->total_billing = $refundMember['total_gross_premium'];
		$billing->type_invoice = 'Refund';
		//
		$billing->status = Billing::STATUS_UNVERIFIED;
		if (!$billing->save(false)) {
			Yii::$app->session->setFlash('error', "Error while saving billing");
			return $this->redirect(['view', 'id' => $id]);
		}

        Yii::$app->session->setFlash('success', "Successfully issued");
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing AlterationRefund model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AlterationRefund model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return AlterationRefund the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlterationRefund::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	
	public function actionImport(){
        $modelImport = new \yii\base\DynamicModel([
                    'fileImport'=>'File Import',
                ]);
        $modelImport->addRule(['fileImport'],'required');
        $modelImport->addRule(['fileImport'],'file',['extensions'=>'ods,xls,xlsx'],['maxSize'=>10000*10000]);
		
        if(Yii::$app->request->post())
		{
            $modelImport->fileImport = \yii\web\UploadedFile::getInstance($modelImport,'fileImport');
            if($modelImport->fileImport && $modelImport->validate()){
                $inputFileType = \PHPExcel_IOFactory::identify($modelImport->fileImport->tempName);
                $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($modelImport->fileImport->tempName);
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
                $baseRow = 2;
				$members = [];
				$totalMember = 0;
				$totalUp = 0;
				$totalGrossPremium = 0;
				$totalRefundPremium = 0;
				
                while(!empty($sheetData[$baseRow]['A'])){
                    $member = new member();
					$member->member_no = (string)$sheetData[$baseRow]['A'];
					$endDateSurrender = Utils::convertDateToYmd($sheetData[$baseRow]['B']);
					
					$get_data_member = (new \yii\db\query())
					->select(['policy_no','start_date','batch_no','now() as tanggal_sekarang','TIMESTAMPDIFF(MONTH, start_date, NOW())  AS selisih_bulan','term','gross_premium'])
					->from('member')
					->where(['member_no' => $member->member_no])
					->one();
					
					$get_data_reff_noinvoice = (new \yii\db\query())
					->select(['invoice_no'])
					->from('billing')
					->where([
					'policy_no' => $get_data_member['policy_no'],
					'batch_no' => $get_data_member ['batch_no'],
					])
					->one();
					
					$termNew = AlterationRefund::getTerm($get_data_member['start_date'], $endDateSurrender);
					
					$sisa_masa = $get_data_member['term'] - $termNew;
					
					$get_quotation_id = (new \yii\db\query())
					->select(['quotation_id'])
					->from('policy')
					->where(['policy_no' => $get_data_member['policy_no']])
					->one();
					
					$refund_premi = (new \yii\db\query())
					->select(['refund_premium'])
					->from('quotation_tc')
					->where(['quotation_id' => $get_quotation_id['quotation_id']])
					->one();
					
					$total_refund_premi = ($sisa_masa / $get_data_member['term']) * ($refund_premi['refund_premium']/100 * $get_data_member['gross_premium']) ;
					
					$dateTime = new \DateTime();
					$currentDateTime = $dateTime->format('Y-m-d H:i:s');
					
					$alterationCount = AlterationRefund::find()->where([
					'policy_no' => $get_data_member['policy_no'],
					'YEAR(alteration_date)' => date("Y")
					])->count();
					
					$newestId = 1;
					$alteration = AlterationRefund::find()->orderBy(['id' => SORT_DESC])->one();
					if ($alteration != null) {
						$newestId = $alteration->id + 1;
					}
					
					$invoiceNoParams = [
						'id' => $alterationCount + 1,
						'policy_no' => $get_data_member['policy_no'],
						'month' => date("n")
					];
					
					$regNoParams = [
					'id' => $newestId,
					'policy_no' => $batch->policy_no,
					'month' => date("n")
					];
					
					$model = new AlterationRefund();
					$model->alteration_no = AlterationRefund::generateAlterationNo($invoiceNoParams);
					$model->reg_no = AlterationRefund::generateRegNo($regNoParams);
					$members = [];
					$totalSi = 0;
					$totalPremium = 0;
					$totalPremiumRefund = 0;
						$member = Member::findOne(['member_no' => $member->member_no]);
						$personal = Personal::findOne(['personal_no' => $member->personal_no]);
						// $remainingTerm = Member::getTerm($quotation->rate_type, $newEndDates[$key], $member->end_date);
						$premiRefund = round(($remainingTerm / $member->term) * (0.5 * $member->total_premium));
						$members[] = [
							'alteration_no' => $model->alteration_no,
							'member_no' => $member->member_no,
							'name' => $personal->name,
							'birth_date' => $personal->birth_date,
							'age' => $member->age,
							'start_date' => $member->start_date,
							'end_date' => $member->end_date,
							'new_end_date' => $endDateSurrender,
							'term' => $member->term,
							'remaining_term' => $sisa_masa,
							'sum_insured' => $member->sum_insured,
							'premi' => $member->total_premium,
							'extra_premi' => $member->extra_premium,
							'premi_refund' => $total_refund_premi,
							'reff_noinvoice' => $get_data_reff_noinvoice['invoice_no']
						];

						$totalSi += $member->sum_insured;
						$totalGrossPremium += $member->total_premium;
						$totalPremiumRefund += $total_refund_premi;
					

					if (count($members) == 0) {
						Yii::$app->session->setFlash('error', "Member was empty");
						return $this->redirect(['import']);
					}

					$attributes = [
						'alteration_no',
						'member_no',
						'name',
						'birth_date',
						'age',
						'start_date',
						'end_date',
						'new_end_date',
						'term',
						'remaining_term',
						'sum_insured',
						'premi',
						'extra_premi',
						'premi_refund',
						'reff_noinvoice'
					];
					
					$totalUp += $totalSi;
					$totalGrossPremium += $totalGrossPremium;
					$totalRefundPremium += $total_refund_premi;
					
					
					$modelSave = Yii::$app->db->createCommand()
						->batchInsert(AlterationRefundMember::tableName(), $attributes, $members)
						->execute();
					if (!$modelSave) {
						Yii::$app->session->setFlash('error', "Error while saving Member");
						return $this->redirect(['import']);
					}
					
                    $baseRow++;
                }
				
					$model = new AlterationRefund();
					$model->alteration_no = AlterationRefund::generateAlterationNo($invoiceNoParams);
					$model->reg_no = AlterationRefund::generateRegNo($regNoParams);
					$model->alteration_date = date("Y-m-d");
					$model->policy_no = $get_data_member['policy_no'];
					$model->total_si = 0;
					$model->total_premium = 0;
					$model->total_premium_refund = 0;
					$model->status = AlterationRefund::STATUS_PENDING;
					$model->created_at = $currentDateTime;
					$model->created_by = Yii::$app->user->identity->id;
					
					if (!$model->save(false))
					{
						Yii::$app->session->setFlash('error', "Error while saving");
						return $this->redirect(['create']);
					} 
					
					$model->total_si = $totalUp;
					$model->total_premium = $totalGrossPremium;
					$model->total_premium_refund = $totalRefundPremium;
					if (!$model->save(false)) {
						Yii::$app->session->setFlash('error', "Error while saving");
						return $this->redirect(['import']);
					}

                yii::$app->getsession()->setflash('success','success');
				$this->redirect(array('alteration-refund/index'));
            }
			else{
                Yii::$app->getSession()->setFlash('error','Error');
            }
        }

        return $this->render('import',[
                'modelImport' => $modelImport,
				'pagination' => $pagination,
            ]);
			
    }
	
	
	
	public function actionPrint($alteration_no)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $this->layout = '/print';

        $AlterationRefund = AlterationRefund::find()
            ->select([
                'id',
                'alteration_no',
                'policy_no',
                'alteration_date',
                'total_si',
                'total_premium',
                'total_premium_refund',
				'reg_no',
            ])
            ->asArray()
            ->where(['alteration_no' => $alteration_no])
            ->one();
        if ($AlterationRefund == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['index']);
        }

        $policy = Policy::find()
            ->select([
                'quotation_id',
                'partner_id',
                'policy_no',
                'spa_date',
                'payment_method',
            ])
            ->asArray()
            ->where(['policy_no' => $AlterationRefund['policy_no']])
            ->one();
        if ($policy == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['index']);
        }

        $partner = Partner::find()
            ->select([
                'name',
                'address',
            ])
            ->asArray()
            ->where(['id' => $policy['partner_id']])
            ->one();
        if ($partner == null) {
            Yii::$app->session->setFlash('error', "Partner not found");
            return $this->redirect(['index']);
        }

        $product = QuotationProduct::find()
            ->select([
                Product::tableName() . '.name',
            ])
            ->asArray()
            ->innerJoin(Product::tableName(), Product::tableName() . '.id = ' . QuotationProduct::tableName() . '.product_id')
            ->where([QuotationProduct::tableName() . '.quotation_id' => $policy['quotation_id']])
            ->one();
        if ($product == null) {
            Yii::$app->session->setFlash('error', "Product not found");
            return $this->redirect(['index']);
        }

        $signature = Signature::findOne(['id' => 1]);

        $qrCodeFilename = 'billing-' . $id . '.png';
        $qrCode = (new QrCode(Url::base(true) . '/billing/print-signature/?id=' . $id))
            ->setSize(75)
            ->setMargin(5);
        $qrCode->writeFile(\Yii::getAlias('@webroot') . '/uploads/signature/' . $qrCodeFilename);
        $qrCodeUrl = Url::base() . Signature::PICTURE_PATH . $qrCodeFilename;

        $firstMember = AlterationRefundMember::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'alteration_no' => $AlterationRefund['alteration_no'],
               
            ])
            ->orderBy(['member_no' => SORT_ASC])
            ->one();

        $lastMember = AlterationRefundMember::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'alteration_no' => $AlterationRefund['alteration_no'],
            ])
            ->orderBy(['member_no' => SORT_DESC])
            ->one();

        $memberNoList = $firstMember['member_no'];
        if ($firstMember['member_no'] != $lastMember['member_no']) {
            $memberNoList = $firstMember['member_no'] . ' s / d ' . $lastMember['member_no'];
        }

        $quotation = Quotation::find()
            ->asArray()
            ->select(['payment_method'])
            ->where(['id' => $policy['quotation_id']])
            ->one();

        $members =  AlterationRefundMember::find()
            ->asArray()
            ->select(['member_no','name','birth_date','age','start_date','new_end_date','sum_insured','premi_refund'])
            ->where([
                'alteration_no' => $AlterationRefund['alteration_no'],
               
            ])
            ->orderBy(['member_no' => SORT_ASC])
            ->all();

        $refundMember = AlterationRefundMember::find()
            ->select([
                'COUNT(alteration_no) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(premi_refund) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'alteration_no' => $AlterationRefund['alteration_no']
            ])
            ->one();
       

        return $this->render('print', [
            'policy' => $policy,
            'partner' => $partner,
            'product' => $product,
            'commission' => $commission,
            'signature' => $signature,
            'qrCodeUrl' => $qrCodeUrl,
            'memberNoList' => $memberNoList,
            'quotation' => $quotation,
            'members' => $members,
			'AlterationRefund' => $AlterationRefund,
            'refundMember' => $refundMember,
        ]);
    }
}
