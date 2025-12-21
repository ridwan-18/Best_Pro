<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reassuradur".
 *
 * @property int $id
 * @property string $name
 * @property string|null $phone
 * @property string|null $fax
 * @property string|null $email
 * @property string|null $address
 * @property string|null $postal_code
 * @property string|null $city
 * @property int|null $established_year
 * @property string|null $tax_payer_identification
 * @property string|null $trade_business_license
 * @property string|null $company_deed
 * @property string|null $pic_name
 * @property int|null $payment_due_date
 * @property string|null $bank_name
 * @property string|null $bank_branch
 * @property string|null $bank_account_name
 * @property string|null $bank_account_number
 * @property string|null $payment_bank_name
 * @property string|null $payment_bank_branch
 * @property string|null $payment_bank_account_name
 * @property string|null $payment_bank_account_number
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Reassuradur extends \yii\db\ActiveRecord
{
    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reassuradur';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['established_year', 'payment_due_date', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'phone', 'fax', 'email', 'address', 'postal_code', 'city', 'tax_payer_identification', 'trade_business_license', 'company_deed', 'pic_name', 'bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number', 'payment_bank_name', 'payment_bank_branch', 'payment_bank_account_name', 'payment_bank_account_number'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'email' => 'Email',
            'address' => 'Address',
            'postal_code' => 'Postal Code',
            'city' => 'City',
            'established_year' => 'Established Year',
            'tax_payer_identification' => 'Tax Payer Identification',
            'trade_business_license' => 'Trade Business License',
            'company_deed' => 'Company Deed',
            'pic_name' => 'Pic Name',
            'payment_due_date' => 'Payment Due Date',
            'bank_name' => 'Bank Name',
            'bank_branch' => 'Bank Branch',
            'bank_account_name' => 'Bank Account Name',
            'bank_account_number' => 'Bank Account Number',
            'payment_bank_name' => 'Payment Bank Name',
            'payment_bank_branch' => 'Payment Bank Branch',
            'payment_bank_account_name' => 'Payment Bank Account Name',
            'payment_bank_account_number' => 'Payment Bank Account Number',
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

        if (isset($params['name']) && $params['name'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.name', $params['name']]);
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

        if (isset($params['name']) && $params['name'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.name', $params['name']]);
        }

        return $query->count();
    }
}
