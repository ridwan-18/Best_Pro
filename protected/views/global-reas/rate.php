<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;

$this->title = 'Rate Global Reas - ' . Yii::$app->name;
?>

<div class="modal fade rate-upload-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload Rate</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['global-reas/upload-rate'], 'post', ['id' => 'rate-upload-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'global_reas_id', $globalReas->id) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="file">File</label>
                            <?= Html::input('file', 'file', null, ['class' => 'form-control', 'required' => true]) ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= Html::submitButton('<i class="fa fa-upload"></i> Upload', ['class' => 'btn btn-primary waves-effect waves-light', 'id' => 'upload-btn']) ?>
                        <?= Html::a('<i class="fa fa-file-excel-o"></i> Template', ['rate-template'], ['class' => 'btn btn-success waves-effect waves-light']); ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="global-reas-rate">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">Rate</h2>
            <h5 class="p-0 m-0"><?= $globalReas->pks_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $globalReas->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4 mb-4">
        <div class="col-md-6">
            <?= Html::a('<i class="fa fa-upload"></i> Upload', 'javascript:void(0)', [
                'class' => 'btn btn-primary waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.rate-upload-modal',
            ]); ?>
            <?= Html::a(
                '<i class="fa fa-remove"></i> Delete All',
                ['delete-all-rate', 'id' => $globalReas->id],
                [
                    'class' => 'btn btn-danger waves-effect waves-light',
                    'data-method' => 'post',
                    'data-confirm' => 'Are you sure want to Delete All?'
                ]
            ); ?>
        </div>
    </div>
    <div class="card-box">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Age</th>
                                <th>Term</th>
                                <th>Rate</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = $pagination->offset + 1;
                            if (!empty($rates)) :
                                foreach ($rates as $rate) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $rate['age']; ?></td>
                                        <td><?= $rate['term']; ?></td>
                                        <td><?= $rate['rate']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                    'class' => 'btn btn-light btn-sm waves-effect',
                                                    'title' => 'Update',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#update-rate-modal-' . $rate['id'],
                                                ]); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'global-reas/delete-rate',
                                                        'id' => $rate['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'Delete',
                                                        'data-confirm' => 'Are you sure want to delete?',
                                                        'data-method' => 'post',
                                                    ]
                                                ); ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="update-rate-modal-<?= $rate['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="myMediumModalLabel">Update Rate #<?= $i; ?></h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?= Html::beginForm(['global-reas/update-rate'], 'post', ['id' => 'rate-update-form']) ?>
                                                    <?= Html::input('hidden', 'global_reas_id', $globalReas->id) ?>
                                                    <?= Html::input('hidden', 'id', $rate['id'], [
                                                        'id' => 'id',
                                                        'required' => 'required',
                                                    ]) ?>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="age">Age</label>
                                                                <?= Html::input('number', 'age', $rate['age'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'age',
                                                                    'value' => $rate['age'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="term">Term</label>
                                                                <?= Html::input('number', 'term', $rate['term'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'term',
                                                                    'value' => $rate['term'],
                                                                ]) ?>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="rate">Rate</label>
                                                                <?= Html::input('number', 'rate', $rate['rate'], [
                                                                    'class' => 'form-control',
                                                                    'id' => 'rate',
                                                                    'step' => 'any',
                                                                    'value' => $rate['rate'],
                                                                ]) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-4">
                                                        <div class="col-md-12">
                                                            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success waves-effect waves-light']) ?>
                                                        </div>
                                                    </div>
                                                    <?= Html::endForm() ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                    $i++;
                                endforeach;
                            else :
                                echo '<tr><td class="text-center" colspan="100">No data</td></tr>';
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'disabledPageCssClass' => 'page-link',
                    'options' => ['class' => 'pagination pagination-split mb-0 mt-4'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<?php
$script = <<< JS
    $('#rate-upload-form').submit(function() {
        $('#upload-btn').html('<i class="fa fa-spinner"></i> Loading');
        $('#upload-btn').attr('class', 'btn btn-primary waves-effect waves-light disabled');
    });
JS;
$this->registerJs($script);
?>