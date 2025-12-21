<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "global_reas".
 *
 * @property int $id
 * @property string $reassuradur_id
 * @property string $pks_no
 * @property string $effective_date
 * @property string $expired_date
 * @property int|null $is_unlimited
 * @property string $reas_type
 * @property string $reas_method
 * @property float $ceding_share
 * @property float $reas_share
 * @property string $rate_period
 * @property string $rate_type
 * @property string $prorate_type
 * @property string $cover_note
 * @property int $retroactive
 * @property int $claim_expired
 * @property float $commission
 * @property string|null $remarks
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class GlobalReas extends \yii\db\ActiveRecord
{
    const REAS_TYPE_TREATY = 'Treaty';
    const REAS_TYPE_FACULTATIVE = 'Facultative';

    const REAS_METHOD_QUOTA_SHARE = 'Quota Share';
    const REAS_METHOD_SURPLUS = 'Surplus';
    const REAS_METHOD_COMBINATION = 'Combination';
    const REAS_METHOD_OR = 'OR';

    const RATE_PERIOD_SINGLE = 'Single';
    const RATE_PERIOD_ANNUALLY = 'Annually';

    const RATE_TYPE_SINGLE = 'Single';
    const RATE_TYPE_AGE = 'Age';
    const RATE_TYPE_TERM = 'Term';
    const RATE_TYPE_AGE_TERM = 'Age+Term';

    const PRORATE_TYPE_PRORATE_DAY = 'Prorate Day';
    const PRORATE_TYPE_ROUND_DOWN = 'Round Down';
    const PRORATE_TYPE_NONE = 'None';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'global_reas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reassuradur_id', 'effective_date', 'expired_date', 'reas_type', 'reas_method', 'ceding_share', 'reas_share', 'rate_period', 'rate_type', 'prorate_type', 'cover_note', 'retroactive', 'claim_expired', 'commission'], 'required'],
            [['effective_date', 'expired_date', 'created_at', 'updated_at'], 'safe'],
            [['is_unlimited', 'retroactive', 'claim_expired', 'created_by', 'updated_by'], 'integer'],
            [['ceding_share', 'reas_share', 'commission'], 'number'],
            [['reassuradur_id', 'pks_no', 'cover_note', 'remarks'], 'string', 'max' => 255],
            [['reas_type', 'reas_method', 'rate_period', 'rate_type', 'prorate_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reassuradur_id' => 'Reassuradur',
            'pks_no' => 'Pks No',
            'effective_date' => 'Effective Date',
            'expired_date' => 'Expired Date',
            'is_unlimited' => 'Is Unlimited',
            'reas_type' => 'Reas Type',
            'reas_method' => 'Reas Method',
            'ceding_share' => 'Ceding Share %',
            'reas_share' => 'Reas Share %',
            'rate_period' => 'Rate Period',
            'rate_type' => 'Rate Type',
            'prorate_type' => 'Prorate Type',
            'cover_note' => 'Cover Note',
            'retroactive' => 'Retroactive Day(s)',
            'claim_expired' => 'Claim Expired Day(s)',
            'commission' => 'Commission %',
            'remarks' => 'Remarks',
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
                self::tableName() . '.pks_no',
                self::tableName() . '.cover_note',
                self::tableName() . '.reas_type',
                self::tableName() . '.reas_method',
                self::tableName() . '.rate_period',
                self::tableName() . '.rate_type',
                self::tableName() . '.effective_date',
                Reassuradur::tableName() . '.name AS reassuradur',
            ])
            ->asArray()
            ->innerJoin(Reassuradur::tableName(), Reassuradur::tableName() . '.id = ' . self::tableName() . '.reassuradur_id');

        if (isset($params['reassuradur_id']) && $params['reassuradur_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.reassuradur_id', $params['reassuradur_id']]);
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

        if (isset($params['reassuradur_id']) && $params['reassuradur_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.reassuradur_id', $params['reassuradur_id']]);
        }

        return $query->count();
    }

    public static function generatePksNo($id)
    {
        return $id . '/REAS-ST/AJRI/' . date("mY");
    }

    public static function reasTypes()
    {
        return [
            self::REAS_TYPE_TREATY => self::REAS_TYPE_TREATY,
            self::REAS_TYPE_FACULTATIVE => self::REAS_TYPE_FACULTATIVE,
        ];
    }

    public static function reasMethods()
    {
        return [
            self::REAS_METHOD_QUOTA_SHARE => self::REAS_METHOD_QUOTA_SHARE,
            self::REAS_METHOD_SURPLUS => self::REAS_METHOD_SURPLUS,
            self::REAS_METHOD_COMBINATION => self::REAS_METHOD_COMBINATION,
            self::REAS_METHOD_OR => self::REAS_METHOD_OR,
        ];
    }

    public static function ratePeriods()
    {
        return [
            self::RATE_PERIOD_SINGLE => self::RATE_PERIOD_SINGLE,
            self::RATE_PERIOD_ANNUALLY => self::RATE_PERIOD_ANNUALLY,
        ];
    }

    public static function rateTypes()
    {
        return [
            self::RATE_TYPE_SINGLE => self::RATE_TYPE_SINGLE,
            self::RATE_TYPE_AGE => self::RATE_TYPE_AGE,
            self::RATE_TYPE_TERM => self::RATE_TYPE_TERM,
            self::RATE_TYPE_AGE_TERM => self::RATE_TYPE_AGE_TERM,
        ];
    }

    public static function prorateTypes()
    {
        return [
            self::PRORATE_TYPE_PRORATE_DAY => self::PRORATE_TYPE_PRORATE_DAY,
            self::PRORATE_TYPE_ROUND_DOWN => self::PRORATE_TYPE_ROUND_DOWN,
            self::PRORATE_TYPE_NONE => self::PRORATE_TYPE_NONE,
        ];
    }
}
