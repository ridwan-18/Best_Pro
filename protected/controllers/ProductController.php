<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Product;
use app\models\ProductEm;
use app\models\TemplateEm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
                        'delete-em' => ['POST'],
                        'delete-all-em' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Product models.
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
            'name' => Yii::$app->request->get('name'),
        ];

        $totalModel = Product::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => Product::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = Product::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    public function actionEm($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $product = $this->findModel($id);
        if ($product == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['index']);
        }

        $totalProductEm = ProductEm::countAll(['product_id' => $id]);

        $pagination = new Pagination([
            'totalCount' => $totalProductEm,
            'pageSize' => ProductEm::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge(['product_id' => $id], [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $productEms = ProductEm::getAll($params);

        return $this->render('em', [
            'product' => $product,
            'productEms' => $productEms,
            'pagination' => $pagination,
        ]);
    }

    public function actionEmTemplate()
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

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Extra Premi');
        $objPHPExcel->getActiveSheet()->mergeCells('A1:Q1');

        $objPHPExcel->getActiveSheet()->setCellValue('A2', 'Percentage');
        $objPHPExcel->getActiveSheet()->setCellValue('B2', 'Usia');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', 'Masa Asuransi');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:A3');
        $objPHPExcel->getActiveSheet()->mergeCells('B2:B3');
        $objPHPExcel->getActiveSheet()->mergeCells('C2:Q2');

        $objPHPExcel->getActiveSheet()->setCellValue('C3', '1');
        $objPHPExcel->getActiveSheet()->setCellValue('D3', '2');
        $objPHPExcel->getActiveSheet()->setCellValue('E3', '3');
        $objPHPExcel->getActiveSheet()->setCellValue('F3', '4');
        $objPHPExcel->getActiveSheet()->setCellValue('G3', '5');
        $objPHPExcel->getActiveSheet()->setCellValue('H3', '6');
        $objPHPExcel->getActiveSheet()->setCellValue('I3', '7');
        $objPHPExcel->getActiveSheet()->setCellValue('J3', '8');
        $objPHPExcel->getActiveSheet()->setCellValue('K3', '9');
        $objPHPExcel->getActiveSheet()->setCellValue('L3', '10');
        $objPHPExcel->getActiveSheet()->setCellValue('M3', '11');
        $objPHPExcel->getActiveSheet()->setCellValue('N3', '12');
        $objPHPExcel->getActiveSheet()->setCellValue('O3', '13');
        $objPHPExcel->getActiveSheet()->setCellValue('P3', '14');
        $objPHPExcel->getActiveSheet()->setCellValue('Q3', '15');

        $i = 4;
        $templateEms = TemplateEm::find()
            ->asArray()
            ->all();
        foreach ($templateEms as $templateEm) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $templateEm['percentage']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $templateEm['age']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $templateEm['em1']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $templateEm['em2']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $templateEm['em3']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $templateEm['em4']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $templateEm['em5']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $templateEm['em6']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $templateEm['em7']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $templateEm['em8']);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $templateEm['em9']);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $templateEm['em10']);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $templateEm['em11']);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $templateEm['em12']);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $templateEm['em13']);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $templateEm['em14']);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $i, $templateEm['em15']);
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="em-template.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save('php://output');
        exit;
    }


    /**
     * Creates a new Product model.
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

        $model = new Product();
        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        $product = Product::findOne(['code' => $model->code]);
        if ($product != null) {
            Yii::$app->session->setFlash('error', "Product already exist");
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        $currentDate = new \DateTime();
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

    public function actionUploadEm()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $productId = Yii::$app->request->post('product_id');
        $file = UploadedFile::getInstanceByName('file');

        $currentDate = new \DateTime();
        $createdAt = $currentDate->format('Y-m-d H:i:s');
        $createdBy = Yii::$app->user->identity->id;

        $inputFileType = \PHPExcel_IOFactory::identify($file->tempName);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($file->tempName);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        $ems = [];
        $lastColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
        $lastColumn++;
        $lastRow = $objPHPExcel->getActiveSheet()->getHighestRow();
        for ($column = 'C'; $column != $lastColumn; $column++) {
            if (!empty($sheetData[3]['C'])) {
                for ($row = 4; $row <= $lastRow; $row++) {
                    if (!empty($sheetData[$row][$column]) && $sheetData[$row][$column] > 0) {
                        $ems[] = [
                            'product_id' => $productId,
                            'percentage' => $sheetData[$row]['A'],
                            'age' => $sheetData[$row]['B'],
                            'term' => $sheetData[3][$column],
                            'em' => $sheetData[$row][$column],
                            'created_at' => $createdAt,
                            'created_by' => $createdBy,
                        ];
                    }
                }
            }
        }

        if (count($ems) == 0) {
            Yii::$app->session->setFlash('error', "EM empty");
            return $this->redirect(['em', 'id' => $productId]);
        }

        $attributes = ['product_id', 'percentage', 'age', 'term', 'em', 'created_at', 'created_by'];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(ProductEm::tableName(), $attributes, $ems)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['em', 'id' => $productId]);
        }

        Yii::$app->session->setFlash('success', "EM Successfully uploaded");
        return $this->redirect(['em', 'id' => $productId]);
    }

    /**
     * Updates an existing Product model.
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

        $product = Product::find()
            ->where(['code' => $model->code])
            ->andWhere(['!=', 'code', Yii::$app->request->post('old_code')])
            ->one();
        if ($product != null) {
            Yii::$app->session->setFlash('error', "Product already exist");
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

    public function actionUpdateEm()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        if (
            Yii::$app->request->post('product_id') == ''
            || Yii::$app->request->post('percentage') == ''
            || Yii::$app->request->post('age') == ''
            || Yii::$app->request->post('term') == ''
            || Yii::$app->request->post('em') == ''
        ) {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->redirect([
                'em',
                'id' => Yii::$app->request->post('product_id'),
            ]);
        }

        $model = ProductEm::findOne(['id' => Yii::$app->request->post('id')]);
        if ($model == null) {
            Yii::$app->session->setFlash('error', "EM not found");
            return $this->redirect([
                'em',
                'id' => Yii::$app->request->post('product_id'),
            ]);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->percentage = Yii::$app->request->post('percentage');
        $model->age = Yii::$app->request->post('age');
        $model->term = Yii::$app->request->post('term');
        $model->em = Yii::$app->request->post('em');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'em',
                'id' => Yii::$app->request->post('product_id'),
            ]);
        }

        Yii::$app->session->setFlash('success', "EM Successfully saved");
        return $this->redirect([
            'em',
            'id' => Yii::$app->request->post('product_id'),
        ]);
    }

    /**
     * Deletes an existing Product model.
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

    public function actionDeleteEm($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = ProductEm::findOne(['id' => $id]);
        $productId = $model->product_id;
        $model->delete();

        Yii::$app->session->setFlash('success', "EM Successfully deleted");
        return $this->redirect([
            'em',
            'id' => $productId,
        ]);
    }

    public function actionDeleteAllEm($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $totalProductEm = ProductEm::countAll(['product_id' => $id]);
        if ($totalProductEm == 0) {
            Yii::$app->session->setFlash('error', "EM is already empty");
            return $this->redirect([
                'em',
                'id' => $id,
            ]);
        }

        $deleteModel = ProductEm::deleteAll(['product_id' => $id]);
        if (!$deleteModel) {
            Yii::$app->session->setFlash('error', "Error while deleting");
            return $this->redirect([
                'em',
                'id' => $id,
            ]);
        }

        Yii::$app->session->setFlash('success', "EM Successfully deleted");
        return $this->redirect([
            'em',
            'id' => $id,
        ]);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
