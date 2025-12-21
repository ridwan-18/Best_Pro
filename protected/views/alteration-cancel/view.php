<?php

use yii\helpers\Html;
use app\widgets\Alert;
use app\models\AlterationCancel;

$this->title = 'View Alteration Cancel - ' . Yii::$app->name;
?>

<div class="member-view">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">Alteration Cancel</h2>
            <h5 class="p-0 m-0"><?= $model->alteration_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a(
                '<i class="fa fa-check"></i> Approve',
                [
                    'alteration-cancel/approve',
                    'id' => $model->id,
                ],
                [
                    'class' => ($model->status == AlterationCancel::STATUS_APPROVED)
                        ? 'btn btn-primary waves-effect waves-light disabled'
                        : 'btn btn-primary waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to approve this alteration?',
                    'data-method' => 'post',
                ]
            ); ?>
            <?= Html::a(
                '<i class="fa fa-print"></i> Print',
                [
                    'alteration-cancel/print',
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
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Policy No</td>
                        <td><?= $model->policy_no; ?></td>
                    </tr>
                    <tr>
                        <td>Policy Holder</td>
                        <td><?= $partner->name; ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><?= $model->status; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Total SI</td>
                        <td><?= number_format($model->total_si); ?></td>
                    </tr>
                    <tr>
                        <td>Total Premium</td>
                        <td><?= number_format($model->total_premium); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <h3>Member</h3>
    <div class="card-box">
        <div class="row mt-4">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <td>#</td>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($members as $member) :
                        ?>
                            <tr>
                                <td><?= $i; ?></td>
                                <td><?= $member['member_no']; ?></td>
                                <td><?= $member['name']; ?></td>
                                <td><?= $member['birth_date']; ?></td>
                                <td><?= $member['age']; ?></td>
                                <td><?= $member['start_date']; ?></td>
                                <td><?= $member['end_date']; ?></td>
                                <td><?= number_format($member['sum_insured']); ?></td>
                                <td><?= number_format($member['premi']); ?></td>
                                <td><?= number_format($member['extra_premi']); ?></td>
                                <td><?= number_format($member['premi']); ?></td>
                            </tr>
                        <?php
                            $i++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>