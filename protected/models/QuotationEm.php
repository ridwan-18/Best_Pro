<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_em".
 *
 * @property int $id
 * @property int $quotation_id
 * @property int $age
 * @property float $percentage
 * @property int $term
 * @property float $em
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class QuotationEm extends \yii\db\ActiveRecord
{
    const PAGE_SIZE = 100;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_em';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'age', 'percentage', 'term', 'em'], 'required'],
            [['quotation_id', 'age', 'term', 'created_by', 'updated_by'], 'integer'],
            [['percentage', 'em'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
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
            'age' => 'Age',
            'percentage' => 'Percentage',
            'term' => 'Term',
            'em' => 'Em',
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

        return $query->all();
    }

    public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['quotation_id']) && $params['quotation_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.quotation_id', $params['quotation_id']]);
        }

        return $query->count();
    }
}
