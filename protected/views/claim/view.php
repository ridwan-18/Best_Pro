<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\widgets\Alert;
use app\models\ClaimReason;
use app\models\Disease;
use app\models\PlaceOfDeath;
use app\models\Claim;
use app\models\ClaimDocument;
use app\models\Document;
use app\models\Bank;
use app\models\Utils;

$this->registerJsFile(
    '@web/theme/assets/js/easy-number-separator.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

$claimReasons = ClaimReason::getAll();
$claimReasons = ArrayHelper::map($claimReasons, 'name', 'name');

$diseases = Disease::getAll();
$diseases = ArrayHelper::map($diseases, 'name', 'name');

$placeOfDeaths = PlaceOfDeath::getAll();
$placeOfDeaths = ArrayHelper::map($placeOfDeaths, 'name', 'name');

$statuses = Claim::statuses();
$decisions = Claim::decisions();
$results = Claim::results();
$approvals = Claim::approvals();

$documents = ClaimDocument::find()
    ->asArray()
    ->select([
        ClaimDocument::tableName() . '.id',
        ClaimDocument::tableName() . '.is_checked',
        ClaimDocument::tableName() . '.is_mandatory',
        Document::tableName() . '.name',
    ])
    ->innerJoin(Document::tableName(), Document::tableName() . '.id = ' . ClaimDocument::tableName() . '.document_id ')
    ->where(['claim_id' => $model->id])
    ->all();

$banks = ArrayHelper::map(Bank::getAll(), 'name', 'name');

$this->title = 'View Claim - ' . Yii::$app->name;
?>

<div class="member-view">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">Claim</h2>
            <h5 class="p-0 m-0"><?= $model->claim_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a(
                '<i class="fa fa-print"></i> Print',
                [
                    'claim/print',
                    'id' => $model->id,
                ],
                [
                    'class' => 'btn btn-warning waves-effect waves-light',
                    'target' => 'blank'
                ]
            ); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <?= Html::beginForm(['claim/update', 'id' => $model->id], 'post', ['id' => 'claim-form']) ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td width="200">Policy No</td>
                        <td><?= $policy->policy_no; ?></td>
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
                        <td>Status</td>
                        <td>
                            <?= Html::dropDownList('status', $model->status, $statuses, [
                                'prompt' => '- Select Status -',
                                'id' => 'status',
                                'class' => 'form-control',
                            ]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Decision</td>
                        <td>
                            <?= Html::dropDownList('decision', $model->decision, $decisions, [
                                'prompt' => '- Select Decision -',
                                'id' => 'decision',
                                'class' => 'form-control',
                            ]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td>
                            <?= Html::textarea('remarks', $model->remarks, [
                                'class' => 'form-control',
                                'id' => 'remarks',
                            ]) ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td width="200">Member No</td>
                        <td><?= $model->member_no; ?></td>
                    </tr>
                    <tr>
                        <td>Member Name</td>
                        <td><?= $personal->name; ?></td>
                    </tr>
                    <tr>
                        <td>Date of Birth</td>
                        <td><?= Utils::convertDateTodMy($personal->birth_date); ?></td>
                    </tr>
                    <tr>
                        <td>Entry Age</td>
                        <td><?= $member->age; ?></td>
                    </tr>
                    <tr>
                        <td>Effective Date</td>
                        <td><?= Utils::convertDateTodMy($member->start_date); ?> <br> <?= Utils::convertDateTodMy($member->end_date); ?></td>
                    </tr>
                    <tr>
                        <td>Sum Insured</td>
                        <td><?= number_format($member->sum_insured); ?></td>
                    </tr>
                    <tr>
                        <td>Premium</td>
                        <td><?= number_format($member->total_premium); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="card-box">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills navtab-bg nav-justified pull-in">
                    <li class="nav-item">
                        <a href="#claim-info" data-toggle="tab" aria-expanded="tru" class="nav-link active">
                            Claim <br> Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#claim-document" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Claim <br> Document
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#payment-plan" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Payment <br> Plan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#history-payment-info" data-toggle="tab" aria-expanded="false" class="nav-link">
                            History <br> Payment Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#analyst1" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Analyst
                            <br>
                            <br>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#approval-investigation" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Approval <br> Investigation
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#approval-process" data-toggle="tab" aria-expanded="false" class="nav-link">
                            Approval <br> Process
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="claim-info">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td width="200">Benefit</td>
                                        <td><?= $component->name; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Estimated Amount</td>
                                        <td>
                                            <?= Html::input('text', null, $model->estimated_amount, [
                                                'class' => 'form-control',
                                                'id' => 'estimated-amount',
                                                'step' => 'any',
                                            ]) ?>
                                            <?= Html::input('hidden', 'estimated_amount', $model->estimated_amount, [
                                                'id' => 'estimated-amount-result',
                                            ]) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Claim Age</td>
                                        <td><?= $model->claim_age; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="incident_date">Incident Date</label>
                                    <?= Html::input('text', 'incident_date', $model->incident_date, [
                                        'class' => 'form-control dtpckr',
                                        'id' => 'incident_date',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="claim_reason">Claim Reason</label>
                                    <?= Html::dropDownList('claim_reason', $model->claim_reason, $claimReasons, [
                                        'prompt' => '- Select Claim Reason -',
                                        'id' => 'claim_reason',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="disease">Disease</label>
                                    <?= Html::dropDownList('disease', $model->disease, $diseases, [
                                        'prompt' => '- Select Disease -',
                                        'id' => 'disease',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="place_of_death">Place of Death</label>
                                    <?= Html::dropDownList('place_of_death', $model->place_of_death, $placeOfDeaths, [
                                        'prompt' => '- Select Place of Death -',
                                        'id' => 'place_of_death',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="claim-document">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="doc_pre_received_date">Pre Received Date</label>
                                    <?= Html::input('text', 'doc_pre_received_date', $model->doc_pre_received_date, [
                                        'class' => 'form-control dtpckr',
                                        'id' => 'doc_pre_received_date',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="doc_received_date">Document Received Date</label>
                                    <?= Html::input('text', 'doc_received_date', $model->doc_received_date, [
                                        'class' => 'form-control dtpckr',
                                        'id' => 'doc_received_date',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="doc_status">Status</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= Html::radio('doc_status', ($model->doc_status == Claim::DOC_STATUS_PENDING) ? true : false, [
                                                'label' => Claim::DOC_STATUS_PENDING,
                                                'value' => Claim::DOC_STATUS_PENDING
                                            ]) ?>
                                            <?= Html::radio('doc_status', ($model->doc_status == Claim::DOC_STATUS_COMPLETE) ? true : false, [
                                                'label' => Claim::DOC_STATUS_COMPLETE,
                                                'value' => Claim::DOC_STATUS_COMPLETE
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="doc_complete_date">Complete Date</label>
                                    <?= Html::input('text', 'doc_complete_date', $model->doc_complete_date, [
                                        'class' => 'form-control dtpckr',
                                        'id' => 'doc_complete_date',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="doc_notes">Notes</label>
                                    <?= Html::textarea('doc_notes', $model->doc_notes, [
                                        'class' => 'form-control',
                                        'id' => 'doc_notes',
                                        'value' => $model->doc_notes,
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table class="table table-hover nowrap m-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center">
                                                    Claim Item <br><br>
                                                    <?= Html::checkbox(null, false, ['id' => 'bulk-checked']) ?>
                                                </th>
                                                <th class="text-center">
                                                    Mandatory <br><br>
                                                    <?= Html::checkbox(null, false, ['id' => 'bulk-mandatory']) ?>
                                                </th>
                                                <th class="text-center">Document <br><br></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($documents as $document) :
                                            ?>
                                                <?= Html::input('hidden', 'claim_document_ids[]', $document['id']) ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <?= Html::checkbox(
                                                            'is_checkeds[]',
                                                            $document['id'],
                                                            [
                                                                'class' => 'checked',
                                                                'checked' => ($document['is_checked'] != null) ? true : false,
                                                                'value' => $document['id']
                                                            ]
                                                        ) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= Html::checkbox(
                                                            'is_mandatories[]',
                                                            $document['id'],
                                                            [
                                                                'class' => 'mandatory',
                                                                'checked' => ($document['is_mandatory'] != null) ? true : false,
                                                                'value' => $document['id']
                                                            ]
                                                        ) ?>
                                                    </td>
                                                    <td><?= $document['name']; ?></td>
                                                </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="payment-plan">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_due_date">SLA / Due Date</label>
                                    <?= Html::input('text', 'payment_due_date', $model->payment_due_date, [
                                        'class' => 'form-control dtpckr',
                                        'id' => 'payment_due_date',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label>Amount by System</label>
                                    <?= Html::input('text', null, number_format($model->estimated_amount), [
                                        'class' => 'form-control',
                                        'disabled' => true,
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="claim_amount">Claim Amount</label>
                                    <?= Html::input('text', null, $model->claim_amount, [
                                        'class' => 'form-control',
                                        'id' => 'claim-amount',
                                        'step' => 'any',
                                    ]) ?>
                                    <?= Html::input('hidden', 'claim_amount', $model->claim_amount, [
                                        'id' => 'claim-amount-result',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="cash_value">Cash Value</label>
                                    <?= Html::input('text', null, $model->cash_value, [
                                        'class' => 'form-control',
                                        'id' => 'cash-value',
                                        'step' => 'any',
                                    ]) ?>
                                    <?= Html::input('hidden', 'cash_value', $model->cash_value, [
                                        'id' => 'cash-value-result',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doc_status">Transfer Type</label>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= Html::radio('transfer_type', ($model->transfer_type == Claim::TRANSFER_TYPE_POLICY) ? true : false, [
                                                'label' => Claim::TRANSFER_TYPE_POLICY,
                                                'value' => Claim::TRANSFER_TYPE_POLICY
                                            ]) ?>
                                            <?= Html::radio('transfer_type', ($model->transfer_type == Claim::TRANSFER_TYPE_MEMBER) ? true : false, [
                                                'label' => Claim::TRANSFER_TYPE_MEMBER,
                                                'value' => Claim::TRANSFER_TYPE_MEMBER
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="bank_name">Bank</label>
                                    <?= Html::dropDownList('bank_name', $model->bank_name, $banks, [
                                        'prompt' => '- Select Bank -',
                                        'id' => 'bank_name',
                                        'class' => 'form-control slct2',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="account_no">Account No</label>
                                    <?= Html::input('text', 'account_no', $model->account_no, [
                                        'class' => 'form-control',
                                        'id' => 'account_no',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="account_name">Account Name</label>
                                    <?= Html::input('text', 'account_name', $model->account_name, [
                                        'class' => 'form-control',
                                        'id' => 'account_name',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="history-payment-info">
                        history payment info
                    </div>
                    <div class="tab-pane" id="analyst1">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="analyst1_diagnosed_by">Diagnosed By</label>
                                    <?= Html::input('text', 'analyst1_diagnosed_by', $model->analyst1_diagnosed_by, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_diagnosed_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_diagnose_notes">Diagnose Notes</label>
                                    <?= Html::textarea('analyst1_diagnose_notes', $model->analyst1_diagnose_notes, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_diagnose_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_historical_disease">Historical Disease</label>
                                    <?= Html::textarea('analyst1_historical_disease', $model->analyst1_historical_disease, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_historical_disease',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_information">Information</label>
                                    <?= Html::textarea('analyst1_information', $model->analyst1_information, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_information',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_investigation_by_phone">Investigation By Phone</label>
                                    <?= Html::textarea('analyst1_investigation_by_phone', $model->analyst1_investigation_by_phone, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_investigation_by_phone',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_medical_analysis">Medical Analysis</label>
                                    <?= Html::textarea('analyst1_medical_analysis', $model->analyst1_medical_analysis, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_medical_analysis',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="analyst1_result1">Result 1</label>
                                    <?= Html::dropDownList('analyst1_result1', $model->analyst1_result1, $results, [
                                        'prompt' => '- Select Result -',
                                        'id' => 'analyst1_result1',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_recommendation1">Recommendation 1</label>
                                    <?= Html::textarea('analyst1_recommendation1', $model->analyst1_recommendation1, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_recommendation1',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_result2">Result 2</label>
                                    <?= Html::dropDownList('analyst1_result2', $model->analyst1_result2, $results, [
                                        'prompt' => '- Select Result -',
                                        'id' => 'analyst1_result2',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="analyst1_recommendation2">Recommendation 2</label>
                                    <?= Html::textarea('analyst1_recommendation2', $model->analyst1_recommendation2, [
                                        'class' => 'form-control',
                                        'id' => 'analyst1_recommendation2',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="approval-investigation">
                        <div class="row">
                            <div class="col-md-6 mb-5">
                                <h4>Head Department</h4>
                                <div class="form-group">
                                    <label for="dept_approved_by">By</label>
                                    <?= Html::input('text', 'dept_approved_by', $model->dept_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'dept_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dept_approve_notes">Notes</label>
                                    <?= Html::textarea('dept_approve_notes', $model->dept_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'dept_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dept_approve_status">Status</label>
                                    <?= Html::dropDownList('dept_approve_status', $model->dept_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'dept_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head Division</h4>
                                <div class="form-group">
                                    <label for="div_approved_by">By</label>
                                    <?= Html::input('text', 'div_approved_by', $model->div_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'div_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="div_approve_notes">Notes</label>
                                    <?= Html::textarea('div_approve_notes', $model->div_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'div_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="div_approve_status">Status</label>
                                    <?= Html::dropDownList('div_approve_status', $model->div_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'div_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head General Manager</h4>
                                <div class="form-group">
                                    <label for="gm_approved_by">By</label>
                                    <?= Html::input('text', 'gm_approved_by', $model->gm_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'gm_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="gm_approve_notes">Notes</label>
                                    <?= Html::textarea('gm_approve_notes', $model->gm_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'gm_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="gm_approve_status">Status</label>
                                    <?= Html::dropDownList('gm_approve_status', $model->gm_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'gm_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head Director I</h4>
                                <div class="form-group">
                                    <label for="dir1_approved_by">By</label>
                                    <?= Html::input('text', 'dir1_approved_by', $model->dir1_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'dir1_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir1_approve_notes">Notes</label>
                                    <?= Html::textarea('dir1_approve_notes', $model->dir1_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'dir1_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir1_approve_status">Status</label>
                                    <?= Html::dropDownList('dir1_approve_status', $model->dir1_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'dir1_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head Director II</h4>
                                <div class="form-group">
                                    <label for="dir2_approved_by">By</label>
                                    <?= Html::input('text', 'dir2_approved_by', $model->dir2_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'dir2_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir2_approve_notes">Notes</label>
                                    <?= Html::textarea('dir2_approve_notes', $model->dir2_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'dir2_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir2_approve_status">Status</label>
                                    <?= Html::dropDownList('dir2_approve_status', $model->dir2_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'dir2_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="approval-process">
                        <div class="row">
                            <div class="col-md-6 mb-5">
                                <h4>Head Department</h4>
                                <div class="form-group">
                                    <label for="dept_process_approved_by">By</label>
                                    <?= Html::input('text', 'dept_process_approved_by', $model->dept_process_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'dept_process_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dept_process_approve_notes">Notes</label>
                                    <?= Html::textarea('dept_process_approve_notes', $model->dept_process_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'dept_process_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dept_process_approve_status">Status</label>
                                    <?= Html::dropDownList('dept_process_approve_status', $model->dept_process_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'dept_process_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head Division</h4>
                                <div class="form-group">
                                    <label for="div_process_approved_by">By</label>
                                    <?= Html::input('text', 'div_process_approved_by', $model->div_process_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'div_process_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="div_process_approve_notes">Notes</label>
                                    <?= Html::textarea('div_process_approve_notes', $model->div_process_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'div_process_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="div_process_approve_status">Status</label>
                                    <?= Html::dropDownList('div_process_approve_status', $model->div_process_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'div_process_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head General Manager</h4>
                                <div class="form-group">
                                    <label for="gm_process_approved_by">By</label>
                                    <?= Html::input('text', 'gm_process_approved_by', $model->gm_process_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'gm_process_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="gm_process_approve_notes">Notes</label>
                                    <?= Html::textarea('gm_process_approve_notes', $model->gm_process_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'gm_process_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="gm_process_approve_status">Status</label>
                                    <?= Html::dropDownList('gm_process_approve_status', $model->gm_process_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'gm_process_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head Director I</h4>
                                <div class="form-group">
                                    <label for="dir1_process_approved_by">By</label>
                                    <?= Html::input('text', 'dir1_process_approved_by', $model->dir1_process_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'dir1_process_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir1_process_approve_notes">Notes</label>
                                    <?= Html::textarea('dir1_process_approve_notes', $model->dir1_process_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'dir1_process_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir1_process_approve_status">Status</label>
                                    <?= Html::dropDownList('dir1_process_approve_status', $model->dir1_process_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'dir1_process_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                            <div class="col-md-6 mb-5">
                                <h4>Head Director II</h4>
                                <div class="form-group">
                                    <label for="dir2_process_approved_by">By</label>
                                    <?= Html::input('text', 'dir2_process_approved_by', $model->dir2_process_approved_by, [
                                        'class' => 'form-control',
                                        'id' => 'dir2_process_approved_by',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir2_process_approve_notes">Notes</label>
                                    <?= Html::textarea('dir2_process_approve_notes', $model->dir2_process_approve_notes, [
                                        'class' => 'form-control',
                                        'id' => 'dir2_process_approve_notes',
                                    ]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="dir2_process_approve_status">Status</label>
                                    <?= Html::dropDownList('dir2_process_approve_status', $model->dir2_process_approve_status, $approvals, [
                                        'prompt' => '- Select Status -',
                                        'id' => 'dir2_process_approve_status',
                                        'class' => 'form-control',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light']) ?>
        </div>
    </div>
    <?= Html::endForm() ?>
</div>

<?php
$script = <<< JS
    easyNumberSeparator({
      selector: '#estimated-amount',
      separator: ',',
      resultInput: '#estimated-amount-result',
    })
    easyNumberSeparator({
      selector: '#claim-amount',
      separator: ',',
      resultInput: '#claim-amount-result',
    })
    easyNumberSeparator({
      selector: '#cash-value',
      separator: ',',
      resultInput: '#cash-value-result',
    })

    // Bulk Checked
    $("#bulk-checked").change(function(){ 
        $(".checked").prop('checked', $(this).prop("checked"));
    });
    $('.checked').change(function(){ 
        if(false == $(this).prop("checked")){
            $("#bulk-checked").prop('checked', false);
        }
        if ($('.checked:checked').length == $('.checked').length ){
            $("#bulk-checked").prop('checked', true);
        }
    });

    // Bulk Mandatory
    $("#bulk-mandatory").change(function(){ 
        $(".mandatory").prop('checked', $(this).prop("checked"));
    });
    $('.mandatory').change(function(){ 
        if(false == $(this).prop("checked")){
            $("#bulk-mandatory").prop('checked', false);
        }
        if ($('.mandatory:checked').length == $('.mandatory').length ){
            $("#bulk-mandatory").prop('checked', true);
        }
    });
JS;
$this->registerJs($script);
?>