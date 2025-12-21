<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "disease".
 *
 * @property int $id
 * @property string $name
 */
class Disease extends \yii\db\ActiveRecord
{
    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'disease';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Name',
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
