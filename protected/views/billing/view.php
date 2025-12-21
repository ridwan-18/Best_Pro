<?php

use yii\helpers\Html;
use app\widgets\Alert;
use app\models\Billing;
use app\models\Utils;

$this->title = 'View Billing - ' . Yii::$app->name;
?>

<div class="member-view">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">Billing</h2>
            <h5 class="p-0 m-0"><?= $quotationModel->proposal_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a(
                '<i class="fa fa-print"></i> Print',
                [
                    'billing/print',
                    'id' => $billing->id,
                ],
                [
                    'class' => 'btn btn-warning waves-effect waves-light',
                    'target' => 'blank'
                ]
            ); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Reg No</td>
                        <td><?= $billing->reg_no; ?></td>
                    </tr>
                    <tr>
                        <td>Invoice No</td>
                        <td><?= $billing->invoice_no; ?></td>
                    </tr>
                    <tr>
                        <td>Policy No</td>
                        <td><?= $billing->policy_no; ?></td>
                    </tr>
                    <tr>
                        <td>Batch No</td>
                        <td><?= $billing->batch_no; ?></td>
                    </tr>
                    <tr>
                        <td>Policy Holder</td>
                        <td><?= $partner->name; ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><?= $billing->status; ?></td>
                    </tr>
                    <tr>
                        <td>Invoice Date</td>
                        <td><?= Utils::convertDateTodMy($billing->invoice_date); ?></td>
                    </tr>
                    <tr>
                        <td>Due Date</td>
                        <td><?= Utils::convertDateTodMy($billing->due_date); ?></td>
                    </tr>
                    <tr>
                        <td>Accept Date</td>
                        <td><?= Utils::convertDateTodMy($billing->accept_date); ?></td>
                    </tr>
                    <tr>
                        <td>Gross Premium</td>
                        <td><?= number_format($billing->gross_premium); ?></td>
                    </tr>
                    <tr>
                        <td>Extra Premium</td>
                        <td><?= number_format($billing->extra_premium); ?></td>
                    </tr>
                    <tr>
                        <td>Nett Premium</td>
                        <td><?= number_format($billing->nett_premium); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Total Member</td>
                        <td><?= $billing->total_member; ?></td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td><?= number_format($billing->discount); ?></td>
                    </tr>
                    <tr>
                        <td>Handling Fee</td>
                        <td><?= number_format($billing->handling_fee); ?></td>
                    </tr>
                    <tr>
                        <td>PPH</td>
                        <td><?= number_format($billing->pph); ?></td>
                    </tr>
                    <tr>
                        <td>PPN</td>
                        <td><?= number_format($billing->ppn); ?></td>
                    </tr>
                    <tr>
                        <td>Admin Cost</td>
                        <td><?= number_format($billing->admin_cost); ?></td>
                    </tr>
                    <tr>
                        <td>Policy Cost</td>
                        <td><?= number_format($billing->policy_cost); ?></td>
                    </tr>
                    <tr>
                        <td>Member Card Cost</td>
                        <td><?= number_format($billing->member_card_cost); ?></td>
                    </tr>
                    <tr>
                        <td>Certificate Cost</td>
                        <td><?= number_format($billing->certificate_cost); ?></td>
                    </tr>
                    <tr>
                        <td>Stamp Cost</td>
                        <td><?= number_format($billing->stamp_cost); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>