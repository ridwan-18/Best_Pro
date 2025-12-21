<?php

use yii\helpers\Html;
use app\widgets\Alert;

$this->title = 'Reset Password User - ' . Yii::$app->name;
?>
<div class="update-create">
    <?= Html::beginForm(['user/reset-password', 'id' => $model->id], 'post') ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="p-0 m-0">Reset Password User - <?= $model->username; ?></h2>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <div class="form-group">
                    <?= Html::tag('label', 'New Password', ['for' => 'new_password']) ?>
                    <?= Html::input('password', 'new_password', null, ['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= Html::tag('label', 'Repeat Password', ['for' => 'repeat_password']) ?>
                    <?= Html::input('password', 'repeat_password', null, ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-block btn-lg waves-effect waves-light']) ?>
        </div>
    </div>
    <?= Html::endForm() ?>
</div>