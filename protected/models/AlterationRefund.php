<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alteration_refund".
 *
 * @property int $id
 * @property string $alteration_no
 * @property string $alteration_date
 * @property string $policy_no
 * @property float $total_si
 * @property float $total_premium
 * @property float $total_premium_refund
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class AlterationRefund extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alteration_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alteration_no', 'alteration_date', 'policy_no', 'total_si', 'total_premium', 'total_premium_refund', 'status'], 'required'],
            [['alteration_date', 'created_at', 'updated_at'], 'safe'],
            [['total_si', 'total_premium', 'total_premium_refund'], 'number'],
            [['created_by', 'updated_by'], 'integer'],
            [['alteration_no'], 'string', 'max' => 100],
            [['policy_no'], 'string', 'max' => 50],
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
            'alteration_no' => 'Alteration No',
            'alteration_date' => 'Alteration Date',
            'policy_no' => 'Policy No',
            'total_si' => 'Total Si',
            'total_premium' => 'Total Premium',
            'total_premium_refund' => 'Total Premium Refund',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
			'reg_no' => 'reg no',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->asArray();

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        $query->orderBy(['id' => $params['sort']]);

        return $query->all();
    }

    public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        return $query->count();
    }

    // public static function generateAlterationNo($params)
    // {
        // return $params['id'] . '/AJRI-CN-R/' . date("Y");
    // }
	
	public static function generateAlterationNo($params)
    {
        $prefix = substr($params['policy_no'], 0, 7) . substr($params['policy_no'], -3, 3);
        return $prefix . '/' . str_pad($params['id'], 3, '0', STR_PAD_LEFT) . '/AJRI-CN-R/' . Utils::getRomanNumeral($params['month']) . '/' . date("Y");
    }
	
	public static function generateRegNo($params)
    {
        return str_pad($params['id'], 6, '0', STR_PAD_LEFT) . '/UW-M/AJRI/' . Utils::getRomanNumeral($params['month']) . '/' . date("Y");
    }
	
	 public static function getTerm($startDate, $endDate)
    {
        $date1 = date_create($startDate);
        $date2 = date_create($endDate);
        $diff = date_diff($date1, $date2);

        $term = ($diff->y * 12) + $diff->m;
         if ($diff->d > 0) {
            $term = $term + 1;
        }

        return $term;
    }
}
