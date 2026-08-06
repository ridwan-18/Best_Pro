<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Claim;
use app\models\Policy;
use app\models\Member;
use app\models\Personal;
use app\models\Partner;
use app\models\QuotationProduct;
use app\models\Product;
use app\models\Component;
use app\models\ClaimDocument;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\dokument_claim_jatim;
use app\models\claim_bank_jatim;
use app\models\claim_bank_jatim_detail;
use yii\web\UploadedFile;


/**
 * ClaimController implements the CRUD actions for Claim model.
 */
class ClaimController extends Controller
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
     * Lists all Claim models.
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
            'claim_no' => Yii::$app->request->get('claim_no'),
            'policy_no' => Yii::$app->request->get('policy_no'),
            'member_no' => Yii::$app->request->get('member_no'),
        ];

        $totalModel = Claim::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => Claim::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = Claim::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Claim model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        $member = Member::findOne(['member_no' => $model->member_no]);
        $personal = Personal::findOne(['personal_no' => $member->personal_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);
        $quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
        $product = Product::findOne(['id' => $quotationProduct->product_id]);
        $component = Component::findOne(['product_id' => $product->id]);
		$dokumen_detail = dokument_claim_jatim::getAll();
		

        return $this->render('view', [
            'model' => $model,
            'policy' => $policy,
            'member' => $member,
            'personal' => $personal,
            'partner' => $partner,
            'product' => $product,
            'component' => $component,
			'dokumen_detail' => $dokumen_detail,
        ]);
    }

    /**
     * Creates a new Claim model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
	public function actionCreate()
	{
		if (
			Yii::$app->user->isGuest ||
			!Yii::$app->user->identity ||
			!User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$request = Yii::$app->request;

		// ✅ WAJIB: pastikan hanya dipanggil sekali
		$dokumen_detail = dokument_claim_jatim::getAll();

		// 🔒 pastikan selalu array
		if (!is_array($dokumen_detail)) {
			$dokumen_detail = [];
		}

		$memberNo = $request->post('member_no');

		if (!$memberNo) {
			return $this->render('create', [
				'dokumen_detail' => $dokumen_detail,
				'selectedMemberNo' => null,
			]);
		}

		$get_member = Member::findOne(['member_no' => $memberNo]);
		// var_dump($get_member);
		

		$policy = Policy::findOne(['policy_no' => $get_member->policy_no]);
		if (!$policy) {
			Yii::$app->session->setFlash('error', "Policy not found");
			return $this->redirect(['create']);
		}

		$personal = Personal::findOne(['personal_no' => $get_member->personal_no]);
		if (!$personal) {
			Yii::$app->session->setFlash('error', "Personal not found");
			return $this->redirect(['create']);
		}
		
		$quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
        $product = Product::findOne(['id' => $quotationProduct->product_id]);
		$partner = Partner::findOne(['id' => $policy->partner_id]);

		if (!$request->post('submit_claim')) {
			return $this->render('create', [
				'dokumen_detail' => $dokumen_detail,
				'selectedMemberNo' => $memberNo,
				'policy' => $policy,
				'get_member' => $get_member,
				'personal' => $personal,
				'partner' => $partner,
				'product' => $product,
			]);
		}

		// ======================
		// SAVE CLAIM
		// ======================
		 $claim = Claim::find()->orderBy(['id' => SORT_DESC])->one();
			if ($claim != null) {
				$newestId = $claim->id + 1;
			} else {
				$newestId = 1;
			}
		$model = new Claim();

		 $model->claim_no = Claim::generateClaimNo(['id' => $newestId]);
		$model->policy_no = $get_member->policy_no;
		$model->member_no = $get_member->member_no;

		$incidentDate = $request->post('incident_date');

		if (!$incidentDate) {
			Yii::$app->session->setFlash('error', "Incident Date wajib diisi");
			return $this->redirect(['create']);
		}

		$model->incident_date = $incidentDate;
		$model->claim_age = Claim::getClaimAge($personal->birth_date, $incidentDate);

		$model->estimated_amount = $request->post('estimated_amount');
		$model->claim_reason = $request->post('claim_reason');
		$model->disease = $request->post('disease');
		$model->place_of_death = $request->post('place_of_death');

		$model->doc_status = $request->post('doc_status');
		$model->doc_notes = $request->post('doc_notes');

		$model->status = Claim::STATUS_REGISTRATION;
		$model->created_at = date('Y-m-d H:i:s');
		$model->created_by = Yii::$app->user->id;

		if (!$model->save()) {
			Yii::$app->session->setFlash('error', json_encode($model->errors));
			return $this->redirect(['create']);
		}
		
		$files = UploadedFile::getInstancesByName('documents');
		$docIds = Yii::$app->request->post('doc_ids');

		if (!empty($files)) {
			foreach ($files as $index => $file) {

				if ($file->error == 0) {
					 // $filename = $this->id;
					$fileName = time() . $model->id . $file->baseName . '.' . $file->extension;
					$uploadPath = Yii::getAlias('@webroot/images/post_images/');
					
					if (!is_dir($uploadPath)) {
						mkdir($uploadPath, 0777, true);
					}

					$file->saveAs($uploadPath . $fileName);

					// simpan ke tabel document
					$doc = new claim_bank_jatim_detail();
					$doc->id_loan = $model->id;
					$doc->kode_dokumen = $docIds[$index] ?? null;
					$doc->files = 'uploads/claims/' . $fileName;
					$doc->tgl_upload = date('Y-m-d H:i:s');
				
					if (!$doc->save()) {
						// Yii::error($doc->errors);
						  echo "<pre>";
						print_r($doc->errors);
						die;
					}
				}
			}
		}
		
			// $response = $model->callAPIPostMemberLogin();

				// // if (!is_array($response)) {
					// // echo "<pre>";
					// // var_dump($response);
					// // die("Login API bukan array");
				// // }

				// // if (!isset($response['token'])) {
					// // echo "<pre>";
					// // var_dump($response);
					// // die("Token tidak ditemukan");
				// // }

				// $token = $response['token'];

				// $policy_number = $policy->policy_no;

				// // ================= REFUND API ===================
				// $response_member = $model->callAPIPostMemberClaimPush(
					// $token,
					// $policy->policy_no,
					// $membersNo
				// );

			
		

				// // Jika response memang array
				// if (
					// is_array($response_member) &&
					// isset($response_member['code']) &&
					// $response_member['code'] != '200'
				// ) {
					// Yii::$app->session->setFlash(
						// 'error',
						// "Error while Calling API"
					// );
				// }
		

		Yii::$app->session->setFlash('success', "Claim berhasil disimpan");

		return $this->redirect(['index', 'id' => $model->id]);
	}

    /**
     * Updates an existing Claim model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        if ($policy == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['create']);
        }

        $member = Member::findOne(['member_no' => $model->member_no]);
        if ($member == null) {
            Yii::$app->session->setFlash('error', "Member not found");
            return $this->redirect(['create']);
        }
		// var_dump($member);
		
        $personal = Personal::findOne(['personal_no' => $member->personal_no]);
        if ($personal == null) {
            Yii::$app->session->setFlash('error', "Personal not found");
            return $this->redirect(['create']);
        }
		
        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        // Claim Info
        $model->claim_age = Claim::getClaimAge($personal->birth_date, Yii::$app->request->post('incident_date'));
        $model->incident_date = Yii::$app->request->post('incident_date');
        $model->estimated_amount = Yii::$app->request->post('estimated_amount');
        $model->claim_reason = Yii::$app->request->post('claim_reason');
        $model->disease = Yii::$app->request->post('disease');
        $model->place_of_death = Yii::$app->request->post('place_of_death');

        // Claim Document
        $model->doc_status = Yii::$app->request->post('doc_status');
        $model->doc_pre_received_date = Yii::$app->request->post('doc_pre_received_date');
        $model->doc_received_date = Yii::$app->request->post('doc_received_date');
        $model->doc_complete_date = Yii::$app->request->post('doc_complete_date');
        $model->doc_notes = Yii::$app->request->post('doc_notes');

        // Payment Plan
        $model->payment_due_date = Yii::$app->request->post('payment_due_date');
        $model->claim_amount = Yii::$app->request->post('claim_amount');
        $model->cash_value = Yii::$app->request->post('cash_value');
        $model->transfer_type = Yii::$app->request->post('transfer_type');
        $model->bank_name = Yii::$app->request->post('bank_name');
        $model->account_no = Yii::$app->request->post('account_no');
        $model->account_name = Yii::$app->request->post('account_name');

        // Analyst I
        $model->analyst1_diagnosed_by = Yii::$app->request->post('analyst1_diagnosed_by');
        $model->analyst1_diagnose_notes = Yii::$app->request->post('analyst1_diagnose_notes');
        $model->analyst1_historical_disease = Yii::$app->request->post('analyst1_historical_disease');
        $model->analyst1_information = Yii::$app->request->post('analyst1_information');
        $model->analyst1_investigation_by_phone = Yii::$app->request->post('analyst1_investigation_by_phone');
        $model->analyst1_medical_analysis = Yii::$app->request->post('analyst1_medical_analysis');
        $model->analyst1_result1 = Yii::$app->request->post('analyst1_result1');
        $model->analyst1_recommendation1 = Yii::$app->request->post('analyst1_recommendation1');
        $model->analyst1_result2 = Yii::$app->request->post('analyst1_result2');
        $model->analyst1_recommendation2 = Yii::$app->request->post('analyst1_recommendation2');

        // Approval Investigation
        $model->dept_approved_by = Yii::$app->request->post('dept_approved_by');
        $model->dept_approve_notes = Yii::$app->request->post('dept_approve_notes');
        $model->dept_approve_status = Yii::$app->request->post('dept_approve_status');
        $model->div_approved_by = Yii::$app->request->post('div_approved_by');
        $model->div_approve_notes = Yii::$app->request->post('div_approve_notes');
        $model->div_approve_status = Yii::$app->request->post('div_approve_status');
        $model->gm_approved_by = Yii::$app->request->post('gm_approved_by');
        $model->gm_approve_notes = Yii::$app->request->post('gm_approve_notes');
        $model->gm_approve_status = Yii::$app->request->post('gm_approve_status');
        $model->dir1_approved_by = Yii::$app->request->post('dir1_approved_by');
        $model->dir1_approve_notes = Yii::$app->request->post('dir1_approve_notes');
        $model->dir1_approve_status = Yii::$app->request->post('dir1_approve_status');
        $model->dir2_approved_by = Yii::$app->request->post('dir2_approved_by');
        $model->dir2_approve_notes = Yii::$app->request->post('dir2_approve_notes');
        $model->dir2_approve_status = Yii::$app->request->post('dir2_approve_status');

        // Approval Process
        $model->dept_process_approved_by = Yii::$app->request->post('dept_process_approved_by');
        $model->dept_process_approve_notes = Yii::$app->request->post('dept_process_approve_notes');
        $model->dept_process_approve_status = Yii::$app->request->post('dept_process_approve_status');
        $model->div_process_approved_by = Yii::$app->request->post('div_process_approved_by');
        $model->div_process_approve_notes = Yii::$app->request->post('div_process_approve_notes');
        $model->div_process_approve_status = Yii::$app->request->post('div_process_approve_status');
        $model->gm_process_approved_by = Yii::$app->request->post('gm_process_approved_by');
        $model->gm_process_approve_notes = Yii::$app->request->post('gm_process_approve_notes');
        $model->gm_process_approve_status = Yii::$app->request->post('gm_process_approve_status');
        $model->dir1_process_approved_by = Yii::$app->request->post('dir1_process_approved_by');
        $model->dir1_process_approve_notes = Yii::$app->request->post('dir1_process_approve_notes');
        $model->dir1_process_approve_status = Yii::$app->request->post('dir1_process_approve_status');
        $model->dir2_process_approved_by = Yii::$app->request->post('dir2_process_approved_by');
        $model->dir2_process_approve_notes = Yii::$app->request->post('dir2_process_approve_notes');
        $model->dir2_process_approve_status = Yii::$app->request->post('dir2_process_approve_status');

        $model->approved_amount = Yii::$app->request->post('claim_amount');
        $model->status = Yii::$app->request->post('status');
        $model->decision = Yii::$app->request->post('decision');
        $model->remarks = Yii::$app->request->post('remarks');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'view',
                'id' => $model->id
            ]);
        }

        // Claim Document
        $isCheckeds = Yii::$app->request->post('is_checkeds');
        $isMandatories = Yii::$app->request->post('is_mandatories');
        $claimDocuments = ClaimDocument::find()
            ->asArray()
            ->where(['claim_id' => $model->id])
            ->all();
        foreach ($claimDocuments as $claimDocument) {
            $document = ClaimDocument::findOne(['id' => $claimDocument['id']]);
            $document->is_checked = (in_array($claimDocument['id'], $isCheckeds)) ? 1 : null;
            $document->is_mandatory = (in_array($claimDocument['id'], $isMandatories)) ? 1 : null;
            $document->save(false);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect([
            'view',
            'id' => $model->id
        ]);
    }

    /**
     * Deletes an existing Claim model.
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
     * Finds the Claim model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Claim the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Claim::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
}
