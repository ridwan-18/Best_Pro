<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alteration_endorsement".
 *
 * @property int $id
 * @property string $alteration_no
 * @property string $alteration_date
 * @property string $policy_no
 * @property string $description
 * @property float $total_si
 * @property float $new_total_si
 * @property float $total_premium
 * @property float $new_total_premium
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class AlterationEndorsement extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alteration_endorsement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alteration_no', 'alteration_date', 'policy_no', 'description', 'total_si', 'new_total_si', 'total_premium', 'new_total_premium', 'status'], 'required'],
            [['alteration_date', 'created_at', 'updated_at'], 'safe'],
            [['total_si', 'new_total_si', 'total_premium', 'new_total_premium'], 'number'],
            [['created_by', 'updated_by'], 'integer'],
            [['alteration_no'], 'string', 'max' => 100],
            [['policy_no'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'total_si' => 'Total Si',
            'new_total_si' => 'New Total Si',
            'total_premium' => 'Total Premium',
            'new_total_premium' => 'New Total Premium',
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
        return $params['id'] . '/CNE/AJRI/' . date("Y");
    }
}
