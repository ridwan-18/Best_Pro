<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_tc".
 *
 * @property int $id
 * @property int $quotation_id
 * @property int $min_age
 * @property int $max_age
 * @property int $age_term
 * @property int $max_term
 * @property int $retroactive
 * @property float $max_si
 * @property float $min_premi
 * @property float $max_premi
 * @property int $maturity_age
 * @property string $rate_em
 * @property float $refund_premium
 * @property string $refund_type
 * @property int $refund_doc
 * @property int $grace_period
 * @property string $grace_type
 * @property int $claim_doc
 * @property float $claim_ratio
 * @property string $claim_type
 * @property float $administration_cost
 * @property float $policy_cost
 * @property float $member_card_cost
 * @property float $certificate_cost
 * @property float $stamp_cost
 * @property string $medical_checkup
 * @property string|null $remarks
 * @property string $release_date
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class QuotationTc extends \yii\db\ActiveRecord
{
    const REFUND_TYPE_GROSS = 'Gross Premium';
    const REFUND_TYPE_NETT = 'Nett Premium';

    const GRACE_TYPE_CALENDAR = 'Calendar';
    const GRACE_TYPE_WORK_DAY = 'Work Day';

    const CLAIM_TYPE_GROSS = 'Gross Premium';
    const CLAIM_TYPE_NETT = 'Nett Premium';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_tc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'min_age', 'max_age', 'age_term', 'max_term', 'retroactive', 'max_si', 'min_premi', 'max_premi', 'maturity_age', 'rate_em', 'refund_premium', 'refund_type', 'refund_doc', 'grace_period', 'grace_type', 'claim_doc', 'claim_ratio', 'claim_type', 'administration_cost', 'policy_cost', 'member_card_cost', 'certificate_cost', 'stamp_cost', 'medical_checkup', 'release_date'], 'required'],
            [['quotation_id', 'min_age', 'max_age', 'age_term', 'max_term', 'retroactive', 'maturity_age', 'refund_doc', 'grace_period', 'claim_doc', 'created_by', 'updated_by'], 'integer'],
            [['max_si', 'min_premi', 'max_premi', 'refund_premium', 'claim_ratio', 'administration_cost', 'policy_cost', 'member_card_cost', 'certificate_cost', 'stamp_cost'], 'number'],
            [['release_date', 'created_at', 'updated_at'], 'safe'],
            [['rate_em', 'refund_type', 'grace_type', 'claim_type', 'medical_checkup'], 'string', 'max' => 50],
            [['remarks'], 'string', 'max' => 255],
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
            'min_age' => 'Min Age',
            'max_age' => 'Max Age',
            'age_term' => 'Age Term',
            'max_term' => 'Max Term',
            'retroactive' => 'Retroactive',
            'max_si' => 'Max Si',
            'min_premi' => 'Min Premi',
            'max_premi' => 'Max Premi',
            'maturity_age' => 'Maturity Age',
            'rate_em' => 'Rate Em',
            'refund_premium' => 'Refund Premium',
            'refund_type' => 'Refund Type',
            'refund_doc' => 'Refund Doc',
            'grace_period' => 'Grace Period',
            'grace_type' => 'Grace Type',
            'claim_doc' => 'Claim Doc',
            'claim_ratio' => 'Claim Ratio',
            'claim_type' => 'Claim Type',
            'administration_cost' => 'Administration Cost',
            'policy_cost' => 'Policy Cost',
            'member_card_cost' => 'Member Card Cost',
            'certificate_cost' => 'Certificate Cost',
            'stamp_cost' => 'Stamp Cost',
            'medical_checkup' => 'Medical Checkup',
            'remarks' => 'Remarks',
            'release_date' => 'Release Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }
}
