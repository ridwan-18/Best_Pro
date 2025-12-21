<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_rate".
 *
 * @property int $id
 * @property int $quotation_id
 * @property int $product_id
 * @property string|null $type
 * @property int|null $age
 * @property int|null $term
 * @property int|null $unit
 * @property float|null $rate
 * @property float|null $interest
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class QuotationRate extends \yii\db\ActiveRecord
{
    const PAGE_SIZE = 100;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'product_id'], 'required'],
            [['quotation_id', 'product_id', 'age', 'term', 'unit', 'created_by', 'updated_by'], 'integer'],
            [['rate', 'interest'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 255],
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
            'product_id' => 'Product ID',
            'type' => 'Type',
            'age' => 'Age',
            'term' => 'Term',
            'unit' => 'Unit',
            'rate' => 'Rate',
            'interest' => 'Interest',
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

        if (isset($params['product_id']) && $params['product_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.product_id', $params['product_id']]);
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

        if (isset($params['quotation_id']) && $params['quotation_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.quotation_id', $params['quotation_id']]);
        }

        if (isset($params['product_id']) && $params['product_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.product_id', $params['product_id']]);
        }

        return $query->count();
    }
}
