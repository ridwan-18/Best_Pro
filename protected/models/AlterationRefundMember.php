<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alteration_refund_member".
 *
 * @property int $id
 * @property string|null $alteration_no
 * @property string|null $member_no
 * @property string|null $name
 * @property string|null $birth_date
 * @property int|null $age
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $new_end_date
 * @property int|null $term
 * @property int|null $remaining_term
 * @property float|null $sum_insured
 * @property float|null $premi
 * @property float|null $extra_premi
 * @property float|null $premi_refund
 */
class AlterationRefundMember extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alteration_refund_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['birth_date', 'start_date', 'end_date', 'new_end_date'], 'safe'],
            [['age', 'term', 'remaining_term'], 'integer'],
            [['sum_insured', 'premi', 'extra_premi', 'premi_refund'], 'number'],
            [['alteration_no', 'member_no'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alteration_no' => 'Alteration No',
            'member_no' => 'Member No',
            'name' => 'Name',
            'birth_date' => 'Birth Date',
            'age' => 'Age',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'new_end_date' => 'New End Date',
            'term' => 'Term',
            'remaining_term' => 'Remaining Term',
            'sum_insured' => 'Sum Insured',
            'premi' => 'Premi',
            'extra_premi' => 'Extra Premi',
            'premi_refund' => 'Premi Refund',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->asArray();

        if (isset($params['alteration_no']) && $params['alteration_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.alteration_no', $params['alteration_no']]);
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
}
