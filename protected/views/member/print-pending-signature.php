<?php

use yii\helpers\Url;
use app\models\PaymentMethod;
use app\models\Utils;
use app\models\Signature;

$this->title = 'Print Member';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10px;
    }

    .text-center {
        text-align: center;
    }

    table.detail-list tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }

    table.detail-list {
        width: 100%;
        border-collapse: collapse;
    }

    table.detail-list,
    table.detail-list td,
    table.detail-list th {
        padding: 3px;
        border: 1px solid #000000;
    }
</style>

<section class="sheet padding-10mm">
    <h1 class="text-center"><b>DAFTAR PESERTA PENDING ASURANSI KUMPULAN</b></h1>
    <br><br>
    <table width="100%">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td width="150">NOMOR POLIS</td>
                        <td>: <?= $batch->policy_no; ?></td>
                    </tr>
                    <tr>
                        <td>PEMEGANG POLIS</td>
                        <td>: <?= $partner->name; ?></td>
                    </tr>
                    <tr>
                        <td>JENIS ASURANSI</td>
                        <td>: <?= $product->name; ?></td>
                    </tr>
                    <tr>
                        <td>CARA BAYAR</td>
                        <td>: <?= PaymentMethod::translate($quotation->payment_method); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table class="detail-list">
        <tr>
            <th>NO</th>
            <th>NAMA PESERTA</th>
            <th>TGL LAHIR</th>
            <th>USIA</th>
            <th>TGL MULAI</th>
            <th>TGL AKHIR</th>
            <th>UP AS POKOK</th>
            <th>KETERANGAN</th>
        </tr>
        <?php
        $i = 1;
        $totalUp = 0;
        if (!empty($members)) :
            foreach ($members as $member) :
        ?>
                <tr>
                    <td align="center"><?= $i; ?></td>
                    <td><?= $member['name']; ?></td>
                    <td align="center"><?= Utils::convertDateTodMy($member['birth_date']); ?></td>
                    <td align="center"><?= $member['age']; ?></td>
                    <td align="center"><?= Utils::convertDateTodMy($member['start_date']); ?></td>
                    <td align="center"><?= Utils::convertDateTodMy($member['end_date']); ?></td>
                    <td align="right"><?= number_format($member['sum_insured']); ?></td>
                    <td align="center"><?= $member['uw_notes']; ?></td>
                </tr>
            <?php
                $i++;
                $totalUp += $member['sum_insured'];
            endforeach;
            ?>
            <tr>
                <th colspan="6">TOTAL</th>
                <th align="right"><?= number_format($totalUp); ?></th>
                <th></th>
            </tr>
        <?php
        endif;
        ?>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                <p>Jakarta, <?= Utils::convertDateTodMyPrint(date("Y-m-d")); ?></p>
                <img src="<?= Url::base() . Signature::PICTURE_PATH . $signature->member_picture; ?>" alt="Signature" class="text-center" height="75">
                <p><b><?= $signature->member_name; ?></b></p>
                <p><?= $signature->member_position; ?></p>
            </td>
        </tr>
    </table>
</section>