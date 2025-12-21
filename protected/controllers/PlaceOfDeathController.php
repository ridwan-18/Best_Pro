<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\PlaceOfDeath;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * PlaceOfDeathController implements the CRUD actions for PlaceOfDeath model.
 */
class PlaceOfDeathController extends Controller
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
     * Lists all PlaceOfDeath models.
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

        $totalModel = PlaceOfDeath::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => PlaceOfDeath::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = PlaceOfDeath::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new PlaceOfDeath model.
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

        $model = new PlaceOfDeath();
        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing PlaceOfDeath model.
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

        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing PlaceOfDeath model.
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
     * Finds the PlaceOfDeath model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return PlaceOfDeath the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PlaceOfDeath::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
