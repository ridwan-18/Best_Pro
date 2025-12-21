<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Partner;
use app\models\Policy;
use app\models\Product;
use app\models\Quotation;
use app\models\QuotationProduct;
use app\models\Signature;
use app\models\forms\PolicyForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use Da\QrCode\QrCode;
use yii\helpers\Url;

/**
 * PolicyController implements the CRUD actions for Policy model.
 */
class PolicyController extends Controller
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
                        'issue' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Policy models.
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
            'spa_no' => Yii::$app->request->get('spa_no'),
            'policy_no' => Yii::$app->request->get('policy_no'),
            'partner_name' => Yii::$app->request->get('partner_name'),
            'spa_status' => Yii::$app->request->get('spa_status'),
        ];

        $totalModel = Policy::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => Policy::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = Policy::getAll($params);

        return $this->render('index', [
            'models' => $models,
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

        $policy = Policy::find()
            ->select([
                'quotation_id',
                'partner_id',
                'policy_no',
                'spa_date',
                'payment_method',
            ])
            ->asArray()
            ->where(['id' => $id])
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

        $qrCodeFilename = 'policy-' . $id . '.png';
        $qrCode = (new QrCode(Url::base(true) . '/policy/print-signature/?id=' . $id))
            ->setSize(75)
            ->setMargin(5);
        $qrCode->writeFile(\Yii::getAlias('@webroot') . '/uploads/signature/' . $qrCodeFilename);
        $qrCodeUrl = Url::base() . Signature::PICTURE_PATH . $qrCodeFilename;

        return $this->render('print', [
            'policy' => $policy,
            'partner' => $partner,
            'product' => $product,
            'signature' => $signature,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    public function actionPrintSignature($id)
    {
        $this->layout = '/print';

        $policy = Policy::find()
            ->select([
                'quotation_id',
                'partner_id',
                'policy_no',
                'spa_date',
                'payment_method',
            ])
            ->asArray()
            ->where(['id' => $id])
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

        return $this->render('print-signature', [
            'policy' => $policy,
            'partner' => $partner,
            'product' => $product,
            'signature' => $signature,
        ]);
    }

    public function actionGetEndDate()
    {
        $quotation = Quotation::findOne(['id' => Yii::$app->request->post('quotation_id')]);
        $data = [];
        $data['end_date'] = Policy::getEndDate($quotation->term, Yii::$app->request->post('effective_date'));
        echo json_encode($data);
    }

    /**
     * Creates a new Policy model.
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

        $policyFormModel = new PolicyForm();

        if ((!$policyFormModel->load(Yii::$app->request->post()) || !$policyFormModel->validate())) {
            return $this->render('create', [
                'policyFormModel' => $policyFormModel,
            ]);
        }

        $quotation = Quotation::findOne(['id' => $policyFormModel->quotation_id]);
        if ($quotation == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['create']);
        }

        $partner = Partner::findOne(['id' => $quotation->partner_id]);
        if ($partner == null) {
            Yii::$app->session->setFlash('error', "Partner not found");
            return $this->redirect(['create']);
        }

        $policy = Policy::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $newestId = ($policy != null) ? $policy->id + 1 : 1;

        $spaNo = Policy::generateSpaNo([
            'id' => $newestId,
        ]);

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $policyModel = new Policy();
        $policyModel->spa_no = $spaNo;
        $policyModel->quotation_id = $policyFormModel->quotation_id;
        $policyModel->partner_id = $quotation->partner_id;
        $policyModel->spa_date = $policyFormModel->spa_date;
        $policyModel->distribution_channel = $quotation->distribution_channel;
        $policyModel->pic_name = $policyFormModel->pic_name;
        $policyModel->pic_title = $policyFormModel->pic_title;
        $policyModel->pic_id_card_no = $policyFormModel->pic_id_card_no;
        $policyModel->pic_phone = $policyFormModel->pic_phone;
        $policyModel->pic_email = $policyFormModel->pic_email;
        $policyModel->bank_id = $policyFormModel->bank_id;
        $policyModel->bank_branch = $policyFormModel->bank_branch;
        $policyModel->bank_account_no = $policyFormModel->bank_account_no;
        $policyModel->bank_account_name = $policyFormModel->bank_account_name;
        $policyModel->payment_method = $quotation->payment_method;
        $policyModel->effective_date = $policyFormModel->effective_date;
        $policyModel->end_date = $policyFormModel->end_date;
        $policyModel->insurance_period = $policyFormModel->insurance_period;
        $policyModel->payment_period = $policyFormModel->payment_period;
        $policyModel->member_type = $quotation->member_type;
        $policyModel->member_qty = $quotation->member_qty;
        $policyModel->member_insured = ($policyFormModel->member_insured > 99999999) ? 99999999 : $policyFormModel->member_insured;
        $policyModel->notes = $policyFormModel->notes;
        $policyModel->work_location = $policyFormModel->work_location;
        $policyModel->sign_location = $policyFormModel->sign_location;
        $policyModel->sign_date = $policyFormModel->sign_date;
        $policyModel->sign_by = $policyFormModel->sign_by;
        $policyModel->sign_title = $policyFormModel->sign_title;
        $policyModel->spa_status = Policy::SPA_STATUS_REGISTER;
        $policyModel->status = $policyFormModel->status;
        $policyModel->created_at = $currentDateTime;
        $policyModel->created_by = Yii::$app->user->identity->id;
        if (!$policyModel->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $partner->zip_code = $policyFormModel->partner_zip_code;
        $partner->phone = $policyFormModel->partner_phone;
        $partner->fax = $policyFormModel->partner_fax;
        $partner->email = $policyFormModel->partner_email;
        $partner->established_date = $policyFormModel->partner_established_date;
        $partner->npwp = $policyFormModel->partner_npwp;
        $partner->certificate_no = $policyFormModel->partner_certificate_no;
        $partner->siup = $policyFormModel->partner_siup;
        $partner->fund_source = $policyFormModel->partner_fund_source;
        $partner->insurance_purpose = $policyFormModel->partner_insurance_purpose;
        $partner->insurance_purpose_description = $policyFormModel->partner_insurance_purpose_description;
        $partner->updated_at = $currentDateTime;
        $partner->updated_by = Yii::$app->user->identity->id;
        if (!$partner->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving partner");
            return $this->redirect(['create']);
        }


        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing Policy model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $policyModel = $this->findModel($id);

        $quotation = Quotation::findOne(['id' => $policyModel->quotation_id]);
        if ($quotation == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['update', 'id' => $id]);
        }

        $partner = Partner::findOne(['id' => $quotation->partner_id]);
        if ($partner == null) {
            Yii::$app->session->setFlash('error', "Partner not found");
            return $this->redirect(['update', 'id' => $id]);
        }

        $policyFormModel = new PolicyForm();
        if ((!$policyFormModel->load(Yii::$app->request->post()) || !$policyFormModel->validate())) {
            return $this->render('update', [
                'policyModel' => $policyModel,
                'quotation' => $quotation,
                'partner' => $partner,
                'policyFormModel' => $policyFormModel,
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $policyModel->policy_no = $policyFormModel->policy_no;
        $policyModel->pic_name = $policyFormModel->pic_name;
        $policyModel->pic_title = $policyFormModel->pic_title;
        $policyModel->pic_id_card_no = $policyFormModel->pic_id_card_no;
        $policyModel->pic_phone = $policyFormModel->pic_phone;
        $policyModel->pic_email = $policyFormModel->pic_email;
        $policyModel->bank_id = $policyFormModel->bank_id;
        $policyModel->bank_branch = $policyFormModel->bank_branch;
        $policyModel->bank_account_no = $policyFormModel->bank_account_no;
        $policyModel->bank_account_name = $policyFormModel->bank_account_name;
        $policyModel->effective_date = $policyFormModel->effective_date;
        $policyModel->end_date = $policyFormModel->end_date;
        $policyModel->insurance_period = $policyFormModel->insurance_period;
        $policyModel->payment_period = $policyFormModel->payment_period;
        $policyModel->member_insured = $policyFormModel->member_insured;
        $policyModel->notes = $policyFormModel->notes;
        $policyModel->work_location = $policyFormModel->work_location;
        $policyModel->sign_location = $policyFormModel->sign_location;
        $policyModel->sign_date = $policyFormModel->sign_date;
        $policyModel->sign_by = $policyFormModel->sign_by;
        $policyModel->sign_title = $policyFormModel->sign_title;
        $policyModel->updated_at = $currentDateTime;
        $policyModel->updated_by = Yii::$app->user->identity->id;
        if (!$policyModel->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['update', 'id' => $id]);
        }

        $partner->zip_code = $policyFormModel->partner_zip_code;
        $partner->phone = $policyFormModel->partner_phone;
        $partner->fax = $policyFormModel->partner_fax;
        $partner->email = $policyFormModel->partner_email;
        $partner->established_date = $policyFormModel->partner_established_date;
        $partner->npwp = $policyFormModel->partner_npwp;
        $partner->certificate_no = $policyFormModel->partner_certificate_no;
        $partner->siup = $policyFormModel->partner_siup;
        $partner->fund_source = $policyFormModel->partner_fund_source;
        $partner->insurance_purpose = $policyFormModel->partner_insurance_purpose;
        $partner->insurance_purpose_description = $policyFormModel->partner_insurance_purpose_description;
        $partner->updated_at = $currentDateTime;
        $partner->updated_by = Yii::$app->user->identity->id;
        if (!$partner->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving partner");
            return $this->redirect(['update', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    public function actionIssue($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);

        $quotationProduct = QuotationProduct::findOne(['quotation_id' => $model->quotation_id]);
        if ($quotationProduct == null) {
            Yii::$app->session->setFlash('error', "Policy does not have product");
            return $this->redirect(['update', 'id' => $id]);
        }

        $product = Product::findOne(['id' => $quotationProduct->product_id]);
        if ($product == null) {
            Yii::$app->session->setFlash('error', "Product not found");
            return $this->redirect(['update', 'id' => $id]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $policyNo = Policy::generatePolicyNo([
            'code' => $product->code,
            'id' => $model->id,
        ]);

        $model->policy_no = $policyNo;
        $model->spa_status = Policy::SPA_STATUS_ISSUED;
        $model->issued_at = $currentDateTime;
        $model->issued_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['update', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully issued");
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Policy model.
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

        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', "Successfully deleted");
        return $this->redirect(['index']);
    }

    /**
     * Finds the Policy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Policy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Policy::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
