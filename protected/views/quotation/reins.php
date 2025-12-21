<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\widgets\Alert;
use app\models\GlobalReas;

$this->registerJsFile(
    '@web/theme/assets/js/easy-number-separator.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

$globalReases = GlobalReas::find()->orderBy(['id' => SORT_ASC])->all();
$globalReases = ArrayHelper::map($globalReases, 'id', 'pks_no');

$this->title = 'Reins - ' . Yii::$app->name;
?>

<div class="modal fade create-reins-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Create Reins</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['quotation/create-reins'], 'post', ['id' => 'reins-form']) ?>
                <?= Html::input('hidden', 'quotation_id', $quotationModel->id, [
                    'id' => 'quotation_id',
                    'required' => 'required',
                ]) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="global_reas_id">Global Reas</label>
                            <?= Html::dropDownList('global_reas_id', null, $globalReases, [
                                'prompt' => '- Select Global Reas -',
                                'id' => 'global_reas_id',
                                'class' => 'form-control slct2',
                                'required' => 'required',
                            ]) ?>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="si_from">SI From</label>
                                    <?= Html::input('text', null, null, [
                                        'class' => 'form-control',
                                        'id' => 'si-from',
                                        'step' => 'any',
                                        'required' => 'required',
                                    ]) ?>
                                </div>
                                <?= Html::input('hidden', 'si_from', null, [
                                    'id' => 'si-from-result',
                                    'required' => 'required',
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="si_to">SI To</label>
                                    <?= Html::input('text', null, null, [
                                        'class' => 'form-control',
                                        'id' => 'si-to',
                                        'step' => 'any',
                                        'required' => 'required',
                                    ]) ?>
                                </div>
                                <?= Html::input('hidden', 'si_to', null, [
                                    'id' => 'si-to-result',
                                    'required' => 'required',
                                ]) ?>
                            </div>
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

<div class="quotation-create">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Reins</h2>
            <h5 class="p-0 m-0"><?= $quotationModel->proposal_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $quotationModel->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-12">
                <?= Html::a('<i class="fa fa-plus"></i> Create', 'javascript:void(0)', [
                    'class' => 'btn btn-warning waves-effect waves-light mb-4',
                    'data-toggle' => 'modal',
                    'data-target' => '.create-reins-modal',
                ]); ?>
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Reassuradur</th>
                                <th>PKS No</th>
                                <th>Reas Type</th>
                                <th>Reas Method</th>
                                <th>Ceding Share</th>
                                <th>Reas Share</th>
                                <th>SI From</th>
                                <th>SI To</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($quotationReinsModels)) :
                                foreach ($quotationReinsModels as $model) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['reassuradur']; ?></td>
                                        <td><?= $model['pks_no']; ?></td>
                                        <td><?= $model['reas_type']; ?></td>
                                        <td><?= $model['reas_method']; ?></td>
                                        <td><?= $model['ceding_share']; ?></td>
                                        <td><?= $model['reas_share']; ?></td>
                                        <td><?= number_format($model['si_from']); ?></td>
                                        <td><?= number_format($model['si_to']); ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                    'class' => 'btn btn-light btn-sm waves-effect',
                                                    'title' => 'Update',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#update-reins-modal-' . $model['id'],
                                                ]); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'quotation/delete-reins',
                                                        'id' => $model['id'],
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

                                    <div class="modal fade" id="update-reins-modal-<?= $model['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                        <div class="modal-dialog modal-md">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h4 class="modal-title" id="myMediumModalLabel">Update Reins</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <?= Html::beginForm(['quotation/update-reins'], 'post', ['id' => 'reins-form']) ?>
                                                    <?= Html::input('hidden', 'quotation_id', $quotationModel->id, [
                                                        'id' => 'quotation_id',
                                                        'required' => 'required',
                                                    ]) ?>
                                                    <?= Html::input('hidden', 'id', $model['id'], [
                                                        'id' => 'id',
                                                        'required' => 'required',
                                                    ]) ?>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="global_reas_id">Global Reas</label>
                                                                <?= Html::dropDownList('global_reas_id', $model['global_reas_id'], $globalReases, [
                                                                    'prompt' => '- Select Global Reas -',
                                                                    'id' => 'global_reas_id',
                                                                    'class' => 'form-control slct2',
                                                                    'required' => 'required',
                                                                ]) ?>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="si_from">SI From</label>
                                                                        <?= Html::input('text', null, $model['si_from'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'si-from' . $model['id'],
                                                                            'step' => 'any',
                                                                            'required' => 'required',
                                                                        ]) ?>
                                                                    </div>
                                                                    <?= Html::input('hidden', 'si_from', $model['si_from'], [
                                                                        'id' => 'si-from-result' . $model['id'],
                                                                        'required' => 'required',
                                                                    ]) ?>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="si_to">SI To</label>
                                                                        <?= Html::input('text', null, $model['si_to'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'si-to' . $model['id'],
                                                                            'step' => 'any',
                                                                            'required' => 'required',
                                                                        ]) ?>
                                                                    </div>
                                                                    <?= Html::input('hidden', 'si_to', $model['si_to'], [
                                                                        'id' => 'si-to-result' . $model['id'],
                                                                        'required' => 'required',
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
                                        $script = <<< JS
                                            easyNumberSeparator({
                                            selector: '#si-from' + $model[id],
                                            separator: ',',
                                            resultInput: '#si-from-result' + $model[id],
                                            })
                                            easyNumberSeparator({
                                            selector: '#si-to' + $model[id],
                                            separator: ',',
                                            resultInput: '#si-to-result' + $model[id],
                                            })
                                        JS;
                                        $this->registerJs($script);
                                        ?>

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
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::a(
                '<i class="fa fa-check"></i> Approve',
                [
                    'quotation/approve-reins',
                    'id' => $quotationModel->id,
                ],
                [
                    'class' => ($quotationModel->is_req_reas == 1)
                        ? 'btn btn-primary btn-lg waves-effect waves-light disabled'
                        : 'btn btn-primary btn-lg waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to approve this Reins?',
                    'data-method' => 'post',
                ]
            ); ?>
        </div>
    </div>
</div>

<?php
$script = <<< JS
    easyNumberSeparator({
      selector: '#si-from',
      separator: ',',
      resultInput: '#si-from-result',
    })
    easyNumberSeparator({
      selector: '#si-to',
      separator: ',',
      resultInput: '#si-to-result',
    })
JS;
$this->registerJs($script);
?>