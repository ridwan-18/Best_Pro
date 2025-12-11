<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\AlterationCancel;
use app\models\AlterationCancelMember;
use app\models\Batch;
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
 * AlterationCancelController implements the CRUD actions for AlterationCancel model.
 */
class AlterationCancelController extends Controller
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
     * Lists all AlterationCancel models.
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

        $totalModel = AlterationCancel::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => AlterationCancel::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = AlterationCancel::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    public function actionGetMemberData()
    {
        $member = Member::findOne(['member_no' => Yii::$app->request->post('member_no')]);
        $personal = Personal::findOne(['id' => $member->personal_id]);
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
     * Displays a single AlterationCancel model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);

        $members = AlterationCancelMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);

        return $this->render('view', [
            'model' => $model,
            'partner' => $partner,
            'members' => $members,
        ]);
    }

    /**
     * Creates a new AlterationCancel model.
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
        $policy = Policy::findOne(['policy_no' => Yii::$app->request->post('policy_no')]);
        if ($policy == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['create']);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $alteration = AlterationCancel::find()->orderBy(['id' => SORT_DESC])->one();
        if ($alteration != null) {
            $newestId = $alteration->id + 1;
        } else {
            $newestId = 1;
        }

        $model = new AlterationCancel();
        $model->alteration_no = AlterationCancel::generateAlterationNo(['id' => $newestId]);
        $model->alteration_date = date("Y-m-d");
        $model->policy_no = $policy->policy_no;
        $model->total_si = 0;
        $model->total_premium = 0;
        $model->status = AlterationCancel::STATUS_PENDING;
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $members = [];
        $totalSi = 0;
        $totalPremium = 0;
        foreach ($membersNo as $key => $value) {
            $member = Member::findOne(['member_no' => $value]);
            $personal = Personal::findOne(['id' => $member->personal_id]);
            $members[] = [
                'alteration_no' => $model->alteration_no,
                'member_no' => $member->member_no,
                'name' => $personal->name,
                'birth_date' => $personal->birth_date,
                'age' => $member->age,
                'start_date' => $member->start_date,
                'end_date' => $member->end_date,
                'sum_insured' => $member->sum_insured,
                'premi' => $member->total_premium,
                'extra_premi' => $member->extra_premium
            ];

            $totalSi += $member->sum_insured;
            $totalPremium += $member->total_premium;
        }

        if (count($members) == 0) {
            Yii::$app->session->setFlash('error', "Member was empty");
            return $this->redirect(['create']);
        }

        $model->total_si = $totalSi;
        $model->total_premium = $totalPremium;
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
            'sum_insured',
            'premi',
            'extra_premi',
        ];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(AlterationCancelMember::tableName(), $attributes, $members)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving Member");
            return $this->redirect(['create']);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing AlterationCancel model.
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

        $members = AlterationCancelMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);
        foreach ($members as $member) {
            $membership = Member::findOne(['member_no' => $member['member_no']]);
            $membership->status = Member::STATUS_CANCEL;
            $membership->save(false);

            $batch = Batch::findOne([
                'batch_no' => $membership->batch_no,
                'policy_no' => $membership->policy_no
            ]);
            $batch->total_up = $batch->total_up - $membership->total_si;
            $batch->total_gross_premium = $batch->total_gross_premium - $membership->gross_premium;
            $batch->total_nett_premium = $batch->total_nett_premium - $membership->nett_premium;
            $batch->save(false);
        }
		
		$cancelMember = alterationcancelmember::find()
            ->select([
                'COUNT(alteration_no) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(premi) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'alteration_no' => $model->alteration_no
            ])
            ->one();
			
		$dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');
		
        $model->status = AlterationCancel::STATUS_APPROVED;
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
		$billing->total_member = $cancelMember['total_member'];
		$billing->gross_premium = $cancelMember['total_gross_premium'];
		$billing->extra_premium = 0;
		$billing->discount = 0;
		$billing->nett_premium = $cancelMember['total_gross_premium'];
	
		$billing->ppn = 0;
		$billing->admin_cost = 0;
		$billing->policy_cost = 0;
		$billing->member_card_cost = 0;
		$billing->certificate_cost = 0;
		$billing->stamp_cost = 0;
		$billing->total_billing = $cancelMember['total_gross_premium'];
		$billing->type_invoice = 'Cancel';
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
     * Deletes an existing AlterationCancel model.
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
     * Finds the AlterationCancel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return AlterationCancel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlterationCancel::findOne(['id' => $id])) !== null) {
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
			
				
                while(!empty($sheetData[$baseRow]['A'])){
                    $member = new member();
					$member->member_no = (string)$sheetData[$baseRow]['A'];
					$endDateCancel = Utils::convertDateToYmd($sheetData[$baseRow]['B']);
					
					$get_data_member = (new \yii\db\query())
					->select(['policy_no','start_date','batch_no','now() as tanggal_sekarang','nett_premium','term','gross_premium'])
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
					
					$dateTime = new \DateTime();
					$currentDateTime = $dateTime->format('Y-m-d H:i:s');
					
					$alterationCount = AlterationCancel::find()->where([
					'policy_no' => $get_data_member['policy_no'],
					'YEAR(alteration_date)' => date("Y")
					])->count();
					
					$newestId = 1;
					$alteration = AlterationCancel::find()->orderBy(['id' => SORT_DESC])->one();
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
					
					$model = new AlterationCancel();
					$model->alteration_no = AlterationCancel::generateAlterationNo($invoiceNoParams);
					$model->reg_no = AlterationCancel::generateRegNo($regNoParams);
					$members = [];
					$totalSi = 0;
					$totalPremium = 0;
						$member = Member::findOne(['member_no' => $member->member_no]);
						$personal = Personal::findOne(['personal_no' => $member->personal_no]);
						$members[] = [
							'alteration_no' => $model->alteration_no,
							'member_no' => $member->member_no,
							'name' => $personal->name,
							'birth_date' => $personal->birth_date,
							'age' => $member->age,
							'start_date' => $member->start_date,
							'end_date' => $member->end_date,
							'sum_insured' => $member->sum_insured,
							'premi' => $member->total_premium,
							'extra_premi' => $member->extra_premium,
							'reff_noinvoice' => $get_data_reff_noinvoice['invoice_no']
						];

						$totalSi += $member->sum_insured;
						$totalGrossPremium += $member->total_premium;

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
						'sum_insured',
						'premi',
						'extra_premi',
						'reff_noinvoice'
					];
					
					$totalUp += $totalSi;
					
					$modelSave = Yii::$app->db->createCommand()
						->batchInsert(alterationcancelmember::tableName(), $attributes, $members)
						->execute();
					if (!$modelSave) {
						Yii::$app->session->setFlash('error', "Error while saving Member");
						return $this->redirect(['import']);
					}
					
                    $baseRow++;
                }
				
					$model = new AlterationCancel();
					$model->alteration_no = AlterationCancel::generateAlterationNo($invoiceNoParams);
					$model->reg_no = AlterationCancel::generateRegNo($regNoParams);
					$model->alteration_date = date("Y-m-d");
					$model->policy_no = $get_data_member['policy_no'];
					$model->total_si = 0;
					$model->total_premium = 0;
					$model->status = AlterationCancel::STATUS_PENDING;
					$model->created_at = $currentDateTime;
					$model->created_by = Yii::$app->user->identity->id;
					
					if (!$model->save(false))
					{
						Yii::$app->session->setFlash('import', "Error while saving");
						return $this->redirect(['import']);
					} 
					
					$model->total_si = $totalUp;
					$model->total_premium = $totalGrossPremium;
					if (!$model->save(false)) {
						Yii::$app->session->setFlash('error', "Error while saving");
						return $this->redirect(['import']);
					}

                yii::$app->getsession()->setflash('success','success');
				$this->redirect(array('alteration-cancel/index'));
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
	
	
	public function actionPrint($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $this->layout = '/print';

        $AlterationCancel = AlterationCancel::find()
            ->select([
                'id',
                'alteration_no',
                'policy_no',
                'alteration_date',
                'total_si',
                'total_premium',
				'reg_no',
            ])
            ->asArray()
            ->where(['id' => $id])
            ->one();
        if ($AlterationCancel == null) {
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
            ->where(['policy_no' => $AlterationCancel['policy_no']])
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

        $firstMember = alterationcancelmember::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'alteration_no' => $AlterationCancel['alteration_no'],
               
            ])
            ->orderBy(['member_no' => SORT_ASC])
            ->one();

        $lastMember = alterationcancelmember::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'alteration_no' => $AlterationCancel['alteration_no'],
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

        $members =  alterationcancelmember::find()
            ->asArray()
            ->select(['member_no','name','birth_date','age','start_date','sum_insured','premi','extra_premi'])
            ->where([
                'alteration_no' => $AlterationCancel['alteration_no'],
               
            ])
            ->orderBy(['member_no' => SORT_ASC])
            ->all();

        $cancelMember = alterationcancelmember::find()
            ->select([
                'COUNT(alteration_no) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(premi) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'alteration_no' => $AlterationCancel['alteration_no']
            ])
            ->one();

        // $pendingMember = Member::find()
            // ->select([
                // 'COUNT(id) AS total_member',
                // 'SUM(sum_insured) AS total_si',
                // 'SUM(gross_premium) AS total_gross_premium',
            // ])
            // ->asArray()
            // ->where([
                // 'batch_no' => $billing['batch_no'],
                // 'policy_no' => $billing['policy_no'],
                // 'member_status' => Member::MEMBER_STATUS_PENDING
            // ])
            // ->one();

        return $this->render('print', [
            // 'billing' => $billing,
            // 'batch' => $batch,
            'policy' => $policy,
            'partner' => $partner,
            'product' => $product,
            'commission' => $commission,
            'signature' => $signature,
            'qrCodeUrl' => $qrCodeUrl,
            'memberNoList' => $memberNoList,
            'quotation' => $quotation,
            'members' => $members,
			'AlterationCancel' => $AlterationCancel,
            // 'pendingMembers' => $pendingMembers,
            // 'declinedMembers' => $declinedMembers,
            'cancelMember' => $cancelMember,
            // 'pendingMember' => $pendingMember,
        ]);
    }
}
