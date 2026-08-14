<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use app\widgets\Alert;
use app\models\Utils;
use app\models\Member;
use app\models\Personal;
use app\models\Partner;
use app\models\Policy;
use app\models\Product;
use app\models\QuotationProduct;
use app\models\Claim;
use app\models\ClaimReason;
use app\models\Disease;
use app\models\PlaceOfDeath;
use app\models\Component;
use app\models\Document;


$members = Member::find()
    ->asArray()
    ->select([
        Member::tableName() . '.member_no',
        Personal::tableName() . '.name'
    ])
    ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' .  Member::tableName() . '.personal_no')
    ->where([Member::tableName() . '.status' => Member::STATUS_INFORCE])
    ->orderBy([Member::tableName() . '.id' => SORT_ASC])
    ->all();

$options = [];
foreach ($members as $member) {
    $items = [];
    $items['value'] = $member['member_no'];
    $items['label'] = $member['member_no'] . ' - ' . $member['name'];
    $options[] = $items;
}

$members = ArrayHelper::map($options, 'value', 'label');

$claimReasons = ClaimReason::getAll();
$claimReasons = ArrayHelper::map($claimReasons, 'name', 'name');

$diseases = Disease::getAll();
$diseases = ArrayHelper::map($diseases, 'name', 'name');

$placeOfDeaths = PlaceOfDeath::getAll();
$placeOfDeaths = ArrayHelper::map($placeOfDeaths, 'name', 'name');

?>
<div class="alteration-cancel-create">

    <?= Html::beginForm(['claim/create'], 'post', [
        'id' => 'main-form',
        'enctype' => 'multipart/form-data'
    ]) ?>

    <!-- HEADER -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Create Claim</h3>
        </div>
        <div class="col-md-6">
            <?= Html::dropDownList('member_no', $selectedMemberNo, $members, [
                'prompt' => '- Select Member -',
                'class' => 'form-control',
                'onchange' => 'this.form.submit()'
            ]) ?>
        </div>
    </div>

    <?php if (!empty($selectedMemberNo)): ?>

        <?= Html::hiddenInput('submit_claim', 1) ?>

        <!-- MEMBER INFO -->
        <div class="card-box mb-4 p-3 border rounded">
            <div class="row">
                <div class="col-md-6">
                    <p><b>Policy:</b> <?= $policy->policy_no ?></p>
                    <p><b>Policy Holder:</b> <?= $partner->name ?></p>
                    <p><b>Product:</b> <?= $product->name ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Member No:</b> <?= $get_member->member_no ?></p>
                    <p><b>Name:</b> <?= $personal->name ?></p>
                    <p><b>DOB:</b> <?= $personal->birth_date ?></p>
                    <p><b>Age:</b> <?= $get_member->age ?></p>
                </div>
            </div>
        </div>

        <!-- CLAIM INFO -->
        <div class="card-box mb-4 p-3 border rounded">
            <h5 class="mb-3">Claim Info</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Incident Date</label>
                    <?= Html::textInput('incident_date', null, [
                        'class' => 'form-control',
                        'required' => true
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Claim Reason</label>
                    <?= Html::dropDownList('claim_reason', null, $claimReasons, [
                        'class' => 'form-control',
                        'prompt' => 'Select Claim Reason',
                        'required' => true
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Disease</label>
                    <?= Html::dropDownList('disease', null, $diseases, [
                        'class' => 'form-control',
                        'prompt' => 'Select Disease',
                        'required' => true
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Place of Death</label>
                    <?= Html::dropDownList('place_of_death', null, $placeOfDeaths, [
                        'class' => 'form-control',
                        'prompt' => 'Select Place',
                        'required' => true
                    ]) ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Estimated Amount</label>
                    <?= Html::textInput('estimated_amount', null, [
                        'class' => 'form-control',
                        'id' => 'estimated-amount',
                        'placeholder' => '0'
                    ]) ?>
                </div>
            </div>
        </div>

        <!-- DOCUMENT -->
        <div class="card-box mb-4 p-3 border rounded">
            <h5 class="mb-3">Documents</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Status</label>
                    <?= Html::dropDownList('doc_status', null, [
                        0 => 'Pending',
                        1 => 'Complete'
                    ], ['class' => 'form-control']) ?>
                </div>

                <div class="col-md-12 mb-3">
                    <label>Notes</label>
                    <?= Html::textarea('doc_notes', null, [
                        'class' => 'form-control',
                        'rows' => 3
                    ]) ?>
                </div>
            </div>

            <table class="table table-bordered mt-3">
                <thead class="thead-light">
                    <tr>
                        <th style="width:50px;">No</th>
                        <th>Document</th>
                        <th>Upload</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($dokumen_detail)): ?>
                    <?php foreach ($dokumen_detail as $i => $dd): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $dd['nama_dokument'] ?></td>
                            <td>
                                <?= Html::hiddenInput('doc_ids[]', $dd['id']) ?>
                                <?= Html::fileInput('documents[]', null, [
                                    'class' => 'form-control'
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- SUBMIT -->
        <div class="text-end">
            <?= Html::submitButton('Save Claim', [
                'class' => 'btn btn-success btn-lg'
            ]) ?>
        </div>

    <?php endif; ?>

    <?= Html::endForm() ?>
</div>

<?php
$this->registerJs("
    easyNumberSeparator({
        selector: '#estimated-amount',
        separator: ','
    });
");
?>