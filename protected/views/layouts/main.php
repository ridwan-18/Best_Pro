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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="<?= Url::base() ?>/theme/assets/images/favicon.png">
    <?php $this->head() ?>
</head>

<body>

<?php $this->beginBody() ?>

<?= $this->render('_topbar') ?>

<div class="container-fluid mt-4">

    <?= $content ?>

</div>

<?php $this->endBody() ?>

</body>

</html>

<?php $this->endPage() ?>