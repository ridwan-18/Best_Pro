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

<body>
    <?php $this->beginBody() ?>
    <div id="wrapper">
        <?= $this->render('_sidebar'); ?>
        <div class="content-page">
            <?= $this->render('_topbar'); ?>
            <div class="content">
                <div class="container-fluid">
                    <?= $content ?>
                </div>
            </div>
            <footer class="footer">
                2022 © Reliance Life.
            </footer>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>