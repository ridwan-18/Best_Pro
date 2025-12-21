<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Partner;
use app\models\Product;
use app\models\Quotation;
use app\models\QuotationCommission;
use app\models\QuotationPic;
use app\models\QuotationProduct;
use app\models\QuotationTc;
use app\models\QuotationUwLimit;
use app\models\QuotationRate;
use app\models\QuotationReins;
use app\models\RateEm;
use app\models\MedicalCheckup;
use app\models\forms\QuotationForm;
use app\models\forms\QuotationCommissionForm;
use app\models\forms\QuotationTcForm;
use app\models\ProductRateType;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\UploadedFile;

/**
 * QuotationController implements the CRUD actions for Quotation model.
 */
class QuotationController extends Controller
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
     * Lists all Quotation models.
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
            'proposal_no' => Yii::$app->request->get('proposal_no'),
            'partner_name' => Yii::$app->request->get('partner_name'),
        ];

        $totalModel = Quotation::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => Quotation::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = Quotation::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    public function actionPic($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationModel = $this->findModel($id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $quotationPicModels = QuotationPic::getAll(['quotation_id' => $id]);

        return $this->render('pic', [
            'quotationModel' => $quotationModel,
            'quotationPicModels' => $quotationPicModels,
        ]);
    }

    public function actionProduct($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationModel = $this->findModel($id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $quotationPicModels = QuotationProduct::getAll(['quotation_id' => $id]);

        return $this->render('product', [
            'quotationModel' => $quotationModel,
            'quotationPicModels' => $quotationPicModels,
        ]);
    }

    public function actionMember($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationModel = $this->findModel($id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        // $quotationMemberModels = QuotationMember::getAll(['quotation_id' => $id]);

        return $this->render('member', [
            'quotationModel' => $quotationModel,
            // 'quotationMemberModels' => $quotationMemberModels,
        ]);
    }

    public function actionTc($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationModel = $this->findModel($id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $model = QuotationTc::findOne(['quotation_id' => $quotationModel->id]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "TC not found");
            return $this->redirect(['index']);
        }

        $uwLimits = QuotationUwLimit::getAll(['quotation_id' => $id]);

        $formModel = new QuotationTcForm();
        if (!$formModel->load(Yii::$app->request->post()) || !$formModel->validate()) {
            return $this->render('tc', [
                'quotationModel' => $quotationModel,
                'model' => $model,
                'formModel' => $formModel,
                'uwLimits' => $uwLimits,
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->min_age = $formModel->min_age;
        $model->max_age = $formModel->max_age;
        $model->age_term = $formModel->age_term;
        $model->max_term = $formModel->max_term;
        $model->retroactive = $formModel->retroactive;
        $model->max_si = $formModel->max_si;
        $model->min_premi = $formModel->min_premi;
        $model->max_premi = $formModel->max_premi;
        $model->maturity_age = $formModel->maturity_age;
        $model->rate_em = $formModel->rate_em;
        $model->refund_premium = $formModel->refund_premium;
        $model->refund_type = $formModel->refund_type;
        $model->refund_doc = $formModel->refund_doc;
        $model->grace_period = $formModel->grace_period;
        $model->grace_type = $formModel->grace_type;
        $model->claim_doc = $formModel->claim_doc;
        $model->claim_ratio = $formModel->claim_ratio;
        $model->claim_type = $formModel->claim_type;
        $model->administration_cost = $formModel->administration_cost;
        $model->policy_cost = $formModel->policy_cost;
        $model->member_card_cost = $formModel->member_card_cost;
        $model->certificate_cost = $formModel->certificate_cost;
        $model->stamp_cost = $formModel->stamp_cost;
        $model->medical_checkup = $formModel->medical_checkup;
        $model->remarks = $formModel->remarks;
        $model->release_date = $formModel->release_date;
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['tc', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "TC Successfully saved");
        return $this->redirect(['tc', 'id' => $id]);
    }

    public function actionRate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationModel = $this->findModel($id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $quotationProducts = QuotationProduct::getAll(['quotation_id' => $id]);

        return $this->render('rate', [
            'quotationModel' => $quotationModel,
            'quotationProducts' => $quotationProducts,
        ]);
    }

    public function actionReins($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationModel = $this->findModel($id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $quotationReinsModels = QuotationReins::getAll(['quotation_id' => $id]);

        return $this->render('reins', [
            'quotationModel' => $quotationModel,
            'quotationReinsModels' => $quotationReinsModels,
        ]);
    }

    public function actionViewRate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationProduct = QuotationProduct::findOne(['id' => $id]);
        if ($quotationProduct == null) {
            Yii::$app->session->setFlash('error', "Quotation Product not found");
            return $this->redirect(['index']);
        }

        $product = Product::findOne(['id' => $quotationProduct->product_id]);
        if ($product == null) {
            Yii::$app->session->setFlash('error', "Quotation Product not found");
            return $this->redirect(['index']);
        }

        $quotationModel = $this->findModel($quotationProduct->quotation_id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $params = [
            'quotation_id' => $quotationModel->id,
            'product_id' => $quotationProduct->product_id,
        ];

        $totalRate = QuotationRate::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalRate,
            'pageSize' => QuotationRate::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_ASC,
        ]);

        $quotationRates = QuotationRate::getAll($params);

        return $this->render('view-rate', [
            'quotationModel' => $quotationModel,
            'product' => $product,
            'quotationProduct' => $quotationProduct,
            'quotationRates' => $quotationRates,
            'pagination' => $pagination,
        ]);
    }

    public function actionUwTemplate()
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

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Jumlah Uang Pertanggungan');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Usia (Tahun)');
        $objPHPExcel->getActiveSheet()->setCellValue('A3', '(Rp)');

        $objPHPExcel->getActiveSheet()->setCellValue('D3', '20 - 45');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', '46 - 50');
        $objPHPExcel->getActiveSheet()->setCellValue('F3', '51 - 55');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', '56 - 60');
        $objPHPExcel->getActiveSheet()->setCellValue('H3', '61 - 64');

        $objPHPExcel->getActiveSheet()->setCellValue('A4', '0');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', '100000000');
        $objPHPExcel->getActiveSheet()->setCellValue('A5', '100000001');
        $objPHPExcel->getActiveSheet()->setCellValue('B5', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C5', '200000000');
        $objPHPExcel->getActiveSheet()->setCellValue('A6', '200000001');
        $objPHPExcel->getActiveSheet()->setCellValue('B6', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C6', '300000000');
        $objPHPExcel->getActiveSheet()->setCellValue('A7', '300000001');
        $objPHPExcel->getActiveSheet()->setCellValue('B7', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C7', '400000000');
        $objPHPExcel->getActiveSheet()->setCellValue('A8', '400000001');
        $objPHPExcel->getActiveSheet()->setCellValue('B8', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C8', '500000000');
        $objPHPExcel->getActiveSheet()->setCellValue('A9', '500000001');
        $objPHPExcel->getActiveSheet()->setCellValue('B9', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C9', '600000000');
        $objPHPExcel->getActiveSheet()->setCellValue('A10', '600000001');
        $objPHPExcel->getActiveSheet()->setCellValue('B10', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C10', '750000000');
        $objPHPExcel->getActiveSheet()->setCellValue('A11', '750000001');
        $objPHPExcel->getActiveSheet()->setCellValue('B11', '<=');
        $objPHPExcel->getActiveSheet()->setCellValue('C11', '1000000000');

        $objPHPExcel->getActiveSheet()->setCellValue('D4', 'FC');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', 'FC');
        $objPHPExcel->getActiveSheet()->setCellValue('F4', 'FC');
        $objPHPExcel->getActiveSheet()->setCellValue('G4', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('H4', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('D5', 'FC');
        $objPHPExcel->getActiveSheet()->setCellValue('E5', 'FC');
        $objPHPExcel->getActiveSheet()->setCellValue('F5', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('G5', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('H5', 'A');
        $objPHPExcel->getActiveSheet()->setCellValue('D6', 'FC');
        $objPHPExcel->getActiveSheet()->setCellValue('E6', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('F6', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('G6', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('H6', 'B');
        $objPHPExcel->getActiveSheet()->setCellValue('D7', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('E7', 'NM');
        $objPHPExcel->getActiveSheet()->setCellValue('F7', 'A');
        $objPHPExcel->getActiveSheet()->setCellValue('G7', 'A');
        $objPHPExcel->getActiveSheet()->setCellValue('H7', 'C');
        $objPHPExcel->getActiveSheet()->setCellValue('D8', 'A');
        $objPHPExcel->getActiveSheet()->setCellValue('E8', 'A');
        $objPHPExcel->getActiveSheet()->setCellValue('F8', 'B');
        $objPHPExcel->getActiveSheet()->setCellValue('G8', 'B');
        $objPHPExcel->getActiveSheet()->setCellValue('H8', 'C');
        $objPHPExcel->getActiveSheet()->setCellValue('D9', 'A');
        $objPHPExcel->getActiveSheet()->setCellValue('E9', 'B');
        $objPHPExcel->getActiveSheet()->setCellValue('F9', 'B');
        $objPHPExcel->getActiveSheet()->setCellValue('G9', 'C');
        $objPHPExcel->getActiveSheet()->setCellValue('H9', 'D');
        $objPHPExcel->getActiveSheet()->setCellValue('D10', 'B');
        $objPHPExcel->getActiveSheet()->setCellValue('E10', 'C');
        $objPHPExcel->getActiveSheet()->setCellValue('F10', 'D');
        $objPHPExcel->getActiveSheet()->setCellValue('G10', 'E');
        $objPHPExcel->getActiveSheet()->setCellValue('H10', 'E');
        $objPHPExcel->getActiveSheet()->setCellValue('D11', 'C');
        $objPHPExcel->getActiveSheet()->setCellValue('E11', 'D');
        $objPHPExcel->getActiveSheet()->setCellValue('F11', 'E');
        $objPHPExcel->getActiveSheet()->setCellValue('G11', 'E');
        $objPHPExcel->getActiveSheet()->setCellValue('H11', 'E + FS');

        $objPHPExcel->getActiveSheet()->mergeCells('A1:C2');
        $objPHPExcel->getActiveSheet()->mergeCells('A3:C3');
        $objPHPExcel->getActiveSheet()->mergeCells('D1:H2');

        $objPHPExcel->getActiveSheet()->getStyle('A1:C2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:C3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D1:H2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle('A1:C2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A3:C3')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('D1:H2')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="uw-limit-template.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save('php://output');
        exit;
    }

    public function actionRateTemplate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationProduct = QuotationProduct::findOne(['quotation_id' => $id]);
        if ($quotationProduct->rate_type == ProductRateType::AGE_TERM) {
            header("Location: https://reli.id/static/rate-age-term.xlsx");
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'x/n');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'TARIF PREMI');

        $objPHPExcel->getActiveSheet()->setCellValue('A2', '1');
        $objPHPExcel->getActiveSheet()->setCellValue('A3', '2');
        $objPHPExcel->getActiveSheet()->setCellValue('A4', '3');
        $objPHPExcel->getActiveSheet()->setCellValue('A5', '4');
        $objPHPExcel->getActiveSheet()->setCellValue('A6', '5');
        $objPHPExcel->getActiveSheet()->setCellValue('A7', '6');
        $objPHPExcel->getActiveSheet()->setCellValue('A8', '7');
        $objPHPExcel->getActiveSheet()->setCellValue('A9', '8');
        $objPHPExcel->getActiveSheet()->setCellValue('A10', '9');
        $objPHPExcel->getActiveSheet()->setCellValue('A11', '10');

        $objPHPExcel->getActiveSheet()->setCellValue('B2', '2');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', '3.95');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', '6.25');
        $objPHPExcel->getActiveSheet()->setCellValue('B5', '8.65');
        $objPHPExcel->getActiveSheet()->setCellValue('B6', '11.35');
        $objPHPExcel->getActiveSheet()->setCellValue('B7', '14.15');
        $objPHPExcel->getActiveSheet()->setCellValue('B8', '15.35');
        $objPHPExcel->getActiveSheet()->setCellValue('B9', '18.55');
        $objPHPExcel->getActiveSheet()->setCellValue('B10', '21.95');
        $objPHPExcel->getActiveSheet()->setCellValue('B11', '25.65');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="rate-template.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Creates a new Quotation model.
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

        $quotationFormModel = new QuotationForm();
        $quotationCommissionFormModel = new QuotationCommissionForm();

        if ((!$quotationFormModel->load(Yii::$app->request->post()) || !$quotationFormModel->validate())
            && (!$quotationCommissionFormModel->load(Yii::$app->request->post()) || !$quotationCommissionFormModel->validate())
        ) {
            return $this->render('create', [
                'quotationFormModel' => $quotationFormModel,
                'quotationCommissionFormModel' => $quotationCommissionFormModel,
            ]);
        }

        $partner = Partner::findOne(['id' => $quotationFormModel->partner_id]);
        if ($partner == null) {
            Yii::$app->session->setFlash('error', "Partner not found");
            return $this->redirect(['create']);
        }

        $quotation = Quotation::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $newestId = ($quotation != null) ? $quotation->id + 1 : 1;

        $proposalNo = Quotation::generateProposalNo([
            'id' => $newestId,
            'partner_name' => $partner->name
        ]);

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $quotationModel = new Quotation();
        $quotationModel->proposal_no = $proposalNo;
        $quotationModel->partner_id = $partner->id;
        $quotationModel->member_type = $quotationFormModel->member_type;
        $quotationModel->member_qty = $quotationFormModel->member_qty;
        $quotationModel->last_insurance = $quotationFormModel->last_insurance;
        $quotationModel->business_type = $quotationFormModel->business_type;
        $quotationModel->proposed_date = $quotationFormModel->proposed_date;
        $quotationModel->expired_date = $quotationFormModel->expired_date;
        $quotationModel->term = $quotationFormModel->term;
        $quotationModel->member_card = $quotationFormModel->member_card;
        $quotationModel->certificate_card = $quotationFormModel->certificate_card;
        $quotationModel->distribution_channel = $quotationFormModel->distribution_channel;
        $quotationModel->agent_id = $quotationFormModel->agent_id;
        $quotationModel->payment_method = $quotationFormModel->payment_method;
        $quotationModel->min_age = $quotationFormModel->min_age;
        $quotationModel->max_age = $quotationFormModel->max_age;
        $quotationModel->age_calculate = $quotationFormModel->age_calculate;
        $quotationModel->rate_type = $quotationFormModel->rate_type;
        $quotationModel->effective_policy = $quotationFormModel->effective_policy;
        $quotationModel->notes = $quotationFormModel->notes;
        $quotationModel->status = Quotation::STATUS_NEW;
        $quotationModel->created_at = $currentDateTime;
        $quotationModel->created_by = Yii::$app->user->identity->id;
        if (!$quotationModel->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $quotationCommissionFormModel->load(Yii::$app->request->post());
        $quotationCommissionModel = new QuotationCommission();
        $quotationCommissionModel->quotation_id = $quotationModel->id;
        $quotationCommissionModel->discount = $quotationCommissionFormModel->discount;
        $quotationCommissionModel->maintenance_agent_id = $quotationCommissionFormModel->maintenance_agent_id;
        $quotationCommissionModel->maintenance_fee = $quotationCommissionFormModel->maintenance_fee;
        $quotationCommissionModel->admin_agent_id = $quotationCommissionFormModel->admin_agent_id;
        $quotationCommissionModel->admin_fee = $quotationCommissionFormModel->admin_fee;
        $quotationCommissionModel->handling_agent_id = $quotationCommissionFormModel->handling_agent_id;
        $quotationCommissionModel->handling_fee = $quotationCommissionFormModel->handling_fee;
        $quotationCommissionModel->pph = $quotationCommissionFormModel->pph;
        $quotationCommissionModel->ppn = $quotationCommissionFormModel->ppn;
        $quotationCommissionModel->refferal_agent_id = $quotationCommissionFormModel->refferal_agent_id;
        $quotationCommissionModel->refferal_fee = $quotationCommissionFormModel->refferal_fee;
        $quotationCommissionModel->closing_agent_id = $quotationCommissionFormModel->closing_agent_id;
        $quotationCommissionModel->closing_fee = $quotationCommissionFormModel->closing_fee;
        $quotationCommissionModel->fee_based_agent_id = $quotationCommissionFormModel->fee_based_agent_id;
        $quotationCommissionModel->fee_based = $quotationCommissionFormModel->fee_based;
        if (!$quotationCommissionModel->save(false)) {
            $quotation->delete();
            Yii::$app->session->setFlash('error', "Error while saving commission");
            return $this->redirect(['create']);
        }

        $quotationTc = new QuotationTc();
        $quotationTc->quotation_id = $quotationModel->id;
        $quotationTc->min_age = 0;
        $quotationTc->max_age = 0;
        $quotationTc->age_term = 0;
        $quotationTc->max_term = 0;
        $quotationTc->retroactive = 0;
        $quotationTc->max_si = 0;
        $quotationTc->min_premi = 0;
        $quotationTc->max_premi = 0;
        $quotationTc->maturity_age = 0;
        $quotationTc->rate_em = RateEm::RATE_EM_PRORATE;
        $quotationTc->refund_premium = 0;
        $quotationTc->refund_type = QuotationTc::REFUND_TYPE_GROSS;
        $quotationTc->refund_doc = 0;
        $quotationTc->grace_period = 0;
        $quotationTc->grace_type = QuotationTc::GRACE_TYPE_CALENDAR;
        $quotationTc->claim_doc = 0;
        $quotationTc->claim_ratio = 0;
        $quotationTc->claim_type = QuotationTc::CLAIM_TYPE_GROSS;
        $quotationTc->administration_cost = 0;
        $quotationTc->policy_cost = 0;
        $quotationTc->member_card_cost = 0;
        $quotationTc->certificate_cost = 0;
        $quotationTc->stamp_cost = 0;
        $quotationTc->medical_checkup = MedicalCheckup::MC_CMP;
        $quotationTc->release_date = $dateTime->format('Y-m-d');
        $quotationTc->created_at = $currentDateTime;
        $quotationTc->created_by = Yii::$app->user->identity->id;
        if (!$quotationTc->save(false)) {
            $quotation->delete();
            Yii::$app->session->setFlash('error', "Error while saving TC");
            return $this->redirect(['create']);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    public function actionCreatePartner()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $partner = Partner::findOne(['code' => Yii::$app->request->post('code')]);
        if ($partner != null) {
            Yii::$app->session->setFlash('error', "Partner already exist");
            return $this->redirect(['create']);
        }

        $partner = Partner::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $newestId = ($partner != null) ? $partner->id + 1 : 1;

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $code = Partner::generateCode([
            'id' => $newestId,
            'partner_name' => Yii::$app->request->post('name')
        ]);

        $model = new Partner();
        $model->code = $code;
        $model->name = Yii::$app->request->post('name');
        $model->province = Yii::$app->request->post('province');
        $model->city = Yii::$app->request->post('city');
        $model->address = Yii::$app->request->post('address');
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        Yii::$app->session->setFlash('success', "Partner Successfully saved");
        return $this->redirect(['create']);
    }

    public function actionCreatePic()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (Yii::$app->request->post('name') == '') {
            Yii::$app->session->setFlash('error', "Name field cannot be blank");
            return $this->redirect([
                'product',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model = new QuotationPic();
        $model->quotation_id = Yii::$app->request->post('quotation_id');
        $model->name = Yii::$app->request->post('name');
        $model->phone = Yii::$app->request->post('phone');
        $model->email = Yii::$app->request->post('email');
        $model->job_position = Yii::$app->request->post('job_position');
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'pic',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "PIC Successfully saved");
        return $this->redirect([
            'pic',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionCreateProduct()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (Yii::$app->request->post('product_id') == '') {
            Yii::$app->session->setFlash('error', "Product field cannot be blank");
            return $this->redirect([
                'product',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model = new QuotationProduct();
        $model->quotation_id = Yii::$app->request->post('quotation_id');
        $model->product_id = Yii::$app->request->post('product_id');
        $model->premium_type = Yii::$app->request->post('premium_type');
        $model->rate_type = Yii::$app->request->post('rate_type');
        $model->period_type = Yii::$app->request->post('period_type');
        $model->si_type = Yii::$app->request->post('si_type');
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'product',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "Product Successfully saved");
        return $this->redirect([
            'product',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionCreateUwLimit()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (
            Yii::$app->request->post('quotation_id') == ''
            || Yii::$app->request->post('min_si') == ''
            || Yii::$app->request->post('max_si') == ''
            || Yii::$app->request->post('min_age') == ''
            || Yii::$app->request->post('max_age') == ''
            || Yii::$app->request->post('medical_code') == ''
        ) {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->redirect([
                'tc',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model = new QuotationUwLimit();
        $model->quotation_id = Yii::$app->request->post('quotation_id');
        $model->min_si = Yii::$app->request->post('min_si');
        $model->max_si = Yii::$app->request->post('max_si');
        $model->min_age = Yii::$app->request->post('min_age');
        $model->max_age = Yii::$app->request->post('max_age');
        $model->medical_code = Yii::$app->request->post('medical_code');
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'tc',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "UW Limit Successfully saved");
        return $this->redirect([
            'tc',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionCreateRate()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (
            Yii::$app->request->post('quotation_product_id') == ''
            || Yii::$app->request->post('quotation_id') == ''
            || Yii::$app->request->post('product_id') == ''
            || Yii::$app->request->post('type') == ''
            || Yii::$app->request->post('age') == ''
            || Yii::$app->request->post('term') == ''
            || Yii::$app->request->post('unit') == ''
            || Yii::$app->request->post('rate') == ''
            || Yii::$app->request->post('interest') == ''
        ) {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->redirect([
                'view-rate',
                'id' => Yii::$app->request->post('quotation_product_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model = new QuotationRate();
        $model->quotation_id = Yii::$app->request->post('quotation_id');
        $model->product_id = Yii::$app->request->post('product_id');
        $model->type = Yii::$app->request->post('type');
        $model->age = Yii::$app->request->post('age');
        $model->term = Yii::$app->request->post('term');
        $model->unit = Yii::$app->request->post('unit');
        $model->rate = Yii::$app->request->post('rate');
        $model->interest = Yii::$app->request->post('interest');
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'view-rate',
                'id' => Yii::$app->request->post('quotation_product_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "Rate Successfully saved");
        return $this->redirect([
            'view-rate',
            'id' => Yii::$app->request->post('quotation_product_id'),
        ]);
    }

    public function actionCreateReins()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (Yii::$app->request->post('global_reas_id') == '') {
            Yii::$app->session->setFlash('error', "PKS No field cannot be blank");
            return $this->redirect([
                'reins',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $reins = QuotationReins::findOne(['quotation_id' => Yii::$app->request->post('quotation_id')]);
        if ($reins != null) {
            Yii::$app->session->setFlash('error', "Already Exists");
            return $this->redirect([
                'reins',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model = new QuotationReins();
        $model->quotation_id = Yii::$app->request->post('quotation_id');
        $model->global_reas_id = Yii::$app->request->post('global_reas_id');
        $model->si_from = Yii::$app->request->post('si_from');
        $model->si_to = Yii::$app->request->post('si_to');
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'reins',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "Reins Successfully saved");
        return $this->redirect([
            'reins',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionUploadUwLimit()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationId = Yii::$app->request->post('quotation_id');
        $file = UploadedFile::getInstanceByName('file');

        $currentDate = new \DateTime();
        $createdAt = $currentDate->format('Y-m-d H:i:s');
        $createdBy = Yii::$app->user->identity->id;

        $inputFileType = \PHPExcel_IOFactory::identify($file->tempName);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($file->tempName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        $age1 = explode(" - ", $sheetData[3]['D']);
        $minAge1 = $age1[0];
        $maxAge1 = $age1[1];
        $age2 = explode(" - ", $sheetData[3]['E']);
        $minAge2 = $age2[0];
        $maxAge2 = $age2[1];
        $age3 = explode(" - ", $sheetData[3]['F']);
        $minAge3 = $age3[0];
        $maxAge3 = $age3[1];
        $age4 = explode(" - ", $sheetData[3]['G']);
        $minAge4 = $age4[0];
        $maxAge4 = $age4[1];
        $age5 = explode(" - ", $sheetData[3]['H']);
        $minAge5 = $age5[0];
        $maxAge5 = $age5[1];
        $age6 = explode(" - ", $sheetData[3]['I']);
        $minAge6 = $age6[0];
        $maxAge6 = $age6[1];
        $age7 = explode(" - ", $sheetData[3]['J']);
        $minAge7 = $age7[0];
        $maxAge7 = $age7[1];
        $age8 = explode(" - ", $sheetData[3]['K']);
        $minAge8 = $age8[0];
        $maxAge8 = $age8[1];
        $age9 = explode(" - ", $sheetData[3]['L']);
        $minAge9 = $age9[0];
        $maxAge9 = $age9[1];

        $baseRow = 4;
        $uwLimits = [];
        while (!empty($sheetData[$baseRow]['C'])) {
            if ($sheetData[$baseRow]['D'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge1,
                    'max_age' => $maxAge1,
                    'medical_code' => $sheetData[$baseRow]['D'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['E'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge2,
                    'max_age' => $maxAge2,
                    'medical_code' => $sheetData[$baseRow]['E'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['F'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge3,
                    'max_age' => $maxAge3,
                    'medical_code' => $sheetData[$baseRow]['F'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['G'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge4,
                    'max_age' => $maxAge4,
                    'medical_code' => $sheetData[$baseRow]['G'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['H'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge5,
                    'max_age' => $maxAge5,
                    'medical_code' => $sheetData[$baseRow]['H'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['I'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge6,
                    'max_age' => $maxAge6,
                    'medical_code' => $sheetData[$baseRow]['I'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['J'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge7,
                    'max_age' => $maxAge7,
                    'medical_code' => $sheetData[$baseRow]['J'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['K'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge8,
                    'max_age' => $maxAge8,
                    'medical_code' => $sheetData[$baseRow]['K'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            if ($sheetData[$baseRow]['L'] != '') {
                $uwLimits[] = [
                    'quotation_id' => $quotationId,
                    'min_si' => $sheetData[$baseRow]['A'],
                    'max_si' => $sheetData[$baseRow]['C'],
                    'min_age' => $minAge9,
                    'max_age' => $maxAge9,
                    'medical_code' => $sheetData[$baseRow]['L'],
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
            }

            $baseRow++;
        }

        if (count($uwLimits) == 0) {
            Yii::$app->session->setFlash('error', "UW Limit empty");
            return $this->redirect(['tc', 'id' => $quotationId]);
        }

        $attributes = ['quotation_id', 'min_si', 'max_si', 'min_age', 'max_age', 'medical_code', 'created_at', 'created_by'];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(QuotationUwLimit::tableName(), $attributes, $uwLimits)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['tc', 'id' => $quotationId]);
        }

        Yii::$app->session->setFlash('success', "UW Limit Successfully uploaded");
        return $this->redirect(['tc', 'id' => $quotationId]);
    }

    public function actionUploadRate()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $quotationId = Yii::$app->request->post('quotation_id');
        $quotationProductId = Yii::$app->request->post('quotation_product_id');
        $productId = Yii::$app->request->post('product_id');
        $file = UploadedFile::getInstanceByName('file');

        $currentDate = new \DateTime();
        $createdAt = $currentDate->format('Y-m-d H:i:s');
        $createdBy = Yii::$app->user->identity->id;

        $quotationProduct = QuotationProduct::findOne(['id' => $quotationProductId]);
        if ($quotationProduct == null) {
            Yii::$app->session->setFlash('error', "Rate empty");
            return $this->redirect(['view-rate', 'id' => $quotationProductId]);
        }

        $inputFileType = \PHPExcel_IOFactory::identify($file->tempName);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($file->tempName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        $rates = [];
        if ($quotationProduct->rate_type == ProductRateType::AGE_TERM) {
            $lastColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
            $lastColumn++;
            $lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
            for ($column = 'B'; $column != $lastColumn; $column++) {
                if (!empty($sheetData[3]['B'])) {
                    for ($row = 4; $row <= $lastRow; $row++) {
                        if (!empty($sheetData[$row][$column]) && $sheetData[$row][$column] > 0) {
                            $rates[] = [
                                'quotation_id' => $quotationId,
                                'product_id' => $productId,
                                'type' => 'SINGLE',
                                'age' => $sheetData[$row]['A'],
                                'term' => $sheetData[3][$column],
                                'unit' => 0,
                                'rate' => $sheetData[$row][$column],
                                'interest' => 0,
                                'created_at' => $createdAt,
                                'created_by' => $createdBy,
                            ];
                        }
                    }
                }
            }
        } else {
            $baseRow = 2;
            while (!empty($sheetData[$baseRow]['A'])) {
                $rates[] = [
                    'quotation_id' => $quotationId,
                    'product_id' => $productId,
                    'type' => 'SINGLE',
                    'age' => 0,
                    'term' => $sheetData[$baseRow]['A'],
                    'unit' => 0,
                    'rate' => $sheetData[$baseRow]['B'],
                    'interest' => 0,
                    'created_at' => $createdAt,
                    'created_by' => $createdBy,
                ];
                $baseRow++;
            }
        }

        if (count($rates) == 0) {
            Yii::$app->session->setFlash('error', "Rate empty");
            return $this->redirect(['view-rate', 'id' => $quotationProductId]);
        }

        $attributes = ['quotation_id', 'product_id', 'type', 'age', 'term', 'unit', 'rate', 'interest', 'created_at', 'created_by'];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(QuotationRate::tableName(), $attributes, $rates)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view-rate', 'id' => $quotationProductId]);
        }

        Yii::$app->session->setFlash('success', "Rate Successfully uploaded");
        return $this->redirect(['view-rate', 'id' => $quotationProductId]);
    }

    /**
     * Updates an existing Quotation model.
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

        $quotationModel = $this->findModel($id);
        if ($quotationModel == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $quotationCommissionModel = QuotationCommission::findOne(['quotation_id' => $id]);
        if ($quotationCommissionModel == null) {
            Yii::$app->session->setFlash('error', "Quotation Commission not found");
            return $this->redirect(['index']);
        }

        $quotationFormModel = new QuotationForm();
        $quotationCommissionFormModel = new QuotationCommissionForm();

        if ((!$quotationFormModel->load(Yii::$app->request->post()) || !$quotationFormModel->validate())
            && (!$quotationCommissionFormModel->load(Yii::$app->request->post()) || !$quotationCommissionFormModel->validate())
        ) {
            return $this->render('update', [
                'quotationFormModel' => $quotationFormModel,
                'quotationCommissionFormModel' => $quotationCommissionFormModel,
                'quotationModel' => $quotationModel,
                'quotationCommissionModel' => $quotationCommissionModel,
            ]);
        }

        if ($quotationModel->partner_id == $quotationFormModel->partner_id) {
            $proposalNo = $quotationModel->proposal_no;
        } else {
            $partner = Partner::findOne(['id' => $quotationFormModel->partner_id]);
            if ($partner == null) {
                Yii::$app->session->setFlash('error', "Partner not found");
                return $this->redirect(['update', 'id' => $id]);
            }

            $quotation = Quotation::find()
                ->orderBy(['id' => SORT_DESC])
                ->one();
            $newestId = ($quotation != null) ? $quotation->id + 1 : 1;

            $proposalNo = Quotation::generateProposalNo([
                'id' => $newestId,
                'partner_name' => $partner->name
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $quotationModel->proposal_no = $proposalNo;
        $quotationModel->partner_id = $quotationFormModel->partner_id;
        $quotationModel->member_type = $quotationFormModel->member_type;
        $quotationModel->member_qty = $quotationFormModel->member_qty;
        $quotationModel->last_insurance = $quotationFormModel->last_insurance;
        $quotationModel->business_type = $quotationFormModel->business_type;
        $quotationModel->proposed_date = $quotationFormModel->proposed_date;
        $quotationModel->expired_date = $quotationFormModel->expired_date;
        $quotationModel->term = $quotationFormModel->term;
        $quotationModel->member_card = $quotationFormModel->member_card;
        $quotationModel->certificate_card = $quotationFormModel->certificate_card;
        $quotationModel->distribution_channel = $quotationFormModel->distribution_channel;
        $quotationModel->agent_id = $quotationFormModel->agent_id;
        $quotationModel->payment_method = $quotationFormModel->payment_method;
        $quotationModel->min_age = $quotationFormModel->min_age;
        $quotationModel->max_age = $quotationFormModel->max_age;
        $quotationModel->age_calculate = $quotationFormModel->age_calculate;
        $quotationModel->rate_type = $quotationFormModel->rate_type;
        $quotationModel->effective_policy = $quotationFormModel->effective_policy;
        $quotationModel->notes = $quotationFormModel->notes;
        $quotationModel->status = Quotation::STATUS_REVISED;
        $quotationModel->updated_at = $currentDateTime;
        $quotationModel->updated_by = Yii::$app->user->identity->id;
        if (!$quotationModel->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['update', 'id' => $id]);
        }

        $quotationCommissionFormModel->load(Yii::$app->request->post());
        $quotationCommissionModel->discount = $quotationCommissionFormModel->discount;
        $quotationCommissionModel->maintenance_agent_id = $quotationCommissionFormModel->maintenance_agent_id;
        $quotationCommissionModel->maintenance_fee = $quotationCommissionFormModel->maintenance_fee;
        $quotationCommissionModel->admin_agent_id = $quotationCommissionFormModel->admin_agent_id;
        $quotationCommissionModel->admin_fee = $quotationCommissionFormModel->admin_fee;
        $quotationCommissionModel->handling_agent_id = $quotationCommissionFormModel->handling_agent_id;
        $quotationCommissionModel->handling_fee = $quotationCommissionFormModel->handling_fee;
        $quotationCommissionModel->pph = $quotationCommissionFormModel->pph;
        $quotationCommissionModel->ppn = $quotationCommissionFormModel->ppn;
        $quotationCommissionModel->refferal_agent_id = $quotationCommissionFormModel->refferal_agent_id;
        $quotationCommissionModel->refferal_fee = $quotationCommissionFormModel->refferal_fee;
        $quotationCommissionModel->closing_agent_id = $quotationCommissionFormModel->closing_agent_id;
        $quotationCommissionModel->closing_fee = $quotationCommissionFormModel->closing_fee;
        $quotationCommissionModel->fee_based_agent_id = $quotationCommissionFormModel->fee_based_agent_id;
        $quotationCommissionModel->fee_based = $quotationCommissionFormModel->fee_based;
        if (!$quotationCommissionModel->save(false)) {
            $quotation->delete();
            Yii::$app->session->setFlash('error', "Error while saving commission");
            return $this->redirect(['update', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    public function actionUpdatePic()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (Yii::$app->request->post('name') == '') {
            Yii::$app->session->setFlash('error', "Name field cannot be blank");
            return $this->redirect([
                'product',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $model = QuotationPic::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "PIC not found");
            return $this->redirect([
                'pic',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->name = Yii::$app->request->post('name');
        $model->phone = Yii::$app->request->post('phone');
        $model->email = Yii::$app->request->post('email');
        $model->job_position = Yii::$app->request->post('job_position');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'pic',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "PIC Successfully saved");
        return $this->redirect([
            'pic',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionUpdateProduct()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (Yii::$app->request->post('product_id') == '') {
            Yii::$app->session->setFlash('error', "Product field cannot be blank");
            return $this->redirect([
                'product',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $model = QuotationProduct::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "Product not found");
            return $this->redirect([
                'product',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->product_id = Yii::$app->request->post('product_id');
        $model->premium_type = Yii::$app->request->post('premium_type');
        $model->rate_type = Yii::$app->request->post('rate_type');
        $model->period_type = Yii::$app->request->post('period_type');
        $model->si_type = Yii::$app->request->post('si_type');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'product',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "Product Successfully saved");
        return $this->redirect([
            'product',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionUpdateReins()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (Yii::$app->request->post('global_reas_id') == '') {
            Yii::$app->session->setFlash('error', "PKS No field cannot be blank");
            return $this->redirect([
                'reins',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }


        $model = QuotationReins::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "Reins not found");
            return $this->redirect([
                'reins',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->quotation_id = Yii::$app->request->post('quotation_id');
        $model->global_reas_id = Yii::$app->request->post('global_reas_id');
        $model->si_from = Yii::$app->request->post('si_from');
        $model->si_to = Yii::$app->request->post('si_to');
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'reins',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "Reins Successfully saved");
        return $this->redirect([
            'reins',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionUpdateUwLimit()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (
            Yii::$app->request->post('quotation_id') == ''
            || Yii::$app->request->post('min_si') == ''
            || Yii::$app->request->post('max_si') == ''
            || Yii::$app->request->post('min_age') == ''
            || Yii::$app->request->post('max_age') == ''
            || Yii::$app->request->post('medical_code') == ''
        ) {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->redirect([
                'tc',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $model = QuotationUwLimit::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "UW Limit not found");
            return $this->redirect([
                'tc',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->min_si = Yii::$app->request->post('min_si');
        $model->max_si = Yii::$app->request->post('max_si');
        $model->min_age = Yii::$app->request->post('min_age');
        $model->max_age = Yii::$app->request->post('max_age');
        $model->medical_code = Yii::$app->request->post('medical_code');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'tc',
                'id' => Yii::$app->request->post('quotation_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "UW Limit Successfully saved");
        return $this->redirect([
            'tc',
            'id' => Yii::$app->request->post('quotation_id'),
        ]);
    }

    public function actionUpdateRate()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (
            Yii::$app->request->post('type') == ''
            || Yii::$app->request->post('age') == ''
            || Yii::$app->request->post('term') == ''
            || Yii::$app->request->post('unit') == ''
            || Yii::$app->request->post('rate') == ''
            || Yii::$app->request->post('interest') == ''
        ) {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->redirect([
                'view-rate',
                'id' => Yii::$app->request->post('quotation_product_id'),
            ]);
        }

        $model = QuotationRate::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "Rate not found");
            return $this->redirect([
                'view-rate',
                'id' => Yii::$app->request->post('quotation_product_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->type = Yii::$app->request->post('type');
        $model->age = Yii::$app->request->post('age');
        $model->term = Yii::$app->request->post('term');
        $model->unit = Yii::$app->request->post('unit');
        $model->rate = Yii::$app->request->post('rate');
        $model->interest = Yii::$app->request->post('interest');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'view-rate',
                'id' => Yii::$app->request->post('quotation_product_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "Rate Successfully saved");
        return $this->redirect([
            'view-rate',
            'id' => Yii::$app->request->post('quotation_product_id'),
        ]);
    }

    public function actionApproveTc($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);
        $model->is_req_tc = 1;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['tc', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully Approved");
        return $this->redirect(['tc', 'id' => $id]);
    }

    public function actionApproveRate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);
        $model->is_req_new_rate = 1;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['rate', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully Approved");
        return $this->redirect(['rate', 'id' => $id]);
    }

    public function actionApproveReins($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);
        $model->is_req_reas = 1;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['reins', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully Approved");
        return $this->redirect(['reins', 'id' => $id]);
    }

    /**
     * Deletes an existing Quotation model.
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

    public function actionDeletePic($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = QuotationPic::findOne(['id' => $id]);
        $quotationId = $model->quotation_id;
        $model->delete();

        Yii::$app->session->setFlash('success', "PIC Successfully deleted");
        return $this->redirect([
            'pic',
            'id' => $quotationId,
        ]);
    }

    public function actionDeleteProduct($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = QuotationProduct::findOne(['id' => $id]);
        $quotationId = $model->quotation_id;
        $model->delete();

        Yii::$app->session->setFlash('success', "Product Successfully deleted");
        return $this->redirect([
            'product',
            'id' => $quotationId,
        ]);
    }

    public function actionDeleteUwLimit($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = QuotationUwLimit::findOne(['id' => $id]);
        $quotationId = $model->quotation_id;
        $model->delete();

        Yii::$app->session->setFlash('success', "UW Limit Successfully deleted");
        return $this->redirect([
            'tc',
            'id' => $quotationId,
        ]);
    }

    public function actionDeleteAllUwLimit($quotationId)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        QuotationUwLimit::deleteAll(['quotation_id' => $quotationId]);

        Yii::$app->session->setFlash('success', "UW Limit Successfully deleted");
        return $this->redirect([
            'tc',
            'id' => $quotationId,
        ]);
    }

    public function actionDeleteRate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = QuotationRate::findOne(['id' => $id]);
        $quotationId = $model->quotation_id;
        $productId = $model->product_id;
        $model->delete();

        $quotationProduct = QuotationProduct::findOne([
            'quotation_id' => $quotationId,
            'product_id' => $productId,
        ]);

        Yii::$app->session->setFlash('success', "Rate Successfully deleted");
        return $this->redirect([
            'view-rate',
            'id' => $quotationProduct->id,
        ]);
    }

    public function actionDeleteAllRate($quotationId, $productId)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        QuotationRate::deleteAll(['quotation_id' => $quotationId, 'product_id' => $productId]);

        $quotationProduct = QuotationProduct::findOne([
            'quotation_id' => $quotationId,
            'product_id' => $productId,
        ]);

        Yii::$app->session->setFlash('success', "Rate Successfully deleted");
        return $this->redirect([
            'view-rate',
            'id' => $quotationProduct->id,
        ]);
    }

    /**
     * Finds the Quotation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Quotation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quotation::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
