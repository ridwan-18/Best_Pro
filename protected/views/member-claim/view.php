<?php

use yii\helpers\Html;
use app\widgets\Alert;
use app\models\MemberClaim;
use app\models\Utils;

$this->title = 'Member Claim - ' . Yii::$app->name;
?>

<div class="member-claim">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">Member Claim</h2>
            <h5 class="p-0 m-0"><?= $model->id_loan; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
           
			<?= Html::a(
                '<i class="fa fa-check"></i> Analisa',
                [
                    'member-claim/analisa',
                    'id' => $model->id,
                ],
                [
                    'class' => ($member->status == MemberClaim::STATUS_PENDING)
                        ? 'btn btn-warning waves-effect waves-light disabled'
                        : 'btn btn-warning waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to Analisa ?',
                    'data-method' => 'post',
                ]
            );
			?>
			
			 <?= Html::a(
                '<i class="fa fa-check"></i> Approve',
                [
                    'member-claim/approve',
                    'id' => $model->id,
                ],
                [
                    'class' => ($member->status == MemberClaim::STATUS_APPROVED)
                        ? 'btn btn-success waves-effect waves-light disabled'
                        : 'btn btn-success waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to approve ?',
                    'data-method' => 'post',
                ]
            );
			?>
			
			<?= Html::a(
                '<i class="fa fa-check"></i> Ditolak',
                [
                    'member-claim/ditolak',
                    'id' => $model->id,
                ],
                [
                    'class' => ($member->status == MemberClaim::STATUS_REJECT)
                        ? 'btn btn-danger waves-effect waves-red disabled'
                        : 'btn btn-danger waves-effect waves-red',
                    'data-confirm' => 'Are you sure want to reject  ?',
                    'data-method' => 'post',
                ]
            );
			?>
			
            <?= Html::a(
                '<i class="fa"></i> Export Data',
                [
                    'member-claim/export',
                    'id' => $model->id,
                ],
                [
                    'class' => 'btn btn-info waves-effect waves-light',
                    'target' => 'blank'
                ]
            ); 
			?>
			
			
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <td>Policy No</td>
                        <td><?= $member->policy_no; ?></td>
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
                        <td><?= number_format($member->total_si); ?></td>
                    </tr>
                    <tr>
                        <td>Total Premium</td>
                        <td><?= number_format($member->total_premium); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <h3>Member</h3>
    <div class="card-box">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <td>No</td>
                                <td>Member No</td>
                                <td>Name</td>
                                <td>Birth Date</td>
                                <td>Age</td>
                                <td>Start Date</td>
                                <td>End Date</td>
                                <td>Term</td>
                                <td>Sum Insured</td>
                                <td>Premi</td>
                                <td>Extra Premi</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            ?>
                                <tr>
                                    <td><?= $i; ?></td>
                                    <td><?= $member->member_no; ?></td>
                                    <td><?= $personal->name; ?></td>
                                    <td><?= Utils::convertDateTodMy ($personal['birth_date']); ?></td>
                                    <td><?= $member->age; ?></td>
                                    <td><?= Utils::convertDateTodMy ($member['start_date']); ?></td>
									<td><?= Utils::convertDateTodMy ($member['end_date']); ?></td>
									<td><?= $member->term; ?></td>
									<td><?= number_format($member['sum_insured']); ?></td>
									<td><?= number_format($member['gross_premium']); ?></td>
									<td><?= number_format($member['em_premium']); ?></td>
                                </tr>
                            <?php
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
	
	 <h3>Dokumen Member</h3>
	<div class="card-box">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <td>Name</td>
                                <td>Nama Dokumen</td>
                                <td>Files</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            ?>
                                <tr>
                                    <td><?= $personal->name; ?></td>
									<td><?= $dokumen_detail->nama_dokument; ?></td>
									<td><?= $claim_detail->files; ?></td>
                                </tr>
                            <?php
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
	
</div>