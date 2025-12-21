<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_commission".
 *
 * @property int $id
 * @property int $quotation_id
 * @property float|null $discount
 * @property int|null $maintenance_agent_id
 * @property float|null $maintenance_fee
 * @property int|null $admin_agent_id
 * @property float|null $admin_fee
 * @property int|null $handling_agent_id
 * @property float|null $handling_fee
 * @property float|null $pph
 * @property float|null $ppn
 * @property int|null $refferal_agent_id
 * @property float|null $refferal_fee
 * @property int|null $closing_agent_id
 * @property float|null $closing_fee
 * @property int|null $fee_based_agent_id
 * @property float|null $fee_based
 */
class QuotationCommission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_commission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id'], 'required'],
            [['quotation_id', 'maintenance_agent_id', 'admin_agent_id', 'handling_agent_id', 'refferal_agent_id', 'closing_agent_id', 'fee_based_agent_id'], 'integer'],
            [['discount', 'maintenance_fee', 'admin_fee', 'handling_fee', 'pph', 'ppn', 'refferal_fee', 'closing_fee', 'fee_based'], 'number'],
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
            'discount' => 'Discount',
            'maintenance_agent_id' => 'Maintenance Agent ID',
            'maintenance_fee' => 'Maintenance Fee',
            'admin_agent_id' => 'Admin Agent ID',
            'admin_fee' => 'Admin Fee',
            'handling_agent_id' => 'Handling Agent ID',
            'handling_fee' => 'Handling Fee',
            'pph' => 'Pph',
            'ppn' => 'Ppn',
            'refferal_agent_id' => 'Refferal Agent ID',
            'refferal_fee' => 'Refferal Fee',
            'closing_agent_id' => 'Closing Agent ID',
            'closing_fee' => 'Closing Fee',
            'fee_based_agent_id' => 'Fee Based Agent ID',
            'fee_based' => 'Fee Based',
        ];
    }
}
