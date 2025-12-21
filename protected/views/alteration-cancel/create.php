<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use app\widgets\Alert;
use app\models\Policy;
use app\models\Partner;
use app\models\Member;
use app\models\Personal;
use app\models\Batch;

$urlMemberData = Url::to(['alteration-refund/get-member-data']);
$urlBatchData = Url::to(['alteration-refund/get-batch-data']);

$policies = Policy::find()
    ->asArray()
    ->select([
        Policy::tableName() . '.policy_no',
        Partner::tableName() . '.name AS partner'
    ])
    ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' .  Policy::tableName() . '.partner_id')
    ->orderBy([Policy::tableName() . '.id' => SORT_ASC])
    ->all();

$options = [];
foreach ($policies as $policy) {
    $items = [];
    $items['value'] = $policy['policy_no'];
    $items['label'] = $policy['policy_no'] . ' - ' . $policy['partner'];
    $options[] = $items;
}

$policies = ArrayHelper::map($options, 'value', 'label');

$this->title = 'Create Alteration Cancel - ' . Yii::$app->name;
?>

<div class="alteration-cancel-create">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Create Alteration Cancel</h2>
        </div>
        <div class="col-md-6 my-auto">
            <?= Html::beginForm(['alteration-cancel/create'], 'post', ['id' => 'alteration-cancel-form', 'class' => 'form-inline']) ?>
            <?= Html::dropDownList('policy_no', Yii::$app->request->post('policy_no'), $policies, [
                'prompt' => '- Select Policy -',
                'id' => 'policy_no',
                'class' => 'form-control slct2',
                'required' => 'required',
                'onchange' => 'submit()',
            ]) ?>
            <?= Html::endForm() ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <?php
    if (Yii::$app->request->post('policy_no') != '') :
        $policy = Policy::findOne(['policy_no' => Yii::$app->request->post('policy_no')]);
        $partner = Partner::findOne(['id' => $policy->partner_id]);

        $members = Member::find()
            ->asArray()
            ->select([
                Member::tableName() . '.member_no',
                Personal::tableName() . '.name'
            ])
            ->innerJoin(Personal::tableName(), Personal::tableName() . '.personal_no = ' .  Member::tableName() . '.personal_no')
            ->where([Member::tableName() . '.policy_no' => $policy->policy_no])
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

        $batchs = Batch::find()
            ->asArray()
            ->where([Batch::tableName() . '.policy_no' => $policy->policy_no])
            ->orderBy([Batch::tableName() . '.id' => SORT_ASC])
            ->all();
        $batchs = ArrayHelper::map($batchs, 'batch_no', 'batch_no');
    ?>
        <?= Html::beginForm(['alteration-cancel/create'], 'post', ['id' => 'alteration-cancel-member-form']) ?>
        <?= Html::input('hidden', 'policy_no', $policy->policy_no, ['id' => 'policy_no']) ?>
        <div class="card-box">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <td>Policy No</td>
                            <td><?= $policy->policy_no; ?></td>
                        </tr>
                        <tr>
                            <td>Policy Holder</td>
                            <td><?= $partner->name; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <h3>Member</h3>
        <div class="card-box">
            <div class="row">
                <div class="col-md-6">
                    <h5>Per Member</h5>
                    <div class="form-group">
                        <?= Html::dropDownList('member_no', null, $members, [
                            'prompt' => '- Select Member -',
                            'id' => 'member_no',
                            'class' => 'form-control slct2',
                        ]) ?>
                    </div>
                    <?= Html::a(
                        '<i class="fa fa-plus"></i> Add',
                        'javascript:void(0)',
                        [
                            'class' => 'btn btn-info btn-sm waves-effect',
                            'id' => 'add-member',
                        ]
                    ); ?>
                </div>
                <div class="col-md-6">
                    <h5>Per Batch</h5>
                    <div class="form-group">
                        <?= Html::dropDownList('batch_no', null, $batchs, [
                            'prompt' => '- Select Batch -',
                            'id' => 'batch_no',
                            'class' => 'form-control slct2',
                        ]) ?>
                    </div>
                    <?= Html::a(
                        '<i class="fa fa-plus"></i> Add',
                        'javascript:void(0)',
                        [
                            'class' => 'btn btn-info btn-sm waves-effect',
                            'id' => 'add-batch',
                        ]
                    ); ?>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-hover nowrap m-0">
                            <thead>
                                <tr>
                                    <td>Member No</td>
                                    <td>Name</td>
                                    <td>Birth Date</td>
                                    <td>Age</td>
                                    <td>Start Date</td>
                                    <td>End Date</td>
                                    <td>Sum Insured</td>
                                    <td>Premi</td>
                                    <td>Extra Premi</td>
                                    <td>Cancelled Premi</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody id="member-data"></tbody>
                        </table>
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
$(document).ready(function(){
    $('#add-member').on('click', function(event){
        $.ajax({
            url: "{$urlMemberData}",
            type: "POST",            
            data: "member_no=" + $("#member_no").val(),
            dataType: "json",
            success: function(data){
                if (data.member_no != null) {
                    var html = '<tr>';
                    html += '<td>' + data.member_no + '<input type="hidden" name="members_no[]" value="' + data.member_no + '"></td>';
                    html += '<td>' + data.name + '</td>';
                    html += '<td>' + data.birth_date + '</td>';
                    html += '<td>' + data.age + '</td>';
                    html += '<td>' + data.start_date + '</td>';
                    html += '<td>' + data.end_date + '</td>';
                    html += '<td>' + data.sum_insured + '</td>';
                    html += '<td>' + data.premi + '</td>';
                    html += '<td>' + data.extra_premi + '</td>';
                    html += '<td>' + data.cancelled_premi + '</td>';
                    html += '<td><a href="javascript:void(0);" class="remove-member btn btn-sm btn-danger"><i class="fa fa-remove"></i></a></td></tr>';
                    $('#member-data').prepend(html);
                }
            }
        });
    });
    $('#add-batch').on('click', function(event){
        $.ajax({
            url: "{$urlBatchData}",
            type: "POST",            
            data: "batch_no=" + $("#batch_no").val() + "&policy_no=" + $("#policy_no").val(),
            dataType: "json",
            success: function(data){
                console.log(data);
                var len = data.length;
                for (var i = 0; i < len; i++) {
                    if (data[i].member_no != null) {
                        var html = '<tr>';
                        html += '<td>' + data[i].member_no + '<input type="hidden" name="members_no[]" value="' + data[i].member_no + '"></td>';
                        html += '<td>' + data[i].name + '</td>';
                        html += '<td>' + data[i].birth_date + '</td>';
                        html += '<td>' + data[i].age + '</td>';
                        html += '<td>' + data[i].start_date + '</td>';
                        html += '<td>' + data[i].end_date + '</td>';
                        html += '<td>' + data[i].sum_insured + '</td>';
                        html += '<td>' + data[i].premi + '</td>';
                        html += '<td>' + data[i].extra_premi + '</td>';
                        html += '<td>' + data[i].cancelled_premi + '</td>';
                        html += '<td><a href="javascript:void(0);" class="remove-member btn btn-sm btn-danger"><i class="fa fa-remove"></i></a></td></tr>';
                        $('#member-data').prepend(html);
                    }
                }
            }
        });
    });
    $("#member-data").on('click','.remove-member',function(){
        $(this).parent().parent().remove();
    });
});
JS;
$this->registerJs($script);
?>