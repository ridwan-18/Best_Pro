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
				self::tableName() . '.id_loan',
				self::tableName() . '.refund_premi',
				self::tableName() . '.no_ktp',
				self::tableName() . '.start_date',
				self::tableName() . '.end_date',
				self::tableName() . '.sum_insured',
				USER::tableName() . '.username',
				self::tableName() . '.gross_premium',
				
            ])
          
			 ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' . self::tableName() . '.personal_no')
			 ->innerJoin(USER::tableName(), USER::tableName() . '.id = ' . self::tableName() . '.created_by')
			   ->asArray();
		
			if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}
		
       if (isset($paramsGetAllProduksi['status']) && $paramsGetAllProduksi['status'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.status', $paramsGetAllProduksi['status']]);
        }
		
        if (isset($paramsGetAllProduksi['batch_no']) && $paramsGetAllProduksi['batch_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.batch_no', $paramsGetAllProduksi['batch_no']]);
        }
		if (isset($paramsGetAllProduksi['id_loan']) && $paramsGetAllProduksi['id_loan'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.id_loan', $paramsGetAllProduksi['id_loan']]);
        }
		
		if (isset($paramsGetAllProduksi['username']) && $paramsGetAllProduksi['username'] != null) {
            $query->andFilterWhere(['=', USER::tableName() . '.username', $paramsGetAllProduksi['username']]);
        }
		
		if (
            isset($paramsGetAllProduksi['start_date'])
            && $paramsGetAllProduksi['start_date'] != null
            && isset($paramsGetAllProduksi['end_date'])
            && $paramsGetAllProduksi['end_date'] != null
        ) {
            $query->andFilterWhere(['>=', self::tableName() . '.updated_at', $paramsGetAllProduksi['start_date']]);
            $query->andFilterWhere(['<=', self::tableName() . '.updated_at', $paramsGetAllProduksi['end_date']]);
        }

        if (isset($paramsGetAllProduksi['offset']) && $paramsGetAllProduksi['offset'] != null) {
            $query->offset($paramsGetAllProduksi['offset']);
        }

        if (isset($paramsGetAllProduksi['limit']) && $paramsGetAllProduksi['limit'] != null) {
            $query->limit($paramsGetAllProduksi['limit']);
        }
		
         // $query->groupBy(['policy_no', 'batch_no']);
        // $query->orderBy(['id' => $params['sort']]);
		// echo $query;
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


