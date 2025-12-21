<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "global_reas_uw_limit".
 *
 * @property int $id
 * @property int $global_reas_id
 * @property float $min_si
 * @property float $max_si
 * @property int $min_age
 * @property int $max_age
 * @property string $medical_code
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class GlobalReasUwLimit extends \yii\db\ActiveRecord
{
    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'global_reas_uw_limit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['global_reas_id', 'min_si', 'max_si', 'min_age', 'max_age', 'medical_code'], 'required'],
            [['global_reas_id', 'min_age', 'max_age', 'created_by', 'updated_by'], 'integer'],
            [['min_si', 'max_si'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['medical_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'global_reas_id' => 'Global Reas ID',
            'min_si' => 'Min Si',
            'max_si' => 'Max Si',
            'min_age' => 'Min Age',
            'max_age' => 'Max Age',
            'medical_code' => 'Medical Code',
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

        if (isset($params['global_reas_id']) && $params['global_reas_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.global_reas_id', $params['global_reas_id']]);
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

        if (isset($params['global_reas_id']) && $params['global_reas_id'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.global_reas_id', $params['global_reas_id']]);
        }

        return $query->count();
    }
}
