<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;

$this->title = 'Global Reas - ' . Yii::$app->name;
?>
<div class="global-reas-index">
    <div class="modal fade search-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['global-reas/index'], 'get', ['id' => 'global-reas-search-form']) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <?= Html::input('text', 'name', Yii::$app->request->get('name'), [
                                    'class' => 'form-control',
                                    'id' => 'name',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary waves-effect waves-light']) ?>
                        </div>
                    </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Global Reas</h2>
        </div>
        <div class="col-md-6 text-right">
            <?= Html::a('<i class="fa fa-plus"></i> Create', ['create'], ['class' => 'btn btn-warning waves-effect waves-light']); ?>
            <?= Html::a('<i class="fa fa-search"></i> Search', 'javascript:void(0)', [
                'class' => 'btn btn-info waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.search-modal',
            ]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-box">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Reassuradur</th>
                                <th>PKS No</th>
                                <th>Cover Note</th>
                                <th>Reas Type</th>
                                <th>Reas Method</th>
                                <th>Rate Period </th>
                                <th>Rate Type</th>
                                <th>Effective Date</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = $pagination->offset + 1;
                            if (!empty($models)) :
                                foreach ($models as $model) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['reassuradur']; ?></td>
                                        <td><?= $model['pks_no']; ?></td>
                                        <td><?= $model['cover_note']; ?></td>
                                        <td><?= $model['reas_type']; ?></td>
                                        <td><?= $model['reas_method']; ?></td>
                                        <td><?= $model['rate_period']; ?></td>
                                        <td><?= $model['rate_type']; ?></td>
                                        <td><?= $model['effective_date']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a(
                                                    '<i class="fa fa-pencil"></i>',
                                                    [
                                                        'global-reas/update',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'Update',
                                                    ]
                                                ); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'global-reas/delete',
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