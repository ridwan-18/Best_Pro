<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\Alert;
use app\models\Policy;
use app\models\Partner;

$this->title = 'Update Data Member - ' . Yii::$app->name;

// Ambil policy
$policies = Policy::find()
    ->select([Policy::tableName() . '.id', Policy::tableName() . '.policy_no'])
    ->innerJoin(Partner::tableName(), Partner::tableName() . '.id = ' . Policy::tableName() . '.partner_id')
    ->orderBy([Policy::tableName() . '.id' => SORT_ASC])
    ->all();
$policyOptions = \yii\helpers\ArrayHelper::map($policies, 'id', 'policy_no');

// URL AJAX
$calculateUrl = \yii\helpers\Url::to(['create-member/calculate-premium']);
?>

<div class="member-update">
    <div class="card shadow-sm">
        <div class="card-body">
            <?= Alert::widget() ?>

            <?php $form = ActiveForm::begin([
                'id' => 'member-form',
                'options' => ['class' => 'row g-3'],
            ]); ?>

            <!-- Policy -->
            <div class="col-12">
                <?= $form->field($model, 'policy_no')->dropDownList($policyOptions, [
                    'prompt' => '- Select Policy -',
                    'class' => 'form-control slct2',
                    'id' => 'member-policy_no',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>

            <!-- Header -->
            <div class="col-12">
                <div class="card-header mt-3 mb-3">
                    <h4 class="m-0">Update Data Member</h4>
                </div>
            </div>

            <!-- Row 1 -->
            <div class="col-md-4">
                <?= $form->field($model, 'id_loan')->textInput() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'name')->textInput() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'birth_date')->input('date', [
                    'id' => 'personal-birth_date',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>

            <!-- Row 2 -->
            <div class="col-md-4">
                <?= $form->field($model, 'start_date')->input('date', [
                    'id' => 'member-start_date',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'end_date')->input('date', [
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

            <!-- Row 3 -->
            <div class="col-md-4">
                <?= $form->field($personal, 'id_card_no')->input('number') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'phone')->input('number') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($personal, 'address')->textInput() ?>
            </div>

            <!-- BMI -->
            <div class="col-md-4">
                <?= $form->field($model, 'tinggi_badan')->input('number', [
                    'id' => 'member-tinggi_badan',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'berat_badan')->input('number', [
                    'id' => 'member-berat_badan',
                    'onchange' => 'updatePremium()'
                ]) ?>
            </div>
            <div class="col-md-4">
                <label>BMI:</label>
                <div id="bmi-display" class="p-2 border rounded bg-light">-</div>
            </div>

            <!-- Premium -->
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
                <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
function updatePremium() {
    var tinggi = parseFloat(document.getElementById('member-tinggi_badan').value);
    var berat = parseFloat(document.getElementById('member-berat_badan').value);
    var sumInsured = parseFloat(document.getElementById('member-sum_insured').value);
    var startDate = document.getElementById('member-start_date').value;
    var endDate = document.getElementById('member-end_date').value;
    var policyId = document.getElementById('member-policy_no').value;

    // Hitung BMI
    var bmi = '-';
    if (!isNaN(tinggi) && !isNaN(berat) && tinggi > 0) {
        bmi = (berat / Math.pow(tinggi / 100, 2)).toFixed(2);
    }
    document.getElementById('bmi-display').innerText = bmi;

    // Jika data lengkap, hitung premium via AJAX
    if (policyId && sumInsured && startDate && endDate) {
        fetch('$calculateUrl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
            },
            body: JSON.stringify({
                policy_id: policyId,
                sum_insured: sumInsured,
                start_date: startDate,
                end_date: endDate,
                bmi: bmi
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('premium-display').innerText = data.premium || '-';
            document.getElementById('rate-display').innerText = data.rate || '-';
            document.getElementById('uw-display').innerText = data.uw_limit || '-';
        })
        .catch(error => console.error('Error:', error));
    } else {
        document.getElementById('premium-display').innerText = '-';
        document.getElementById('rate-display').innerText = '-';
        document.getElementById('uw-display').innerText = '-';
    }
}

// Jalankan sekali saat halaman load untuk tampilkan nilai awal
updatePremium();
JS;

$this->registerJs($js);
?>