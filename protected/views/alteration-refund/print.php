<?php

use app\models\Utils;
use app\models\PaymentMethod;

$this->title = 'Print Credit Note';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    .text-center {
        text-align: center;
    }

    .debit-note {
        margin-top: 2.5cm;
        font-size: 12px;
    }

    .member-approval {
        margin-top: 2.5cm;
        font-size: 12px;
    }

    .member-list {
        font-size: 10px;
    }

    table.price-list {
        border-collapse: collapse;
    }

    table.price-list td,
    table.price-list th {
        padding: 3px;
        border: 1px solid #000;
        vertical-align: top;
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

<section class="sheet padding-10mm credit-note">
    <hr>
    <br>
    <h4 class="text-center"><b>CREDIT NOTE</b></h4>
    <br>
    <hr>

    <br>

    <table width="100%">
        <tr>
            <td><b>No: <?= $AlterationRefund['alteration_no']; ?></b></td>
            <td align="right"><b>Jakarta, <?= Utils::convertDateTodMyPrint($AlterationRefund['alteration_date']); ?></b></td>
        </tr>
    </table>
    <br>
    <p><b>Kepada Yth:</b></p>
    <p><b><?= $partner['name']; ?></b></p>
    <p><?= $partner['address']; ?></p>
    <br>
    <table width="100%" class="price-list">
        <tr>
            <td align="center" width="75%"><b>KETERANGAN</b></td>
            <td align="center"><b>REFUND PREMI (Rp)</b></td>
        </tr>
        <tr>
            <td style="border-top: none; border-bottom: none">
                Pengurangan Kepesertaan Asuransi produk <?= $product['name']; ?>
                dengan nomor Polis <?= $policy['policy_no']; ?> dan jumlah peserta
                <?= $refundMember['total_member']  ; ?> orang 
                <br><br><br>
            </td>
            <td style="border-top: none; border-bottom: none" align="right">
			 <?= number_format($AlterationRefund['total_premium_refund']) ; ?> 
            </td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">
            </td>
			<td style="padding-left: 300px; border-top: none; border-bottom: none">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Terbilang : <?= Utils::numberToText($AlterationRefund['total_premium_refund']); ?> Rupiah
            </td>
        </tr>
    </table>

    <br>

    <br><br>

    <p><b>Hormat Kami,</b></p>
    <p><b>PT. Asuransi Jiwa Reliance Indonesia</b></p>


    <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">

    <p><b><?= $signature->member_name; ?></b></p>
    <p><?= $signature->member_position; ?></p>

    <br>
</section>

<section class="sheet padding-10mm member-approval">
    <table width="100%">
        <tr>
            <td width="55%" valign="top">Jakarta, <?= Utils::convertDateTodMyPrint($AlterationRefund['alteration_date']); ?></td>
            <td>
                <table>
                    <tr>
                        <td width="75">Nomor</td>
                        <td width="10">:</td>
                        <td><?= $AlterationRefund['reg_no']; ?></td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <td>Pengurangan Kepesertaan Asuransi</td>
                    </tr>
                    <tr>
                        <td>Lamp.</td>
                        <td>:</td>
                        <td>2 (dua) berkas</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br>
    <p><b>Kepada Yth:</b></p>
    <p><b><?= $partner['name']; ?></b></p>
    <p><?= $partner['address']; ?></p>
    <br>

    <p>Dengan hormat,</p>
    <br>
    <p>Terimakasih atas kepercayaan yang telah diberikan kepada kami sebagai mitra untuk memberikan perlindungan kepada nasabah Anda.</p>
    <p>Sehubungan dengan pengajuan Data Perserta yang diterima atas Penutupan Asuransi Jiwa, sebagai berikut :</p>
    <br>

    <table>
        <tr>
            <td width="100">Nomor Polis</td>
            <td width="5">:</td>
            <td><b><?= $policy['policy_no']; ?></b></td>
        </tr>
        <tr>
            <td>Pemegang Polis</td>
            <td>:</td>
            <td><b><?= $partner['name']; ?></b></td>
        </tr>
        <tr>
            <td>Jenis Asuransi</td>
            <td>:</td>
            <td><b><?= $product['name']; ?></b></td>
        </tr>
    </table>
    <br>

    <p>Dengan ini kami lampirkan :</p>
    <br>

    <p><b>1. Daftar Pengurangan Peserta Asuransi Kumpulan</b></p>
    <div style="padding-left: 12px;">
        <table>
            <tr>
                <td width="100">Total Peserta</td>
                <td width="10">:</td>
                <td><?= $refundMember['total_member']; ?></td>
            </tr>
            <tr>
                <td>No. Peserta</td>
                <td>:</td>
                <td><?= $memberNoList; ?></td>
            </tr>
            <tr>
                <td>Total UP</td>
                <td>:</td>
                <td>Rp. <?= number_format($AlterationRefund['total_si']); ?></td>
            </tr>
            <tr>
                <td>Total Premi Gross</td>
                <td>:</td>
                <td>Rp. <?= number_format($AlterationRefund['total_premium_refund']); ?></td>
            </tr>
        </table>
    </div>
    <br>

    <p><b>2. Credit Note</b></p>
    <br>

    <p>Dapat diinformasikan bahwa seluruh Peserta tersebut dalam Daftar Pengurangan Peserta Asuransi Kumpulan diberlakukan 
	efektif pengurangan per tanggal sesuai dengan Daftar Pengurangan Peserta Terlampir.</p>
    <br>
    <p>Apabila terdapat pertanyaan, silahkan menghubungi kami pada hotline 021-5793 0008, di hari Senin - Jumat
        pukul 09.00 - 17.00 WIB dengan Div. Underwriting.
    </p>
    <br>
    <p>Demikian disampaikan, atas perhatian dan kerjasamanya diucapkan terima kasih.</p>
    <br>

    <p><b>Hormat Kami,</b></p>
    <p><b>PT. Asuransi Jiwa Reliance Indonesia</b></p>

    <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">

    <p><b><?= $signature->member_name; ?></b></p>
    <p><?= $signature->member_position; ?></p>
</section>

<?php
$pageStartNumber = 0;
$splitTotal = 32;
$chunked = array_chunk($members, $splitTotal);
$totalPage = count($chunked);
$currentPage = 1;
$grandTotalUp = 0;
$grandTotalPremi = 0;
$grandTotalEm = 0;
$grandTotalNett = 0;
foreach ($chunked as $splits) :
?>
    <section class="sheet padding-10mm member-list">
        <h4 class="text-center"><b>DAFTAR PENGURANGAN PESERTA ASURANSI KUMPULAN</b></h4>
        <br><br>
        <table width="100%">
            <tr>
                <td width="50%">
                    <table width="100%">
                        <tr>
                            <td width="150">NOMOR POLIS</td>
                            <td>: <?= $policy['policy_no']; ?></td>
                        </tr>
                        <tr>
                            <td>PEMEGANG POLIS</td>
                            <td>: <?= $partner['name']; ?></td>
                        </tr>
                        <tr>
                            <td>JENIS ASURANSI</td>
                            <td>: <?= $product['name']; ?></td>
                        </tr>
                        <tr>
                            <td>CARA BAYAR</td>
                            <td>: <?= PaymentMethod::translate($quotation['payment_method']); ?></td>
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
                <th>TGL . EFEKTIF PENGURANGAN</th>
                <th>UP AS POKOK</th>
                <th>PENGEMBALIAN PREMI</th>
            </tr>
            <?php
            $i = $pageStartNumber + 1;
            $totalUp = 0;
            $premi_refund = 0;
            $totalEm = 0;
            $totalNett = 0;
            foreach ($splits as $member) :
            ?>
                <tr>
                    <td align="center"><?= $i; ?></td>
                    <td nowrap><?= $member['member_no']; ?></td>
                    <td><?= $member['name']; ?></td>
                    <td align="center" nowrap><?= Utils::convertDateTodMy($member['birth_date']); ?></td>
                    <td align="center"><?= $member['age']; ?></td>
                    <td align="center" nowrap><?= Utils::convertDateTodMy($member['start_date']); ?></td>
                    <td align="center" nowrap><?= Utils::convertDateTodMy($member['new_end_date']); ?></td>
                    <td align="right"><?= number_format($member['sum_insured']); ?></td>
                    <td align="right"><?= number_format($member['premi_refund']); ?></td>
                </tr>
            <?php
                $i++;
                $totalUp += $member['sum_insured'];
                $premi_refund += $member['premi_refund'];
            endforeach;
            $grandTotalUp += $totalUp;
            $grandTotalPremi += $premi_refund;
            if ($currentPage == $totalPage) :
            ?>
                <tr>
                    <th colspan="7">TOTAL</th>
                    <th align="right"><?= number_format($grandTotalUp); ?></th>
                    <th align="right"><?= number_format($grandTotalPremi); ?></th>
                </tr>
            <?php
            endif;
            ?>
        </table>
        <?php
        if ($currentPage == $totalPage) :
        ?>
            <br>
            <table width="100%">
                <tr>
                    <td width="70%"></td>
                    <td width="30%" align="center">
                        <p>Jakarta, <?= Utils::convertDateTodMyPrint($AlterationRefund['alteration_date']); ?></p>
                        <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">
                        <p><b><?= $signature->member_name; ?></b></p>
                        <p><?= $signature->member_position; ?></p>
                    </td>
                </tr>
            </table>
        <?php
        endif;
        ?>
    </section>
<?php
    $pageStartNumber = $pageStartNumber + $splitTotal;
    $currentPage++;
endforeach;
?>

<?php
if (!empty($pendingMembers)) :
    $pageStartNumber = 0;
    $splitTotal = 32;
    $chunked = array_chunk($pendingMembers, $splitTotal);
    $totalPage = count($chunked);
    $currentPage = 1;
    $grandTotalUp = 0;
    $grandTotalPremi = 0;
    $grandTotalEm = 0;
    $grandTotalNett = 0;
    foreach ($chunked as $splits) :
?>
        <section class="sheet padding-10mm member-list">
            <h4 class="text-center"><b>DAFTAR PESERTA PENDING ASURANSI KUMPULAN</b></h4>
            <br><br>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <table width="100%">
                            <tr>
                                <td width="150">NOMOR POLIS</td>
                                <td>: <?= $policy['policy_no']; ?></td>
                            </tr>
                            <tr>
                                <td>PEMEGANG POLIS</td>
                                <td>: <?= $partner['name']; ?></td>
                            </tr>
                            <tr>
                                <td>JENIS ASURANSI</td>
                                <td>: <?= $product['name']; ?></td>
                            </tr>
                            <tr>
                                <td>CARA BAYAR</td>
                                <td>: <?= PaymentMethod::translate($quotation['payment_method']); ?></td>
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
                    <th>SEX</th>
                    <th>TGL MULAI</th>
                    <th>TGL AKHIR</th>
                    <th>UP AS POKOK</th>
                    <th>KETERANGAN</th>
                </tr>
                <?php
                $i = $pageStartNumber + 1;
                $totalUp = 0;
                foreach ($splits as $member) :
                ?>
                    <tr>
                        <td align="center"><?= $i; ?></td>
                        <td><?= $member['name']; ?></td>
                        <td align="center" nowrap><?= Utils::convertDateTodMy($member['birth_date']); ?></td>
                        <td align="center"><?= $member['age']; ?></td>
                        <td align="center"></td>
                        <td align="center" nowrap><?= Utils::convertDateTodMy($member['start_date']); ?></td>
                        <td align="center" nowrap><?= Utils::convertDateTodMy($member['end_date']); ?></td>
                        <td align="right"><?= number_format($member['sum_insured']); ?></td>
                        <td><?= $member['uw_notes']; ?></td>
                    </tr>
                <?php
                    $i++;
                    $totalUp += $member['sum_insured'];
                endforeach;
                $grandTotalUp += $totalUp;
                if ($currentPage == $totalPage) :
                ?>
                    <tr>
                        <th colspan="7">TOTAL</th>
                        <th align="right"><?= number_format($grandTotalUp); ?></th>
                        <th></th>
                    </tr>
                <?php
                endif;
                ?>
            </table>
            <?php
            if ($currentPage == $totalPage) :
            ?>
                <br>
                <table width="100%">
                    <tr>
                        <td width="70%"></td>
                        <td width="30%" align="center">
                            <p>Jakarta, <?= Utils::convertDateTodMyPrint($billing['invoice_date']); ?></p>
                            <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">
                            <p><b><?= $signature->member_name; ?></b></p>
                            <p><?= $signature->member_position; ?></p>
                        </td>
                    </tr>
                </table>
            <?php
            endif;
            ?>
        </section>
<?php
        $pageStartNumber = $pageStartNumber + $splitTotal;
        $currentPage++;
    endforeach;
endif;
?>

<?php
if (!empty($declinedMembers)) :
    $pageStartNumber = 0;
    $splitTotal = 32;
    $chunked = array_chunk($declinedMembers, $splitTotal);
    $totalPage = count($chunked);
    $currentPage = 1;
    $grandTotalUp = 0;
    $grandTotalPremi = 0;
    $grandTotalEm = 0;
    $grandTotalNett = 0;
    foreach ($chunked as $splits) :
?>
        <section class="sheet padding-10mm member-list">
            <h4 class="text-center"><b>DAFTAR PESERTA DECLINED ASURANSI KUMPULAN</b></h4>
            <br><br>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <table width="100%">
                            <tr>
                                <td width="150">NOMOR POLIS</td>
                                <td>: <?= $policy['policy_no']; ?></td>
                            </tr>
                            <tr>
                                <td>PEMEGANG POLIS</td>
                                <td>: <?= $partner['name']; ?></td>
                            </tr>
                            <tr>
                                <td>JENIS ASURANSI</td>
                                <td>: <?= $product['name']; ?></td>
                            </tr>
                            <tr>
                                <td>CARA BAYAR</td>
                                <td>: <?= PaymentMethod::translate($quotation['payment_method']); ?></td>
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
                    <th>SEX</th>
                    <th>TGL MULAI</th>
                    <th>TGL AKHIR</th>
                    <th>UP AS POKOK</th>
                    <th>KETERANGAN</th>
                </tr>
                <?php
                $i = $pageStartNumber + 1;
                $totalUp = 0;
                foreach ($splits as $member) :
                ?>
                    <tr>
                        <td align="center"><?= $i; ?></td>
                        <td><?= $member['name']; ?></td>
                        <td align="center" nowrap><?= Utils::convertDateTodMy($member['birth_date']); ?></td>
                        <td align="center"><?= $member['age']; ?></td>
                        <td align="center"></td>
                        <td align="center" nowrap><?= Utils::convertDateTodMy($member['start_date']); ?></td>
                        <td align="center" nowrap><?= Utils::convertDateTodMy($member['end_date']); ?></td>
                        <td align="right"><?= number_format($member['sum_insured']); ?></td>
                        <td><?= $member['uw_notes']; ?></td>
                    </tr>
                <?php
                    $i++;
                    $totalUp += $member['sum_insured'];
                endforeach;
                $grandTotalUp += $totalUp;
                if ($currentPage == $totalPage) :
                ?>
                    <tr>
                        <th colspan="7">TOTAL</th>
                        <th align="right"><?= number_format($grandTotalUp); ?></th>
                        <th></th>
                    </tr>
                <?php
                endif;
                ?>
            </table>
            <?php
            if ($currentPage == $totalPage) :
            ?>
                <br>
                <table width="100%">
                    <tr>
                        <td width="70%"></td>
                        <td width="30%" align="center">
                            <p>Jakarta, <?= Utils::convertDateTodMyPrint($billing['invoice_date']); ?></p>
                            <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">
                            <p><b><?= $signature->member_name; ?></b></p>
                            <p><?= $signature->member_position; ?></p>
                        </td>
                    </tr>
                </table>
            <?php
            endif;
            ?>
        </section>
<?php
        $pageStartNumber = $pageStartNumber + $splitTotal;
        $currentPage++;
    endforeach;
endif;
?>