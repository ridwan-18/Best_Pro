<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "partner".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $province
 * @property string|null $city
 * @property string|null $address
 * @property string|null $zip_code
 * @property string|null $phone
 * @property string|null $fax
 * @property string|null $email
 * @property string|null $established_date
 * @property string|null $npwp
 * @property string|null $certificate_no
 * @property string|null $siup
 * @property string|null $business_type
 * @property string|null $fund_source
 * @property string|null $insurance_purpose
 * @property string|null $insurance_purpose_description
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Partner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'name'], 'required'],
            [['established_date', 'created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['code', 'name', 'province', 'city', 'address', 'email', 'fund_source', 'insurance_purpose', 'insurance_purpose_description'], 'string', 'max' => 255],
            [['zip_code', 'phone', 'fax', 'certificate_no', 'business_type'], 'string', 'max' => 50],
            [['npwp', 'siup'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'province' => 'Province',
            'city' => 'City',
            'address' => 'Address',
            'zip_code' => 'Zip Code',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'email' => 'Email',
            'established_date' => 'Established Date',
            'npwp' => 'Npwp',
            'certificate_no' => 'Certificate No',
            'siup' => 'Siup',
            'business_type' => 'Business Type',
            'fund_source' => 'Fund Source',
            'insurance_purpose' => 'Insurance Purpose',
            'insurance_purpose_description' => 'Insurance Purpose Description',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function generateCode($params)
    {
        $words = explode(" ", $params['partner_name']);
        $partnerName = "";
        foreach ($words as $w) {
            $partnerName .= $w[0];
        }

        return $partnerName . $params['id'];
    }
}
