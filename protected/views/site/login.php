<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use app\widgets\Alert;

$this->title = 'Login - ' . Yii::$app->name;
?>

<div class="site-login">
    <div class="card">
        <div class="card-block">
            <div class="account-box">
                <div class="card-box p-5">
                    <h2 class="text-uppercase text-center pb-5">
                        <a href="#" class="text-success">
                            <span><img src="<?= Url::base() . '/theme/assets/images/logo.png'; ?>" alt="" height="64"></span>
                        </a>
                    </h2>
                    <?= Alert::widget() ?>
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                    <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Enter your username']) ?>
                    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Enter your password']) ?>
                    <div class="form-group row text-center m-t-10">
                        <div class="col-12">
                            <?= Html::submitButton('Login', [
                                'class' => 'btn btn-block btn-primary waves-effect waves-light',
                                'name' => 'login-button',
                            ]) ?>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>