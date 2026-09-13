<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\Partner;

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
class Batch extends \yii\db\ActiveRecord
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_PENDING = 'PENDING';
    const STATUS_CLOSED = 'CLOSED';
	
	const ROLE_SUPER_ADMIN = 1; const ROLE_CABANG = 2; const ROLE_PUSAT = 6;

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'batch';
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
            'batch_no' => 'Batch No',
            'policy_no' => 'Policy No',
            'total_member' => 'Total Member',
            'total_member_accepted' => 'Total Member Accepted',
            'total_member_pending' => 'Total Member Pending',
            'total_up' => 'Total Up',
            'total_gross_premium' => 'Total Gross Premium',
            'total_discount_premium' => 'Total Discount Premium',
            'total_extra_premium' => 'Total Extra Premium',
            'total_saving_premium' => 'Total Saving Premium',
            'total_nett_premium' => 'Total Nett Premium',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

   
	
	public static function getAll($params = [])
	{
		$identity = Yii::$app->user->identity;

		$query = self::find()
			->select([
				self::tableName() . '.id',
				self::tableName() . '.policy_no',
				self::tableName() . '.batch_no',
				self::tableName() . '.total_member',
				self::tableName() . '.status',
				self::tableName() . '.created_at',
				self::tableName() . '.created_by',
				self::tableName() . '.files',

				// Nama Partner
				'(' .
					'SELECT ' . Partner::tableName() . '.name
					 FROM ' . Policy::tableName() . '
					 INNER JOIN ' . Partner::tableName() . '
						ON ' . Policy::tableName() . '.partner_id = ' .
						   Partner::tableName() . '.id
					 WHERE ' . Policy::tableName() . '.policy_no = ' .
						   self::tableName() . '.policy_no
					 LIMIT 1
				) AS partner',
			])
			->asArray();


		/*
		 * ==========================================================
		 * FILTER BERDASARKAN ROLE
		 * ==========================================================
		 */

		// ==========================================================
		// SUPER ADMIN
		// role = 1
		// ==========================================================
		if ($identity->role == User::ROLE_SUPERADMIN) {

			// Super Admin melihat semua data
		}


		// ==========================================================
		// PUSAT
		// role = 6
		// ==========================================================
		elseif ($identity->role == User::ROLE_PUSAT) {

			/*
			 * Pusat hanya melihat data berdasarkan partner_id
			 * milik user.
			 */

			$query->andWhere([
				self::tableName() . '.partner_id' => $identity->partner_id
			]);
		}


		// ==========================================================
		// CABANG
		// role = 2
		// ==========================================================
		elseif ($identity->role == User::ROLE_UW) {

			/*
			 * Cabang hanya melihat data berdasarkan partner_id
			 * milik user.
			 */

			$query->andWhere([
				self::tableName() . '.partner_id' => $identity->partner_id
			]);
		}


		// ==========================================================
		// UNDERWRITING
		// ==========================================================
		elseif ($identity->role == User::ROLE_UW) {

			$query->andWhere([
				self::tableName() . '.created_by' => $identity->id
			]);
		}


		/*
		 * ==========================================================
		 * FILTER PARAMETER
		 * ==========================================================
		 */

		if (isset($params['policy_no']) && $params['policy_no'] != null) {

			$query->andFilterWhere([
				'=',
				self::tableName() . '.policy_no',
				$params['policy_no']
			]);
		}

		if (isset($params['batch_no']) && $params['batch_no'] != null) {

			$query->andFilterWhere([
				'=',
				self::tableName() . '.batch_no',
				$params['batch_no']
			]);
		}

		if (isset($params['status']) && $params['status'] != null) {

			$query->andFilterWhere([
				'=',
				self::tableName() . '.status',
				$params['status']
			]);
		}


		/*
		 * ==========================================================
		 * PAGINATION
		 * ==========================================================
		 */

		if (isset($params['offset']) && $params['offset'] != null) {
			$query->offset($params['offset']);
		}

		if (isset($params['limit']) && $params['limit'] != null) {
			$query->limit($params['limit']);
		}


		/*
		 * ==========================================================
		 * GROUP
		 * ==========================================================
		 */

		$query->groupBy([
			self::tableName() . '.policy_no',
			self::tableName() . '.batch_no'
		]);


		/*
		 * ==========================================================
		 * SORT
		 * ==========================================================
		 */

		$sort = isset($params['sort']) && $params['sort'] != null
			? $params['sort']
			: SORT_DESC;

		$query->orderBy([
			self::tableName() . '.id' => $sort
		]);


		return $query->all();
	}


    public static function countAll($params = [])
    {
        $query = self::find();
		
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

    public static function generateBatchNo($policyNo)
    {
        $batch = Batch::find()
            ->where(['policy_no' => $policyNo])
            ->orderBy(['id' => SORT_DESC])
            ->one();
        return str_pad(($batch != null) ? (int)$batch->batch_no + 1 : 1, 6, '0', STR_PAD_LEFT);
    }
}
