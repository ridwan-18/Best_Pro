<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "batch".
 *
 * @property int $id
 * @property string $batch_no
 * @property string $policy_no
 * @property int $total_member
 * @property int $total_member_accepted
 * @property int $total_member_pending
 * @property float $total_up
 * @property float $total_gross_premium
 * @property float $total_discount_premium
 * @property float $total_extra_premium
 * @property float $total_saving_premium
 * @property float $total_nett_premium
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class ReportBilling extends \yii\db\ActiveRecord
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';
    const STATUS_CLOSED = 'CLOSED';

    const PAGE_SIZE = 100;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['batch_no', 'policy_no', 'total_member', 'total_member_accepted', 'total_member_pending', 'total_up', 'total_gross_premium', 'total_discount_premium', 'total_extra_premium', 'total_saving_premium', 'total_nett_premium', 'status'], 'required'],
            [['total_member', 'total_member_accepted', 'total_member_pending', 'created_by', 'updated_by'], 'integer'],
            [['total_up', 'total_gross_premium', 'total_discount_premium', 'total_extra_premium', 'total_saving_premium', 'total_nett_premium'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['batch_no', 'policy_no'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'policy_no' => 'Policy No',
            'batch_no' => 'Batch No',
            'member_no' => 'Member No',
            'personal_no' => 'Personal No',
            'age' => 'Age',
            'branch' => 'Branch',
            'branch_code' => 'Branch Code',
            'account_no' => 'Account No',
            'bank_branch' => 'Bank Branch',
            'term' => 'Term',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'sum_insured' => 'Sum Insured',
            'total_si' => 'Total Si',
            'total_premium' => 'Total Premium',
            'rate_premi' => 'Rate Premi',
            'rate_saving' => 'Rate Saving',
            'gross_premium' => 'Gross Premium',
            'basic_premium' => 'Basic Premium',
            'saving_premium' => 'Saving Premium',
            'percentage_discount' => 'Percentage Discount',
            'discount_premium' => 'Discount Premium',
            'nett_premium' => 'Nett Premium',
            'medical_code' => 'Medical Code',
            'status' => 'Status',
            'member_status' => 'Member Status',
            'reas_status' => 'Reas Status',
            'status_reason' => 'Status Reason',
            'stnc_date' => 'Stnc Date',
            'stnc_status' => 'Stnc Status',
            'stnc_reason' => 'Stnc Reason',
            'acc_status' => 'Acc Status',
            'percentage_extra_premium' => 'Percentage Extra Premium',
            'extra_premium' => 'Extra Premium',
            'em_type' => 'Em Type',
            'percentage_em' => 'Percentage Em',
            'rate_em' => 'Rate Em',
            'em_premium' => 'Em Premium',
            'em_notes' => 'Em Notes',
            'uw_notes' => 'Uw Notes',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.policy_no',
                self::tableName() . '.member_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.batch_no',
                '(SELECT ' . Partner::tableName() . '.name' .  ' FROM ' . Policy::tableName() . ' INNER JOIN ' . Partner::tableName() . ' ON ' . Policy::tableName() . '.partner_id = ' . Partner::tableName() . '.id WHERE ' . Policy::tableName() . '.policy_no = ' . self::tableName() . '.policy_no GROUP BY ' . Policy::tableName() . '.policy_no) AS partner',
            ])
            ->asArray();
			
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status', $params['status']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        // $query->groupBy(['policy_no', 'batch_no']);
        // $query->orderBy(['id' => $params['sort']]);

        return $query->all();
    }


    
	
	public static function getAllParticipantByFilter($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.policy_no',
                self::tableName() . '.member_no',
                self::tableName() . '.batch_no',
                Personal::tableName() . '.name',
				Personal::tableName() . '.birth_date',
				Personal::tableName() . '.gender',
				self::tableName() . '.status',
            ])
          
			 ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no')
			   ->asArray();
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}
       if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }
		
        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status', $params['status']]);
        }
		if (isset($params['member_no']) && $params['member_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.member_no', $params['member_no']]);
        }
		// if (isset($params['name']) && $params['name'] != null) {
            // $query->andFilterWhere(['=', self::tableName() . '.name', $params['name']]);
        // }
		if (isset($params['name']) && $params['name'] != null) {
            $query->andFilterWhere(['=', Personal::tableName() . '.name', $params['name']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }
		
         // $query->groupBy(['policy_no', 'batch_no']);
        // $query->orderBy(['id' => $params['sort']]);

        return $query->all();
		
    }
	
	public static function getAllProductionParticipant($paramsGetAllProduksi = [])
	{
		$table = self::tableName();
		$cabangTable = Cabang::tableName();
		$userTable = User::tableName();
		$dokument= map_member_dokumen_medis ::tableName();

		$query = self::find()
			->select([
				$table . '.id',
				$table . '.policy_no',
				$table . '.member_no',
				$table . '.batch_no',
				$table . '.status',
				$table . '.id_loan',
				$table . '.refund_premi',
				$table . '.no_ktp',
				$table . '.start_date',
				$table . '.end_date',
				$table . '.sum_insured',
				$table . '.nama',
				$table . '.tgl_lahir',
				$table . '.gross_premium',
				$table . '.medical_code',
				$table . '.extra_premium',
				$table . '.age',
				$table . '.term',
				$table . '.rate_premi',
				$table . '.nett_premium',
				$table . '.pekerjaan',
				$table . '.produk',

				// Data dari tabel cabang
				$cabangTable . '.kode_kantor AS kode_kantor',
				$cabangTable . '.nama_kantor AS nama_kantor',
				$dokument . '.approve',
				$dokument . '.keterangan',
			 ])
			->leftJoin(
				$cabangTable,
				$table . '.kode_cabang = ' . $cabangTable . '.kode_kantor'
			)
			->leftJoin(
				$dokument,
				$table . '.id_pengajuan = ' . $dokument . '.id_loan'
			)
			->asArray();

		/*
		 * Filter berdasarkan user login
		 */
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere([
					$table . '.created_by' => Yii::$app->user->identity->id
				]);
			}
		}

		/*
		 * Filter ID Member
		 */
		if (
			isset($paramsGetAllProduksi['member_id']) &&
			$paramsGetAllProduksi['member_id'] !== '' &&
			$paramsGetAllProduksi['member_id'] !== null
		) {
			$query->andWhere([
				$table . '.id' => $paramsGetAllProduksi['member_id']
			]);
		}

		/*
		 * Filter Policy
		 */
		if (
			isset($paramsGetAllProduksi['policy_no']) &&
			$paramsGetAllProduksi['policy_no'] !== ''
		) {
			$query->andWhere([
				$table . '.policy_no' => $paramsGetAllProduksi['policy_no']
			]);
		}

		/*
		 * Filter Batch
		 */
		if (
			isset($paramsGetAllProduksi['batch_no']) &&
			$paramsGetAllProduksi['batch_no'] !== ''
		) {
			$query->andWhere([
				$table . '.batch_no' => $paramsGetAllProduksi['batch_no']
			]);
		}

		/*
		 * Filter Status
		 */
		if (
			isset($paramsGetAllProduksi['status']) &&
			$paramsGetAllProduksi['status'] !== ''
		) {
			$query->andWhere([
				$table . '.status' => $paramsGetAllProduksi['status']
			]);
		}
		
		if (
			isset($paramsGetAllProduksi['nama']) &&
			$paramsGetAllProduksi['nama'] !== ''
		) {
			$query->andWhere([
				$table . '.nama' => $paramsGetAllProduksi['nama']
			]);
		}
		
		if (
			isset($paramsGetAllProduksi['approve']) &&
			$paramsGetAllProduksi['approve'] !== ''
		) {
			$query->andWhere([
				$dokument . '.approve' => $paramsGetAllProduksi['approve']
			]);
		}

		/*
		 * Filter ID Loan
		 */
		if (
			isset($paramsGetAllProduksi['id_loan']) &&
			$paramsGetAllProduksi['id_loan'] !== ''
		) {
			$query->andWhere([
				$table . '.id_loan' => $paramsGetAllProduksi['id_loan']
			]);
		}

		/*
		 * Filter Username
		 */
		if (
			isset($paramsGetAllProduksi['nama_kantor']) &&
			$paramsGetAllProduksi['nama_kantor'] !== ''
		) {
			// Catatan: filter ini memerlukan JOIN tabel user.
			$query->andWhere([
				$cabangTable . '.nama_kantor' => $paramsGetAllProduksi['nama_kantor']
			]);
		}

		/*
		 * Filter Tanggal
		 */
		if (
			!empty($paramsGetAllProduksi['start_date']) &&
			!empty($paramsGetAllProduksi['end_date'])
		) {
			$query->andWhere([
				'between',
				$table . '.updated_at',
				$paramsGetAllProduksi['start_date'],
				$paramsGetAllProduksi['end_date']
			]);
		}

		/*
		 * Pagination
		 */
		if (
			isset($paramsGetAllProduksi['offset']) &&
			$paramsGetAllProduksi['offset'] !== ''
		) {
			$query->offset($paramsGetAllProduksi['offset']);
		}

		if (
			isset($paramsGetAllProduksi['limit']) &&
			$paramsGetAllProduksi['limit'] !== ''
		) {
			$query->limit($paramsGetAllProduksi['limit']);
		}

		/*
		 * Sorting
		 */
		$query->orderBy([
			$table . '.id' => SORT_DESC
		]);

		return $query->all();
	}
	
		
	public static function countAllDataproduksi($paramsGetAllProduksi = [])
	{
		$query = self::find();
		
		

		if (isset($paramsGetAllProduksi['policy_no']) && $paramsGetAllProduksi['policy_no'] != null) {
			$query->andFilterWhere(['=', self::tableName() . '.policy_no', $paramsGetAllProduksi['policy_no']]);
		}

		if (isset($paramsGetAllProduksi['batch_no']) && $paramsGetAllProduksi['batch_no'] != null) {
			$query->andFilterWhere(['=', self::tableName() . '.batch_no', $paramsGetAllProduksi['batch_no']]);
		}

		$query->groupBy(['policy_no', 'batch_no']);

		return $query->count();
	}	
	
	public static function getSummaryProduction($params = [])
	{
		$table = self::tableName();
		$cabangTable = \app\models\Cabang::tableName();
		$dokumenTable = \app\models\map_member_dokumen_medis::tableName();

		$query = self::find()
			->alias('m')
			->select([
				'total_pengajuan' => new \yii\db\Expression(
					'COUNT(DISTINCT m.id)'
				),
				'Inforce' => new \yii\db\Expression(
					"SUM(CASE WHEN LOWER(m.status) = 'Inforce'
					THEN 1 ELSE 0 END)"
				),
				'pending' => new \yii\db\Expression(
					"SUM(CASE WHEN LOWER(m.status) = 'pending'
					THEN 1 ELSE 0 END)"
				),
				'cancel' => new \yii\db\Expression(
					"SUM(CASE WHEN LOWER(m.status) = 'cancel'
					THEN 1 ELSE 0 END)"
				),
				'total_gross_premium' => new \yii\db\Expression(
					'COALESCE(SUM(m.gross_premium), 0)'
				),
				'total_net_premium' => new \yii\db\Expression(
					'COALESCE(SUM(m.nett_premium), 0)'
				),
				'extra_premium' => new \yii\db\Expression(
					'COALESCE(SUM(m.extra_premium), 0)'
				),
			]);

		// Filter user login
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == \app\models\User::ROLE_UW) {
				$query->andWhere([
					'm.created_by' => Yii::$app->user->identity->id,
				]);
			}
		}

		// Filter nama
		if (!empty($params['nama'])) {
			$query->andWhere([
				'like',
				'm.nama',
				$params['nama'],
			]);
		}

		// Filter status kepesertaan
		if (!empty($params['status'])) {
			$query->andWhere([
				'm.status' => $params['status'],
			]);
		}

		// Filter batch number
		if (!empty($params['batch_no'])) {
			$query->andWhere([
				'm.batch_no' => $params['batch_no'],
			]);
		}

		// Filter cabang
		if (!empty($params['nama_kantor'])) {
			$query->innerJoin(
				['c' => $cabangTable],
				'm.kode_cabang = c.kode_kantor'
			);

			$query->andWhere([
				'like',
				'c.nama_kantor',
				$params['nama_kantor'],
			]);
		}

		// Filter status dokumen menggunakan EXISTS
		// supaya nilai premi tidak berlipat akibat JOIN dokumen
		if (!empty($params['approve'])) {
			$query->andWhere([
				'exists',
				(new \yii\db\Query())
					->select(new \yii\db\Expression('1'))
					->from(['d' => $dokumenTable])
					->where('d.id_loan = m.id_pengajuan')
					->andWhere([
						'd.approve' => $params['approve'],
					]),
			]);
		}

		// Filter tanggal berdasarkan updated_at
		if (!empty($params['start_date'])) {
			$startDate = date(
				'Y-m-d',
				strtotime($params['start_date'])
			);

			$query->andWhere([
				'>=',
				'm.updated_at',
				$startDate . ' 00:00:00',
			]);
		}

		if (!empty($params['end_date'])) {
			$endDate = date(
				'Y-m-d',
				strtotime($params['end_date'])
			);

			$query->andWhere([
				'<',
				'm.updated_at',
				date(
					'Y-m-d',
					strtotime($endDate . ' +1 day')
				) . ' 00:00:00',
			]);
		}

		return $query->asArray()->one();
	}
	
	public static function getMonthlySummary($params = [])
	{
		$cabangTable = \app\models\Cabang::tableName();
		$dokumenTable = \app\models\map_member_dokumen_medis::tableName();

		$tahun = !empty($params['tahun'])
			? (int) $params['tahun']
			: (int) date('Y');

		$query = self::find()
			->alias('m')
			->select([
				'bulan' => new \yii\db\Expression(
					'MONTH(m.start_date)'
				),

				'total_pengajuan' => new \yii\db\Expression(
					'COUNT(DISTINCT m.id)'
				),

				'Inforce' => new \yii\db\Expression(
					"COUNT(DISTINCT CASE
						WHEN LOWER(m.status) = 'inforce' THEN m.id
						ELSE NULL
					END)"
				),

				'pending' => new \yii\db\Expression(
					"COUNT(DISTINCT CASE
						WHEN LOWER(m.status) = 'pending' THEN m.id
						ELSE NULL
					END)"
				),

				'cancel' => new \yii\db\Expression(
					"COUNT(DISTINCT CASE
						WHEN LOWER(m.status) = 'cancel' THEN m.id
						ELSE NULL
					END)"
				),

				'total_gross_premium' => new \yii\db\Expression(
					'COALESCE(SUM(m.gross_premium), 0)'
				),

				'total_net_premium' => new \yii\db\Expression(
					'COALESCE(SUM(m.nett_premium), 0)'
				),

				'extra_premium' => new \yii\db\Expression(
					'COALESCE(SUM(m.extra_premium), 0)'
				),
			]);

		// Tahun yang dipilih
		$query->andWhere([
			'>=',
			'm.start_date',
			$tahun . '-01-01 00:00:00',
		]);

		$query->andWhere([
			'<',
			'm.start_date',
			($tahun + 1) . '-01-01 00:00:00',
		]);

		// Hak akses user
		if (!Yii::$app->user->isGuest) {
			if (
				Yii::$app->user->identity->role
				== \app\models\User::ROLE_UW
			) {
				$query->andWhere([
					'm.created_by' => Yii::$app->user->identity->id,
				]);
			}
		}

		// Filter nama peserta
		if (!empty($params['nama'])) {
			$query->andWhere([
				'like',
				'm.nama',
				$params['nama'],
			]);
		}

		// Filter status
		if (!empty($params['status'])) {
			$query->andWhere([
				'm.status' => $params['status'],
			]);
		}

		// Filter batch number
		if (!empty($params['batch_no'])) {
			$query->andWhere([
				'm.batch_no' => $params['batch_no'],
			]);
		}

		// Filter cabang
		if (!empty($params['nama_kantor'])) {
			$query->innerJoin(
				['c' => $cabangTable],
				'm.kode_cabang = c.kode_kantor'
			);

			$query->andWhere([
				'like',
				'c.nama_kantor',
				$params['nama_kantor'],
			]);
		}

		// Filter persetujuan dokumen
		if (!empty($params['approve'])) {
			$query->andWhere([
				'exists',
				(new \yii\db\Query())
					->select(new \yii\db\Expression('1'))
					->from(['d' => $dokumenTable])
					->where('d.id_loan = m.id_pengajuan')
					->andWhere([
						'd.approve' => $params['approve'],
					]),
			]);
		}

		// Filter tanggal awal
		if (!empty($params['start_date'])) {
			$startDate = date(
				'Y-m-d',
				strtotime($params['start_date'])
			);

			$query->andWhere([
				'>=',
				'm.start_date',
				$startDate . ' 00:00:00',
			]);
		}

		// Filter tanggal akhir
		if (!empty($params['end_date'])) {
			$endDate = date(
				'Y-m-d',
				strtotime($params['end_date'])
			);

			$query->andWhere([
				'<',
				'm.start_date',
				date(
					'Y-m-d',
					strtotime($endDate . ' +1 day')
				) . ' 00:00:00',
			]);
		}

		return $query
			->groupBy(new \yii\db\Expression('MONTH(m.start_date)'))
			->orderBy(new \yii\db\Expression('MONTH(m.start_date) ASC'))
			->asArray()
			->all();
	}


	
	

}


