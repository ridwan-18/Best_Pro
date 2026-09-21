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
use app\models\MemberClaim;
use app\models\claim_bank_jatim_detail;
use app\models\dokument_claim_jatim;
use yii\web\UploadedFile;
use app\models\map_member_dokumen_medis;

/**
 * AlterationRefundController implements the CRUD actions for AlterationRefund model.
 */
class MemberClaimController extends Controller
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
			|| !User::findIdentityByAccessToken(
				Yii::$app->user->identity->access_token
			)
		) {
			return $this->goHome();
		}

		$params = [ 'id_pengajuan' => Yii::$app->request->get('id_pengajuan'), 
		'ktp' => Yii::$app->request->get('ktp'),
		'nama' => Yii::$app->request->get('nama'),
		'id_transaksi' => Yii::$app->request->get('id_transaksi'), 
		'status_claim' => Yii::$app->request->get('status_claim'), 
		];

		$totalModel = MemberClaim::countAll($params);

		$pagination = new Pagination([
			'totalCount' => $totalModel,
			'pageSize' => MemberClaim::PAGE_SIZE,
			'pageSizeParam' => false,
		]);

		$params = array_merge($params, [
			'offset' => $pagination->offset,
			'limit' => $pagination->limit,
			'sort' => SORT_DESC,
		]);

		$models = MemberClaim::getAll($params);

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
		$model = MemberClaim::findOne($id);

		if ($model === null) {
			throw new \yii\web\NotFoundHttpException(
				'Data claim dengan ID ' . $id . ' tidak ditemukan.'
			);
		}
		
		$filecbc = [];
		
		// var_dump($model);
		
		
		if ($model !== null && $model->id_pengajuan != null) {
			$filecbc = map_member_dokumen_medis::find()
				->asArray()
				->where([
					'id_loan' => $model->no_akad,
					'jenis_dokumen' => 'claim',
				])
				->all();
		}

		return $this->render('view', [
			'model' => $model,
			'filecbc' => $filecbc,
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

        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');
		
		$revisiDokumen = Yii::$app->request->post('revisi_dokumen');

		$dokumenPayload = [];

		if (!empty($revisiDokumen)) {

			foreach ($revisiDokumen as $detailId => $revisiText) {

				$modelDetail = claim_bank_jatim_detail::findOne($detailId);

				if ($modelDetail) {

					$modelDetail->revisi_dokumen = $revisiText;
					$modelDetail->save(false);

					$dokumenPayload[] = [
						'code_dokumen' => $modelDetail->kode_dokumen, // pastikan field ini ada
						'catatan'      => $revisiText,
					];
				}
			}

			// panggil API sekali saja (IMPORTANT)
			$response_revisi = $model->callAPIPostRevisiDokumen($dokumenPayload);
			// var_dump($response_revisi);
			if (isset($response_dok['Status']) && $response_dok['Status'] == '01') {
				Yii::$app->session->setFlash(
					'error',
					"Error while Calling API : " . $response_dok['Message']
				);
			}
		}
		
		
        $model->call_status_daluwarsa = Yii::$app->request->post('call_status_daluwarsa');
        $model->call_keterangan_daluwarsa = Yii::$app->request->post('call_keterangan_daluwarsa');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
		
		$model->keputusan_claim = Yii::$app->request->post('keputusan_claim');
		$model->catatan_hasil_claim = Yii::$app->request->post('catatan_hasil_claim');
        $model->alasan_penolakan_hasil_claim = Yii::$app->request->post('alasan_penolakan_hasil_claim');
		
		$model->status_banding = Yii::$app->request->post('status_banding');
		$model->alasan_banding = Yii::$app->request->post('alasan_banding');
       // Call API post-id-loan	
		
		$model->tanggal_pembayaran_claim = Yii::$app->request->post('tanggal_pembayaran_claim');
		$model->nilai_claim_disetujui = Yii::$app->request->post('nilai_claim_disetujui');
		
		
		$model->nilai_komponen_claim_disetujui = Yii::$app->request->post('nilai_komponen_claim_disetujui');
		$model->komponen_1 = Yii::$app->request->post('komponen_1');
        $model->nilai_1 = Yii::$app->request->post('nilai_1');
		$model->keterangan_1 = Yii::$app->request->post('keterangan_1');
		
		$model->komponen_2 = Yii::$app->request->post('komponen_2');
        $model->nilai_2 = Yii::$app->request->post('nilai_2');
		$model->keterangan_2 = Yii::$app->request->post('keterangan_2');
		$model->catatan_komponen = Yii::$app->request->post('catatan_komponen');
		
		$model->file_upload = UploadedFile::getInstanceByName('bukti_bayar');
		
		$model->nomor_reff_pembayaran = Yii::$app->request->post('nomor_reff_pembayaran');
		$model->bukti_bayar = Yii::$app->request->post('bukti_bayar');
		
		if ($model->file_upload) {

				$uploadPath = Yii::getAlias('@webroot/uploads/bukti-pembayaran/');

				if (!is_dir($uploadPath)) {
					mkdir($uploadPath, 0777, true);
				}

				$fileName = $model->id_loan . '_' . $model->file_upload->baseName . '.' . $model->file_upload->extension;

				$fullPath = $uploadPath . $fileName;

				if ($model->file_upload->saveAs($fullPath)) {

					$model->bukti_bayar = $fileName;

				} else
				{

					die('Gagal save file');
				}
		}
		
	    $daluwarsa = Yii::$app->request->post('call_status_daluwarsa');
		if($daluwarsa != null )
		{
			$response = $model->callAPIPostDaluwarsa();
		     // var_dump($response);
				if ($response['Status'] == '01') {
				Yii::$app->session->setFlash(
						'error',
						"Error while Calling API : " . $response['Message']
					);

					return $this->redirect([
							'view',
							'id' => $id,
						]);
				}
		};
		
		$resultanalis = Yii::$app->request->post('catatan_hasil_claim');
		if ($resultanalis != null)
		{
			$responseanalist = $model->callAPIPostAnalisResult();
			// var_dump($responseanalist);
			if ($responseanalist['Status'] == '01') {
			Yii::$app->session->setFlash(
					'error',
					"Error while Calling API : " . $responseanalist['Message']
				);

				return $this->redirect([
							'view',
							'id' => $id,
						]);
				}
		};
		
		$resultbanding = Yii::$app->request->post('status_banding');
		if ($resultbanding != null)
		{
			$responebanding = $model->callAPIPostBandingResult();
			// var_dump($responebanding);
			if ($responebanding['Status'] == '01') {
			Yii::$app->session->setFlash(
					'error',
					"Error while Calling API : " . $responebanding['Message']
				);

				return $this->redirect([
								'view',
								'id' => $id,
							]);
			}
		};
		
		$nilai_claim_disetujui = Yii::$app->request->post('nilai_komponen_claim_disetujui');
		if ($nilai_claim_disetujui != null)
		{
			$responenilaiclaim = $model->callAPIPostNilaiClaimDisetujui();
			var_dump($responenilaiclaim);
			if ($responenilaiclaim['Status'] == '01') {
			Yii::$app->session->setFlash(
					'error',
					"Error while Calling API : " . $responenilaiclaim['Message']
				);

				return $this->redirect([
								'view',
								'id' => $id,
							]);
			}
		};
		
		
		$pembayaran_claim = Yii::$app->request->post('tanggal_pembayaran_claim');
		if ($pembayaran_claim != null)
		{
			$responepembayaran = $model->callAPIPostPaymentClaim();
			// var_dump($responebanding);
			if ($responepembayaran['Status'] == '01') {
			Yii::$app->session->setFlash(
					'error',
					"Error while Calling API : " . $responepembayaran['Message']
				);

				return $this->redirect([
								'view',
								'id' => $id,
							]);
			}
		};
		
		if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => Yii::$app->request->post('id')]);
        }

		
        Yii::$app->session->setFlash('success', "Successfully");
        return $this->redirect(['index']);
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
		 $member = member::findOne(['id_loan' => $model->id_loan]);
		 	$polis = $member->policy_no ;
		
        $model->status = MemberClaim::STATUS_APPROVED;

        // Call API post-status
        // $response = $model->callAPIPostStatus('Description By AJRI',$polis);
		$response = $model->callAPIPostStatusClaim();
		var_dump($response);
        if ($response['Status'] == '01') {
            Yii::$app->session->setFlash('error', "Error while Calling API, " . $response['Keterangan']);
            return $this->redirect(['view', 'id' => $id]);
        }

        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully issued");
        return $this->redirect(['index']);
    }

    public function actionAnalisa($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);
		
		 $member = member::findOne(['id_loan' => $model->id_loan]);
		 	$polis = $member->policy_no ;
        $model->status = MemberClaim::STATUS_PENDING;

        // Call API post-status
        // $response = $model->callAPIPostStatus('Description By AJRI',$polis);
		$response = $model->callAPIPostStatusClaim();
        if ($response['Status'] == '01') {
            Yii::$app->session->setFlash('error', "Error while Calling API, " . $response['Keterangan']);
            return $this->redirect(['view', 'id' => $id]);
        }

        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => $id]);
        }

        Yii::$app->session->setFlash('success', "Successfully issued");
        return $this->redirect(['index']);
    }

    public function actionDitolak($id)
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel($id);
		
		 $member = member::findOne(['id_loan' => $model->id_loan]);
		 	$polis = $member->policy_no ;
        $model->status = MemberClaim::STATUS_REJECT;

        // Call API post-status
        // $response = $model->callAPIPostStatus('Description By AJRI',$polis);
		$response = $model->callAPIPostStatusClaim();
        if ($response['Status'] == '01') {
            Yii::$app->session->setFlash('error', "Error while Calling API, " . $response['Keterangan']);
            return $this->redirect(['view', 'id' => $id]);
        }

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
        if (($model = MemberClaim::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateClaim()
    {
        if (
            Yii::$app->user->isGuest
            || !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
        ) {
            return $this->goHome();
        }

        $model = $this->findModel(Yii::$app->request->post('id'));
		$member = member::findOne(['id_loan' => $model->id_loan]);
		 	$polis = $member->policy_no ;
		
		
        $model->file_upload = UploadedFile::getInstanceByName('bukti_bayar');
        $model->nilai_claim_disetujui = Yii::$app->request->post('nilai_claim_disetujui');
        $model->Keterangan_Reliance = Yii::$app->request->post('Keterangan_Reliance');
		
        // Call API post-payment
        $response = $model->callAPIPostPayment($polis);
		if ($response['Status'] == '01') {
			Yii::$app->session->setFlash('error', "Error while Calling API, " . $response['Keterangan']);
            return $this->redirect(['view', 'id' => Yii::$app->request->post('id')]);
        }

        if ($model->file_upload != null) {
            if (!$model->upload(false)) {
                Yii::$app->session->setFlash('error', "Error while uploading member");
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }

        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => Yii::$app->request->post('id')]);
        }

        Yii::$app->session->setFlash('success', "Successfully issued");
        return $this->redirect(['index']);
    }
	
	public function actionUpdateDaluwarsa()
	{
		if (
			Yii::$app->user->isGuest
			|| !User::findIdentityByAccessToken(Yii::$app->user->identity->access_token)
		) {
			return $this->goHome();
		}

		$model = $this->findModel(Yii::$app->request->post('id'));
		if ($model == null) {
			Yii::$app->session->setFlash('error', "Member not found");
			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}
		
		$model->call_status_daluwarsa = Yii::$app->request->post('call_status_daluwarsa');
		$model->call_keterangan_daluwarsa = Yii::$app->request->post('call_keterangan_daluwarsa');
		$model->updated_at = $currentDateTime;
		$model->updated_by = Yii::$app->user->identity->id;
		
		// Call API post-id-loan	
		$response = $model->callAPIPostDaluwarsa();
		var_dump($response);
		if ($response['Status'] == '01') {
		Yii::$app->session->setFlash(
				'error',
				"Error while Calling API : " . $response['Message']
			);

			return $this->redirect([
				'view',
				'id' => Yii::$app->request->post('batch_id'),
			]);
		}
		
		
		if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect(['view', 'id' => Yii::$app->request->post('id')]);
        }

		
        Yii::$app->session->setFlash('success', "Successfully Confirmation Daluwarsa");
        return $this->redirect(['index']);
	}
	
	
	public function actionApprovedoc($id_loan)
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		try {

			$request = Yii::$app->request;

			// =========================================================
			// 1. HANYA POST
			// =========================================================
			if (!$request->isPost) {
				return [
					'Result' => [
						'status' => '405',
						'kode_response' => '05',
						'message' => 'Method harus POST',
					],
				];
			}

			// =========================================================
			// 2. AMBIL POST
			// =========================================================
			$action = trim((string) $request->post('action', ''));
			$keterangan = trim((string) $request->post('keterangan', ''));
			$status_bayar = trim((string) $request->post('status_bayar', ''));

			Yii::info(
				'APPROVEDOC POST: ' . json_encode([
					'id_loan' => $id_loan,
					'action' => $action,
					'keterangan' => $keterangan,
					'status_bayar' => $status_bayar,
				]),
				'restitusi'
			);

			// =========================================================
			// 3. VALIDASI ACTION
			// =========================================================
			$allowedAction = [
				'1',
				'2',
				'3',
				'4',
				'5',
				'6',
				'7',
				'8'
			];

			if (!in_array($action, $allowedAction, true)) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '02',
						'message' => 'Action tidak valid',
					],
				];
			}

			// =========================================================
			// 4. VALIDASI STATUS BAYAR
			// =========================================================
			if (!in_array($status_bayar, ['1', '2'], true)) {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '02',
						'message' => 'Status bayar harus 1 atau 2',
					],
				];
			}

			// =========================================================
			// 5. VALIDASI KETERANGAN
			// =========================================================
			if ($keterangan === '') {
				return [
					'Result' => [
						'status' => '400',
						'kode_response' => '02',
						'message' => 'Keterangan wajib diisi',
					],
				];
			}

			// =========================================================
			// 6. CARI DOKUMEN CLAIM TERAKHIR
			// =========================================================
			$document = map_member_dokumen_medis::find()
				->where([
					'id_loan' => $id_loan,
					'jenis_dokumen' => 'claim',
				])
				->orderBy([
					'id' => SORT_DESC
				])
				->one();

			if (!$document) {
				return [
					'Result' => [
						'status' => '404',
						'kode_response' => '04',
						'message' => 'Dokumen claim tidak ditemukan',
					],
				];
			}

			// =========================================================
			// 7. UPDATE DOKUMEN
			// =========================================================
			$document->approve = $action;
			$document->keterangan = $keterangan;

			if (!$document->save(false)) {

				return [
					'Result' => [
						'status' => '500',
						'kode_response' => '07',
						'message' => 'Gagal menyimpan dokumen claim',
					],
					'debug' => [
						'errors' => $document->errors,
					],
				];
			}

			// =========================================================
			// 8. CARI MEMBER
			// =========================================================
			$model = Member::find()
				->where([
					'nomor_akad' => $id_loan,
				])
				->one();

			if (!$model) {
				return [
					'Result' => [
						'status' => '404',
						'kode_response' => '04',
						'message' => 'Member dengan nomor akad ' . $id_loan . ' tidak ditemukan',
					],
				];
			}

			// =========================================================
			// 9. CARI CLAIM
			// =========================================================
			$klaim = MemberClaim::find()
				->where([
					'no_akad' => $id_loan,
				])
				->orderBy([
					'id' => SORT_DESC
				])
				->one();

			if (!$klaim) {
				return [
					'Result' => [
						'status' => '404',
						'kode_response' => '04',
						'message' => 'Data claim tidak ditemukan',
					],
				];
			}

			// =========================================================
			// 10. UPDATE CLAIM
			// =========================================================
			$klaim->status_bayar = $status_bayar;
			$klaim->status_claim = $action;

			if (!$klaim->save(false)) {

				return [
					'Result' => [
						'status' => '500',
						'kode_response' => '07',
						'message' => 'Gagal menyimpan status claim',
					],
					'debug' => [
						'errors' => $klaim->errors,
					],
				];
			}

			// =========================================================
			// 11. LOGIN KE BANK RIAU
			// =========================================================
			$loginResponse = $klaim->callAPIPostMemberLoginRiau();

			Yii::info(
				'LOGIN BANK RESPONSE: ' .
				json_encode($loginResponse),
				'restitusi'
			);

			if (!is_array($loginResponse)) {

				return [
					'Result' => [
						'status' => '500',
						'kode_response' => '07',
						'message' => 'Response login Bank tidak valid',
					],
					'debug' => [
						'login_response' => $loginResponse,
					],
				];
			}

			// =========================================================
			// 12. AMBIL TOKEN
			// =========================================================
			$token = '';

			if (isset($loginResponse['token'])) {
				$token = trim((string) $loginResponse['token']);
			}

			if ($token === '' && isset($loginResponse['response']['token'])) {
				$token = trim((string) $loginResponse['response']['token']);
			}

			if ($token === '') {

				return [
					'Result' => [
						'status' => '500',
						'kode_response' => '07',
						'message' => 'Token login Bank tidak ditemukan',
					],
					'debug' => [
						'login_response' => $loginResponse,
					],
				];
			}

			// =========================================================
			// 13. CALLBACK KE BANK
			// =========================================================
			$apiResponse = $klaim->callAPIPostDebitur(
				$token,
				$model,
				$document,
				$klaim
			);
			
			return [
    'api_response' => $apiResponse,
];

			// =========================================================
			// 14. TAMPILKAN RESPONSE CALLBACK JIKA GAGAL
			// =========================================================
			if (!is_array($apiResponse)) {

				return [
					'Result' => [
						'status' => '500',
						'kode_response' => '07',
						'message' => 'Response callback Bank bukan array',
					],
					'debug' => [
						'api_response' => $apiResponse,
					],
				];
			}

			if (
				!isset($apiResponse['success']) ||
				$apiResponse['success'] !== true
			) {

				return [
					'Result' => [
						'status' => '500',
						'kode_response' => '07',
						'message' => 'Gagal callback ke Bank',
					],

					// =================================================
					// INI RESPONSE ASLI BANK / CURL
					// =================================================
					'debug' => [
						'http_code' => isset($apiResponse['http_code'])
							? $apiResponse['http_code']
							: null,

						'curl_errno' => isset($apiResponse['curl_errno'])
							? $apiResponse['curl_errno']
							: null,

						'curl_error' => isset($apiResponse['curl_error'])
							? $apiResponse['curl_error']
							: null,

						'message' => isset($apiResponse['message'])
							? $apiResponse['message']
							: null,

						'body' => isset($apiResponse['body'])
							? $apiResponse['body']
							: null,

						'response' => isset($apiResponse['response'])
							? $apiResponse['response']
							: null,

						'json_error' => isset($apiResponse['json_error'])
							? $apiResponse['json_error']
							: null,
					],
				];
			}

			// =========================================================
			// 15. CALLBACK BERHASIL
			// =========================================================
			return [
				'Result' => [
					'status' => isset($apiResponse['status'])
						? $apiResponse['status']
						: '200',

					'kode_response' => isset($apiResponse['kode_response'])
						? $apiResponse['kode_response']
						: '00',

					'message' => isset($apiResponse['message'])
						? $apiResponse['message']
						: 'Callback ke Bank berhasil',
				],

				'debug' => [
					'http_code' => isset($apiResponse['http_code'])
						? $apiResponse['http_code']
						: null,

					'body' => isset($apiResponse['body'])
						? $apiResponse['body']
						: null,

					'response' => isset($apiResponse['response'])
						? $apiResponse['response']
						: null,
				],
			];

		} catch (\Throwable $e) {

			Yii::error(
				"ERROR actionApprovedoc\n" .
				"ID LOAN : " . $id_loan . "\n" .
				"MESSAGE : " . $e->getMessage() . "\n" .
				"FILE    : " . $e->getFile() . "\n" .
				"LINE    : " . $e->getLine() . "\n" .
				"TRACE   : " . $e->getTraceAsString(),
				'restitusi'
			);

			return [
				'Result' => [
					'status' => '500',
					'kode_response' => '07',
					'message' => 'ERROR actionApprovedoc: ' . $e->getMessage(),
				],

				'debug' => [
					'file' => $e->getFile(),
					'line' => $e->getLine(),
				],
			];
		}
	}
	
}
