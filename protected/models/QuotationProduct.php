<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_product".
 *
 * @property int $id
 * @property int $quotation_id
 * @property int $product_id
 * @property string $premium_type
 * @property string $rate_type
 * @property string $period_type
 * @property string $si_type
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class QuotationProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'product_id', 'premium_type', 'rate_type', 'period_type', 'si_type'], 'required'],
            [['quotation_id', 'product_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['premium_type', 'rate_type', 'period_type', 'si_type'], 'string', 'max' => 50],
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
            'premium_type' => 'Premium Type',
            'rate_type' => 'Rate Type',
            'period_type' => 'Period Type',
            'si_type' => 'Si Type',
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
                self::tableName() . '.id',
                self::tableName() . '.product_id',
                self::tableName() . '.premium_type',
                self::tableName() . '.rate_type',
                self::tableName() . '.period_type',
                self::tableName() . '.si_type',
                self::tableName() . '.created_at',
                self::tableName() . '.created_by',
                Product::tableName() . '.name AS product',
                User::tableName() . '.username AS created_by',
            ])
            ->asArray()
            ->innerJoin(Product::tableName(), Product::tableName() . '.id = ' . self::tableName() . '.product_id')
            ->innerJoin(User::tableName(), User::tableName() . '.id = ' . self::tableName() . '.created_by');

        if (isset($params['quotation_id']) && $params['quotation_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.quotation_id', $params['quotation_id']]);
        }

        return $query->all();
    }
}
