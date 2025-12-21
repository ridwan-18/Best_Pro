<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;
use app\models\ReportBilling;
use app\models\Member;

class ReportBillingController extends Controller
{
    public function actionIndex()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $wheres = [];
        if (Yii::$app->request->get('invoice_no') != '') {
            $wheres['invoice_no'] = Yii::$app->request->get('invoice_no');
        }
        if (Yii::$app->request->get('reg_no') != '') {
            $wheres['reg_no'] = Yii::$app->request->get('reg_no');
        }
        if (Yii::$app->request->get('status') != '') {
            $wheres[Member::tableName() . '.status'] = Yii::$app->request->get('status');
        }
        if (Yii::$app->request->get('member_status') != '') {
            $wheres[Member::tableName() . '.member_status'] = Yii::$app->request->get('member_status');
        }

        $models = [];
        if (!empty($wheres)) {
            $totalModel = ReportBilling::countAll($wheres);
            $models = ReportBilling::getAll($wheres, $totalModel);
        }

        $renderParams = [
            'models' => $models,
            'statuses' => Member::statuses(),
            'memberStatuses' => Member::memberStatuses(),
        ];

        return $this->render('index', $renderParams);
    }

    public function actionExport()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $wheres = [];
        if (Yii::$app->request->get('invoice_no') != '') {
            $wheres['invoice_no'] = Yii::$app->request->get('invoice_no');
        }
        if (Yii::$app->request->get('reg_no') != '') {
            $wheres['reg_no'] = Yii::$app->request->get('reg_no');
        }
        if (Yii::$app->request->get('status') != '') {
            $wheres[Member::tableName() . '.status'] = Yii::$app->request->get('status');
        }
        if (Yii::$app->request->get('member_status') != '') {
            $wheres[Member::tableName() . '.member_status'] = Yii::$app->request->get('member_status');
        }

        $models = [];
        if (!empty($wheres)) {
            $totalModel = ReportBilling::countAll($wheres);
            $models = ReportBilling::getAll($wheres, $totalModel);
        }

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_FOLIO);

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'NO');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'POLICY NO');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'POLICY HOLDER');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', 'PRODUCT');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', 'MEMBER NO');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', 'NAME');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', 'BIRTH DATE');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', 'AGE');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', 'START DATE');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', 'END DATE');
        $objPHPExcel->getActiveSheet()->setCellValue('K1', 'TERM');
        $objPHPExcel->getActiveSheet()->setCellValue('L1', 'UP');
        $objPHPExcel->getActiveSheet()->setCellValue('M1', 'PREMI');
        $objPHPExcel->getActiveSheet()->setCellValue('N1', 'MORTALITA');
        $objPHPExcel->getActiveSheet()->setCellValue('O1', 'EXTRA PREMI');
        $objPHPExcel->getActiveSheet()->setCellValue('P1', 'TOTAL PREMI');
        $objPHPExcel->getActiveSheet()->setCellValue('Q1', 'MEDICAL CODE');
        $objPHPExcel->getActiveSheet()->setCellValue('R1', 'INVOICE NO');
        $objPHPExcel->getActiveSheet()->setCellValue('S1', 'REG NO');
        $objPHPExcel->getActiveSheet()->setCellValue('T1', 'ACCEPTED DATE');
        $objPHPExcel->getActiveSheet()->setCellValue('U1', 'BATCH NO');
        $objPHPExcel->getActiveSheet()->setCellValue('V1', 'STNC DATE');
        $objPHPExcel->getActiveSheet()->setCellValue('W1', 'STATUS');
        $objPHPExcel->getActiveSheet()->setCellValue('X1', 'MEMBER STATUS');

        $i = 1;
        $baseRow = 2;
        foreach ($models as $model) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $baseRow, $i);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $baseRow, $model['policy_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $baseRow, $model['partner']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $baseRow, $model['product']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $baseRow, $model['member_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $baseRow, $model['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $baseRow, $model['birth_date']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $baseRow, $model['age']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $baseRow, $model['start_date']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $baseRow, $model['end_date']);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $baseRow, $model['term']);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $baseRow, $model['sum_insured']);
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $baseRow, $model['gross_premium']);
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $baseRow, $model['em_premium']);
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $baseRow, $model['extra_premium']);
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $baseRow, $model['nett_premium']);
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $baseRow, $model['medical_code']);
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $baseRow, $model['invoice_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $baseRow, $model['reg_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $baseRow, $model['accept_date']);
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $baseRow, $model['batch_no']);
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $baseRow, $model['stnc_date']);
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $baseRow, $model['status']);
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $baseRow, $model['member_status']);

            $i++;
            $baseRow++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report-billing.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $objWriter->save('php://output');
        exit;
    }
}
