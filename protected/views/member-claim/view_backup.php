<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\Alert;
use app\models\MemberClaim;
use app\models\Utils;

use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use app\models\Member;
use app\models\claim_bank_jatim_detail;
use app\models\dokument_claim_jatim;

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
								<th width="1">Action</th>
								<th width="1">Konfirmasi Kadaluwarsa</th>
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
									 <td>
                                                    <div class="btn-group mb-2">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                            'class' => 'btn btn-light btn-sm waves-effect',
                                                            'title' => 'Update',
                                                            'data-toggle' => 'modal',
                                                            // 'data-target' => '#update-member-modal-' . $model['id'],
															'data-target' => '#update-member-modal-claim-' . $model['id'],
                                                        ]); ?>
                                                    </div>
                                                </td>
												
												<td>
                                                    <div class="btn-group mb-2">
                                                        <?= Html::a('<i class="fa fa-pencil"></i>', 'javascript:void(0)', [
                                                            'class' => 'btn btn-light btn-sm waves-effect',
                                                            'title' => 'Update',
                                                            'data-toggle' => 'modal',
                                                            // 'data-target' => '#update-member-modal-' . $model['id'],
															'data-target' => '#update-member-modal-daluwarsa-' . $model['id'],
                                                        ]); ?>
                                                    </div>
                                                </td>
												
												
                                </tr>
                          </div>
                 </div>
				</div>
				</div>
				
				
				 <div class="modal fade" id="update-member-modal-daluwarsa-<?= $model['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h4 class="modal-title" id="myMediumModalLabel">Konfirmasi<?= $i; ?></h4>
                                                        </div>
                                                        <div class="modal-body">
															<?= Html::beginForm(
															['member-claim/update-daluwarsa'],
															'post',
															[
																'id' => 'uw-update-form',
																'enctype' => 'multipart/form-data'
															]
															) ?>
                                                            <?= Html::input('hidden', 'id', $model['id'], [
                                                                'id' => 'id',
                                                                'required' => 'required',
                                                            ]) ?>
														   <?= Html::input('hidden', 'batch_id', $batch->id) ?>
                                                           
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="call_status_daluwarsa">status</label>
                                                                        <?= Html::input('text', 'call_status_daluwarsa', '', [
                                                                            'class' => 'form-control',
                                                                            'id' => 'call_status_daluwarsa',
                                                                            'required' => 'required',
                                                                            'value' => $model['call_status_daluwarsa'],
                                                                        ]) ?>
                                                                    </div>
																</div>
																
																
																<div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="call_keterangan_daluwarsa">Keterangan</label>
                                                                        <?= Html::input('text', 'call_keterangan_daluwarsa', '', [
                                                                            'class' => 'form-control',
                                                                            'id' => 'call_keterangan_daluwarsa',
                                                                            'required' => 'required',
                                                                            'value' => $model['call_keterangan_daluwarsa'],
                                                                        ]) ?>
                                                                    </div>
																</div>
																
																
															
                                                            <div class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success waves-effect waves-light']) ?>
                                                                </div>
                                                            </div>
                                                            <?= Html::endForm() ?>
                                                        </div>
                                              </div>
										</div>
									</div>
				
				
				
				
				
				
	  <div class="modal fade" id="update-member-modal-claim-<?= $model['id']; ?>" role="dialog" aria-labelledby="myMediumModalLabel" aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                            <h4 class="modal-title" id="myMediumModalLabel">Update Claim #<?= $i; ?></h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?= Html::beginForm(['member-claim/update-claim'], 'post', ["enctype" => "multipart/form-data", 'id' => 'uw-update-form']) ?>
                                                            <?= Html::input('hidden', 'batch_id', $batch->id) ?>
                                                            <?= Html::input('hidden', 'id', $model['id'], [
                                                                'id' => 'id',
                                                                'required' => 'required',
                                                            ]) ?>
                                                           
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="nilai_claim_disetujui">Nilai claim disetujui</label>
                                                                        <?= Html::input('text', 'nilai_claim_disetujui', '', [
                                                                            'class' => 'form-control',
                                                                            'id' => 'nilai_claim_disetujui',
                                                                            'required' => 'required',
                                                                            'value' => $model['nilai_claim_disetujui'],
                                                                        ]) ?>
                                                                    </div>
																</div>
																
																
																<div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="Keterangan_Reliance">Keterangan Reliance</label>
                                                                        <?= Html::input('text', 'Keterangan_Reliance', '', [
                                                                            'class' => 'form-control',
                                                                            'id' => 'Keterangan_Reliance',
                                                                            'required' => 'required',
                                                                            'value' => $model['Keterangan_Reliance'],
                                                                        ]) ?>
                                                                    </div>
																</div>
																
																<div class="row">
																	<div class="col-md-6">
																		Upload Bukti Bayar format (.jpg)
																		</br>
																		<input type="file" name="bukti_bayar"/>
																	</div>
																</div>      
																
															
                                                            <div class="row mt-4">
                                                                <div class="col-md-12">
                                                                    <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success waves-effect waves-light']) ?>
                                                                </div>
                                                            </div>
                                                            <?= Html::endForm() ?>
                                                        </div>
                                              </div>
										</div>
									</div>
									
								
	
	<div class="card-box">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                               
                                <td>Nama Dokumen</td>
                                <td>Files</td>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                            $i = 1;
							foreach($claim_detail as $cd) {					
								$dokumen_detail = dokument_claim_jatim::findone(['kode' => $cd['kode_dokumen']]);
                            ?>
                                <tr>
									<td><?= $dokumen_detail->nama_dokument; ?></td>
									<td>
									<a download href="<?= Url::base() . '/images/post_images/' . $cd['files']; ?>"><?= $cd['files']; ?></a>
									</td>
                                </tr>
                            <?php
							}
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>