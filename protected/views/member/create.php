<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\widgets\Alert;
use app\models\Policy;
use app\models\Partner;

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

$this->title = 'Upload Member - ' . Yii::$app->name;
?>
<div class="member-create">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="p-0 m-0">Upload Member</h2>
        </div>
    </div>
    <?= Alert::widget() ?>
    <?= Html::beginForm(['member/upload'], 'post', ['id' => 'upload-form', 'enctype' => 'multipart/form-data']) ?>
    <div class="card-box">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="file">Policy</label>
                    <?= Html::dropDownList('policy_no', null, $policies, [
                        'prompt' => '- Select Policy -',
                        'id' => 'policy_no',
                        'class' => 'form-control slct2',
                        'required' => 'required',
                    ]) ?>
                </div>
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

<?php
$script = <<< JS
    $('#upload-form').submit(function() {
        $('#upload-btn').html('<i class="fa fa-spinner"></i> Loading');
        $('#upload-btn').attr('class', 'btn btn-primary waves-effect waves-light disabled');
    });
JS;
$this->registerJs($script);
?>