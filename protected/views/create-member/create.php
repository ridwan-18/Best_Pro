<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\Member;
use app\models\Policy;
use app\models\Partner;
use app\models\User;

$createdBy = Yii::$app->user->identity->id;
$user = User::findOne(['id' => $createdBy]);

$policies = Policy::find()
    ->select([Policy::tableName() . '.id', Policy::tableName() . '.policy_no'])
    ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' . Policy::tableName() . '.partner_id')
    ->orderBy([Policy::tableName() . '.id' => SORT_ASC])
    ->all();

$policyOptions = \yii\helpers\ArrayHelper::map($policies, 'id', 'policy_no');

$this->title = 'Input Data Member - ' . Yii::$app->name;

// URL untuk AJAX
$calculateUrl = \yii\helpers\Url::to(['create-member/calculate-premium']);
?>

<div class="member-create">
    <div class="card shadow-sm">
        <div class="card-body">
            <?= Alert::widget() ?>

            <?php $form = ActiveForm::begin([
                'id' => 'member-form',
                'options' => ['class' => 'row g-3'],
            ]); ?>

            <!-- Policy Dropdown -->
            <div class="col-12">
                <?= $form->field($model, 'policy_no')->dropDownList($policyOptions, [
                    'prompt' => '- Select Policy -',
                    'class' => 'form-control slct2',
                    'required' => true,
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>

            <!-- Header -->
            <div class="col-12">
                <div class="card-header mt-3 mb-3">
                    <h4 class="m-0">Input Data Member</h4>
                </div>
            </div>

            <!-- Row 1: Loan, Name, Birth Date -->
            <div class="col-md-4">
                <?= $form->field($model, 'id_loan')->textInput() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'name')->textInput() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'birth_date')->input('date', [
                    'value' => Member::dobDate(),
                    'id' => 'personal-birth_date',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>

            <!-- Row 2: Start Date, End Date, Sum Insured -->
            <div class="col-md-4">
                <?= $form->field($model, 'start_date')->input('date', [
                    'value' => Member::startDate(),
                    'id' => 'member-start_date',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'end_date')->input('date', [
                    'value' => Member::endDate(),
                    'id' => 'member-end_date',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'sum_insured')->input('number', [
                    'id' => 'member-sum_insured',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>

            <!-- Row 3: ID Card, Phone, Address -->
            <div class="col-md-4">
                <?= $form->field($personal, 'id_card_no')->input('number') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'phone')->input('number') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'address')->textInput() ?>
            </div>
			

            <!-- Calculated Premium Display -->
            <div class="col-md-4 mt-3">
                <label>Calculated Premium:</label>
                <div id="premium-display" class="p-2 border rounded bg-light">-</div>
            </div>
            <div class="col-md-4 mt-3">
                <label>Rate Premium:</label>
                <div id="rate-display" class="p-2 border rounded bg-light">-</div>
            </div>
            <div class="col-md-4 mt-3">
                <label>UW Limit:</label>
                <div id="uw-display" class="p-2 border rounded bg-light">-</div>
            </div>

            <!-- Submit -->
            <div class="col-12 text-end mt-3">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$script = <<< JS
window.updatePremium = function() {
    var policyNo = $('#member-policy_no').val();
    var birthDate = $('#personal-birth_date').val();
    var startDate = $('#member-start_date').val();
    var endDate = $('#member-end_date').val();
    var sumInsured = $('#member-sum_insured').val();
	var berat_badan = $('#member-berat_badan').val();
	var tinggi_badan = $('#member-tinggi_badan').val();

    if (!policyNo || !birthDate || !startDate || !endDate || !sumInsured) {
        $('#premium-display').html('-');
        $('#rate-display').html('-');
        $('#uw-display').html('-');
		$('#uw-display').html('-');
        return;
    }

    $.ajax({
       url: '{$calculateUrl}',
       type: 'POST',
       data: {
           policy_no: policyNo,
           birth_date: birthDate,
           start_date: startDate,
           end_date: endDate,
           sum_insured: sumInsured
       },
       success: function(data) {
           if (data.success) {
               $('#premium-display').html(data.premium_formatted);
               $('#rate-display').html(data.rate);
               $('#uw-display').html(data.uw_limit || '-');
           } else {
               $('#premium-display').html(data.message);
               $('#rate-display').html('-');
               $('#uw-display').html('-');
           }
       },
       error: function(xhr) {
           console.error(xhr.responseText);
           $('#premium-display').html('Error calculating premium');
           $('#rate-display').html('-');
           $('#uw-display').html('-');
       }
    });
}
JS;

$this->registerJs($script, \yii\web\View::POS_END);
?>