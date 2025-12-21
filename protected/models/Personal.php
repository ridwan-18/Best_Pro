<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "personal".
 *
 * @property int $id
 * @property string $personal_no
 * @property string $name
 * @property string|null $birth_place
 * @property string $birth_date
 * @property string|null $gender
 * @property string|null $id_card_no
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $province
 * @property string|null $city
 */
class Personal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['personal_no', 'name', 'birth_date'], 'required'],
            [['birth_date'], 'safe'],
            [['personal_no', 'name', 'birth_place', 'id_card_no', 'phone', 'email', 'address', 'province', 'city', 'profession'], 'string', 'max' => 255],
            [['gender'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'personal_no' => 'Personal No',
            'name' => 'Name',
            'birth_place' => 'Birth Place',
            'birth_date' => 'Birth Date',
            'gender' => 'Gender',
            'id_card_no' => 'Id Card No',
            'phone' => 'Phone',
            'email' => 'Email',
            'address' => 'Address',
            'province' => 'Province',
            'city' => 'City',
            'profession' => 'Profession',
        ];
    }

    public static function generatePersonalNo($name, $birthDate)
    {
        $name = str_replace(' ', '', $name);
        $birthDate = str_replace('-', '', $birthDate);
        return $name . $birthDate;
    }
}
