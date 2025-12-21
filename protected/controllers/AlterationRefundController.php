<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\AlterationRefund;
use app\models\AlterationRefundMember;
use app\models\Policy;
use app\models\Member;
use app\models\Personal;
use app\models\Partner;
use app\models\Quotation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;

/**
 * AlterationRefundController implements the CRUD actions for AlterationRefund model.
 */
class AlterationRefundController extends Controller
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
     * Lists all AlterationRefund models.
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

        $totalModel = AlterationRefund::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => AlterationRefund::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = AlterationRefund::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    public function actionGetMemberData()
    {
        $member = Member::findOne(['member_no' => Yii::$app->request->post('member_no')]);
        $personal = Personal::findOne(['personal_no' => $member->personal_no]);
        $data = [];
        $data['member_no'] = $member->member_no;
        $data['name'] = $personal->name;
        $data['birth_date'] = $personal->birth_date;
        $data['age'] = $member->age;
        $data['start_date'] = $member->start_date;
        $data['end_date'] = $member->end_date;
        $data['sum_insured'] = number_format($member->total_si);
        $data['premi'] = number_format($member->total_premium);
        $data['extra_premi'] = number_format($member->extra_premium);
        $data['cancelled_premi'] = number_format($member->total_premium);
        echo json_encode($data);
    }

    public function actionGetBatchData()
    {
        $members = Member::find()
            ->asArray()
            ->select([
                Member::tableName() . '.member_no',
                Member::tableName() . '.age',
                Member::tableName() . '.start_date',
                Member::tableName() . '.end_date',
                Member::tableName() . '.total_si',
                Member::tableName() . '.total_premium',
                Member::tableName() . '.extra_premium',
                Personal::tableName() . '.name',
                Personal::tableName() . '.birth_date'
            ])
            ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . Member::tableName() . '.personal_no')
            ->where([
                'policy_no' => Yii::$app->request->post('policy_no'),
                'batch_no' => Yii::$app->request->post('batch_no')
            ])
            ->all();

        $data = [];
        foreach ($members as $member) {
            $data[] = [
                'member_no' => $member['member_no'],
                'name' => $member['name'],
                'birth_date' => $member['birth_date'],
                'age' => $member['age'],
                'start_date' => $member['start_date'],
                'end_date' => $member['end_date'],
                'sum_insured' => number_format($member['total_si']),
                'premi' => number_format($member['total_premium']),
                'extra_premi' => number_format($member['extra_premium']),
                'cancelled_premi' => number_format($member['total_premium'])
            ];
        }
        echo json_encode($data);
    }

    /**
     * Displays a single AlterationRefund model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);

        $members = AlterationRefundMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);

        return $this->render('view', [
            'model' => $model,
            'partner' => $partner,
            'members' => $members,
        ]);
    }

    /**
     * Creates a new AlterationRefund model.
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

        if (!Yii::$app->request->post('members_no')) {
            return $this->render('create');
        }

        $membersNo = Yii::$app->request->post('members_no');
        $newEndDates = Yii::$app->request->post('new_end_dates');
        $policy = Policy::findOne(['policy_no' => Yii::$app->request->post('policy_no')]);
        if ($policy == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['create']);
        }

        $quotation = Quotation::findOne(['id' => $policy->quotation_id]);
        if ($quotation == null) {
            Yii::$app->session->setFlash('error', "Quotation not found");
            return $this->redirect(['create']);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $alteration = AlterationRefund::find()->orderBy(['id' => SORT_DESC])->one();
        if ($alteration != null) {
            $newestId = $alteration->id + 1;
        } else {
            $newestId = 1;
        }

        $model = new AlterationRefund();
        $model->alteration_no = AlterationRefund::generateAlterationNo(['id' => $newestId]);
        $model->alteration_date = date("Y-m-d");
        $model->policy_no = $policy->policy_no;
        $model->total_si = 0;
        $model->total_premium = 0;
        $model->total_premium_refund = 0;
        $model->status = AlterationRefund::STATUS_PENDING;
        $model->created_at = $currentDateTime;
        $model->created_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $members = [];
        $totalSi = 0;
        $totalPremium = 0;
        $totalPremiumRefund = 0;
        foreach ($membersNo as $key => $value) {
            $member = Member::findOne(['member_no' => $value]);
            $personal = Personal::findOne(['personal_no' => $member->personal_no]);
            $remainingTerm = Member::getTerm($quotation->rate_type, $newEndDates[$key], $member->end_date);
            $premiRefund = round(($remainingTerm / $member->term) * (0.5 * $member->total_premium));
            $members[] = [
                'alteration_no' => $model->alteration_no,
                'member_no' => $member->member_no,
                'name' => $personal->name,
                'birth_date' => $personal->birth_date,
                'age' => $member->age,
                'start_date' => $member->start_date,
                'end_date' => $member->end_date,
                'new_end_date' => $newEndDates[$key],
                'term' => $member->term,
                'remaining_term' => $remainingTerm,
                'sum_insured' => $member->sum_insured,
                'premi' => $member->total_premium,
                'extra_premi' => $member->extra_premium,
                'premi_refund' => $premiRefund
            ];

            $totalSi += $member->sum_insured;
            $totalPremium += $member->total_premium;
            $totalPremiumRefund += $premiRefund;
        }

        if (count($members) == 0) {
            Yii::$app->session->setFlash('error', "Member was empty");
            return $this->redirect(['create']);
        }

        $model->total_si = $totalSi;
        $model->total_premium = $totalPremium;
        $model->total_premium_refund = $totalPremiumRefund;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['create']);
        }

        $attributes = [
            'alteration_no',
            'member_no',
            'name',
            'birth_date',
            'age',
            'start_date',
            'end_date',
            'new_end_date',
            'term',
            'remaining_term',
            'sum_insured',
            'premi',
            'extra_premi',
            'premi_refund',
        ];
        $modelSave = Yii::$app->db->createCommand()
            ->batchInsert(AlterationRefundMember::tableName(), $attributes, $members)
            ->execute();
        if (!$modelSave) {
            Yii::$app->session->setFlash('error', "Error while saving Member");
            return $this->redirect(['create']);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing AlterationRefund model.
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

    public function actionApprove($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);

        $members = AlterationRefundMember::getAll([
            'alteration_no' => $model->alteration_no,
        ]);
        foreach ($members as $member) {
            $membership = Member::findOne(['member_no' => $member['member_no']]);
            $membership->status = Member::STATUS_SURRENDER;
            $membership->save(false);
        }

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        $model->status = AlterationRefund::STATUS_APPROVED;
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully issued");
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing AlterationRefund model.
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
     * Finds the AlterationRefund model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return AlterationRefund the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AlterationRefund::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
