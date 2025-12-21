<?php

$this->title = 'Dashboard - ' . Yii::$app->name;
?>

<div class="site-index">
    <div class="row">
        <!-- <div class="col-md-3">
            <div class="card-box tilebox-one">
                <i class="fa fa-users float-right text-muted"></i>
                <h6 class="text-muted text-uppercase mt-0">Partner</h6>
            </div>
        </div> -->
        <div class="col-md-3">
            <div class="card-box tilebox-one">
                <i class="fa fa-life-ring float-right text-muted"></i>
                <h6 class="text-muted text-uppercase mt-0">Product</h6>
                <h2 class="m-b-20"><?= number_format($totalProduct); ?></h2>
            </div>
        </div>
    </div>
</div>