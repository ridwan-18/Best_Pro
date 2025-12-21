<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\User;

$this->title = 'User - ' . Yii::$app->name;
?>
<div class="user-index">
    <div class="modal fade search-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
                </div>
                <div class="modal-body">
                    <?= Html::beginForm(['user/index'], 'get', ['id' => 'user-search-form']) ?>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <?= Html::input('text', 'username', Yii::$app->request->get('username'), [
                                    'class' => 'form-control',
                                    'id' => 'username',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <?= Html::input('text', 'phone', Yii::$app->request->get('phone'), [
                                    'class' => 'form-control',
                                    'id' => 'phone',
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <?= Html::input('text', 'email', Yii::$app->request->get('email'), [
                                    'class' => 'form-control',
                                    'id' => 'email',
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
            <h2 class="p-0 m-0">User</h2>
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
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
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
                                        <td><?= $model['username']; ?></td>
                                        <td><?= $model['name']; ?></td>
                                        <td><?= $model['email']; ?></td>
                                        <td><?= $model['phone']; ?></td>
                                        <td><?= User::roles($model['role']); ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a(
                                                    '<i class="fa fa-lock"></i>',
                                                    [
                                                        'user/reset-password',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'Reset Password',
                                                    ]
                                                ); ?>
                                                <?= Html::a(
                                                    '<i class="fa fa-pencil"></i>',
                                                    [
                                                        'user/update',
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
                                                        'user/delete',
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