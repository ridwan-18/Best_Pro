<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\AlterationEndorsement;
use app\models\AlterationEndorsementMember;
use app\models\Batch;
use app\models\Policy;
use app\models\Member;
use app\models\Personal;
use app\models\Partner;
use app\models\Quotation;
use app\models\QuotationRate;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Billing;
use app\models\QuotationCommission;
use app\models\QuotationProduct;
use app\models\ProductEm;
use app\models\Product;
use app\models\PeriodType;
use app\models\Utils;
use app\models\Signature;
use yii\data\Pagination;
use yii\web\UploadedFile;
use Da\QrCode\QrCode;
use yii\helpers\Url;

/**
 * AlterationEndorsementController implements the CRUD actions for AlterationEndorsement model.
 */
class AlterationEndorsementController extends Controller
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
     * Lists all AlterationEndorsement models.
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

        $totalModel = AlterationEndorsement::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => AlterationEndorsement::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = AlterationEndorsement::getAll($params);

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
        $data['sum_insured_raw'] = $member->total_si;
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
                'sum_insured_new' => $member['total_si'],
                'premi' => number_format($member['total_premium']),
                'extra_premi' => number_format($member['extra_premium']),
                'cancelled_premi' => number_format($member['total_premium'])
            ];
        }
        echo json_encode($data);
    }

    /**
     * Displays a single AlterationEndorsement model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);

        $members = AlterationEndorsementMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);

        return $this->render('view', [
            'model' => $model,
            'partner' => $partner,
            'members' => $members,
        ]);
    }

    /**
     * Creates a new AlterationEndorsement model.
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
        $birthDates = Yii::$app->request->post('birth_dates');
        $startDates = Yii::$app->request->post('start_dates');
        $endDates = Yii::$app->request->post('end_dates');
        $sumInsureds = Yii::$app->request->post('sum_insureds');
		$new_names = Yii::$app->request->post('new_names');
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

        $alteration = AlterationEndorsement::find()->orderBy(['id' => SORT_DESC])->one();
        if ($alteration != null) {
            $newestId = $alteration->id + 1;
        } else {
            $newestId = 1;
        }

        $model = new AlterationEndorsement();
        $model->alteration_no = AlterationEndorsement::generateAlterationNo(['id' => $newestId]);
        $model->alteration_date = date("Y-m-d");
        $model->policy_no = $policy->policy_no;
        $model->description = Yii::$app->request->post('description');
        $model->total_si = 0;
        $model->new_total_si = 0;
        $model->total_premium = 0;
        $model->new_total_premium = 0;
        $model->status = AlterationEndorsement::STATUS_PENDING;
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $members = [];
        $totalSi = 0;
        $newTotalSi = 0;
        $totalPremium = 0;
        $newTotalPremium = 0;
        foreach ($membersNo as $key => $value) {
            $member = Member::findOne(['member_no' => $value]);
            $personal = Personal::findOne(['personal_no' => $member->personal_no]);

            $newAge = Member::getAge($quotation->age_calculate, $birthDates[$key], $startDates[$key]);

            $newTerm = Member::getTerm($quotation->rate_type, $startDates[$key], $endDates[$key]);
            $termYear = floor($newTerm / 12);

            $quotationRate = QuotationRate::findOne([
                'quotation_id' => $policy->quotation_id,
                'term' => $termYear
            ]);
			
			
			$newSumInsured = isset($sumInsureds[$key])
        ? trim((string)$sumInsureds[$key])
        : '';
		
		$newSumInsured = (float)$newSumInsured;

          $newPremi = (float)$newSumInsured[$key] * (float)$quotationRate->rate / 1000;
		  var_dump($newPremi);

            $members[] = [
                'alteration_no' => $model->alteration_no,
                'member_no' => $member->member_no,
                'name' => $personal->name,
                'birth_date' => $personal->birth_date,
                'new_birth_date' => $birthDates[$key],
                'age' => $member->age,
                'new_age' => $newAge,
                'start_date' => $member->start_date,
                'end_date' => $member->end_date,
                'new_start_date' => $startDates[$key],
                'new_end_date' => $endDates[$key],
                'term' => $member->term,
                'new_term' => $newTerm,
                'sum_insured' => $member->sum_insured,
                // 'new_sum_insured' => $sumInsureds[$key],
				'new_sum_insured' => $newSumInsured,
                'premi' => $member->total_premium,
                'new_premi' => $newPremi,
                'extra_premi' => $member->extra_premium,
				'new_name' => $new_names[$key]
            ];

            $totalSi += $member->sum_insured;
            $newTotalSi += (float)$sumInsureds[$key];
            $totalPremium += $member->total_premium;
            $newTotalPremium += $newPremi;
        }

        if (count($members) == 0) {
            Yii::$app->session->setFlash('error', "Member was empty");
            return $this->redirect(['create']);
        }

        $model->total_si = $totalSi;
        $model->new_total_si = $newTotalSi;
        $model->total_premium = $totalPremium;
        $model->new_total_premium = $newTotalPremium;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $attributes = [
            'alteration_no',
            'member_no',
            'name',
            'birth_date',
            'new_birth_date',
            'age',
            'new_age',
            'start_date',
            'end_date',
            'new_start_date',
            'new_end_date',
            'term',
            'new_term',
            'sum_insured',
            'new_sum_insured',
            'premi',
            'new_premi',
            'extra_premi',
			'new_name',
        ];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(AlterationEndorsementMember::tableName(), $attributes, $members)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving Member");
            return $this->redirect(['create']);
        }
		
		$response = $model->callAPIPostMemberLogin();

				// if (!is_array($response)) {
					// echo "<pre>";
					// var_dump($response);
					// die("Login API bukan array");
				// }

				// if (!isset($response['token'])) {
					// echo "<pre>";
					// var_dump($response);
					// die("Token tidak ditemukan");
				// }

				$token = $response['token'];

				$policy_number = $policy->policy_no;

				// ================= REFUND API ===================
				$response_member = $model->callAPIPostMemberEndorsementPush(
					$token,
					$policy->policy_no,
					$membersNo
				);
				var_dump($response_member);

				// Jika response memang array
				if (
					is_array($response_member) &&
					isset($response_member['code']) &&
					$response_member['code'] != '200'
				) {
					Yii::$app->session->setFlash(
						'error',
						"Error while Calling API"
					);
				}

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

 
     
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

        $members = AlterationEndorsementMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);
        foreach ($members as $member) {
            $membership = Member::findOne(['member_no' => $member['member_no']]);
            $membership->start_date = $member['new_start_date'];
            $membership->end_date = $member['new_end_date'];
            $membership->term = $member['new_term'];
            $membership->sum_insured = $member['new_sum_insured'];
            $membership->total_si = $member['new_sum_insured'];
            $membership->total_premium = $member['new_premi'];
            $membership->gross_premium = $member['new_premi'];
            $membership->basic_premium = $member['new_premi'];
            $membership->nett_premium = $member['new_premi'];
            $membership->status = Member::STATUS_CHANGE;
            $membership->save(false);

            $batch = Batch::findOne([
                'batch_no' => $membership->batch_no,
                'policy_no' => $membership->policy_no
            ]);
            $batch->total_up = $batch->total_up - $member['sum_insured'] + $member['new_sum_insured'];
            $batch->total_gross_premium = $batch->total_gross_premium - $member['premi'] + $member['new_premi'];
            $batch->total_nett_premium = $batch->total_nett_premium - $member['premi'] + $member['new_premi'];
            $batch->save(false);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->status = AlterationEndorsement::STATUS_APPROVED;
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => $id]);
        }
		
		
		// INSERT INTO INVOICE
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

		// $tc = QuotationTc::findOne(['quotation_id' => $policy->quotation_id]);
		// if ($tc == null) {
			// Yii::$app->session->setFlash('error', "TC not found");
			// return $this->redirect(['index']);
		// }

		// $commission = QuotationCommission::findOne(['quotation_id' => $policy->quotation_id]);
		// if ($commission == null) {
			// Yii::$app->session->setFlash('error', "Commission not found");
			// return $this->redirect(['index']);
		// }
		
		
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
		
		// $billing->created_by = Yii::$app->user->identity->id;
		$billing->created_by = $batch->created_by;
		$billing->policy_no = $batch->policy_no;
		$billing->batch_no = $batch->batch_no;
		$billing->reg_no = Billing::generateRegNo($regNoParams);
		$billing->invoice_no = Billing::generateInvoiceNo($invoiceNoParams);
		$billing->invoice_date = date("Y-m-d");
		// $billing->due_date = Billing::getDueDate($tc->grace_period);
		$billing->accept_date = date("Y-m-d");
		$billing->remarks = "Endorsment";
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
		// $billing->ppn = ($billing->discount * $commission->ppn / 100) + ($billing->handling_fee * $commission->ppn / 100);
		// $billing->admin_cost = $administrationCost;
		// $billing->policy_cost = $policyCost;
		// $billing->member_card_cost = $memberCardCost;
		// $billing->certificate_cost = $certificateCost;
		// $billing->stamp_cost = $stampCost;
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
		
		// INSERT INTO INVOICE
		

        Yii::$app->session->setFlash('success', "Successfully issued");
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing AlterationEndorsement model.
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
     * Finds the AlterationEndorsement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return AlterationEndorsement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlterationEndorsement::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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
}
