<?php

use yii\helpers\Html;
?>

<div class="row">
    <div class="col-md-12">
        <?= Html::a(
            '<i class="fa fa-pencil"></i> Update',
            ['update', 'id' => $id],
            ['class' => 'btn btn-lg btn-purple waves-effect waves-light']
        ); ?>
        <?= Html::a(
            '<i class="fa fa-file"></i> UW Limit',
            ['uw-limit', 'id' => $id],
            ['class' => 'btn btn-lg btn-purple waves-effect waves-light']
        ); ?>
        <?= Html::a(
            '<i class="fa fa-file-text"></i> Rate',
            ['rate', 'id' => $id],
            ['class' => 'btn btn-lg btn-purple waves-effect waves-light']
        ); ?>
    </div>
</div>