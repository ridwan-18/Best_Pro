<?php

use yii\helpers\Html;
use app\widgets\Alert;
use app\models\Member;
use app\models\Utils;

$this->title = 'View Accumulation - ' . Yii::$app->name;
?>

<div class="member-accumulation">
    <div class="row mb-4">
        <div class="col-md-6 my-auto">
            <h2 class="p-0 m-0">Accumulation</h2>
            <h5 class="p-0 m-0"><?= $personal->name; ?> - <?= $personal->birth_date; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= Html::a(
                '<i class="fa fa-file-excel-o"></i> Export',
                [
                    'export-accumulation',
                    'id' => $member->id
                ],
                ['class' => 'btn btn-success waves-effect waves-light']
            ); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover nowrap m-0">
                                <thead>
                                    <tr>
                                        <th width="1">#</th>
                                        <th>Policy No</th>
                                        <th>Batch No</th>
                                        <th>Member No</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>Age</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Term</th>
                                        <th>Sum Insured</th>
                                        <th>Gross Premium</th>
                                        <th>Nett Premium</th>
                                        <th>EM Premium</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = $pagination->offset + 1;
                                    if (!empty($members)) :
                                        foreach ($members as $member) :
                                    ?>
                                            <tr>
                                                <td><?= $i; ?></td>
                                                <td><?= $member['policy_no']; ?></td>
                                                <td><?= $member['batch_no']; ?></td>
                                                <td><?= $member['member_no']; ?></td>
                                                <td><?= $member['name']; ?></td>
                                                <td><?= Utils::convertDateTodMy($member['birth_date']); ?></td>
                                                <td><?= $member['age']; ?></td>
                                                <td><?= Utils::convertDateTodMy($member['start_date']); ?></td>
                                                <td><?= Utils::convertDateTodMy($member['end_date']); ?></td>
                                                <td><?= $member['term']; ?></td>
                                                <td><?= number_format($member['sum_insured']); ?></td>
                                                <td><?= number_format($member['gross_premium']); ?></td>
                                                <td><?= number_format($member['nett_premium']); ?></td>
                                                <td><?= number_format($member['em_premium']); ?></td>
                                            </tr>
                                    <?php
                                            $i++;
                                        endforeach;
                                    else :
                                        echo '<tr><td class="text-center" colspan="100">No data</td></tr>';
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>