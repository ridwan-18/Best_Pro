<?php

use yii\helpers\Html;
?>

<div class="row">
    <div class="col-md-12">
        <?= Html::a('<i class="fa fa-pencil"></i> Update', ['update', 'id' => $id], ['class' => 'btn btn-sm btn-primary waves-effect waves-light']); ?>
        <?= Html::a('<i class="fa fa-users"></i> PIC', ['pic', 'id' => $id], ['class' => 'btn btn-sm btn-primary waves-effect waves-light']); ?>
        <?= Html::a('<i class="fa fa-cubes"></i> Product', ['product', 'id' => $id], ['class' => 'btn btn-sm btn-primary waves-effect waves-light']); ?>
    </div>
</div>
<div class="row mt-1">
    <div class="col-md-12">
        <?= Html::a('<i class="fa fa-file-text"></i> Rate', ['rate', 'id' => $id], ['class' => 'btn btn-sm btn-purple waves-effect waves-light']); ?>
        <?= Html::a('<i class="fa fa-file"></i> TC', ['tc', 'id' => $id], ['class' => 'btn btn-sm btn-purple waves-effect waves-light']); ?>
        <?= Html::a('<i class="fa fa-retweet"></i> Reins', ['reins', 'id' => $id], ['class' => 'btn btn-sm btn-purple waves-effect waves-light']); ?>
    </div>
</div>