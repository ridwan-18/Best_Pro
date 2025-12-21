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

$this->registerJsFile(
    '@web/theme/assets/js/easy-number-separator.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

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

$documents = Document::getAll();

$this->title = 'Create Claim - ' . Yii::$app->name;
?>

<div class="alteration-cancel-create">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Create Claim</h2>
        </div>
        <div class="col-md-6 my-auto">
            <?= Html::beginForm(['claim/create'], 'post', ['id' => 'claim-form', 'class' => 'form-inline']) ?>
            <?= Html::dropDownList('member_no', Yii::$app->request->post('member_no'), $members, [
                'prompt' => '- Select Member -',
                'id' => 'member_no',
                'class' => 'form-control slct2',
                'required' => 'required',
                'onchange' => 'submit()',
            ]) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <?php
    if (Yii::$app->request->post('member_no') != '') :
        $member = Member::findOne(['member_no' => Yii::$app->request->post('member_no')]);
        $personal = Personal::findOne(['personal_no' => $member->personal_no]);
        $policy = Policy::findOne(['policy_no' => $member->policy_no]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);
        $quotationProduct = QuotationProduct::findOne(['quotation_id' => $policy->quotation_id]);
        $product = Product::findOne(['id' => $quotationProduct->product_id]);
        $component = Component::findOne(['product_id' => $product->id]);
    ?>
        <?= Html::beginForm(['claim/create'], 'post', ['id' => 'claim-form']) ?>
        <?= Html::input('hidden', 'member_no', $member->member_no) ?>
        <?= Html::input('hidden', 'policy_no', $member->policy_no) ?>
        <div class="card-box">
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
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <td width="200">Member No</td>
                            <td><?= $member->member_no; ?></td>
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
                <div class="col-md-4">
                    <ul class="nav nav-pills navtab-bg nav-justified pull-in">
                        <li class="nav-item">
                            <a href="#claim-info" data-toggle="tab" aria-expanded="tru" class="nav-link active">
                                Claim Info
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#claim-document" data-toggle="tab" aria-expanded="false" class="nav-link">
                                Claim Document
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
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
                                        <?= Html::input('text', null, null, [
                                            'class' => 'form-control',
                                            'id' => 'estimated-amount',
                                            'step' => 'any',
                                            'required' => 'required',
                                        ]) ?>
                                        <?= Html::input('hidden', 'estimated_amount', null, [
                                            'id' => 'estimated-amount-result',
                                            'required' => 'required',
                                        ]) ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incident_date">Incident Date</label>
                                <?= Html::input('text', 'incident_date', null, [
                                    'class' => 'form-control dtpckr',
                                    'id' => 'incident_date',
                                    'required' => 'required',
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label for="claim_reason">Claim Reason</label>
                                <?= Html::dropDownList('claim_reason', null, $claimReasons, [
                                    'prompt' => '- Select Claim Reason -',
                                    'id' => 'claim_reason',
                                    'class' => 'form-control',
                                    'required' => 'required',
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label for="disease">Disease</label>
                                <?= Html::dropDownList('disease', null, $diseases, [
                                    'prompt' => '- Select Disease -',
                                    'id' => 'disease',
                                    'class' => 'form-control',
                                    'required' => 'required',
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label for="place_of_death">Place of Death</label>
                                <?= Html::dropDownList('place_of_death', null, $placeOfDeaths, [
                                    'prompt' => '- Select Place of Death -',
                                    'id' => 'place_of_death',
                                    'class' => 'form-control',
                                    'required' => 'required',
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
                                <?= Html::input('text', 'doc_pre_received_date', null, [
                                    'class' => 'form-control dtpckr',
                                    'id' => 'doc_pre_received_date',
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label for="doc_received_date">Document Received Date</label>
                                <?= Html::input('text', 'doc_received_date', null, [
                                    'class' => 'form-control dtpckr',
                                    'id' => 'doc_received_date',
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label for="doc_status">Status</label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= Html::radio('doc_status', true, [
                                            'label' => Claim::DOC_STATUS_PENDING,
                                            'value' => Claim::DOC_STATUS_PENDING
                                        ]) ?>
                                        <?= Html::radio('doc_status', false, [
                                            'label' => Claim::DOC_STATUS_COMPLETE,
                                            'value' => Claim::DOC_STATUS_COMPLETE
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="doc_complete_date">Complete Date</label>
                                <?= Html::input('text', 'doc_complete_date', null, [
                                    'class' => 'form-control dtpckr',
                                    'id' => 'doc_complete_date',
                                ]) ?>
                            </div>
                            <div class="form-group">
                                <label for="doc_notes">Notes</label>
                                <?= Html::textarea('doc_notes', null, [
                                    'class' => 'form-control',
                                    'id' => 'doc_notes',
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
                                            <?= Html::input('hidden', 'document_ids[]', $document['id']) ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?= Html::checkbox(
                                                        'is_checkeds[]',
                                                        $document['id'],
                                                        [
                                                            'class' => 'checked',
                                                            'checked' => false,
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
                                                            'checked' => false,
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
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-12 text-right">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success btn-lg waves-effect waves-light']) ?>
            </div>
        </div>
        <?= Html::endForm() ?>
    <?php
    endif;
    ?>
</div>

<?php
$script = <<< JS
    easyNumberSeparator({
      selector: '#estimated-amount',
      separator: ',',
      resultInput: '#estimated-amount-result',
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