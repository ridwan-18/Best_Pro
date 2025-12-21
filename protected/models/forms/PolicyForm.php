<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class PolicyForm extends Model
{
    public $quotation_id;
    public $spa_date;
    public $partner_zip_code;
    public $partner_phone;
    public $partner_fax;
    public $partner_email;
    public $partner_established_date;
    public $partner_npwp;
    public $partner_certificate_no;
    public $partner_siup;
    public $partner_fund_source;
    public $partner_insurance_purpose;
    public $partner_insurance_purpose_description;
    public $pic_name;
    public $pic_title;
    public $pic_id_card_no;
    public $pic_phone;
    public $pic_email;
    public $bank_id;
    public $bank_branch;
    public $bank_account_no;
    public $bank_account_name;
    public $effective_date;
    public $end_date;
    public $insurance_period;
    public $payment_period;
    public $member_insured;
    public $notes;
    public $work_location;
    public $sign_location;
    public $sign_date;
    public $sign_by;
    public $sign_title;
    public $status = 'New';
    public $policy_no;

    public function rules()
    {
        return [
            [['quotation_id', 'spa_date', 'bank_id', 'bank_branch', 'bank_account_no', 'bank_account_name', 'effective_date', 'end_date', 'insurance_period', 'payment_period', 'member_insured'], 'required'],
            [['quotation_id', 'bank_id', 'member_insured'], 'integer'],
            [['spa_date', 'effective_date', 'end_date', 'sign_date', 'partner_established_date'], 'safe'],
            [['policy_no', 'pic_id_card_no', 'pic_phone', 'pic_email', 'partner_zip_code', 'partner_phone', 'partner_fax', 'partner_email', 'status'], 'string', 'max' => 50],
            [['pic_name', 'pic_title', 'bank_branch', 'bank_account_no', 'bank_account_name', 'notes', 'work_location', 'sign_location', 'sign_by', 'sign_title', 'partner_npwp', 'partner_certificate_no', 'partner_siup', 'partner_fund_source', 'partner_insurance_purpose', 'partner_insurance_purpose_description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'policy_no' => 'Policy No',
            'quotation_id' => 'Policy Holder',
            'partner_zip_code' => 'Zip Code',
            'partner_phone' => 'Phone',
            'partner_fax' => 'Fax',
            'partner_email' => 'Email',
            'partner_established_date' => 'Established Date',
            'partner_npwp' => 'NPWP',
            'partner_certificate_no' => 'Certificate No',
            'partner_siup' => 'SIUP',
            'partner_fund_source' => 'Fund Source',
            'partner_insurance_purpose' => 'Insurance Purpose',
            'partner_insurance_purpose_description' => 'Insurance Purpose Description',
            'bank_id' => 'Bank',
        ];
    }
}
