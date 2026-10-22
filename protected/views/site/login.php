
<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Login - ' . Yii::$app->name;
?>

<style>
    html,
    body {
        width: 100%;
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
    }

    body {
        overflow-x: hidden;
    }

    /* Full page */
    .site-login {
        position: fixed !important;
        inset: 0 !important;

        width: 100% !important;
        height: 100vh !important;
        min-height: 100vh !important;

        display: flex !important;
        justify-content: center !important;
        align-items: center !important;

        margin: 0 !important;
        padding: 20px !important;
        box-sizing: border-box !important;

        background: linear-gradient(135deg, #eef3fb, #dfe8f5);
        overflow-y: auto;
        z-index: 99999;
    }

    /* Kotak form di tengah */
    .site-login .login-card {
        width: 100% !important;
        max-width: 430px !important;

        margin: auto !important;
        padding: 0 !important;

        float: none !important;
        position: relative !important;
        left: auto !important;
        right: auto !important;
        transform: none !important;

        background: #fff;
        border: none !important;
        border-radius: 14px !important;
        box-shadow: 0 12px 40px rgba(30, 50, 80, 0.15);
        overflow: hidden;
    }

    .site-login .card-block,
    .site-login .account-box,
    .site-login .card-box {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        float: none !important;
        box-sizing: border-box !important;
    }

    .site-login .card-box {
        padding: 40px !important;
    }

    .site-login .login-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .site-login .login-logo img {
        height: 64px;
        max-width: 100%;
        object-fit: contain;
    }

    .site-login label {
        font-weight: 600;
        color: #344054;
        margin-bottom: 8px;
    }

    .site-login .form-control {
        width: 100%;
        height: 46px;
        border: 1px solid #dce2ea;
        border-radius: 7px;
        padding: 10px 14px;
        box-shadow: none;
    }

    .site-login .form-control:focus {
        border-color: #2864dc;
        box-shadow: 0 0 0 3px rgba(40, 100, 220, 0.1);
    }

    .site-login .form-group {
        margin-bottom: 20px;
    }

    .site-login .login-button {
        width: 100%;
        height: 46px;
        border: none;
        border-radius: 7px;
        background: #2864dc;
        color: #fff;
        font-weight: 600;
    }

    .site-login .login-button:hover {
        background: #1e4fb5;
        color: #fff;
    }

    @media (max-width: 576px) {
        .site-login {
            padding: 15px !important;
        }

        .site-login .card-box {
            padding: 28px 22px !important;
        }
    }
</style>

<div class="site-login">

    <div class="card login-card">
        <div class="card-block">
            <div class="account-box">
                <div class="card-box">

                    <div class="login-logo">
                        <a href="<?= Url::to(['/site/login']) ?>">
                            <img
                                src="<?= Url::base() . '/theme/assets/images/logo.png' ?>"
                                alt="Logo"
                            >
                        </a>
                    </div>

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

                    <div class="form-group mt-4 mb-0">
                        <?= Html::submitButton('Login', [
                            'class' => 'btn btn-primary login-button',
                            'name' => 'login-button',
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>

</div>
