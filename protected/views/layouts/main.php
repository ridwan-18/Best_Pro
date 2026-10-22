
<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$isLogin = Yii::$app->controller->route === 'site/login';
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php $this->registerCsrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?></title>

    <link
        rel="shortcut icon"
        href="<?= Url::base() ?>/theme/assets/images/favicon.png"
    >

    <?php $this->head() ?>

    <?php if ($isLogin): ?>
        <style>
            html,
            body {
                width: 100%;
                min-height: 100%;
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                overflow-x: hidden;
            }

            body > .site-login {
                position: fixed !important;
                inset: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                height: 100dvh !important;
                margin: 0 !important;
                padding: 20px !important;
                box-sizing: border-box !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                background: linear-gradient(135deg, #edf2f9, #dce6f4);
                overflow-y: auto;
                z-index: 99999;
            }

            .site-login .login-card {
                width: 100%;
                max-width: 430px;
                margin: auto !important;
                background: #fff;
                border: none;
                border-radius: 14px;
                box-shadow: 0 12px 40px rgba(0, 0, 0, .12);
            }

            .site-login .card-block,
            .site-login .account-box,
            .site-login .card-box {
                width: 100%;
                max-width: 100%;
                box-sizing: border-box;
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

            .site-login .form-control {
                height: 46px;
                border-radius: 7px;
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
    <?php endif; ?>
</head>

<body>

<?php $this->beginBody() ?>

<?php if ($isLogin): ?>

    <?= $content ?>

<?php else: ?>

    <?= $this->render('_topbar') ?>

    <div class="container-fluid mt-4">
        <?= $content ?>
    </div>

<?php endif; ?>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
