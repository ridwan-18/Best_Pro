<?php

namespace app\controllers\api\v1;

use Yii;

use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actionHeaderError()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        Yii::$app->response->statusCode = 401;
        return [
            'is_success' => 0,
            'message' => 'Unknown Client, Key not Found'
        ];
    }
}
