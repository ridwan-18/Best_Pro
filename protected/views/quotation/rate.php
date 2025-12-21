<?php

use yii\helpers\Html;
use app\widgets\Alert;

$this->title = 'Rate - ' . Yii::$app->name;
?>

<div class="quotation-create">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="p-0 m-0">Rate</h2>
            <h5 class="p-0 m-0"><?= $quotationModel->proposal_no; ?></h5>
        </div>
        <div class="col-md-6 text-right my-auto">
            <?= $this->render('_menu', ['id' => $quotationModel->id]); ?>
        </div>
    </div>
    <?= Alert::widget() ?>
    <div class="card-box mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover nowrap m-0">
                        <thead>
                            <tr>
                                <th width="1">#</th>
                                <th>Product</th>
                                <th>Premium Type</th>
                                <th>Rate Type</th>
                                <th>Period Type</th>
                                <th>Sum Insured</th>
                                <th width="1">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($quotationProducts)) :
                                foreach ($quotationProducts as $model) :
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $model['product']; ?></td>
                                        <td><?= $model['premium_type']; ?></td>
                                        <td><?= $model['rate_type']; ?></td>
                                        <td><?= $model['period_type']; ?></td>
                                        <td><?= $model['si_type']; ?></td>
                                        <td>
                                            <div class="btn-group mb-2">
                                                <?= Html::a(
                                                    '<i class="fa fa-search"></i>',
                                                    [
                                                        'quotation/view-rate',
                                                        'id' => $model['id'],
                                                    ],
                                                    [
                                                        'class' => 'btn btn-light btn-sm waves-effect',
                                                        'title' => 'View',
                                                    ]
                                                ); ?>
                                            </div>
                                        </td>
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
    <div class="row mb-4">
        <div class="col-md-12 text-right">
            <?= Html::a(
                '<i class="fa fa-check"></i> Approve',
                [
                    'quotation/approve-rate',
                    'id' => $quotationModel->id,
                ],
                [
                    'class' => ($quotationModel->is_req_new_rate == 1)
                        ? 'btn btn-primary btn-lg waves-effect waves-light disabled'
                        : 'btn btn-primary btn-lg waves-effect waves-light',
                    'data-confirm' => 'Are you sure want to approve this Rate?',
                    'data-method' => 'post',
                ]
            ); ?>
        </div>
    </div>
</div>