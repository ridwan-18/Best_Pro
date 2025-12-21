<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class QuotationTcForm extends Model
{
    public $min_age;
    public $max_age;
    public $age_term;
    public $max_term;
    public $retroactive;
    public $max_si;
    public $min_premi;
    public $max_premi = 9999999999;
    public $maturity_age;
    public $rate_em;
    public $refund_premium;
    public $refund_type;
    public $refund_doc;
    public $grace_period;
    public $grace_type;
    public $claim_doc;
    public $claim_ratio;
    public $claim_type;
    public $administration_cost;
    public $policy_cost;
    public $member_card_cost;
    public $certificate_cost;
    public $stamp_cost;
    public $medical_checkup;
    public $remarks;
    public $release_date;

    public function rules()
    {
        return [
            [['min_age', 'max_age', 'age_term', 'max_term', 'retroactive', 'max_si', 'min_premi', 'maturity_age', 'rate_em', 'refund_premium', 'refund_type', 'refund_doc', 'grace_period', 'grace_type', 'claim_doc', 'claim_ratio', 'claim_type', 'administration_cost', 'policy_cost', 'member_card_cost', 'certificate_cost', 'stamp_cost', 'medical_checkup', 'release_date'], 'required'],
            [['min_age', 'max_age', 'age_term', 'max_term', 'retroactive', 'maturity_age', 'refund_doc', 'grace_period', 'claim_doc'], 'integer'],
            [['max_si', 'min_premi', 'max_premi', 'refund_premium', 'claim_ratio', 'administration_cost', 'policy_cost', 'member_card_cost', 'certificate_cost', 'stamp_cost'], 'number'],
            [['release_date'], 'safe'],
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
            'min_age' => 'Min Age',
            'age_term' => 'Entry Age + Insurance Term',
            'max_term' => 'Max Term (Year)',
            'retroactive' => 'Retroactive (Day)',
            'max_si' => 'Max Si',
            'min_premi' => 'Min Premi',
            'max_premi' => 'Max Premi',
            'maturity_age' => 'Maturity Age',
            'rate_em' => 'Rate Em',
            'refund_premium' => 'Refund Premium (%)',
            'refund_type' => 'Refund Type',
            'refund_doc' => 'Refund Doc (Day)',
            'grace_period' => 'Grace Period (Day)',
            'grace_type' => 'Grace Type',
            'claim_doc' => 'Claim Doc (Day)',
            'claim_ratio' => 'Claim Ratio (%)',
            'claim_type' => 'Claim Type',
            'administration_cost' => 'Administration Cost',
            'policy_cost' => 'Policy Cost',
            'member_card_cost' => 'Member Card Cost',
            'certificate_cost' => 'Certificate Cost',
            'stamp_cost' => 'Stamp Cost',
            'medical_checkup' => 'Medical Checkup',
            'remarks' => 'Remarks',
            'release_date' => 'Release Date',
        ];
    }
}
