<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\GlobalReas;
use app\models\GlobalReasRate;
use app\models\GlobalReasUwLimit;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\UploadedFile;

/**
 * GlobalReasController implements the CRUD actions for GlobalReas model.
 */
class GlobalReasController extends Controller
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
     * Lists all GlobalReas models.
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
            'reassuradur_id' => Yii::$app->request->get('reassuradur_id'),
        ];

        $totalModel = GlobalReas::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => GlobalReas::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = GlobalReas::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    public function actionRate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $globalReas = $this->findModel($id);
        if ($globalReas == null) {
            Yii::$app->session->setFlash('error', "Global Reas not found");
            return $this->redirect(['index']);
        }


        $params = ['global_reas_id' => $id];
        $totalRate = GlobalReasRate::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalRate,
            'pageSize' => GlobalReasRate::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $rates = GlobalReasRate::getAll($params);

        return $this->render('rate', [
            'globalReas' => $globalReas,
            'rates' => $rates,
            'pagination' => $pagination,
        ]);
    }

    public function actionUwLimit($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $globalReas = $this->findModel($id);
        if ($globalReas == null) {
            Yii::$app->session->setFlash('error', "Global Reas not found");
            return $this->redirect(['index']);
        }


        $params = ['global_reas_id' => $id];
        $totalRate = GlobalReasUwLimit::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalRate,
            'pageSize' => GlobalReasUwLimit::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $uwLimits = GlobalReasUwLimit::getAll($params);

        return $this->render('uw-limit', [
            'globalReas' => $globalReas,
            'uwLimits' => $uwLimits,
            'pagination' => $pagination,
        ]);
    }

    public function actionUwLimitTemplate()
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
        header('Content-Disposition: attachment;filename="reas-uw-limit-template.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save('php://output');
        exit;
    }

    public function actionRateTemplate()
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
        header('Content-Disposition: attachment;filename="reas-rate-template.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save('php://output');
        exit;
    }

    /**
     * Creates a new GlobalReas model.
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

        $model = new GlobalReas();
        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        $globalReas = GlobalReas::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();
        $newestId = ($globalReas != null) ? $globalReas->id + 1 : 1;
        $pksNo = GlobalReas::generatePksNo($newestId);

        $currentDate = new \DateTime();
        $model->pks_no = $pksNo;
        $model->created_at = $currentDate->format('Y-m-d H:i:s');
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    public function actionUploadUwLimit()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $globalReasId = Yii::$app->request->post('global_reas_id');
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

        $baseRow = 4;
        $uwLimits = [];
        while (!empty($sheetData[$baseRow]['C'])) {
            $uwLimits[] = [
                'global_reas_id' => $globalReasId,
                'min_si' => $sheetData[$baseRow]['A'],
                'max_si' => $sheetData[$baseRow]['C'],
                'min_age' => $minAge1,
                'max_age' => $maxAge1,
                'medical_code' => $sheetData[$baseRow]['D'],
                'created_at' => $createdAt,
                'created_by' => $createdBy,
            ];
            $uwLimits[] = [
                'global_reas_id' => $globalReasId,
                'min_si' => $sheetData[$baseRow]['A'],
                'max_si' => $sheetData[$baseRow]['C'],
                'min_age' => $minAge2,
                'max_age' => $maxAge2,
                'medical_code' => $sheetData[$baseRow]['E'],
                'created_at' => $createdAt,
                'created_by' => $createdBy,
            ];
            $uwLimits[] = [
                'global_reas_id' => $globalReasId,
                'min_si' => $sheetData[$baseRow]['A'],
                'max_si' => $sheetData[$baseRow]['C'],
                'min_age' => $minAge3,
                'max_age' => $maxAge3,
                'medical_code' => $sheetData[$baseRow]['F'],
                'created_at' => $createdAt,
                'created_by' => $createdBy,
            ];
            $uwLimits[] = [
                'global_reas_id' => $globalReasId,
                'min_si' => $sheetData[$baseRow]['A'],
                'max_si' => $sheetData[$baseRow]['C'],
                'min_age' => $minAge4,
                'max_age' => $maxAge4,
                'medical_code' => $sheetData[$baseRow]['G'],
                'created_at' => $createdAt,
                'created_by' => $createdBy,
            ];
            $uwLimits[] = [
                'global_reas_id' => $globalReasId,
                'min_si' => $sheetData[$baseRow]['A'],
                'max_si' => $sheetData[$baseRow]['C'],
                'min_age' => $minAge5,
                'max_age' => $maxAge5,
                'medical_code' => $sheetData[$baseRow]['H'],
                'created_at' => $createdAt,
                'created_by' => $createdBy,
            ];

            $baseRow++;
        }

        if (count($uwLimits) == 0) {
            Yii::$app->session->setFlash('error', "UW Limit empty");
            return $this->redirect(['uw-limit', 'id' => $globalReasId]);
        }

        $attributes = ['global_reas_id', 'min_si', 'max_si', 'min_age', 'max_age', 'medical_code', 'created_at', 'created_by'];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(GlobalReasUwLimit::tableName(), $attributes, $uwLimits)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['uw-limit', 'id' => $globalReasId]);
        }

        Yii::$app->session->setFlash('success', "UW Limit Successfully uploaded");
        return $this->redirect(['uw-limit', 'id' => $globalReasId]);
    }

    public function actionUploadRate()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $globalReasId = Yii::$app->request->post('global_reas_id');
        $file = UploadedFile::getInstanceByName('file');

        $currentDate = new \DateTime();
        $createdAt = $currentDate->format('Y-m-d H:i:s');
        $createdBy = Yii::$app->user->identity->id;

        $inputFileType = \PHPExcel_IOFactory::identify($file->tempName);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($file->tempName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        $baseRow = 2;
        $rates = [];
        while (!empty($sheetData[$baseRow]['A'])) {
            $rates[] = [
                'global_reas_id' => $globalReasId,
                'term' => $sheetData[$baseRow]['A'],
                'rate' => $sheetData[$baseRow]['B'],
                'created_at' => $createdAt,
                'created_by' => $createdBy,
            ];

            $baseRow++;
        }

        if (count($rates) == 0) {
            Yii::$app->session->setFlash('error', "Rate empty");
            return $this->redirect(['rate', 'id' => $globalReasId]);
        }

        $attributes = ['global_reas_id', 'term', 'rate', 'created_at', 'created_by'];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(GlobalReasRate::tableName(), $attributes, $rates)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['rate', 'id' => $globalReasId]);
        }

        Yii::$app->session->setFlash('success', "Rate Successfully uploaded");
        return $this->redirect(['rate', 'id' => $globalReasId]);
    }

    /**
     * Updates an existing GlobalReas model.
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

        $model = $this->findModel($id);

        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        $currentDate = new \DateTime();
        $model->updated_at = $currentDate->format('Y-m-d H:i:s');
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
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
            Yii::$app->request->post('global_reas_id') == ''
            || Yii::$app->request->post('age') == ''
            || Yii::$app->request->post('term') == ''
            || Yii::$app->request->post('rate') == ''
        ) {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->redirect([
                'rate',
                'id' => Yii::$app->request->post('global_reas_id'),
            ]);
        }

        $model = GlobalReasRate::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "Rate not found");
            return $this->redirect([
                'rate',
                'id' => Yii::$app->request->post('global_reas_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->age = Yii::$app->request->post('age');
        $model->term = Yii::$app->request->post('term');
        $model->rate = Yii::$app->request->post('rate');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'rate',
                'id' => Yii::$app->request->post('global_reas_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "Rate Successfully saved");
        return $this->redirect([
            'rate',
            'id' => Yii::$app->request->post('global_reas_id'),
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
            Yii::$app->request->post('global_reas_id') == ''
            || Yii::$app->request->post('min_si') == ''
            || Yii::$app->request->post('max_si') == ''
            || Yii::$app->request->post('min_age') == ''
            || Yii::$app->request->post('max_age') == ''
            || Yii::$app->request->post('medical_code') == ''
        ) {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->redirect([
                'uw-limit',
                'id' => Yii::$app->request->post('global_reas_id'),
            ]);
        }

        $model = GlobalReasUwLimit::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "UW Limit not found");
            return $this->redirect([
                'uw-limit',
                'id' => Yii::$app->request->post('global_reas_id'),
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
                'uw-limit',
                'id' => Yii::$app->request->post('global_reas_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "UW Limit Successfully saved");
        return $this->redirect([
            'uw-limit',
            'id' => Yii::$app->request->post('global_reas_id'),
        ]);
    }

    /**
     * Deletes an existing GlobalReas model.
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

    public function actionDeleteRate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = GlobalReasRate::findOne(['id' => $id]);
        $globalReasId = $model->global_reas_id;
        $model->delete();

        Yii::$app->session->setFlash('success', "Rate Successfully deleted");
        return $this->redirect([
            'rate',
            'id' => $globalReasId,
        ]);
    }

    public function actionDeleteAllRate($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $totalUwLimit = GlobalReasRate::countAll(['global_reas_id' => $id]);
        if ($totalUwLimit == 0) {
            Yii::$app->session->setFlash('error', "Rate is already empty");
            return $this->redirect([
                'rate',
                'id' => $id,
            ]);
        }

        $deleteModel = GlobalReasRate::deleteAll(['global_reas_id' => $id]);
        if (!$deleteModel) {
            Yii::$app->session->setFlash('error', "Error while deleting");
            return $this->redirect([
                'rate',
                'id' => $id,
            ]);
        }

        Yii::$app->session->setFlash('success', "Rate Successfully deleted");
        return $this->redirect([
            'rate',
            'id' => $id,
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

        $model = GlobalReasUwLimit::findOne(['id' => $id]);
        $globalReasId = $model->global_reas_id;
        $model->delete();

        Yii::$app->session->setFlash('success', "UW Limit Successfully deleted");
        return $this->redirect([
            'uw-limit',
            'id' => $globalReasId,
        ]);
    }

    public function actionDeleteAllUwLimit($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $totalUwLimit = GlobalReasUwLimit::countAll(['global_reas_id' => $id]);
        if ($totalUwLimit == 0) {
            Yii::$app->session->setFlash('error', "UW Limit is already empty");
            return $this->redirect([
                'uw-limit',
                'id' => $id,
            ]);
        }

        $deleteModel = GlobalReasUwLimit::deleteAll(['global_reas_id' => $id]);
        if (!$deleteModel) {
            Yii::$app->session->setFlash('error', "Error while deleting");
            return $this->redirect([
                'uw-limit',
                'id' => $id,
            ]);
        }

        Yii::$app->session->setFlash('success', "UW Limit Successfully deleted");
        return $this->redirect([
            'uw-limit',
            'id' => $id,
        ]);
    }

    /**
     * Finds the GlobalReas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return GlobalReas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GlobalReas::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
