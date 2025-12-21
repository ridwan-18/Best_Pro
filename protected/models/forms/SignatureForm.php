<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

class SignatureForm extends Model
{
    public $policy_name;
    public $policy_position;
    public $policy_picture_file;
    public $member_name;
    public $member_position;
    public $member_picture_file;
    public $claim_name;
    public $claim_position;
    public $claim_picture_file;

    public function rules()
    {
        return [
            [['policy_name', 'policy_position', 'member_name', 'member_position', 'claim_name', 'claim_position'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'policy_name' => 'Name',
            'policy_position' => 'Position',
            'policy_picture_file' => 'Picture',
            'member_name' => 'Name',
            'member_position' => 'Position',
            'member_picture_file' => 'Picture',
            'claim_name' => 'Name',
            'claim_position' => 'Position',
            'claim_picture_file' => 'Picture',
        ];
    }
}
