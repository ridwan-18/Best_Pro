
<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Login - ' . Yii::$app->name;
?>

<div class="site-login">
    <div class="card login-card">
        <div class="account-box">
            <div class="card-box p-5">

                <h2 class="text-uppercase text-center mb-4">
                    <a href="#" class="text-success">
                        <img
                            src="<?= Url::base() . '/theme/assets/images/logo.png'; ?>"
                            alt="Logo"
                            height="64"
                        >
                    </a>
                </h2>

                <?= Alert::widget() ?>

                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                ]); ?>

                <?= $form->field($model, 'username')->textInput([
                    'autofocus' => true,
                    'placeholder' => 'Enter your username',
                ]) ?>

                <?= $form->field($model, 'password')->passwordInput([
                    'placeholder' => 'Enter your password',
                ]) ?>

                <div class="form-group mt-4">
                    <?= Html::submitButton('Login', [
                        'class' => 'btn btn-block btn-primary waves-effect waves-light',
                        'name' => 'login-button',
                    ]) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

<style>
    html,
    body {
        min-height: 100%;
        margin: 0;
    }

    .site-login {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box;
    }

    .login-card {
        width: 100%;
        max-width: 450px;
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
        background: #fff;
    }

    .login-card .account-box,
    .login-card .card-box {
        width: 100%;
    }

    .login-card .card-box {
        padding: 40px !important;
    }

    .login-card .form-control {
        height: 45px;
        border-radius: 6px;
    }

    .login-card .btn-primary {
        height: 45px;
        border-radius: 6px;
    }

    @media (max-width: 576px) {
        .login-card .card-box {
            padding: 25px !important;
        }
    }
</style>
