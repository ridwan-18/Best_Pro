<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        $user = User::findByUsername($this->username);
        if ($user == null) {
            Yii::$app->session->setFlash('error', "User not found");
            return false;
        }

        if (!User::validatePassword($this->password, $user->password)) {
            Yii::$app->session->setFlash('error', "Wrong password");
            return false;
        }

        $user->access_token = User::generateAccessToken();
        if (!$user->save(false)) {
            Yii::$app->session->setFlash('error', "Token Failed");
            return false;
        }

        return Yii::$app->user->login($user);
    }
}
