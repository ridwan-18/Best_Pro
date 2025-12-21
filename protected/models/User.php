<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const ROLE_SUPERADMIN = 1;
    const ROLE_UW = 2;
    const ROLE_REAS = 3;
    const ROLE_AKTUARI = 4;
    const ROLE_CLAIM = 5;
	const ROLE_PUSAT = 6;

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'username', 'password'], 'required'],
            [['role', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['email', 'phone', 'password', 'password_reset_token', 'access_token'], 'string', 'max' => 255],
            [['username'], 'string', 'max' => 30],
            [['auth_key'], 'string', 'max' => 32],
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
            'email' => 'Email',
            'phone' => 'Phone',
            'username' => 'Username',
            'password' => 'Password',
            'password_reset_token' => 'Password Reset Token',
            'auth_key' => 'Auth Key',
            'access_token' => 'Access Token',
            'role' => 'Role',
            'status' => 'Status',
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

        if (isset($params['username']) && $params['username'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.username', $params['username']]);
        }

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

        if (isset($params['username']) && $params['username'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.username', $params['username']]);
        }

        if (isset($params['name']) && $params['name'] != null) {
            $query->andFilterWhere(['like', self::tableName() . '.name', $params['name']]);
        }

        return $query->count();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public static function validatePassword($inputPassword, $password)
    {
        $passwordHash = substr($password, 0, 40);
        $salt = substr($password, -16);
        return $passwordHash == sha1($inputPassword . $salt);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function generateAccessToken()
    {
        return md5(Yii::$app->security->generateRandomString() . rand(0, 99) . date("YmdHis"));
    }

    public static function roles($selected = null)
    {
        $data = [
            self::ROLE_SUPERADMIN => 'Super Admin',
            self::ROLE_UW => 'Underwriting and Customer Support',
            self::ROLE_REAS => 'Reas',
            self::ROLE_AKTUARI => 'Aktuari',
            self::ROLE_CLAIM => 'Claim',
			 self::ROLE_PUSAT => 'Pusat',
        ];

        if ($selected == null) {
            return $data;
        }

        return $data[$selected];
    }

    public function hashPassword()
    {
        $salt = Yii::$app->security->generateRandomString(16);
        $this->password = sha1($this->password . $salt) . $salt;
    }
}
