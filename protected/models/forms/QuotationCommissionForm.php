<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class QuotationCommissionForm extends Model
{
    public $discount;
    public $maintenance_agent_id;
    public $maintenance_fee;
    public $admin_agent_id;
    public $admin_fee;
    public $handling_agent_id;
    public $handling_fee;
    public $pph;
    public $ppn;
    public $refferal_agent_id;
    public $refferal_fee;
    public $closing_agent_id;
    public $closing_fee;
    public $fee_based_agent_id;
    public $fee_based;

    public function rules()
    {
        return [
            [['maintenance_agent_id', 'admin_agent_id', 'handling_agent_id', 'refferal_agent_id', 'closing_agent_id', 'fee_based_agent_id'], 'integer'],
            [['discount', 'maintenance_fee', 'admin_fee', 'handling_fee', 'pph', 'ppn', 'refferal_fee', 'closing_fee', 'fee_based'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'discount' => 'Discount (%)',
            'maintenance_agent_id' => 'Maintenance',
            'maintenance_fee' => 'Maintenance Fee (%)',
            'admin_agent_id' => 'Admin Ref',
            'admin_fee' => 'Admin Fee (%)',
            'handling_agent_id' => 'Handling Ref',
            'handling_fee' => 'Handling Fee (%)',
            'refferal_agent_id' => 'Refferal Ref',
            'refferal_fee' => 'Refferal Fee (%)',
            'closing_agent_id' => 'Closing Agent',
            'closing_fee' => 'Closing Fee (%)',
            'fee_based_agent_id' => 'Fee Based Ref',
            'fee_based' => 'Fee Based (%)',
            'pph' => 'PPH (%)',
            'ppn' => 'PPN (%)',
        ];
    }
}
