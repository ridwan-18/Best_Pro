<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Signature;
use app\models\forms\SignatureForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
// use yii\data\Pagination;
use yii\web\UploadedFile;

/**
 * SignatureController implements the CRUD actions for Signature model.
 */
class SignatureController extends Controller
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
     * Lists all Signature models.
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

        return $this->redirect(['update', 'id' => 1]);

        // $params = [
        //     'name' => Yii::$app->request->get('name'),
        // ];

        // $totalModel = Signature::countAll($params);

        // $pagination = new Pagination([
        //     'totalCount' => $totalModel,
        //     'pageSize' => Signature::PAGE_SIZE,
        //     'pageSizeParam' => false,
        // ]);

        // $params = array_merge($params, [
        //     'offset' => $pagination->offset,
        //     'limit' => $pagination->limit,
        //     'sort' => SORT_DESC,
        // ]);

        // $models = Signature::getAll($params);

        // return $this->render('index', [
        //     'models' => $models,
        //     'pagination' => $pagination,
        // ]);
    }

    /**
     * Creates a new Signature model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    // public function actionCreate()
    // {
    //     if (
    //         Yii::$app->user->isGuest
    //         || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
    //     ) {
    //         return $this->goHome();
    //     }

    //     $formModel = new SignatureForm();
    //     if (!$formModel->load(Yii::$app->request->post()) || !$formModel->validate()) {
    //         return $this->render('create', [
    //             'formModel' => $formModel,
    //         ]);
    //     }

    //     $formModel->picture_file = UploadedFile::getInstance($formModel, 'picture_file');
    //     if ($formModel->picture_file == null) {
    //         Yii::$app->session->setFlash('error', "Please select a Picture");
    //         return $this->render('create', [
    //             'formModel' => $formModel,
    //         ]);
    //     }

    //     $model = new Signature();
    //     $model->picture_file = $formModel->picture_file;
    //     if (!$model->upload(false)) {
    //         Yii::$app->session->setFlash('error', "Error while uploading picture");
    //         return $this->render('create', [
    //             'formModel' => $formModel,
    //         ]);
    //     }

    //     $model->name = $formModel->name;
    //     $model->position = $formModel->position;
    //     $currentDate = new \DateTime();
    //     $model->created_at = $currentDate->format('Y-m-d H:i:s');
    //     $model->created_by = Yii::$app->user->identity->id;
    //     if (!$model->save(false)) {
    //         Yii::$app->session->setFlash('error', "Error while saving");
    //         return $this->render('create', [
    //             'formModel' => $formModel,
    //         ]);
    //     }

    //     Yii::$app->session->setFlash('success', "Successfully saved");
    //     return $this->redirect(['index']);
    // }

    /**
     * Updates an existing Signature model.
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
        $formModel = new SignatureForm();
        $formModel->policy_name = $model->policy_name;
        $formModel->policy_position = $model->policy_position;
        $formModel->member_name = $model->member_name;
        $formModel->member_position = $model->member_position;
        $formModel->claim_name = $model->claim_name;
        $formModel->claim_position = $model->claim_position;
        if (!$formModel->load(Yii::$app->request->post()) || !$formModel->validate()) {
            return $this->render('update', [
                'model' => $model,
                'formModel' => $formModel,
            ]);
        }

        $formModel->policy_picture_file = UploadedFile::getInstance($formModel, 'policy_picture_file');
        if ($formModel->policy_picture_file != null) {
            $model->policy_picture_file = $formModel->policy_picture_file;
            if (!$model->uploadPolicy(false)) {
                Yii::$app->session->setFlash('error', "Error while uploading policy");
                return $this->render('update', [
                    'model' => $model,
                    'formModel' => $formModel,
                ]);
            }
        }

        $formModel->member_picture_file = UploadedFile::getInstance($formModel, 'member_picture_file');
        if ($formModel->member_picture_file != null) {
            $model->member_picture_file = $formModel->member_picture_file;
            if (!$model->uploadMember(false)) {
                Yii::$app->session->setFlash('error', "Error while uploading member");
                return $this->render('update', [
                    'model' => $model,
                    'formModel' => $formModel,
                ]);
            }
        }

        $formModel->claim_picture_file = UploadedFile::getInstance($formModel, 'claim_picture_file');
        if ($formModel->claim_picture_file != null) {
            $model->claim_picture_file = $formModel->claim_picture_file;
            if (!$model->uploadClaim(false)) {
                Yii::$app->session->setFlash('error', "Error while uploading claim");
                return $this->render('update', [
                    'model' => $model,
                    'formModel' => $formModel,
                ]);
            }
        }

        $model->policy_name = $formModel->policy_name;
        $model->policy_position = $formModel->policy_position;
        $model->member_name = $formModel->member_name;
        $model->member_position = $formModel->member_position;
        $model->claim_name = $formModel->claim_name;
        $model->claim_position = $formModel->claim_position;
        $currentDate = new \DateTime();
        $model->updated_at = $currentDate->format('Y-m-d H:i:s');
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->render('update', [
                'model' => $model,
                'formModel' => $formModel,
            ]);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Signature model.
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
     * Finds the Signature model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Signature the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Signature::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
