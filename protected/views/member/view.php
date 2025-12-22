<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\widgets\Alert;
use app\models\Member;
use app\models\Utils;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;

$this->registerCssFile("@web/theme/plugins/tooltipster/tooltipster.bundle.min.css");
$this->registerJsFile(
    '@web/theme/plugins/tooltipster/tooltipster.bundle.min.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
$this->registerJsFile(
    '@web/theme/assets/pages/jquery.tooltipster.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

$statuses = Member::statuses();
$memberStatuses = Member::memberStatuses();
$reasStatuses = Member::reasStatuses();
$totalShows = Member::totalShows();
$accumulateOptions = Member::accumulateOptions();

$this->title = 'View Member - ' . Yii::$app->name;
?>

<div class="modal fade search-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Search</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['member/view'], 'get', ['id' => 'member-search-form']) ?>
                <?= Html::input('hidden', 'id', $batch->id) ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="member_id">Member</label>
                            <?= Html::dropDownList('member_id', Yii::$app->request->get('member_id'), ArrayHelper::map($personals, 'id', 'name'), [
                                'prompt' => '- Select Member -',
                                'id' => 'member_id',
                                'class' => 'form-control slct2',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <?= Html::input('text', 'start_date', Yii::$app->request->get('start_date'), [
                                'class' => 'form-control dtpckr',
                                'id' => 'start_date',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <?= Html::input('text', 'end_date', Yii::$app->request->get('end_date'), [
                                'class' => 'form-control dtpckr',
                                'id' => 'end_date',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <?= Html::dropDownList('status', Yii::$app->request->get('status'), $statuses, [
                                'prompt' => '- Select Status -',
                                'id' => 'status',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="member_status">Member Status</label>
                            <?= Html::dropDownList('member_status', Yii::$app->request->get('member_status'), $memberStatuses, [
                                'prompt' => '- Select Status -',
                                'id' => 'member_status',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="reas_status">Reas Status</label>
                            <?= Html::dropDownList('reas_status', Yii::$app->request->get('reas_status'), $reasStatuses, [
                                'prompt' => '- Select Reas Status -',
                                'id' => 'reas_status',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total_show">Show</label>
                            <?= Html::dropDownList(
                                'total_show',
                                (Yii::$app->request->get('total_show') == null) ? Member::PAGE_SIZE : Yii::$app->request->get('total_show'),
                                $totalShows,
                                [
                                    'id' => 'total_show',
                                    'class' => 'form-control',
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="is_accumulated">Is Accumulated?</label> <br>
                            <?= Html::checkbox('is_accumulated', Yii::$app->request->get('is_accumulated'), ['id' => 'is_accumulated']) ?>
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

<div class="modal fade member-upload-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload Member</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['member/upload-existing'], 'post', ['id' => 'member-upload-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'batch_id', $batch->id) ?>
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
                        <?= Html::a('<i class="fa fa-file-excel-o"></i> Template', ['template'], ['class' => 'btn btn-success waves-effect waves-light']); ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade print-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Print</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['member/print'], 'get', ['id' => 'member-print-form', 'target' => '_blank']) ?>
                <?= Html::input('hidden', 'id', $batch->id) ?>
                <?= Html::input('hidden', 'policy_no', $batch->policy_no) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="member_status">Member Status</label>
                            <?= Html::dropDownList('member_status', Yii::$app->request->get('member_status'), $memberStatuses, [
                                'prompt' => '- Select Status -',
                                'id' => 'member_status',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= Html::submitButton('<i class="fa fa-print"></i> Print', ['class' => 'btn btn-warning waves-effect waves-light']) ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade export-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Export</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['member/export'], 'get', ['id' => 'member-export-form']) ?>
                <?= Html::input('hidden', 'id', $batch->id) ?>
                <?= Html::input('hidden', 'policy_no', $batch->policy_no) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="member_status">Member Status</label>
                            <?= Html::dropDownList('member_status', Yii::$app->request->get('member_status'), $memberStatuses, [
                                'prompt' => '- Select Status -',
                                'id' => 'member_status',
                                'class' => 'form-control',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?= Html::submitButton('<i class="fa fa-file-excel-o"></i> Export', ['class' => 'btn btn-success waves-effect waves-light']) ?>
                    </div>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade policy-no-modal" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myMediumModalLabel">Upload Member</h4>
            </div>
            <div class="modal-body">
                <?= Html::beginForm(['member/update'], 'post', ['id' => 'update-form', 'enctype' => 'multipart/form-data']) ?>
                <?= Html::input('hidden', 'batch_id', $batch->id) ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="policy_no">Policy No</label>
                            <?= Html::input('text', 'policy_no', $batch->policy_no, [
                                'class' => 'form-control',
                                'id' => 'policy_no',
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



<div class="member-view">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">Member</h2>
            <h5 class="p-0 m-0"><?= $quotationModel->proposal_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a('<i class="fa fa-print"></i> Print', 'javascript:void(0)', [
                'class' => 'btn btn-warning waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.print-modal',
            ]); ?>
            <?= Html::a('<i class="fa fa-file-excel-o"></i> Export', 'javascript:void(0)', [
                'class' => 'btn btn-success waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.export-modal',
            ]); ?>
            <?= Html::a('<i class="fa fa-upload"></i> Upload', 'javascript:void(0)', [
                'class' => 'btn btn-primary waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.member-upload-modal',
            ]); ?>
			
		

			
			  <?php if ($user==1) { ?>
            <?= Html::a(
                '<i class="fa fa-check"></i> Approve',
                [
                    'member/approve',
                    'id' => $batch->id,
                ],
                [
                    'class' => 'btn btn-primary waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to approve this batch?',
                    'data-method' => 'post',
                ]
            ); ?>
			 <?php } ?>
			 
			 
			 
			 
            <?= Html::a('<i class="fa fa-search"></i> Search', 'javascript:void(0)', [
                'class' => 'btn btn-info waves-effect waves-light',
                'data-toggle' => 'modal',
                'data-target' => '.search-modal',
            ]); ?>
        </div>
    </div>

    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Policy No</td>
                        <td><?= $batch->policy_no; ?>
                            <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                'class' => 'btn btn-sm btn-info waves-effect waves-light',
                                'data-toggle' => 'modal',
                                'data-target' => '.policy-no-modal',
                            ]); ?></td>
                    </tr>
                    <tr>
                        <td>Policy Holder</td>
                        <td><?= $partner->name; ?></td>
                    </tr>
                    <tr>
                        <td>Product</td>
                        <td><?= $product->name; ?></td>
                    </tr>
                    <tr>
                        <td>Batch No</td>
                        <td><?= $batch->batch_no; ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><?= $batch->status; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Total Member</td>
                        <td><?= $batch->total_member; ?></td>
                    </tr>
                    <tr>
                        <td>Total Member Accepted</td>
                        <td><?= $batch->total_member_accepted; ?></td>
                    </tr>
                    <tr>
                        <td>Total Member Pending</td>
                        <td><?= $batch->total_member_pending; ?></td>
                    </tr>
                    <tr>
                        <td>Total UP</td>
                        <td><?= number_format($batch->total_up); ?></td>
                    </tr>
                    <tr>
                        <td>Gross Premium</td>
                        <td><?= number_format($batch->total_gross_premium); ?></td>
                    </tr>
                    <tr>
                        <td>Discount Premium</td>
                        <td><?= number_format($batch->total_discount_premium); ?></td>
                    </tr>
                    <tr>
                        <td>Extra Premium</td>
                        <td><?= number_format($batch->total_extra_premium); ?></td>
                    </tr>
                    <tr>
                        <td>Saving Premium</td>
                        <td><?= number_format($batch->total_saving_premium); ?></td>
                    </tr>
                    <tr>
                        <td>Nett Premium</td>
                        <td><?= number_format($batch->total_nett_premium); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="card-box">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover nowrap m-0">
                                <thead>
                                    <tr>
                                        <th width="1">#</th>
										<th>Id Loan</th>
                                        <th>Member No</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Term</th>
                                        <th>Sum Insured</th>
                                        <th>Rate Premi</th>
                                        <th>Gross Premium</th>
                                        <th>Percentage Disc (%)</th>
                                        <th>Disc Premium</th>
                                        <th>Nett Premium</th>
                                        <th>Medical Code</th>
                                        <th>Status</th>
                                        <th>Member Status</th>
                                        <th>Acc. Status</th>
                                        <th>Percent Extra Premium (%)</th>
                                        <th>Extra Premium</th>
                                        <th>Percent EM (%)</th>
                                        <th>EM Premium</th>
                                        <th>EM Notes</th>
                                        <th>UW Notes</th>
										<th>Upload FIle SPK</th>
										<th>Download FIle SPK</th>
                                        <th width="1">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = $pagination->offset + 1;
                                    if (!empty($members)) :
                                        foreach ($members as $member) :
                                    ?>
                                            <tr <?php
                                                if ($member['member_status'] == Member::MEMBER_STATUS_PENDING) {
                                                    echo 'class="table-warning"';
                                                } else if ($member['member_status'] == Member::MEMBER_STATUS_DECLINED) {
                                                    echo 'class="table-danger"';
                                                }
                                                ?>>
                                                <td><?= $i; ?></td>
												 <td><?= $member['id_loan']; ?></td>
                                                <td><?= $member['member_no']; ?></td>
                                                <td><?= $member['name']; ?></td>
                                                <td><?= Utils::convertDateTodMy($member['birth_date']); ?></td>
                                                <td><?= $member['gender']; ?></td>
                                                <td><?= $member['age']; ?></td>
                                                <td><?= Utils::convertDateTodMy($member['start_date']); ?></td>
                                                <td><?= Utils::convertDateTodMy($member['end_date']); ?></td>
                                                <td><?= $member['term']; ?></td>
                                                <td><?= number_format($member['sum_insured']); ?></td>
                                                <td><?= $member['rate_premi']; ?></td>
                                                <td><?= number_format($member['gross_premium']); ?></td>
                                                <td><?= $member['percentage_discount']; ?></td>
                                                <td><?= number_format($member['discount_premium']); ?></td>
                                                <td><?= number_format($member['nett_premium']); ?></td>
                                                <td><?= $member['medical_code']; ?></td>
                                                <td><?= $member['status']; ?></td>
                                                <td><?= $member['member_status']; ?></td>
                                                <td>
                                                    <?php
                                                    if ($member['acc_status'] != '') :
                                                    ?>
                                                        <a href="<?= Url::base() . '/member/accumulation/?id=' . $member['id']; ?>" onclick="window.open(this.href,'newwindow','width=800,height=800'); return false;" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="" data-original-title="View Accumulation">
                                                            <?= $member['acc_status']; ?>
                                                        </a>
                                                    <?php
                                                    endif;
                                                    ?>
                                                </td>
                                                <td><?= $member['percentage_extra_premium']; ?></td>
                                                <td><?= number_format($member['extra_premium']); ?></td>
                                                <td><?= $member['percentage_em']; ?></td>
                                                <td><?= number_format($member['em_premium']); ?></td>
                                                <td><?= nl2br($member['em_notes']); ?></td>
                                                <td><?= nl2br($member['uw_notes']); ?></td>
												
												
												 <td>
												 
												  <?php if ($member['medical_code']=='NM') { ?>
												 
												
												<?= Html::beginForm(['member/upload-existing'], 'post', [$member['id'] => 'member-upload-form', 'enctype' => 'multipart/form-data']) ?>
												<?= Html::input('hidden', 'id', $member['id'], [
                                                                'id' => 'id',
                                                                'required' => 'required',
                                                            ]) ?>
												<?= Html::input('hidden', 'batch_id', $batch->id) ?>
														
															<?= Html::input('file', 'files_medis', null, ['class' => 'form-control', 'required' => true]) ?>
												
												
													
														<?= Html::submitButton('<i class="fa fa-upload"></i> Upload', ['class' => 'btn btn-primary waves-effect waves-light', 'id' => 'upload-btn']) ?>
												
												<?= Html::endForm() ?>
											
											<?php } ?>
												 </td>
												 
												 
												   <td>
												   <a download href="<?= Url::base() . '/images/penghantar_medis/' . $member['file_medis']; ?>"><?= $member['file_medis']; ?></a>
												   </td>
												 
                                                <td>
												 <?php if ($user==1) { ?>
                                                    <div class="btn-group mb-2">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                            'class' => 'btn btn-light btn-sm waves-effect',
                                                            'title' => 'Update',
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#update-member-modal-' . $member['id'],
                                                        ]); ?>
                                                        <?= Html::a(
                                                            '<i class="fa fa-trash"></i>',
                                                            [
                                                                'member/delete-member',
                                                                'id' => $member['id'],
                                                            ],
                                                            [
                                                                'class' => 'btn btn-light btn-sm waves-effect',
                                                                'title' => 'Delete',
                                                                'data-confirm' => 'Are you sure want to delete?',
                                                                'data-method' => 'post',
                                                            ]
                                                        ); ?>
                                                    </div>
													 <?php } ?>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="update-member-modal-<?= $member['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h4 class="modal-title" id="myMediumModalLabel">Update Member #<?= $i; ?></h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?= Html::beginForm(['member/update-member'], 'post', ['id' => 'uw-update-form']) ?>
                                                            <?= Html::input('hidden', 'batch_id', $batch->id) ?>
                                                            <?= Html::input('hidden', 'id', $member['id'], [
                                                                'id' => 'id',
                                                                'required' => 'required',
                                                            ]) ?>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="member_no">Member No</label>
                                                                        <?= Html::input('text', 'member_no', $member['member_no'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'member_no',
                                                                            'value' => $member['member_no'],
                                                                            'disabled' => true
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="name">Name</label>
                                                                        <?= Html::input('text', 'name', $member['name'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'name',
                                                                            'required' => 'required',
                                                                            'value' => $member['name'],
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="birth_date">Date of Birth</label>
                                                                        <?= Html::input('text', 'birth_date', $member['birth_date'], [
                                                                            'class' => 'form-control dtpckr',
                                                                            'id' => 'birth_date',
                                                                            'required' => 'required',
                                                                            'value' => $member['birth_date']
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="start_date">Start Date</label>
                                                                        <?= Html::input('text', 'start_date', $member['start_date'], [
                                                                            'class' => 'form-control dtpckr',
                                                                            'id' => 'start_date',
                                                                            'required' => 'required',
                                                                            'value' => $member['start_date']
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="end_date">End Date</label>
                                                                        <?= Html::input('text', 'end_date', $member['end_date'], [
                                                                            'class' => 'form-control dtpckr',
                                                                            'id' => 'end_date',
                                                                            'required' => 'required',
                                                                            'value' => $member['end_date']
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="sum_insured">Sum Insured</label>
                                                                        <?= Html::input('number', 'sum_insured', $member['sum_insured'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'sum_insured',
                                                                            'required' => 'required',
                                                                            'value' => $member['sum_insured']
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="medical_code">Medical Code</label>
                                                                        <?= Html::input('text', 'medical_code', $member['medical_code'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'medical_code',
                                                                            'required' => 'required',
                                                                            'value' => $member['medical_code'],
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="status">Status</label>
                                                                        <?= Html::dropDownList('status', $member['status'], $statuses, [
                                                                            'prompt' => '- Select Status -',
                                                                            'id' => 'status',
                                                                            'class' => 'form-control',
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="member_status">Member Status</label>
                                                                        <?= Html::dropDownList('member_status', $member['member_status'], $memberStatuses, [
                                                                            'prompt' => '- Select Member Status -',
                                                                            'id' => 'member_status',
                                                                            'class' => 'form-control',
                                                                            'required' => 'required',
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="reas_status">Reas Status</label>
                                                                        <?= Html::dropDownList('reas_status', $member['reas_status'], $reasStatuses, [
                                                                            'prompt' => '- Select Reas Status -',
                                                                            'id' => 'reas_status',
                                                                            'class' => 'form-control',
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="percentage_em">EM (%)</label>
                                                                                <?= Html::input('number', 'percentage_em', $member['percentage_em'], [
                                                                                    'class' => 'form-control',
                                                                                    'id' => 'percentage_em',
                                                                                    'step' => 'any',
                                                                                    'value' => $member['percentage_em'],
                                                                                ]) ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="percentage_em">EM Rate</label>
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <?= Html::radio('em_type', ($member['em_type'] == Member::EM_MANUAL) ? true : false, [
                                                                                            'label' => 'Manual',
                                                                                            'value' => '1'
                                                                                        ]) ?>
                                                                                        <?= Html::radio('em_type', ($member['em_type'] == Member::EM_FROM_PRODUCT) ? true : false, [
                                                                                            'label' => 'From Product',
                                                                                            'value' => '2'
                                                                                        ]) ?>
                                                                                        <?= Html::input('number', 'rate_em', $member['rate_em'], [
                                                                                            'class' => 'form-control',
                                                                                            'id' => 'rate_em',
                                                                                            'step' => 'any',
                                                                                            'value' => $member['rate_em'],
                                                                                        ]) ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="em_notes">EM Notes</label>
                                                                        <?= Html::textarea('em_notes', $member['em_notes'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'em_notes',
                                                                            'value' => $member['em_notes'],
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="uw_notes">UW Notes</label>
                                                                        <?= Html::textarea('uw_notes', $member['uw_notes'], [
                                                                            'class' => 'form-control',
                                                                            'id' => 'uw_notes',
                                                                            'value' => $member['uw_notes'],
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
    </div>
</div>

<?php
$script = <<< JS
    $('#member-upload-form').submit(function() {
        $('#upload-btn').html('<i class="fa fa-spinner"></i> Loading');
        $('#upload-btn').attr('class', 'btn btn-primary waves-effect waves-light disabled');
    });
JS;
$this->registerJs($script);
?>