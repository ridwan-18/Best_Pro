
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
        width: 100% !important;
        height: 100% !important;
        min-height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    body {
        overflow: hidden !important;
    }

    body > .wrapper,
    body > #wrapper,
    .wrapper,
    #wrapper,
    .content-page,
    .content-page > .content,
    .container,
    .container-fluid,
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: none !important;
        min-height: 0 !important;
    }

    /* BACKGROUND ANIMASI */
    .site-login {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        min-height: 100vh !important;

        margin: 0 !important;
        padding: 20px !important;
        box-sizing: border-box !important;

        display: flex !important;
        justify-content: center !important;
        align-items: center !important;

        background: linear-gradient(
            -45deg,
            #edf2f9,
            #dce6f4,
            #e6edff,
            #f5f8ff
        );
        background-size: 400% 400%;
        animation: backgroundMove 14s ease infinite;

        overflow-y: auto !important;
        z-index: 999999 !important;
    }

    @keyframes backgroundMove {
        0%   { background-position: 0% 50%; }
        50%  { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* ANIMASI KARTU LOGIN */
    .site-login .login-card {
        display: block !important;
        position: relative !important;

        top: auto !important;
        left: auto !important;
        right: auto !important;
        float: none !important;

        width: 100% !important;
        max-width: 430px !important;

        margin: auto !important;
        padding: 0 !important;

        background: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;

        box-shadow:
            0 20px 60px rgba(31, 56, 100, 0.13),
            0 4px 15px rgba(31, 56, 100, 0.05) !important;

        overflow: hidden;

        animation: loginAppear 0.8s cubic-bezier(
            0.22, 1, 0.36, 1
        ) both;

        transition:
            box-shadow 0.35s ease,
            border-color 0.35s ease;
    }

    .site-login .login-card:hover {
        box-shadow:
            0 25px 70px rgba(31, 56, 100, 0.17),
            0 8px 20px rgba(31, 56, 100, 0.06) !important;
    }

    @keyframes loginAppear {
        from {
            opacity: 0;
            transform: translateY(35px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
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

    /* LOGO MENGAMBANG */
    .site-login .login-logo {
        text-align: center;
        margin-bottom: 30px;
        animation: logoAppear 1s ease 0.2s both;
    }

    .site-login .login-logo img {
        height: 64px;
        max-width: 100%;
        object-fit: contain;
        animation: logoFloat 3s ease-in-out infinite;
        filter: drop-shadow(0 5px 8px rgba(40, 100, 220, 0.10));
    }

    @keyframes logoAppear {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes logoFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    /* LABEL FORM */
    .site-login label {
        font-weight: 600;
        color: #344054;
        transition: color 0.25s ease;
    }

    .site-login .form-group {
        animation: fieldAppear 0.6s ease both;
    }

    .site-login .form-group:nth-of-type(1) {
        animation-delay: 0.15s;
    }

    .site-login .form-group:nth-of-type(2) {
        animation-delay: 0.25s;
    }

    .site-login .form-group:nth-of-type(3) {
        animation-delay: 0.35s;
    }

    @keyframes fieldAppear {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* INPUT ANIMASI */
    .site-login .form-control {
        width: 100%;
        height: 46px;
        border: 1px solid #dce2ea;
        border-radius: 8px;
        box-shadow: none;

        background-color: #fbfcfe;

        transition:
            border-color 0.3s ease,
            box-shadow 0.3s ease,
            background-color 0.3s ease,
            transform 0.25s ease;
    }

    .site-login .form-control:hover {
        border-color: #b6c7e4;
    }

    .site-login .form-control:focus {
        border-color: #2864dc;
        background-color: #ffffff;

        box-shadow:
            0 0 0 4px rgba(40, 100, 220, 0.10),
            0 4px 12px rgba(40, 100, 220, 0.06);

        outline: none;
    }

    .site-login .form-group:focus-within label {
        color: #2864dc;
    }

    /* TOMBOL LOGIN */
    .site-login .login-button {
        position: relative;
        overflow: hidden;

        width: 100%;
        height: 46px;

        border: none;
        border-radius: 8px;

        background: linear-gradient(135deg, #3478f6, #2054c7);
        color: #ffffff;
        font-weight: 600;
        letter-spacing: 0.3px;

        box-shadow: 0 5px 14px rgba(40, 100, 220, 0.22);

        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            background 0.25s ease;
    }

    .site-login .login-button:hover {
        background: linear-gradient(135deg, #2868e8, #1949b0);
        color: #ffffff;

        transform: translateY(-2px);

        box-shadow: 0 9px 20px rgba(40, 100, 220, 0.30);
    }

    .site-login .login-button:active {
        transform: translateY(0) scale(0.98);
        box-shadow: 0 3px 8px rgba(40, 100, 220, 0.20);
    }

    .site-login .login-button:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(40, 100, 220, 0.16);
    }

    /* ALERT */
    .site-login .alert {
        border-radius: 8px;
        animation: fieldAppear 0.4s ease both;
    }

    /* RESPONSIVE */
    @media (max-width: 576px) {
        .site-login {
            padding: 15px !important;
        }

        .site-login .card-box {
            padding: 28px 22px !important;
        }

        .site-login .login-card {
            max-width: 100% !important;
            border-radius: 14px !important;
        }

        .site-login .login-logo img {
            height: 56px;
        }
    }

    /* HORMATI PENGATURAN REDUKSI ANIMASI */
    @media (prefers-reduced-motion: reduce) {
        .site-login,
        .site-login *,
        .site-login *::before,
        .site-login *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>


<div class="site-login">

    <div class="card login-card">

        <div class="card-block">

            <div class="account-box">

                <div class="card-box">

                    <div class="login-logo">
                        <img
                            src="<?= Url::base() ?>/theme/assets/images/logo.png"
                            alt="Logo"
                        >
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
