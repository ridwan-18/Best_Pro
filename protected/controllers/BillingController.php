<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Billing;
use app\models\Batch;
use app\models\Policy;
use app\models\Partner;
use app\models\Product;
use app\models\Member;
use app\models\Quotation;
use app\models\QuotationCommission;
use app\models\QuotationProduct;
use app\models\Signature;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use Da\QrCode\QrCode;
use yii\helpers\Url;

/**
 * BillingController implements the CRUD actions for Billing model.
 */
class BillingController extends Controller
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
     * Lists all Billing models.
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

        $totalModel = Billing::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => Billing::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = Billing::getAll($params);

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

        $billing = Billing::find()
            ->select([
                'id',
                'batch_no',
                'policy_no',
                'reg_no',
                'invoice_no',
                'invoice_date',
                'due_date',
                'total_member',
                'gross_premium',
                'extra_premium',
                'discount',
                'handling_fee',
                'ppn',
                'pph',
                'admin_cost',
                'policy_cost',
                'member_card_cost',
                'certificate_cost',
                'stamp_cost',
                'total_billing',
            ])
            ->asArray()
            ->where(['id' => $id])
            ->one();
        if ($billing == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['index']);
        }

        $batch = Batch::find()
            ->select([
                'total_up',
            ])
            ->asArray()
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
            ])
            ->one();
        if ($batch == null) {
            Yii::$app->session->setFlash('error', "Batch not found");
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
            ->where(['policy_no' => $billing['policy_no']])
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

        $commission = QuotationCommission::find()
            ->select([
                'discount',
                'handling_fee',
                'ppn',
                'pph',
            ])
            ->asArray()
            ->where([QuotationCommission::tableName() . '.quotation_id' => $policy['quotation_id']])
            ->one();

        $signature = Signature::findOne(['id' => 1]);

        $qrCodeFilename = 'billing-' . $id . '.png';
        $qrCode = (new QrCode(Url::base(true) . '/billing/print-signature/?id=' . $id))
            ->setSize(75)
            ->setMargin(5);
        $qrCode->writeFile(\Yii::getAlias('@webroot') . '/uploads/signature/' . $qrCodeFilename);
        $qrCodeUrl = Url::base() . Signature::PICTURE_PATH . $qrCodeFilename;

        $firstMember = Member::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_INFORCE,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        $lastMember = Member::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_INFORCE,
            ])
            ->orderBy(['id' => SORT_DESC])
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

        $members = Member::getAll([
            'policy_no' => $billing['policy_no'],
            'batch_no' => $billing['batch_no'],
            'member_status' => Member::MEMBER_STATUS_INFORCE,
        ]);

        $pendingMembers = Member::getAll([
            'policy_no' => $billing['policy_no'],
            'batch_no' => $billing['batch_no'],
            'member_status' => Member::MEMBER_STATUS_PENDING,
        ]);

        $declinedMembers = Member::getAll([
            'policy_no' => $billing['policy_no'],
            'batch_no' => $billing['batch_no'],
            'member_status' => Member::MEMBER_STATUS_DECLINED,
        ]);

        $inforceMember = Member::find()
            ->select([
                'COUNT(id) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(gross_premium) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_INFORCE
            ])
            ->one();

        $pendingMember = Member::find()
            ->select([
                'COUNT(id) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(gross_premium) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_PENDING
            ])
            ->one();

        return $this->render('print', [
            'billing' => $billing,
            'batch' => $batch,
            'policy' => $policy,
            'partner' => $partner,
            'product' => $product,
            'commission' => $commission,
            'signature' => $signature,
            'qrCodeUrl' => $qrCodeUrl,
            'memberNoList' => $memberNoList,
            'quotation' => $quotation,
            'members' => $members,
            'pendingMembers' => $pendingMembers,
            'declinedMembers' => $declinedMembers,
            'inforceMember' => $inforceMember,
            'pendingMember' => $pendingMember,
        ]);
    }

    public function actionPrintSignature($id)
    {
        $this->layout = '/print';

        $billing = Billing::find()
            ->select([
                'id',
                'batch_no',
                'policy_no',
                'reg_no',
                'invoice_no',
                'invoice_date',
                'due_date',
                'total_member',
                'gross_premium',
                'extra_premium',
                'discount',
                'handling_fee',
                'ppn',
                'pph',
                'admin_cost',
                'policy_cost',
                'member_card_cost',
                'certificate_cost',
                'stamp_cost',
                'total_billing',
            ])
            ->asArray()
            ->where(['id' => $id])
            ->one();
        if ($billing == null) {
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
            ->where(['policy_no' => $billing['policy_no']])
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

        $commission = QuotationCommission::find()
            ->select([
                'discount',
                'handling_fee',
                'ppn',
                'pph',
            ])
            ->asArray()
            ->where([QuotationCommission::tableName() . '.quotation_id' => $policy['quotation_id']])
            ->one();

        $signature = Signature::findOne(['id' => 1]);

        $firstMember = Member::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_INFORCE,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        $lastMember = Member::find()
            ->asArray()
            ->select(['member_no'])
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_INFORCE,
            ])
            ->orderBy(['id' => SORT_DESC])
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

        $members = Member::getAll([
            'policy_no' => $billing['policy_no'],
            'batch_no' => $billing['batch_no'],
            'member_status' => Member::MEMBER_STATUS_INFORCE,
        ]);

        $pendingMembers = Member::getAll([
            'policy_no' => $billing['policy_no'],
            'batch_no' => $billing['batch_no'],
            'member_status' => Member::MEMBER_STATUS_PENDING,
        ]);

        $declinedMembers = Member::getAll([
            'policy_no' => $billing['policy_no'],
            'batch_no' => $billing['batch_no'],
            'member_status' => Member::MEMBER_STATUS_DECLINED,
        ]);

        $inforceMember = Member::find()
            ->select([
                'COUNT(id) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(gross_premium) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_INFORCE
            ])
            ->one();

        $pendingMember = Member::find()
            ->select([
                'COUNT(id) AS total_member',
                'SUM(sum_insured) AS total_si',
                'SUM(gross_premium) AS total_gross_premium',
            ])
            ->asArray()
            ->where([
                'batch_no' => $billing['batch_no'],
                'policy_no' => $billing['policy_no'],
                'member_status' => Member::MEMBER_STATUS_PENDING
            ])
            ->one();

        return $this->render('print-signature', [
            'billing' => $billing,
            'policy' => $policy,
            'partner' => $partner,
            'product' => $product,
            'commission' => $commission,
            'signature' => $signature,
            'memberNoList' => $memberNoList,
            'quotation' => $quotation,
            'members' => $members,
            'pendingMembers' => $pendingMembers,
            'declinedMembers' => $declinedMembers,
            'inforceMember' => $inforceMember,
            'pendingMember' => $pendingMember,
        ]);
    }

    /**
     * Displays a single Billing model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $billing = $this->findModel($id);
        $policy = Policy::findOne(['policy_no' => $billing->policy_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);

        return $this->render('view', [
            'billing' => $billing,
            'policy' => $policy,
            'partner' => $partner,
        ]);
    }

    /**
     * Creates a new Billing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Billing();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Billing model.
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

    /**
     * Deletes an existing Billing model.
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
     * Finds the Billing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Billing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Billing::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
