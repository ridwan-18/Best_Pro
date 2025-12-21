<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "global_reas_rate".
 *
 * @property int $id
 * @property int $global_reas_id
 * @property int|null $age
 * @property int $term
 * @property float $rate
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class GlobalReasRate extends \yii\db\ActiveRecord
{
    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'global_reas_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['global_reas_id', 'term', 'rate'], 'required'],
            [['global_reas_id', 'age', 'term', 'created_by', 'updated_by'], 'integer'],
            [['rate'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
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
            'age' => 'Age',
            'term' => 'Term',
            'rate' => 'Rate',
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
