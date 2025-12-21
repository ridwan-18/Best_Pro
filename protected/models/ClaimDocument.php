<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "claim_document".
 *
 * @property int $id
 * @property int $claim_id
 * @property int $document_id
 * @property int|null $is_checked
 * @property int|null $is_mandatory
 */
class ClaimDocument extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'claim_document';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['claim_id', 'document_id'], 'required'],
            [['claim_id', 'document_id', 'is_checked', 'is_mandatory'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'claim_id' => 'Claim ID',
            'document_id' => 'Document ID',
            'is_checked' => 'Is Checked',
            'is_mandatory' => 'Is Mandatory',
        ];
    }
}
