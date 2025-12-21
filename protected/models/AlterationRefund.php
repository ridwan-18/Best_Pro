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

    public static function generateAlterationNo($params)
    {
        return $params['id'] . '/CNR/AJRI/' . date("Y");
    }
}
