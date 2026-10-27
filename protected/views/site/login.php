
<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Login - ' . Yii::$app->name;
?>

<style>
    /* RESET LAYOUT */
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

    /* BACKGROUND */
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
            #e8edff,
            #f5f8ff
        );

        background-size: 400% 400%;
        animation: backgroundMove 12s ease infinite;

        overflow-y: auto !important;
        z-index: 999999 !important;
    }

    @keyframes backgroundMove {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    /* KARTU LOGIN */
    .login-card {
        position: relative !important;

        width: 100% !important;
        max-width: 430px !important;

        margin: auto !important;
        padding: 0 !important;

        background: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 18px !important;

        box-shadow: 0 18px 55px rgba(31, 56, 100, 0.14) !important;

        overflow: hidden;

        animation: loginAppear 0.8s ease both;

        transition: box-shadow 0.4s ease;
    }

    @keyframes loginAppear {
        from {
            opacity: 0;
            transform: translateY(25px) scale(0.97);
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

    /* LOGO */
    .login-logo {
        text-align: center;
        margin-bottom: 25px;
    }

    .login-logo img {
        height: 64px;
        max-width: 100%;
        object-fit: contain;

        animation: logoFloat 3s ease-in-out infinite;

        filter: drop-shadow(
            0 5px 8px rgba(40, 100, 220, 0.12)
        );
    }

    @keyframes logoFloat {
        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    /* AREA LAMPU */
    .lamp-area {
        position: relative;
        isolation: isolate;

        text-align: center;
        margin: 0 0 25px;
        padding: 12px 0 5px;
    }

    /* CAHAYA */
    .light {
        position: absolute;

        top: -35px;
        left: 50%;

        width: 170px;
        height: 170px;

        transform: translateX(-50%);
        border-radius: 50%;

        background: radial-gradient(
            circle,
            rgba(250, 235, 131, 0.85) 0%,
            rgba(250, 235, 131, 0.40) 35%,
            rgba(250, 235, 131, 0.12) 60%,
            rgba(250, 235, 131, 0) 75%
        );

        opacity: 0;
        z-index: -1;
        pointer-events: none;

        transition: opacity 0.5s ease;
    }

    /* BOLA LAMPU */
    .bulb {
        position: relative;

        width: 42px;
        height: 42px;

        margin: 0 auto 15px;

        border-radius: 50%;
        background: #cbd5e1;

        border: 3px solid #ffffff;

        box-shadow: 0 0 0 1px #b8c4d4;

        transition: all 0.4s ease;
    }

    .bulb::after {
        content: "";

        position: absolute;

        width: 16px;
        height: 8px;

        left: 50%;
        bottom: -9px;

        transform: translateX(-50%);

        background: #94a3b8;
        border-radius: 0 0 4px 4px;

        transition: background 0.4s ease;
    }

    /* STATUS LAMPU HIDUP */
    .site-login.lamp-on .light {
        opacity: 1;
    }

    .site-login.lamp-on .bulb {
        background: #FAEB83;

        box-shadow:
            0 0 12px rgba(250, 235, 131, 0.95),
            0 0 28px rgba(250, 235, 131, 0.75),
            0 0 50px rgba(250, 235, 131, 0.35);

        transform: scale(1.08);

        animation: bulbGlow 1.8s ease-in-out infinite;
    }

    .site-login.lamp-on .bulb::after {
        background: #d9c45c;
    }

    @keyframes bulbGlow {
        0%,
        100% {
            box-shadow:
                0 0 12px rgba(250, 235, 131, 0.95),
                0 0 28px rgba(250, 235, 131, 0.75);
        }

        50% {
            box-shadow:
                0 0 20px rgba(250, 235, 131, 1),
                0 0 45px rgba(250, 235, 131, 0.9);
        }
    }

    /* TOMBOL LAMPU */
    .lamp-button {
        display: inline-block;

        position: relative;
        z-index: 2;

        padding: 5px 12px;

        font-size: 13px;
        font-weight: 600;

        text-decoration: none !important;
        cursor: pointer;

        border: none;
        background: transparent;

        transition: all 0.3s ease;
    }

    .lamp-button:hover {
        transform: translateY(-2px);
    }

    .lamp-on-button {
        color: #2864dc;
    }

    .lamp-off-button {
        display: none;
        color: #667085;
    }

    .site-login.lamp-on .lamp-on-button {
        display: none;
    }

    .site-login.lamp-on .lamp-off-button {
        display: inline-block;
    }

    /* KARTU IKUT BERCahaya */
    .site-login.lamp-on .login-card {
        box-shadow:
            0 18px 55px rgba(31, 56, 100, 0.14),
            0 0 30px rgba(250, 235, 131, 0.30) !important;
    }

    /* FORM LOGIN DEFAULT TERSEMBUNYI */
    .login-form-content {
        display: none;
        opacity: 0;
    }

    /* FORM LOGIN SAAT LAMPU HIDUP */
    .site-login.lamp-on .login-form-content {
        display: block;
        animation: showLogin 0.6s ease forwards;
    }

    @keyframes showLogin {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* LABEL */
    .site-login label {
        font-weight: 600;
        color: #344054;

        transition: color 0.3s ease;
    }

    /* INPUT */
    .site-login .form-control {
        width: 100%;
        height: 46px;

        border: 1px solid #dce2ea;
        border-radius: 8px;

        background: #fbfcfe;
        box-shadow: none;

        transition: all 0.3s ease;
    }

    .site-login .form-control:hover {
        border-color: #b6c7e4;
    }

    .site-login .form-control:focus {
        border-color: #2864dc;
        background: #ffffff;

        box-shadow:
            0 0 0 4px rgba(40, 100, 220, 0.10),
            0 4px 12px rgba(40, 100, 220, 0.05);

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

        border: none !important;
        border-radius: 8px !important;

        background: linear-gradient(
            135deg,
            #3478f6,
            #2054c7
        ) !important;

        color: #ffffff !important;

        font-weight: 600;
        letter-spacing: 0.3px;

        box-shadow: 0 5px 14px rgba(40, 100, 220, 0.22);

        transition: all 0.25s ease;
    }

    .site-login .login-button:hover {
        background: linear-gradient(
            135deg,
            #2868e8,
            #1949b0
        ) !important;

        color: #ffffff !important;

        transform: translateY(-2px);

        box-shadow: 0 9px 20px rgba(40, 100, 220, 0.30);
    }

    .site-login .login-button:active {
        transform: scale(0.98);
    }

    /* ALERT */
    .site-login .alert {
        border-radius: 8px;
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

    /* REDUKSI ANIMASI */
    @media (prefers-reduced-motion: reduce) {
        .site-login,
        .site-login *,
        .site-login *::before,
        .site-login *::after {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div class="site-login" id="loginPage">

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

                        <button
                            type="button"
                            class="lamp-button lamp-on-button"
                            id="lampOn"
                        >
                            Nyalakan lampu
                        </button>

                        <button
                            type="button"
                            class="lamp-button lamp-off-button"
                            id="lampOff"
                        >
                            Matikan lampu
                        </button>

                    </div>

                    <!-- FORM LOGIN -->
                    <div class="login-form-content" id="loginFormContent">

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

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var loginPage = document.getElementById('loginPage');
    var lampOn = document.getElementById('lampOn');
    var lampOff = document.getElementById('lampOff');
    var loginFormContent = document.getElementById('loginFormContent');

    if (!loginPage || !lampOn || !lampOff || !loginFormContent) {
        return;
    }

    /* KONDISI AWAL: LAMPU MATI */
    loginPage.classList.remove('lamp-on');
    loginFormContent.style.display = 'none';

    /* NYALAKAN LAMPU DAN TAMPILKAN FORM */
    lampOn.addEventListener('click', function () {

        loginPage.classList.add('lamp-on');

        loginFormContent.style.display = 'block';
        loginFormContent.style.opacity = '0';

        /* Animasi form */
        loginFormContent.style.animation = 'none';

        void loginFormContent.offsetWidth;

        loginFormContent.style.animation =
            'showLogin 0.6s ease forwards';

    });

    /* MATIKAN LAMPU DAN SEMBUNYIKAN FORM */
    lampOff.addEventListener('click', function () {

        loginPage.classList.remove('lamp-on');

        loginFormContent.style.display = 'none';
        loginFormContent.style.opacity = '0';

    });

});
</script>
