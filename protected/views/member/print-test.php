<?php

use app\models\Member;
use app\models\PaymentMethod;
use app\models\Utils;

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
    <h1 class="text-center"><b>DAFTAR PESERTA DITERIMA ASURANSI</b></h1>
    <br><br>
    <table width="100%">
        <tr>
            <td width="50%">
                <table width="100%">
					<tr>
                        <td>ID LOAN</td>
                        <td>: <?=  $member->id_loan; ?></td>
                    </tr>
                    <tr>
                        <td width="150">NOMOR POLIS</td>
                        <td>: <?= $member->policy_no; ?></td>
                    </tr>
                    <tr>
                        <td>PEMEGANG POLIS</td>
                        <td>: <?= 'Bank Jatim'; ?></td>
                    </tr>
                    <tr>
                        <td>JENIS ASURANSI</td>
                        <td>: <?= $member->produk; ?></td>
                    </tr>
                   
                </table>
            </td>
        </tr>
    </table>
    <br>
    <table class="detail-list">
        <tr>
            <th>NO</th>
            <th>NO PESERTA</th>
            <th>NAMA PESERTA</th>
            <th>TGL LAHIR</th>
            <th>USIA</th>
            <th>TGL MULAI</th>
            <th>TGL AKHIR</th>
            <th>UP AS POKOK</th>
            <th>PREMI</th>
        </tr>
        <?php
        $i = 1;
        $totalUp = 0;
        $totalPremi = 0;
        $totalEm = 0;
        $totalNett = 0;
        if (!empty($member)) :
            // foreach ($members as $member) :
        ?>
                <tr>
                    <td align="center"><?= $i; ?></td>
                    <td nowrap><?= $member['member_no']; ?></td>
                    <td><?= $personal['name']; ?></td>
                    <td align="center"><?= Utils::convertDateTodMy($personal['birth_date']); ?></td>
                    <td align="center"><?= $member['age']; ?></td>
                    <td align="center"><?= Utils::convertDateTodMy($member['start_date']); ?></td>
                    <td align="center"><?= Utils::convertDateTodMy($member['end_date']); ?></td>
                    <td align="right"><?= number_format($member['sum_insured']); ?></td>
                    <td align="right"><?= number_format($member['gross_premium']); ?></td>
                </tr>
            <?php
                $i++;
                $totalUp += $member['sum_insured'];
                $totalPremi += $member['gross_premium'];
                $totalEm += $member['em_premium'];
                $totalNett += $member['nett_premium'];
            // endforeach;
            ?>
            <tr>
                <th colspan="7">TOTAL</th>
                <th align="right"><?= number_format($totalUp); ?></th>
                <th align="right"><?= number_format($totalPremi); ?></th>
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
                <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">
                <p><b><?= $signature->member_name; ?></b></p>
                <p><?= $signature->member_position; ?></p>
            </td>
        </tr>
    </table>
</section>