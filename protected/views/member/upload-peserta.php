```php
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Upload Peserta';
?>

<div class="member-upload-peserta">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>


    <div class="panel panel-default">

        <div class="panel-heading">
            <b>Upload Data Peserta Excel</b>
        </div>

        <div class="panel-body">

            <?php $form = ActiveForm::begin([
                'action' => ['upload-peserta'],
                'method' => 'post',
                'options' => [
                    'enctype' => 'multipart/form-data'
                ]
            ]); ?>


            <div class="form-group">

                <label>File Excel Peserta</label>

                <input
                    type="file"
                    name="file_excel"
                    class="form-control"
                    accept=".xls,.xlsx"
                    required
                >

                <small class="text-muted">
                    Format yang diperbolehkan: XLS / XLSX
                </small>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="glyphicon glyphicon-upload"></i>
                Upload Peserta
            </button>


            <?php ActiveForm::end(); ?>

        </div>

    </div>

</div>
```
