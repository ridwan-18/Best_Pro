
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
        min-height: 100%;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Container login memenuhi layar */
    .site-login {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;

        width: 100vw !important;
        height: 100vh !important;
        min-height: 100vh !important;

        display: flex !important;
        justify-content: center !important;
        align-items: center !important;

        margin: 0 !important;
        padding: 20px !important;

        box-sizing: border-box !important;
        overflow-y: auto;
        background: #f4f7fb;

        z-index: 9999;
    }

    /* Card login */
    .site-login .login-card {
        display: block !important;

        position: relative !important;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        float: none !important;
        transform: none !important;

        width: 100% !important;
        max-width: 430px !important;

        margin: auto !important;
        padding: 0 !important;

        background: #ffffff;
        border: none !important;
        border-radius: 14px !important;

        box-shadow: 0 10px 40px rgba(30, 50, 80, 0.12);
        overflow: hidden;
    }

    /* Hilangkan pengaruh CSS template */
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

    /* Logo */
    .site-login .login-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .site-login .login-logo img {
        max-width: 100%;
        height: 64px;
        object-fit: contain;
    }

    /* Label input */
    .site-login label {
        font-weight: 600;
        color: #344054;
        margin-bottom: 8px;
    }

    /* Input */
    .site-login .form-control {
        width: 100%;
        height: 46px;
        padding: 10px 14px;

        border: 1px solid #dce2ea;
        border-radius: 7px;

        background: #ffffff;
        box-shadow: none;
        font-size: 14px;
    }

    .site-login .form-control:focus {
        border-color: #2864dc;
        box-shadow: 0 0 0 3px rgba(40, 100, 220, 0.10);
    }

    .site-login .form-group {
        margin-bottom: 20px;
    }

    /* Tombol login */
    .site-login .login-button {
        width: 100%;
        height: 46px;

        border: none;
        border-radius: 7px;

        background: #2864dc;
        color: #ffffff;

        font-size: 15px;
        font-weight: 600;

        transition: background 0.2s ease;
    }

    .site-login .login-button:hover {
        background: #1e4fb5;
        color: #ffffff;
    }

    /* Alert */
    .site-login .alert {
        border-radius: 7px;
        font-size: 13px;
    }

    /* Tampilan HP */
    @media (max-width: 576px) {
        .site-login {
            padding: 15px !important;
        }

        .site-login .login-card {
            max-width: 100% !important;
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
                        'options' => [
                            'autocomplete' => 'on',
                        ],
                    ]); ?>

                    <?= $form->field($model, 'username')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'Enter your username',
                        'autocomplete' => 'username',
                    ]) ?>

                    <?= $form->field($model, 'password')->passwordInput([
                        'placeholder' => 'Enter your password',
                        'autocomplete' => 'current-password',
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
