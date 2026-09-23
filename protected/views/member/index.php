
<?php

use yii\helpers\Html;
use app\widgets\Alert;
use yii\widgets\LinkPager;
use app\models\Batch;
use app\models\Member;

$statuses = Batch::statuses();

$this->title = 'Member - ' . Yii::$app->name;
?>

<div class="member-index">

    <!-- =========================
         SEARCH MODAL
    ========================== -->
    <div
        class="modal fade"
        id="search-modal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">
                        Search Member
                    </h4>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-hidden="true"
                    >
                        ×
                    </button>
                </div>

                <div class="modal-body">

                    <?= Html::beginForm(
                        ['member/index'],
                        'get',
                        ['id' => 'member-search-form']
                    ) ?>

                    <div class="row">

                        <!-- Policy No -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="policy_no">
                                    Policy No
                                </label>

                                <?= Html::input(
                                    'text',
                                    'policy_no',
                                    Yii::$app->request->get('policy_no'),
                                    [
                                        'class' => 'form-control',
                                        'id' => 'policy_no',
                                        'placeholder' => 'Enter Policy No',
                                    ]
                                ) ?>
                            </div>
                        </div>

                        <!-- Batch No -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="batch_no">
                                    Batch No
                                </label>

                                <?= Html::input(
                                    'text',
                                    'batch_no',
                                    Yii::$app->request->get('batch_no'),
                                    [
                                        'class' => 'form-control',
                                        'id' => 'batch_no',
                                        'placeholder' => 'Enter Batch No',
                                    ]
                                ) ?>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">
                                    Status
                                </label>

                                <?= Html::dropDownList(
                                    'status',
                                    Yii::$app->request->get('status'),
                                    $statuses,
                                    [
                                        'prompt' => '- Select Status -',
                                        'id' => 'status',
                                        'class' => 'form-control',
                                    ]
                                ) ?>
                            </div>
                        </div>

                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">

                            <?= Html::submitButton(
                                '<i class="fa fa-search"></i> Search',
                                [
                                    'class' => 'btn btn-primary waves-effect waves-light',
                                ]
                            ) ?>

                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                            >
                                Close
                            </button>

                        </div>
                    </div>

                    <?= Html::endForm() ?>

                </div>
            </div>
        </div>
    </div>


    <!-- =========================
         PAGE HEADER
    ========================== -->
    <div class="row mb-4 align-items-center">

        <div class="col-md-6">
            <h2 class="p-0 m-0">
                Member
            </h2>
        </div>

        <div class="col-md-6 text-right">

            <?= Html::a(
                '<i class="fa fa-upload"></i> Upload',
                ['create'],
                [
                    'class' => 'btn btn-primary waves-effect waves-light',
                ]
            ) ?>

            <?= Html::a(
                '<i class="fa fa-search"></i> Search',
                'javascript:void(0)',
                [
                    'class' => 'btn btn-info waves-effect waves-light',
                    'data-toggle' => 'modal',
                    'data-target' => '#search-modal',
                ]
            ) ?>

        </div>
    </div>


    <!-- =========================
         ALERT
    ========================== -->
    <?= Alert::widget() ?>


    <!-- =========================
         MEMBER TABLE
    ========================== -->
    <div class="row mt-4">
        <div class="col-12">

            <div class="card-box">

                <div class="table-responsive">

                    <table class="table table-hover nowrap m-0">

                        <thead>
                            <tr>

                                <th width="1">#</th>

                                <th>Policy No</th>

                                <th>Policy Holder</th>

                                <th>Branch Name</th>

                                <th>Batch No</th>

                                <th>Total Member</th>

                                <th>Status</th>

                                <th>Print Invoice</th>

                                <th>Created At</th>

                                <th width="1">Action</th>

                                <?php if ((int) $user->id === 1): ?>
                                    <th>Upload Invoice</th>
                                <?php endif; ?>

                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $i = $pagination->offset + 1;
                            ?>

                            <?php if (!empty($models)): ?>

                                <?php foreach ($models as $model): ?>

                                    <tr>

                                        <!-- No -->
                                        <td>
                                            <?= $i ?>
                                        </td>


                                        <!-- Policy No -->
                                        <td>
                                            <?= Html::encode($model['policy_no']) ?>
                                        </td>


                                        <!-- Policy Holder -->
                                        <td>
                                            <?= Html::encode($model['partner']) ?>
                                        </td>


                                        <!-- Branch Name -->
                                        <td>
                                            <?= Html::encode($model['name']) ?>
                                        </td>


                                        <!-- Batch No -->
                                        <td>
                                            <?= Html::encode($model['batch_no']) ?>
                                        </td>


                                        <!-- Total Member -->
                                        <td>
                                            <?= Html::encode($model['total_member']) ?>
                                        </td>


                                        <!-- Status -->
                                        <td>
                                            <?= Html::encode($model['status']) ?>
                                        </td>


                                        <!-- Print Invoice -->
                                        <td>

                                            <?php if (!empty($model['files'])): ?>

                                                <?= Html::a(
                                                    '<i class="fa fa-file-pdf-o"></i> Open',
                                                    $model['files'],
                                                    [
                                                        'target' => '_blank',
                                                        'rel' => 'noopener noreferrer',
                                                        'class' => 'btn btn-sm btn-primary',
                                                    ]
                                                ) ?>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    -
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- Created At -->
                                        <td>
                                            <?= Html::encode($model['created_at']) ?>
                                        </td>


                                        <!-- Action -->
                                        <td>

                                            <div class="btn-group">

                                                <?= Html::a(
                                                    '<i class="fa fa-search"></i>',
                                                    [
                                                        'member/view',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'View',
                                                        'data-toggle' => 'tooltip',
                                                    ]
                                                ) ?>

                                                <?= Html::a(
                                                    '<i class="fa fa-trash"></i>',
                                                    [
                                                        'member/delete',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'Delete',
                                                        'data-toggle' => 'tooltip',
                                                        'data-confirm' => 'Are you sure want to delete?',
                                                        'data-method' => 'post',
                                                    ]
                                                ) ?>

                                            </div>

                                        </td>


                                        <!-- Upload Invoice -->
                                        <?php if ((int) $user->id === 1): ?>

                                            <td>

                                                <?php if ($model['status'] === 'CLOSED'): ?>

                                                    <?= Html::beginForm(
                                                        ['member/upload-invoice'],
                                                        'post',
                                                        [
                                                            'enctype' => 'multipart/form-data',
                                                            'id' => 'member-upload-form-' . $model['id'],
                                                        ]
                                                    ) ?>

                                                        <?= Html::hiddenInput(
                                                            'batch_id',
                                                            $model['id']
                                                        ) ?>

                                                        <div class="input-group">

                                                            <?= Html::fileInput(
                                                                'files_medis',
                                                                null,
                                                                [
                                                                    'class' => 'form-control',
                                                                    'required' => true,
                                                                    'accept' => '.pdf',
                                                                ]
                                                            ) ?>

                                                        </div>

                                                        <div class="mt-2">

                                                            <?= Html::submitButton(
                                                                '<i class="fa fa-upload"></i> Upload',
                                                                [
                                                                    'class' => 'btn btn-primary btn-sm',
                                                                ]
                                                            ) ?>

                                                        </div>

                                                    <?= Html::endForm() ?>

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        -
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        <?php endif; ?>

                                    </tr>

                                    <?php $i++; ?>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td
                                        colspan="100"
                                        class="text-center text-muted"
                                    >
                                        No data
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>


                <!-- =========================
                     PAGINATION
                ========================== -->
                <?= LinkPager::widget([
                    'pagination' => $pagination,
                    'disabledPageCssClass' => 'page-link',
                    'options' => [
                        'class' => 'pagination pagination-split mb-0 mt-4',
                    ],
                    'linkContainerOptions' => [
                        'class' => 'page-item',
                    ],
                    'linkOptions' => [
                        'class' => 'page-link',
                    ],
                ]) ?>

            </div>
        </div>
    </div>

</div>
