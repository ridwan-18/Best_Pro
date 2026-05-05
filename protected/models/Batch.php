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
		
		$createdBy = Yii::$app->user->identity->id;
		$user = user::findOne(['id' => $createdBy]);
		// var_dump($user);
        $query = self::find()
            ->select([
                self::tableName() . '.id',
                self::tableName() . '.policy_no',
                self::tableName() . '.batch_no',
                self::tableName() . '.total_member',
                self::tableName() . '.status',
                self::tableName() . '.created_at',
				self::tableName() . '.created_by',
                '(SELECT ' . Partner::tableName() . '.name' .  ' FROM ' . Policy::tableName() . 
				' INNER JOIN ' . Partner::tableName() . ' ON ' . Policy::tableName() . '.partner_id = ' . Partner::tableName() . '.id'.
				' INNER JOIN ' . User::tableName() . ' ON ' . Partner::tableName() . '.id = ' . User::tableName() . '.partner_id where '
				. Policy::tableName() . '.policy_no = ' . self::tableName() . '.policy_no GROUP BY ' 
				. Policy::tableName() . '.policy_no) AS partner',
				])
            ->asArray();
			
			
		if (!Yii::$app->user->isGuest) {
			if (Yii::$app->user->identity->role == User::ROLE_UW) {
				$query->andWhere(['=', self::tableName() . '.created_by', Yii::$app->user->identity->id]);
			}
		}
		
		
		// if (!yii::$app->user->isguest) {
			// if (yii::$app->user->identity->role == user::ROLE_PUSAT) {
				// $query->andwhere(['=', user::tablename() . '.partner_id', $user->partner_id]);
			// }
		// }
		
		// $policies = Policy::find()
    // ->asArray()
    // ->select([
        // Policy::tableName() . '.policy_no',
        // Partner::tableName() . '.name AS partner'
    // ])
    // ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' .  Policy::tableName() . '.partner_id')
	// ->innerJoin(User::tableName(), Partner::tableName() . '.id = ' .  User::tableName() . '.partner_id')
	// ->where([
						// User::tableName() . '.partner_id' => $user->partner_id
					// ])
    // ->orderBy([Policy::tableName() . '.id' => SORT_ASC])
    // ->all();
		
		
		
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

        $query->groupBy(['policy_no', 'batch_no']);
        $query->orderBy(['id' => $params['sort']]);

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
