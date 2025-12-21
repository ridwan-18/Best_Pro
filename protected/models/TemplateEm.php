<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "template_em".
 *
 * @property int $id
 * @property float $percentage
 * @property int $age
 * @property float $em1
 * @property float $em2
 * @property float $em3
 * @property float $em4
 * @property float $em5
 * @property float $em6
 * @property float $em7
 * @property float $em8
 * @property float $em9
 * @property float $em10
 * @property float $em11
 * @property float $em12
 * @property float $em13
 * @property float $em14
 * @property float $em15
 */
class TemplateEm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'template_em';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['percentage', 'age', 'em1', 'em2', 'em3', 'em4', 'em5', 'em6', 'em7', 'em8', 'em9', 'em10', 'em11', 'em12', 'em13', 'em14', 'em15'], 'required'],
            [['percentage', 'em1', 'em2', 'em3', 'em4', 'em5', 'em6', 'em7', 'em8', 'em9', 'em10', 'em11', 'em12', 'em13', 'em14', 'em15'], 'number'],
            [['age'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'percentage' => 'Percentage',
            'age' => 'Age',
            'em1' => 'Em 1',
            'em2' => 'Em 2',
            'em3' => 'Em 3',
            'em4' => 'Em 4',
            'em5' => 'Em 5',
            'em6' => 'Em 6',
            'em7' => 'Em 7',
            'em8' => 'Em 8',
            'em9' => 'Em 9',
            'em10' => 'Em 10',
            'em11' => 'Em 11',
            'em12' => 'Em 12',
            'em13' => 'Em 13',
            'em14' => 'Em 14',
            'em15' => 'Em 15',
        ];
    }
}
