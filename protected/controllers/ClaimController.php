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

        return $this->render('view', [
            'model' => $model,
            'policy' => $policy,
            'member' => $member,
            'personal' => $personal,
            'partner' => $partner,
            'product' => $product,
            'component' => $component,
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
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (!Yii::$app->request->post('policy_no')) {
            return $this->render('create');
        }

        $policy = Policy::findOne(['policy_no' => Yii::$app->request->post('policy_no')]);
        if ($policy == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['create']);
        }

        $member = Member::findOne(['member_no' => Yii::$app->request->post('member_no')]);
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

        $claim = Claim::find()->orderBy(['id' => SORT_DESC])->one();
        if ($claim != null) {
            $newestId = $claim->id + 1;
        } else {
            $newestId = 1;
        }

        $model = new Claim();
        $model->claim_no = Claim::generateClaimNo(['id' => $newestId]);
        $model->policy_no = $policy->policy_no;
        $model->member_no = $member->member_no;

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

        $model->status = Claim::STATUS_REGISTRATION;
        $model->doc_status = Claim::DOC_STATUS_PENDING;
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        // Claim Document
        $documentIds = Yii::$app->request->post('document_ids');
        $documents = [];
        foreach ($documentIds as $key => $value) {
            $documents[] = [
                'claim_id' => $model->id,
                'document_id' => $value,
                'is_checked' => null,
                'is_mandatory' => null,
            ];
        }

        $attributes = ['claim_id', 'document_id', 'is_checked', 'is_mandatory'];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(ClaimDocument::tableName(), $attributes, $documents)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving Document");
            return $this->redirect(['create']);
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
            $document->is_checked = (in_array($claimDocument['document_id'], $isCheckeds)) ? 1 : null;
            $document->is_mandatory = (in_array($claimDocument['document_id'], $isMandatories)) ? 1 : null;
            $document->save(false);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect([
            'view',
            'id' => $model->id
        ]);
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
