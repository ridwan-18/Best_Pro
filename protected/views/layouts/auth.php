<?php

use app\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="<?= Url::base() . '/theme/assets/images/favicon.png'; ?>">
    <?php $this->head() ?>
</head>

<body class="account-pages">
    <?php $this->beginBody() ?>
    <div class="accountbg" style="background: url('<?= Url::base() . '/theme/assets/images/bg-1.jpg'; ?>');background-size: cover;background-position: center;"></div>
    <div class="wrapper-page account-page-full">
        <?= $content ?>
        <div class="m-t-40 text-center">
            <p class="account-copyright">2022 © Reliance Life.</p>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>