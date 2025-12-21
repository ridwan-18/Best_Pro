<?php

namespace app\controllers\api\v1;

use Yii;

use yii\web\Controller;
use yii\web\Response;

use app\models\Api;
use app\models\Utils;

class PingController extends Controller
{
    public function beforeAction($action)
    {
        $h = Yii::$app->request->headers;
        $k = Utils::sanitize($h->get('x-api-key'));
        $s = Utils::sanitize($h->get('x-api-secret'));
        if (!Api::validate($k, $s)) {
            $this->redirect(['/api/v1/site/header-error']);
            return false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::$app->response->statusCode = 200;
        return [
            'is_success' => 1,
            'message' => 'Server is running...'
        ];
    }
}
