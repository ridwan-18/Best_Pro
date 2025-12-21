<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
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
            'username' => Yii::$app->request->get('username'),
            'email' => Yii::$app->request->get('email'),
        ];

        $totalModel = User::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => User::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = User::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new User model.
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

        $model = new User();
        $roles = User::roles();
        if (!$model->load(Yii::$app->request->post()) || !$model->validate()) {
            return $this->render('create', [
                'model' => $model,
                'roles' => $roles,
            ]);
        }

        $user = User::findOne(['username' => $model->username]);
        if ($user != null) {
            Yii::$app->session->setFlash('error', "Username already exist");
            return $this->render('create', [
                'model' => $model,
                'roles' => $roles,
            ]);
        }

        $currentDate = new \DateTime();
        $model->created_at = $currentDate->format('Y-m-d H:i:s');
        $model->created_by = Yii::$app->user->identity->id;
        $model->status = User::STATUS_ACTIVE;
        $model->hashPassword($model->password);
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->render('create', [
                'model' => $model,
                'roles' => $roles,
            ]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing User model.
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

    public function actionResetPassword($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);

        if (!Yii::$app->request->post() || Yii::$app->request->post() == null) {
            return $this->render('reset-password', [
                'model' => $model,
            ]);
        }

        if (Yii::$app->request->post('new_password') == '' || Yii::$app->request->post('repeat_password') == '') {
            Yii::$app->session->setFlash('error', "Field cannot be blank");
            return $this->render('reset-password', [
                'model' => $model,
            ]);
        }

        if (Yii::$app->request->post('new_password') != Yii::$app->request->post('repeat_password')) {
            Yii::$app->session->setFlash('error', "Passoword not match");
            return $this->render('reset-password', [
                'model' => $model,
            ]);
        }

        $model->password = Yii::$app->request->post('new_password');
        $model->hashPassword();
        $currentDate = new \DateTime();
        $model->updated_at = $currentDate->format('Y-m-d H:i:s');
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->render('reset-password', [
                'model' => $model,
            ]);
            return $this->redirect(['index']);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
