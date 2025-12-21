<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_uw_limit".
 *
 * @property int $id
 * @property int $quotation_id
 * @property float $min_si
 * @property float $max_si
 * @property int $min_age
 * @property int $max_age
 * @property string $medical_code
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class QuotationUwLimit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_uw_limit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'min_si', 'max_si', 'min_age', 'max_age', 'medical_code'], 'required'],
            [['quotation_id', 'min_age', 'max_age', 'created_by', 'updated_by'], 'integer'],
            [['min_si', 'max_si'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['medical_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quotation_id' => 'Quotation ID',
            'min_si' => 'Min Si',
            'max_si' => 'Max Si',
            'min_age' => 'Min Age',
            'max_age' => 'Max Age',
            'medical_code' => 'Medical Code',
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

        if (isset($params['quotation_id']) && $params['quotation_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.quotation_id', $params['quotation_id']]);
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
}
