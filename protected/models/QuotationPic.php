<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "quotation_pic".
 *
 * @property int $id
 * @property int $quotation_id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $job_position
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class QuotationPic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_pic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'name', 'phone', 'email', 'job_position'], 'required'],
            [['quotation_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'phone', 'email', 'job_position'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'job_position' => 'Job Position',
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
                self::tableName() . '.name',
                self::tableName() . '.phone',
                self::tableName() . '.email',
                self::tableName() . '.job_position',
                self::tableName() . '.created_at',
                self::tableName() . '.created_by',
                User::tableName() . '.username AS created_by',
            ])
            ->asArray()
            ->innerJoin(User::tableName(), User::tableName() . '.id = ' . self::tableName() . '.created_by');

        if (isset($params['quotation_id']) && $params['quotation_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.quotation_id', $params['quotation_id']]);
        }

        return $query->all();
    }
}
