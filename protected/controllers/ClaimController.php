<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\Claim;
use app\models\Policy;
use app\models\Member;
use app\models\Personal;
use app\models\Partner;
use app\models\QuotationProduct;
use app\models\Product;
use app\models\Component;
use app\models\ClaimDocument;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\dokument_claim_jatim;
use app\models\claim_bank_jatim;
use app\models\claim_bank_jatim_detail;
use yii\web\UploadedFile;


/**
 * ClaimController implements the CRUD actions for Claim model.
 */
class ClaimController extends Controller
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
     * Lists all Claim models.
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
            'claim_no' => Yii::$app->request->get('claim_no'),
            'policy_no' => Yii::$app->request->get('policy_no'),
            'member_no' => Yii::$app->request->get('member_no'),
        ];

        $totalModel = Claim::countAll($params);

        $pagination = new Pagination([
            'totalCount' => $totalModel,
            'pageSize' => Claim::PAGE_SIZE,
            'pageSizeParam' => false,
        ]);

        $params = array_merge($params, [
            'offset' => $pagination->offset,
            'limit' => $pagination->limit,
            'sort' => SORT_DESC,
        ]);

        $models = Claim::getAll($params);

        return $this->render('index', [
            'models' => $models,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Displays a single Claim model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        $member = Member::findOne(['member_no' => $model->member_no]);
        $personal = Personal::findOne(['personal_no' => $member->personal_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);
        $quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
        $product = Product::findOne(['id' => $quotationProduct->product_id]);
        $component = Component::findOne(['product_id' => $product->id]);
		$dokumen_detail = dokument_claim_jatim::getAll();
		

        return $this->render('view', [
            'model' => $model,
            'policy' => $policy,
            'member' => $member,
            'personal' => $personal,
            'partner' => $partner,
            'product' => $product,
            'component' => $component,
			'dokumen_detail' => $dokumen_detail,
        ]);
    }

    /**
     * Creates a new Claim model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
	public function actionCreate()
	{

		if (
			Yii::$app->user->isGuest ||
			!Yii::$app->user->identity ||
			!User::findIdentityByAccessToken(
				Yii::$app->user->identity->access_token
			)
		) {
			return $this->goHome();
		}

		$request = Yii::$app->request;
		$dokumenDetail = dokument_claim_jatim::getAll();

		if (!is_array($dokumenDetail)) {
			$dokumenDetail = [];
		}

		$memberNo = $request->post('member_no');

		if (!$memberNo) {
			return $this->render('create', [
				'dokumen_detail' => $dokumenDetail,
				'selectedMemberNo' => null,
			]);
		}

		$member = Member::findOne([
			'member_no' => $memberNo,
		]);

		if (!$member) {
			Yii::$app->session->setFlash(
				'error',
				'Member tidak ditemukan.'
			);

			return $this->redirect(['create']);
		}

		$policy = Policy::findOne([
			'policy_no' => $member->policy_no,
		]);

		if (!$policy) {
			Yii::$app->session->setFlash(
				'error',
				'Policy tidak ditemukan.'
			);

			return $this->redirect(['create']);
		}

		$personal = Personal::findOne([
			'personal_no' => $member->personal_no,
		]);

		if (!$personal) {
			Yii::$app->session->setFlash(
				'error',
				'Personal tidak ditemukan.'
			);

			return $this->redirect(['create']);
		}

		$quotationProduct = QuotationProduct::findOne([
			'quotation_id' => $policy->quotation_id,
		]);

		$product = null;

		if ($quotationProduct) {
			$product = Product::findOne([
				'id' => $quotationProduct->product_id,
			]);
		}

		$partner = Partner::findOne([
			'id' => $policy->partner_id,
		]);

		if (!$request->post('submit_claim')) {
			return $this->render('create', [
				'dokumen_detail' => $dokumenDetail,
				'selectedMemberNo' => $memberNo,
				'policy' => $policy,
				'get_member' => $member,
				'personal' => $personal,
				'partner' => $partner,
				'product' => $product,
			]);
		}

		$incidentDate = $request->post('incident_date');

		if (!$incidentDate) {
			Yii::$app->session->setFlash(
				'error',
				'Incident Date wajib diisi.'
			);

			return $this->redirect(['create']);
		}

		$lastClaim = Claim::find()
			->orderBy([
				'id' => SORT_DESC,
			])
			->one();

		$newestId = $lastClaim
			? $lastClaim->id + 1
			: 1;

		$claim = new Claim();

		$claim->claim_no = Claim::generateClaimNo([
			'id' => $newestId,
		]);

		$claim->policy_no = $member->policy_no;
		$claim->member_no = $member->member_no;

		$claim->incident_date = $incidentDate;

		$claim->claim_age = Claim::getClaimAge(
			$personal->birth_date,
			$incidentDate
		);

		$claim->estimated_amount = $request->post(
			'estimated_amount'
		);

		$claim->claim_reason = $request->post(
			'claim_reason'
		);

		$claim->disease = $request->post(
			'disease'
		);

		$claim->place_of_death = $request->post(
			'place_of_death'
		);

		$claim->doc_status = $request->post(
			'doc_status'
		);

		$claim->doc_notes = $request->post(
			'doc_notes'
		);

		$claim->status = Claim::STATUS_REGISTRATION;
		$claim->created_at = date('Y-m-d H:i:s');
		$claim->created_by = Yii::$app->user->id;

		if (!$claim->save()) {
			Yii::$app->session->setFlash(
				'error',
				json_encode($claim->errors)
			);

			return $this->redirect(['create']);
		}

		$files = UploadedFile::getInstancesByName('documents');
		$docIds = $request->post('doc_ids', []);

		$apiFiles = [
			'formulir_pengajuan_klaim' => null,
			'surat_keterangan_meninggal_kelurahan' => null,
			'surat_keterangan_meninggal_rs' => null,
			'copy_ktp' => null,
			'copy_ktp_ahli_waris' => null,
			'resume_medis' => null,
			'daftar_angsuran' => null,
			'copy_akad_pembiayaan' => null,
			'surat_kuasa' => null,
			'surat_keterangan_ahli_waris' => null,
			'surat_dari_pemegang_polis' => null,
			'dokumen_lain' => null,
		];

		$uploadPath = Yii::getAlias(
			'@webroot/images/post_images/'
		);

		if (!is_dir($uploadPath)) {
			mkdir($uploadPath, 0777, true);
		}

		if (!empty($files)) {
			foreach ($files as $index => $file) {

				if (
					!$file ||
					$file->error !== UPLOAD_ERR_OK
				) {
					continue;
				}

				$docId = $docIds[$index] ?? null;

				$fileName = sprintf(
					'%s_%s_%s.%s',
					time(),
					$claim->id,
					$file->baseName,
					$file->extension
				);

				$filePath = $uploadPath . $fileName;

				if (!$file->saveAs($filePath)) {
					Yii::$app->session->setFlash(
						'error',
						'Gagal upload file: ' . $file->name
					);

					return $this->redirect(['create']);
				}


				$detail = new claim_bank_jatim_detail();

				$detail->id_loan = $claim->id;
				$detail->kode_dokumen = $docId;
				$detail->files = 'images/post_images/' . $fileName;
				$detail->tgl_upload = date('Y-m-d H:i:s');

				if (!$detail->save()) {
					Yii::error([
						'claim_id' => $claim->id,
						'doc_id' => $docId,
						'errors' => $detail->errors,
					]);

					Yii::$app->session->setFlash(
						'error',
						'Gagal menyimpan detail dokumen.'
					);

					return $this->redirect(['create']);
				}

				$document = null;

				foreach ($dokumenDetail as $item) {
					if (
						isset($item['id']) &&
						(string) $item['id'] === (string) $docId
					) {
						$document = $item;
						break;
					}
				}

				if (!$document) {
					continue;
				}

				$documentName = strtolower(
					trim($document['nama_dokument'])
				);

				$documentMapping = [
					'formulir pengajuan klaim'
						=> 'formulir_pengajuan_klaim',

					'surat keterangan meninggal kelurahan'
						=> 'surat_keterangan_meninggal_kelurahan',

					'surat keterangan meninggal rs'
						=> 'surat_keterangan_meninggal_rs',

					'copy ktp ahli waris'
						=> 'copy_ktp_ahli_waris',

					'copy ktp'
						=> 'copy_ktp',

					'resume medis'
						=> 'resume_medis',

					'daftar angsuran'
						=> 'daftar_angsuran',

					'copy akad pembiayaan'
						=> 'copy_akad_pembiayaan',

					'surat keterangan ahli waris'
						=> 'surat_keterangan_ahli_waris',

					'surat dari pemegang polis'
						=> 'surat_dari_pemegang_polis',

					'surat kuasa'
						=> 'surat_kuasa',

					'dokumen lain'
						=> 'dokumen_lain',
				];

				foreach ($documentMapping as $keyword => $apiField) {
					if (strpos($documentName, $keyword) !== false) {
						$apiFiles[$apiField] = $filePath;
						break;
					}
				}
			}
		}
		
		
		
		echo '<pre>';

foreach ($apiFiles as $key => $file) {
    echo $key . ' => ';

    if ($file) {
        echo $file . ' | ';

        echo file_exists($file)
            ? 'EXISTS'
            : 'NOT FOUND';
    } else {
        echo 'NULL';
    }

    echo PHP_EOL;
}

echo '</pre>';
die;

		$loginResponse = $claim->callAPIPostMemberLogin();

		if (
			!is_array($loginResponse) ||
			empty($loginResponse['token'])
		) {
			Yii::error([
				'claim_id' => $claim->id,
				'response' => $loginResponse,
			]);

			Yii::$app->session->setFlash(
				'error',
				'Gagal mendapatkan token API.'
			);

			return $this->redirect([
				'index',
				'id' => $claim->id,
			]);
		}

		$token = $loginResponse['token'];

		$data = [
			'kepesertaan_id' => $member->member_no,
			'tanggal_meninggal' => $claim->incident_date,
			'nilai_klaim' => $claim->estimated_amount,
			'jenis_klaim' => $claim->claim_reason,
			'tempat_dan_sebab' => $claim->place_of_death,
			'sebab' => $claim->disease,
			'id' => $claim->id,
		];


		$peserta = [
			'no_peserta' => $member->member_no,
		];


		$apiResponse = $claim->callAPIPostMemberClaimPush(
			$token,
			$policy->policy_no,
			$data,
			$peserta,
			$apiFiles['formulir_pengajuan_klaim'],
			$apiFiles['surat_keterangan_meninggal_kelurahan'],
			$apiFiles['surat_keterangan_meninggal_rs'],
			$apiFiles['copy_ktp'],
			$apiFiles['copy_ktp_ahli_waris'],
			$apiFiles['resume_medis'],
			$apiFiles['daftar_angsuran'],
			$apiFiles['copy_akad_pembiayaan'],
			$apiFiles['surat_kuasa'],
			$apiFiles['surat_keterangan_ahli_waris'],
			$apiFiles['surat_dari_pemegang_polis'],
			$apiFiles['dokumen_lain']
		);

		if (!is_array($apiResponse)) {
			Yii::error([
				'claim_id' => $claim->id,
				'response' => $apiResponse,
			]);

			Yii::$app->session->setFlash(
				'error',
				'Response API Claim tidak valid.'
			);

			return $this->redirect([
				'index',
				'id' => $claim->id,
			]);
		}


		if (
			isset($apiResponse['validate']) &&
			!empty($apiResponse['validate'])
		) {
			Yii::error([
				'claim_id' => $claim->id,
				'api_response' => $apiResponse,
			]);

			Yii::$app->session->setFlash(
				'error',
				'API Claim gagal: ' .
				json_encode($apiResponse['validate'])
			);

			return $this->redirect([
				'index',
				'id' => $claim->id,
			]);
		}

		Yii::$app->session->setFlash(
			'success',
			'Claim berhasil disimpan dan berhasil dikirim ke API.'
		);

		return $this->redirect([
			'index',
			'id' => $claim->id,
		]);
	}


    /**
     * Updates an existing Claim model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $policy = Policy::findOne(['policy_no' => $model->policy_no]);
        if ($policy == null) {
            Yii::$app->session->setFlash('error', "Policy not found");
            return $this->redirect(['create']);
        }

        $member = Member::findOne(['member_no' => $model->member_no]);
        if ($member == null) {
            Yii::$app->session->setFlash('error', "Member not found");
            return $this->redirect(['create']);
        }
		// var_dump($member);
		
        $personal = Personal::findOne(['personal_no' => $member->personal_no]);
        if ($personal == null) {
            Yii::$app->session->setFlash('error', "Personal not found");
            return $this->redirect(['create']);
        }
		
        $dateTime = new \DateTime();
        $currentDateTime = $dateTime->format('Y-m-d H:i:s');

        // Claim Info
        $model->claim_age = Claim::getClaimAge($personal->birth_date, Yii::$app->request->post('incident_date'));
        $model->incident_date = Yii::$app->request->post('incident_date');
        $model->estimated_amount = Yii::$app->request->post('estimated_amount');
        $model->claim_reason = Yii::$app->request->post('claim_reason');
        $model->disease = Yii::$app->request->post('disease');
        $model->place_of_death = Yii::$app->request->post('place_of_death');

        // Claim Document
        $model->doc_status = Yii::$app->request->post('doc_status');
        $model->doc_pre_received_date = Yii::$app->request->post('doc_pre_received_date');
        $model->doc_received_date = Yii::$app->request->post('doc_received_date');
        $model->doc_complete_date = Yii::$app->request->post('doc_complete_date');
        $model->doc_notes = Yii::$app->request->post('doc_notes');

        // Payment Plan
        $model->payment_due_date = Yii::$app->request->post('payment_due_date');
        $model->claim_amount = Yii::$app->request->post('claim_amount');
        $model->cash_value = Yii::$app->request->post('cash_value');
        $model->transfer_type = Yii::$app->request->post('transfer_type');
        $model->bank_name = Yii::$app->request->post('bank_name');
        $model->account_no = Yii::$app->request->post('account_no');
        $model->account_name = Yii::$app->request->post('account_name');

        // Analyst I
        $model->analyst1_diagnosed_by = Yii::$app->request->post('analyst1_diagnosed_by');
        $model->analyst1_diagnose_notes = Yii::$app->request->post('analyst1_diagnose_notes');
        $model->analyst1_historical_disease = Yii::$app->request->post('analyst1_historical_disease');
        $model->analyst1_information = Yii::$app->request->post('analyst1_information');
        $model->analyst1_investigation_by_phone = Yii::$app->request->post('analyst1_investigation_by_phone');
        $model->analyst1_medical_analysis = Yii::$app->request->post('analyst1_medical_analysis');
        $model->analyst1_result1 = Yii::$app->request->post('analyst1_result1');
        $model->analyst1_recommendation1 = Yii::$app->request->post('analyst1_recommendation1');
        $model->analyst1_result2 = Yii::$app->request->post('analyst1_result2');
        $model->analyst1_recommendation2 = Yii::$app->request->post('analyst1_recommendation2');

        // Approval Investigation
        $model->dept_approved_by = Yii::$app->request->post('dept_approved_by');
        $model->dept_approve_notes = Yii::$app->request->post('dept_approve_notes');
        $model->dept_approve_status = Yii::$app->request->post('dept_approve_status');
        $model->div_approved_by = Yii::$app->request->post('div_approved_by');
        $model->div_approve_notes = Yii::$app->request->post('div_approve_notes');
        $model->div_approve_status = Yii::$app->request->post('div_approve_status');
        $model->gm_approved_by = Yii::$app->request->post('gm_approved_by');
        $model->gm_approve_notes = Yii::$app->request->post('gm_approve_notes');
        $model->gm_approve_status = Yii::$app->request->post('gm_approve_status');
        $model->dir1_approved_by = Yii::$app->request->post('dir1_approved_by');
        $model->dir1_approve_notes = Yii::$app->request->post('dir1_approve_notes');
        $model->dir1_approve_status = Yii::$app->request->post('dir1_approve_status');
        $model->dir2_approved_by = Yii::$app->request->post('dir2_approved_by');
        $model->dir2_approve_notes = Yii::$app->request->post('dir2_approve_notes');
        $model->dir2_approve_status = Yii::$app->request->post('dir2_approve_status');

        // Approval Process
        $model->dept_process_approved_by = Yii::$app->request->post('dept_process_approved_by');
        $model->dept_process_approve_notes = Yii::$app->request->post('dept_process_approve_notes');
        $model->dept_process_approve_status = Yii::$app->request->post('dept_process_approve_status');
        $model->div_process_approved_by = Yii::$app->request->post('div_process_approved_by');
        $model->div_process_approve_notes = Yii::$app->request->post('div_process_approve_notes');
        $model->div_process_approve_status = Yii::$app->request->post('div_process_approve_status');
        $model->gm_process_approved_by = Yii::$app->request->post('gm_process_approved_by');
        $model->gm_process_approve_notes = Yii::$app->request->post('gm_process_approve_notes');
        $model->gm_process_approve_status = Yii::$app->request->post('gm_process_approve_status');
        $model->dir1_process_approved_by = Yii::$app->request->post('dir1_process_approved_by');
        $model->dir1_process_approve_notes = Yii::$app->request->post('dir1_process_approve_notes');
        $model->dir1_process_approve_status = Yii::$app->request->post('dir1_process_approve_status');
        $model->dir2_process_approved_by = Yii::$app->request->post('dir2_process_approved_by');
        $model->dir2_process_approve_notes = Yii::$app->request->post('dir2_process_approve_notes');
        $model->dir2_process_approve_status = Yii::$app->request->post('dir2_process_approve_status');

        $model->approved_amount = Yii::$app->request->post('claim_amount');
        $model->status = Yii::$app->request->post('status');
        $model->decision = Yii::$app->request->post('decision');
        $model->remarks = Yii::$app->request->post('remarks');
        $model->updated_at = $currentDateTime;
        $model->updated_by = Yii::$app->user->identity->id;
        if (!$model->save(false)) {
            Yii::$app->session->setFlash('error', "Error while saving");
            return $this->redirect([
                'view',
                'id' => $model->id
            ]);
        }

        // Claim Document
        $isCheckeds = Yii::$app->request->post('is_checkeds');
        $isMandatories = Yii::$app->request->post('is_mandatories');
        $claimDocuments = ClaimDocument::find()
            ->asArray()
            ->where(['claim_id' => $model->id])
            ->all();
        foreach ($claimDocuments as $claimDocument) {
            $document = ClaimDocument::findOne(['id' => $claimDocument['id']]);
            $document->is_checked = (in_array($claimDocument['id'], $isCheckeds)) ? 1 : null;
            $document->is_mandatory = (in_array($claimDocument['id'], $isMandatories)) ? 1 : null;
            $document->save(false);
        }

        Yii::$app->session->setFlash('success', "Successfully saved");
        return $this->redirect([
            'view',
            'id' => $model->id
        ]);
    }

    /**
     * Deletes an existing Claim model.
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
     * Finds the Claim model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Claim the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Claim::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
}
