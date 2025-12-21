<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_reins".
 *
 * @property int $id
 * @property int $quotation_id
 * @property int $global_reas_id
 * @property float $si_from
 * @property float $si_to
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class QuotationReins extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_reins';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'global_reas_id', 'si_from', 'si_to'], 'required'],
            [['quotation_id', 'global_reas_id', 'created_by', 'updated_by'], 'integer'],
            [['si_from', 'si_to'], 'number'],
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
            'global_reas_id' => 'Global Reas ID',
            'si_from' => 'Si From',
            'si_to' => 'Si To',
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
                self::tableName() . '.global_reas_id',
                self::tableName() . '.si_from',
                self::tableName() . '.si_to',
                GlobalReas::tableName() . '.pks_no',
                GlobalReas::tableName() . '.reas_type',
                GlobalReas::tableName() . '.reas_method',
                GlobalReas::tableName() . '.ceding_share',
                GlobalReas::tableName() . '.reas_share',
                Reassuradur::tableName() . '.name AS reassuradur',
            ])
            ->asArray()
            ->innerJoin(GlobalReas::tableName(), GlobalReas::tableName() . '.id = ' . self::tableName() . '.global_reas_id')
            ->innerJoin(Reassuradur::tableName(), Reassuradur::tableName() . '.id = ' . GlobalReas::tableName() . '.reassuradur_id');

        if (isset($params['quotation_id']) && $params['quotation_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.quotation_id', $params['quotation_id']]);
        }

        return $query->all();
    }
}
