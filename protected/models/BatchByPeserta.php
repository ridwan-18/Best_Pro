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
class BatchByPeserta extends \yii\db\ActiveRecord
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';
    const STATUS_CLOSED = 'CLOSED';

    const PAGE_SIZE = 10;

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

    public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['batch_no']) && $params['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $params['batch_no']]);
        }

        $query->groupBy(['policy_no', 'batch_no']);

        return $query->count();
    }

    public static function statuses($selected = null)
    {
        $data = [
            self::STATUS_OPEN => self::STATUS_OPEN,
            self::STATUS_PENDING => self::STATUS_PENDING,
            self::STATUS_CLOSED => self::STATUS_CLOSED,
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
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
    $memberTable = self::tableName();

    $query = self::find()
        ->alias('m')
        ->select([
            'm.id',
            'm.policy_no',
            'm.batch_no',
            'm.member_no',
            'm.term',
            'm.age',
            'm.start_date',
            'm.end_date',
            'm.sum_insured',
            'm.total_si',
            'm.total_premium',
            'm.rate_premi',
            'm.rate_saving',
            'm.gross_premium',
            'm.basic_premium',
            'm.saving_premium',
            'm.percentage_discount',
            'm.discount_premium',
            'm.nett_premium',
            'm.medical_code',
            'm.status',
            'm.member_status',
            'm.reas_status',
            'm.status_reason',
            'm.stnc_date',
            'm.stnc_status',
            'm.stnc_reason',
            'm.acc_status',
            'm.percentage_extra_premium',
            'm.extra_premium',
            'm.em_type',
            'm.percentage_em',
            'm.rate_em',
            'm.em_premium',
            'm.em_notes',
            'm.uw_notes',
            'm.id_loan',

            '(SELECT ' . Personal::tableName() . '.name
                FROM ' . Personal::tableName() . '
                WHERE ' . Personal::tableName() . '.personal_no = m.personal_no
                GROUP BY ' . Personal::tableName() . '.personal_no) AS name',

            '(SELECT ' . Personal::tableName() . '.birth_date
                FROM ' . Personal::tableName() . '
                WHERE ' . Personal::tableName() . '.personal_no = m.personal_no
                GROUP BY ' . Personal::tableName() . '.personal_no) AS birth_date',

            '(SELECT ' . Personal::tableName() . '.gender
                FROM ' . Personal::tableName() . '
                WHERE ' . Personal::tableName() . '.personal_no = m.personal_no
                GROUP BY ' . Personal::tableName() . '.personal_no) AS gender',
        ])
        ->asArray();


    /*
     * =====================================================
     * HAK AKSES USER
     * =====================================================
     */

    if (!Yii::$app->user->isGuest) {

        $user = Yii::$app->user->identity;


        // =================================================
        // ROLE 1 = SUPER ADMIN
        // Bisa melihat semua produksi
        // =================================================
        if ((int)$user->role === 1) {

            // Tidak perlu filter


        // =================================================
        // ROLE 6 = PUSAT
        // Bisa melihat semua produksi berdasarkan partner
        // =================================================
        } elseif ((int)$user->role === 6) {

            $query->innerJoin(
                ['u' => User::tableName()],
                'u.id = m.created_by'
            );

            $query->andWhere([
                'u.partner_id' => $user->partner_id
            ]);


        // =================================================
        // ROLE 2 = CABANG
        // Hanya melihat produksi miliknya sendiri
        // =================================================
        } elseif ((int)$user->role === 2) {

            $query->andWhere([
                'm.created_by' => $user->id
            ]);
        }
    }


    /*
     * =====================================================
     * FILTER
     * =====================================================
     */

    if (
        isset($paramsGetAllProduksi['member_id'])
        && $paramsGetAllProduksi['member_id'] != null
    ) {
        $query->andFilterWhere([
            '=',
            'm.id',
            $paramsGetAllProduksi['member_id']
        ]);
    }


    if (
        isset($paramsGetAllProduksi['policy_no'])
        && $paramsGetAllProduksi['policy_no'] != null
    ) {
        $query->andFilterWhere([
            '=',
            'm.policy_no',
            $paramsGetAllProduksi['policy_no']
        ]);
    }


    if (
        isset($paramsGetAllProduksi['batch_no'])
        && $paramsGetAllProduksi['batch_no'] != null
    ) {
        $query->andFilterWhere([
            '=',
            'm.batch_no',
            $paramsGetAllProduksi['batch_no']
        ]);
    }


    if (
        isset($paramsGetAllProduksi['start_date'])
        && $paramsGetAllProduksi['start_date'] != null
        && isset($paramsGetAllProduksi['end_date'])
        && $paramsGetAllProduksi['end_date'] != null
    ) {

        $query->andFilterWhere([
            '>=',
            'm.start_date',
            $paramsGetAllProduksi['start_date']
        ]);

        $query->andFilterWhere([
            '<=',
            'm.end_date',
            $paramsGetAllProduksi['end_date']
        ]);
    }


    if (
        isset($paramsGetAllProduksi['status'])
        && $paramsGetAllProduksi['status'] != null
    ) {
        $query->andFilterWhere([
            '=',
            'm.status',
            $paramsGetAllProduksi['status']
        ]);
    }


    if (
        isset($paramsGetAllProduksi['member_status'])
        && $paramsGetAllProduksi['member_status'] != null
    ) {
        $query->andFilterWhere([
            '=',
            'm.member_status',
            $paramsGetAllProduksi['member_status']
        ]);
    }


    if (
        isset($paramsGetAllProduksi['reas_status'])
        && $paramsGetAllProduksi['reas_status'] != null
    ) {
        $query->andFilterWhere([
            '=',
            'm.reas_status',
            $paramsGetAllProduksi['reas_status']
        ]);
    }


    if (
        isset($paramsGetAllProduksi['is_accumulated'])
        && $paramsGetAllProduksi['is_accumulated'] == 1
    ) {
        $query->andFilterWhere([
            'like',
            'm.acc_status',
            'Accumulated'
        ]);
    }


    /*
     * =====================================================
     * PAGINATION
     * =====================================================
     */

    if (
        isset($paramsGetAllProduksi['offset'])
        && $paramsGetAllProduksi['offset'] != null
    ) {
        $query->offset($paramsGetAllProduksi['offset']);
    }


    if (
        isset($paramsGetAllProduksi['limit'])
        && $paramsGetAllProduksi['limit'] != null
    ) {
        $query->limit($paramsGetAllProduksi['limit']);
    }


    /*
     * =====================================================
     * GROUP & ORDER
     * =====================================================
     */

    $query->groupBy([
        'm.id',
        'm.personal_no'
    ]);


    if (
        isset($paramsGetAllProduksi['sort'])
        && $paramsGetAllProduksi['sort'] != null
    ) {
        $query->orderBy([
            'm.id' => $paramsGetAllProduksi['sort']
        ]);
    } else {
        $query->orderBy([
            'm.id' => SORT_DESC
        ]);
    }


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

}


