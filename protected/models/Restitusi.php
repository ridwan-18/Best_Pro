<?php

namespace app\models;

use Yii;

class Restitusi extends \yii\db\ActiveRecord
{
	const PAGE_SIZE = 10;
    public static function tableName()
    {
        return 'restitusi';
    }

    public function rules()
    {
        return [
            [
                [
                    'id_transaksi',
                    'id_pengajuan',
                    'kode_broker',
                    'kode_cabang',
                    'nomor_rekening',
                    'old_nomor_akad',
                    'tanggal_pembiayaan',
                    'plafon_pembiayaan',
                    'tenor',
                    'benefit',
                ],
                'required'
            ],

            [
                [
                    'id_transaksi',
                    'plafon_pembiayaan',
                    'tenor'
                ],
                'integer'
            ],

            [
                [
                    'id_pengajuan',
                    'kode_broker',
                    'kode_cabang',
                    'nomor_rekening',
                    'old_nomor_akad',
                    'benefit'
                ],
                'string'
            ],

            [
                'tanggal_pembiayaan',
                'safe'
            ],

            [
                'restitusi_jiwa',
                'string'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_transaksi' => 'ID Transaksi',
            'id_pengajuan' => 'ID Pengajuan',
            'kode_broker' => 'Kode Broker',
            'kode_cabang' => 'Kode Cabang',
            'nomor_rekening' => 'Nomor Rekening',
            'old_nomor_akad' => 'Old Nomor Akad',
            'tanggal_pembiayaan' => 'Tanggal Pembiayaan',
            'plafon_pembiayaan' => 'Plafon Pembiayaan',
            'tenor' => 'Tenor',
            'benefit' => 'Benefit',
            'restitusi_jiwa' => 'Restitusi Jiwa',
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
		$restitusiTable = self::tableName();
		$memberTable    = Member::tableName();
		$userTable      = User::tableName();

		$query = self::find()
			->alias('r')
			->select([
		
				'r.id',
				'r.nomor_akad',
				'r.status_restitusi',
				'member.id AS member_id',
				'member.member_no',
				'member.no_ktp',
				'member.nama',
				'member.tgl_lahir',
				'member.age',
				'member.term',
				'member.policy_no',
				'member.batch_no',
				'member.status',
				'member.id_loan',
				'member.refund_premi',
				'member.start_date',
				'member.end_date',
				'member.sum_insured',
				'member.gross_premium',
			])

			// Join Member
			->leftJoin(
				$memberTable . ' member',
				'member.nomor_akad = r.nomor_akad'
			)

			->asArray();

		/*
		 * FILTER BERDASARKAN USER LOGIN
		 */
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere([
					'p.created_by' => Yii::$app->user->identity->id
				]);
			}
		}

		/*
		 * FILTER ID MEMBER
		 */
		if (
			isset($paramsGetAllProduksi['member_id']) &&
			$paramsGetAllProduksi['member_id'] !== '' &&
			$paramsGetAllProduksi['member_id'] !== null
		) {
			$query->andWhere([
				'member.id' => $paramsGetAllProduksi['member_id']
			]);
		}

		/*
		 * FILTER POLICY
		 */
		if (
			isset($paramsGetAllProduksi['policy_no']) &&
			$paramsGetAllProduksi['policy_no'] !== ''
		) {
			$query->andWhere([
				'member.policy_no' => $paramsGetAllProduksi['policy_no']
			]);
		}

		/*
		 * FILTER BATCH
		 */
		if (
			isset($paramsGetAllProduksi['batch_no']) &&
			$paramsGetAllProduksi['batch_no'] !== ''
		) {
			$query->andWhere([
				'member.batch_no' => $paramsGetAllProduksi['batch_no']
			]);
		}

		/*
		 * FILTER STATUS
		 */
		if (
			isset($paramsGetAllProduksi['status']) &&
			$paramsGetAllProduksi['status'] !== ''
		) {
			$query->andWhere([
				'member.status' => $paramsGetAllProduksi['status']
			]);
		}

		/*
		 * FILTER ID LOAN
		 */
		if (
			isset($paramsGetAllProduksi['id_loan']) &&
			$paramsGetAllProduksi['id_loan'] !== ''
		) {
			$query->andWhere([
				'member.id_loan' => $paramsGetAllProduksi['id_loan']
			]);
		}

		/*
		 * FILTER USERNAME
		 */
		if (
			isset($paramsGetAllProduksi['username']) &&
			$paramsGetAllProduksi['username'] !== ''
		) {
			$query
				->innerJoin(
					$userTable . ' u',
					'u.id = p.created_by'
				)
				->andWhere([
					'u.username' => $paramsGetAllProduksi['username']
				]);
		}

		/*
		 * FILTER TANGGAL
		 */
		if (
			!empty($paramsGetAllProduksi['start_date']) &&
			!empty($paramsGetAllProduksi['end_date'])
		) {
			$query->andWhere([
				'between',
				'member.updated_at',
				$paramsGetAllProduksi['start_date'],
				$paramsGetAllProduksi['end_date']
			]);
		}

		/*
		 * PAGINATION
		 */
		if (
			isset($paramsGetAllProduksi['offset']) &&
			$paramsGetAllProduksi['offset'] !== ''
		) {
			$query->offset(
				(int)$paramsGetAllProduksi['offset']
			);
		}

		if (
			isset($paramsGetAllProduksi['limit']) &&
			$paramsGetAllProduksi['limit'] !== ''
		) {
			$query->limit(
				(int)$paramsGetAllProduksi['limit']
			);
		}

		/*
		 * SORTING
		 */
		$query->orderBy([
			'r.id' => SORT_DESC
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

		// $query->groupBy(['policy_no', 'batch_no']);

		return $query->count();
	}	

	public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['member_id']) && $params['member_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.id', $params['member_id']]);
        }

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        if (
            isset($params['start_date'])
            && $params['start_date'] != null
            && isset($params['end_date'])
            && $params['end_date'] != null
        ) {
            $query->andFilterWhere(['>=', self::tableName() . '.start_date', $params['start_date']]);
            $query->andFilterWhere(['<=', self::tableName() . '.end_date', $params['end_date']]);
        }

        if (isset($params['status']) && $params['status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status', $params['status']]);
        }

        if (isset($params['member_status']) && $params['member_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.member_status', $params['member_status']]);
        }

        if (isset($params['reas_status']) && $params['reas_status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.reas_status', $params['reas_status']]);
        }

        $query->groupBy([self::tableName() . '.id', self::tableName() . '.personal_no']);

        return $query->count();
    }

    public static function getAccumulation($params = [])
    {
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.policy_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.member_no',
                Personal::tableName() . '.name',
                Personal::tableName() . '.birth_date',
                self::tableName() . '.age',
                self::tableName() . '.start_date',
                self::tableName() . '.end_date',
                self::tableName() . '.term',
                self::tableName() . '.sum_insured',
                self::tableName() . '.gross_premium',
                self::tableName() . '.nett_premium',
                self::tableName() . '.em_premium',
            ])
            ->asArray()
            ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no');

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['name']) && $params['name'] != null) {
            $query->andFilterWhere(['=', Personal::tableName() . '.name', $params['name']]);
        }

        if (isset($params['birth_date']) && $params['birth_date'] != null) {
            $query->andFilterWhere(['=', Personal::tableName() . '.birth_date', $params['birth_date']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        $query->groupBy([self::tableName() . '.id', self::tableName() . '.personal_no']);
        $query->orderBy([self::tableName() . '.id' => $params['sort']]);

        return $query->all();
    }

    public static function statuses($selected = null)
    {
        $data = [
            self::STATUS_INFORCE => self::STATUS_INFORCE,
            self::STATUS_LAPSED => self::STATUS_LAPSED,
            self::STATUS_CLAIM => self::STATUS_CLAIM,
            self::STATUS_SURRENDER => self::STATUS_SURRENDER,
            self::STATUS_MATURITY => self::STATUS_MATURITY,
            self::STATUS_CHANGE => self::STATUS_CHANGE,
            self::STATUS_CANCEL => self::STATUS_CANCEL,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function memberStatuses($selected = null)
    {
        $data = [
            self::MEMBER_STATUS_INFORCE => self::MEMBER_STATUS_INFORCE,
            self::MEMBER_STATUS_PENDING => self::MEMBER_STATUS_PENDING,
            self::MEMBER_STATUS_DECLINED => self::MEMBER_STATUS_DECLINED,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function reasStatuses($selected = null)
    {
        $data = [
            self::REAS_STATUS_TREATY => self::REAS_STATUS_TREATY,
            self::REAS_STATUS_OUT => self::REAS_STATUS_OUT,
            self::REAS_STATUS_FACULTATIVE => self::REAS_STATUS_FACULTATIVE,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public static function totalShows($selected = null)
    {
        $data = [
            20 => 20,
            50 => 50,
            100 => 100,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }
}