<?php

use app\assets\PrintAsset;
use yii\helpers\Html;

PrintAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="utf-8">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        @page {
            size: A4
        }
    </style>
</head>

<body class="A4" onload="window.print()">
    <?php $this->beginBody() ?>
    <?= $content; ?>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>