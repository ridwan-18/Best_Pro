
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
    }

    /* BACKGROUND LOGIN */
    .site-login {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        padding: 20px !important;
        box-sizing: border-box !important;

        display: flex !important;
        justify-content: center !important;
        align-items: center !important;

        background: linear-gradient(
            135deg,
            #edf2f9,
            #dce6f4
        );

        overflow-y: auto !important;
        z-index: 999999 !important;

        transition: background 0.5s ease;
    }

    /* KARTU LOGIN */
    .login-card {
        position: relative !important;
        width: 100% !important;
        max-width: 430px !important;
        margin: auto !important;
        padding: 0 !important;

        background: #ffffff !important;
        border: none !important;
        border-radius: 16px !important;

        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.12) !important;
        overflow: hidden;

        animation: loginAppear 0.8s ease both;
    }

    @keyframes loginAppear {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
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

    /* LOGO */
    .login-logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-logo img {
        height: 64px;
        max-width: 100%;
        object-fit: contain;
        animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    /* LABEL DAN INPUT */
    .site-login label {
        font-weight: 600;
        color: #344054;
    }

    .site-login .form-control {
        width: 100%;
        height: 46px;
        border: 1px solid #dce2ea;
        border-radius: 8px;
        background: #fbfcfe;
        box-shadow: none;

        transition: all 0.3s ease;
    }

    .site-login .form-control:focus {
        border-color: #2864dc;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(40, 100, 220, 0.12);
    }

    /* TOMBOL LOGIN */
    .login-button {
        width: 100%;
        height: 46px;
        border: none !important;
        border-radius: 8px !important;

        background: linear-gradient(135deg, #3478f6, #2054c7) !important;
        color: #ffffff !important;
        font-weight: 600;

        transition: all 0.3s ease;
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 100, 220, 0.28);
    }

    /* AREA LAMPU */
    .lamp-area {
        position: relative;
        text-align: center;
        margin-bottom: 20px;
        padding-top: 10px;
    }

    .lamp {
        display: inline-block;
        color: #2864dc;
        font-size: 13px;
        text-decoration: none !important;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .lamp:hover {
        color: #1949b0;
    }

    .turnoff {
        display: none;
        color: #667085;
        font-size: 13px;
        text-decoration: none !important;
        cursor: pointer;
    }

    /* BENTUK LAMPU */
    .bulb {
        position: relative;
        width: 42px;
        height: 42px;
        margin: 0 auto 10px;

        border-radius: 50%;
        background: #cbd5e1;
        border: 3px solid #ffffff;

        box-shadow: 0 0 0 1px #cbd5e1;

        transition: all 0.5s ease;
    }

    .bulb::after {
        content: "";
        position: absolute;
        width: 15px;
        height: 8px;
        bottom: -9px;
        left: 50%;
        transform: translateX(-50%);

        background: #94a3b8;
        border-radius: 0 0 4px 4px;
    }

    /* CAHAYA LAMPU */
    .light {
        position: absolute;
        width: 100px;
        height: 100px;
        left: 50%;
        top: -15px;
        transform: translateX(-50%);

        border-radius: 50%;
        background: radial-gradient(
            circle,
            rgba(250, 235, 131, 0.65) 0%,
            rgba(250, 235, 131, 0.25) 40%,
            rgba(250, 235, 131, 0) 75%
        );

        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s ease;
    }

    /* EFEK SAAT LAMPU DINYALAKAN */
    #turnon:target .light {
        opacity: 1;
    }

    #turnon:target .bulb {
        background: #FAEB83;
        box-shadow:
            0 0 15px rgba(250, 235, 131, 0.9),
            0 0 35px rgba(250, 235, 131, 0.55);
    }

    #turnon:target .turnoff {
        display: inline-block;
    }

    #turnon:target .lamp {
        display: none;
    }

    #turnon:target ~ .login-card {
        box-shadow:
            0 15px 45px rgba(0, 0, 0, 0.12),
            0 0 30px rgba(250, 235, 131, 0.25) !important;
    }

    /* RESPONSIVE */
    @media (max-width: 576px) {
        .site-login {
            padding: 15px !important;
        }

        .site-login .card-box {
            padding: 28px 22px !important;
        }

        .login-logo img {
            height: 56px;
        }
    }

    /* AKSESIBILITAS */
    @media (prefers-reduced-motion: reduce) {
        .login-card,
        .login-logo img,
        .site-login *,
        .site-login *::before,
        .site-login *::after {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div class="site-login">

    <!-- ID TARGET UNTUK ANIMASI LAMPU -->
    <div id="turnon" style="display: none;"></div>

    <div class="card login-card">

        <div class="card-block">
            <div class="account-box">
                <div class="card-box">

                    <!-- LOGO -->
                    <div class="login-logo">
                        <img
                            src="<?= Url::base() ?>/theme/assets/images/logo.png"
                            alt="Logo"
                        >
                    </div>

                    <!-- KONTROL LAMPU -->
                    <div class="lamp-area">

                        <div class="light"></div>
                        <div class="bulb"></div>

                        <a href="#turnon" class="lamp">
                            Nyalakan lampu
                        </a>

                        <a href="#off" class="turnoff">
                            Matikan lampu
                        </a>

                    </div>

                    <!-- ALERT -->
                    <?= Alert::widget() ?>

                    <!-- FORM LOGIN YII2 -->
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
