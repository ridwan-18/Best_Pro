<?php

namespace app\models;

use Yii;
use yii\image\drivers\Image;

/**
 * This is the model class for table "signature".
 *
 * @property int $id
 * @property string $policy_name
 * @property string $policy_position
 * @property string $policy_picture
 * @property string $member_name
 * @property string $member_position
 * @property string $member_picture
 * @property string $claim_name
 * @property string $claim_position
 * @property string $claim_picture
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class Signature extends \yii\db\ActiveRecord
{
    const PICTURE_PATH = '/uploads/signature/';
    const PICTURE_MAX_WIDTH = 300;
    const PICTURE_MAX_HEIGHT = 300;

    const PAGE_SIZE = 10;

    public $policy_picture_file;
    public $member_picture_file;
    public $claim_picture_file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'signature';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['policy_name', 'policy_position', 'policy_picture', 'member_name', 'member_position', 'member_picture', 'claim_name', 'claim_position', 'claim_picture'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['policy_name', 'policy_position', 'policy_picture', 'member_name', 'member_position', 'member_picture', 'claim_name', 'claim_position', 'claim_picture'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'policy_name' => 'Policy Name',
            'policy_position' => 'Policy Position',
            'policy_picture' => 'Policy Picture',
            'member_name' => 'Member Name',
            'member_position' => 'Member Position',
            'member_picture' => 'Member Picture',
            'claim_name' => 'Claim Name',
            'claim_position' => 'Claim Position',
            'claim_picture' => 'Claim Picture',
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

    public function uploadPolicy()
    {
        $filename = 'sig-' . sha1(rand(1, 99) . date("YmdHis")) . date("HmYisd");
        $extension = $this->policy_picture_file->extension;

        $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . "." . $extension;
        $this->policy_picture_file->saveAs($path);

        $path = Yii::$app->image->load($path);
        $path->resize(self::PICTURE_MAX_WIDTH, self::PICTURE_MAX_HEIGHT, Image::WIDTH);
        if (!$path->save()) {
            return false;
        }

        $this->policy_picture_file = null;
        $this->policy_picture = $filename . "." . $extension;
        return true;
    }

    public function uploadMember()
    {
        $filename = 'sig-' . sha1(rand(1, 99) . date("YmdHis")) . date("HmYisd");
        $extension = $this->member_picture_file->extension;

        $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . "." . $extension;
        $this->member_picture_file->saveAs($path);

        $path = Yii::$app->image->load($path);
        $path->resize(self::PICTURE_MAX_WIDTH, self::PICTURE_MAX_HEIGHT, Image::WIDTH);
        if (!$path->save()) {
            return false;
        }

        $this->member_picture_file = null;
        $this->member_picture = $filename . "." . $extension;
        return true;
    }

    public function uploadClaim()
    {
        $filename = 'sig-' . sha1(rand(1, 99) . date("YmdHis")) . date("HmYisd");
        $extension = $this->claim_picture_file->extension;

        $path = \Yii::getAlias('@webroot') . self::PICTURE_PATH . $filename . "." . $extension;
        $this->claim_picture_file->saveAs($path);

        $path = Yii::$app->image->load($path);
        $path->resize(self::PICTURE_MAX_WIDTH, self::PICTURE_MAX_HEIGHT, Image::WIDTH);
        if (!$path->save()) {
            return false;
        }

        $this->claim_picture_file = null;
        $this->claim_picture = $filename . "." . $extension;
        return true;
    }
}
