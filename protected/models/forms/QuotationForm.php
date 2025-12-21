<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class QuotationForm extends Model
{
    public $partner_id;
    public $member_type;
    public $member_qty;
    public $last_insurance;
    public $business_type;
    public $proposed_date;
    public $expired_date;
    public $term;
    public $member_card = 0;
    public $certificate_card = 0;
    public $distribution_channel;
    public $agent_id;
    public $payment_method;
    public $min_age;
    public $max_age;
    public $age_calculate;
    public $rate_type;
    public $effective_policy = 'day to day';
    public $notes;

    public function rules()
    {
        return [
            [['member_type', 'member_qty', 'business_type', 'proposed_date', 'expired_date', 'term', 'distribution_channel', 'agent_id', 'payment_method', 'min_age', 'max_age', 'age_calculate', 'rate_type', 'effective_policy'], 'required'],
            [['partner_id', 'member_qty', 'member_card', 'certificate_card', 'agent_id', 'min_age', 'max_age'], 'integer'],
            [['proposed_date', 'expired_date'], 'safe'],
            [['member_type', 'business_type', 'term', 'distribution_channel', 'payment_method', 'age_calculate', 'effective_policy', 'rate_type'], 'string', 'max' => 50],
            [['last_insurance', 'notes'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'partner_id' => 'Partner',
            'agent_id' => 'PIC',
        ];
    }
}
