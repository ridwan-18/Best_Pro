<?php

use app\models\Utils;
use app\models\PaymentMethod;

$this->title = 'Print Billing';
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

<section class="sheet padding-10mm debit-note">
    <hr>
    <br>
    <h4 class="text-center"><b>DEBIT NOTE</b></h4>
    <br>
    <hr>

    <br>

    <table width="100%">
        <tr>
            <td><b>No: <?= $billing['invoice_no']; ?></b></td>
            <td align="right"><b>Jakarta, <?= Utils::convertDateTodMyPrint($billing['invoice_date']); ?></b></td>
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
            <td align="center"><b>JUMLAH</b></td>
        </tr>
        <tr>
            <td style="border-top: none; border-bottom: none">
                Tagihan Penutupan Asuransi produk <?= $product['name']; ?>
                dengan nomor Polis <?= $policy['policy_no']; ?> dan jumlah peserta
                <?= $inforceMember['total_member']; ?> orang (No peserta <?= $memberNoList; ?>)
                <br><br><br>
            </td>
            <td style="border-top: none; border-bottom: none">
            </td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">Premi Standar</td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['gross_premium'] == 0) ? '-' : number_format($billing['gross_premium']); ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">Premi Tambahan</td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['extra_premium'] == 0) ? '-' : number_format($billing['extra_premium']); ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">Diskon <span style="float: right;"><?= $commission['discount']; ?> %</span></td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['discount'] == 0) ? '-' : '(' . number_format($billing['discount']) . ')'; ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">Handling Fee <span style="float: right;"><?= $commission['handling_fee']; ?> %</span></td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['handling_fee'] == 0) ? '-' : '(' . number_format($billing['handling_fee']) . ')'; ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">PPN <span style="float: right;"><?= $commission['ppn']; ?> %</span></td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['ppn'] == 0) ? '-' : '(' . number_format($billing['ppn']) . ')'; ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">PPH <span style="float: right;"><?= $commission['pph']; ?> %</span></td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['pph'] == 0) ? '-' : number_format($billing['pph']); ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">Biaya Polis dan Materai</td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['policy_cost'] + $billing['stamp_cost'] == 0) ? '-' : number_format($billing['policy_cost'] + $billing['stamp_cost']); ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">Biaya Sertifikat/Kartu</td>
            <td style="border-top: none; border-bottom: none">Rp. <?= ($billing['certificate_cost'] == 0) ? '-' : number_format($billing['certificate_cost'] + $billing['member_card_cost']); ?></td>
        </tr>
        <tr>
            <td style="padding-left: 300px; border-top: none; border-bottom: none">
                <h3>Total Premi Dibayar</h3>
            </td>
            <td style="border-bottom: none">
                <h3>Rp. <?= ($billing['total_billing'] == 0) ? '-' : number_format($billing['total_billing']); ?></h3>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Terbilang : <?= Utils::numberToText($billing['total_billing']); ?> Rupiah
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Jatuh Tempo Pembiayaan : <?= Utils::convertDateTodMyPrint($billing['due_date']); ?>
            </td>
        </tr>
    </table>

    <br>

    <p>Pembayaran premi dapat dilakukan melalui transfer ke rekening sebagai berikut :</p>
    <?php
    if ($billing['policy_no'] == '1032209000433') {
    ?>
        <table>
            <tr>
                <td width="100">Bank</td>
                <td width="5">:</td>
                <td><b>Bank JTrust</b></td>
            </tr>
            <tr>
                <td>No Rekening</td>
                <td>:</td>
                <td><b>1100025344</b></td>
            </tr>
            <tr>
                <td>Nama Rekening</td>
                <td>:</td>
                <td><b>Asuransi Jiwa Reliance Indonesia, PT</b></td>
            </tr>
        </table>
    <?php
    } else if ($billing['policy_no'] == '1032301000473' || $billing['policy_no'] == '1032301000472') {
    ?>
        <table>
            <tr>
                <td width="100">Bank</td>
                <td width="5">:</td>
                <td><b>Bank Banten</b></td>
            </tr>
            <tr>
                <td>No Rekening</td>
                <td>:</td>
                <td><b>0712006277</b></td>
            </tr>
            <tr>
                <td>Nama Rekening</td>
                <td>:</td>
                <td><b>ASURANSI JIWA RELIANCE IND PT</b></td>
            </tr>
        </table>
    <?php
    } else if ($billing['policy_no'] == '1032305000531' || $billing['policy_no'] == '1012305000339') {
    ?>
        <table>
            <tr>
                <td width="100">Bank</td>
                <td width="5">:</td>
                <td><b>Mandiri</b></td>
            </tr>
            <tr>
                <td>No Rekening</td>
                <td>:</td>
                <td><b>1220006239886</b></td>
            </tr>
            <tr>
                <td>Nama Rekening</td>
                <td>:</td>
                <td><b>PT. Asuransi Jiwa Reliance Indonesia</b></td>
            </tr>
        </table>
    <?php
    } else {
    ?>
        <table>
            <tr>
                <td width="100">Bank</td>
                <td width="5">:</td>
                <td><b>Bank Central Asia</b></td>
            </tr>
            <tr>
                <td>No Rekening</td>
                <td>:</td>
                <td><b>5460316199</b></td>
            </tr>
            <tr>
                <td>Nama Rekening</td>
                <td>:</td>
                <td><b>PT. Asuransi Jiwa Reliance Indonesia</b></td>
            </tr>
        </table>
    <?php
    }
    ?>

    <br><br>

    <p><b>Hormat Kami,</b></p>
    <p><b>PT. Asuransi Jiwa Reliance Indonesia</b></p>


    <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">

    <p><b><?= $signature->member_name; ?></b></p>
    <p><?= $signature->member_position; ?></p>

    <br>

    <p>Catatan : </p>
    <p>Harap pembayaran premi mencantumkan nomor debit note</p>
</section>

<section class="sheet padding-10mm member-approval">
    <table width="100%">
        <tr>
            <td width="55%" valign="top">Jakarta, <?= Utils::convertDateTodMyPrint($billing['invoice_date']); ?></td>
            <td>
                <table>
                    <tr>
                        <td width="75">Nomor</td>
                        <td width="10">:</td>
                        <td><?= $billing['reg_no']; ?></td>
                    </tr>
                    <tr>
                        <td>Perihal</td>
                        <td>:</td>
                        <td>Penerimaan Kepesertaan Asuransi</td>
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

    <p><b>1. Daftar Penambahan Peserta Asuransi Kumpulan</b></p>
    <div style="padding-left: 12px;">
        <table>
            <tr>
                <td width="100">Total Peserta</td>
                <td width="10">:</td>
                <td><?= $inforceMember['total_member']; ?></td>
            </tr>
            <tr>
                <td>No. Peserta</td>
                <td>:</td>
                <td><?= $memberNoList; ?></td>
            </tr>
            <tr>
                <td>Total UP</td>
                <td>:</td>
                <td>Rp. <?= number_format($inforceMember['total_si']); ?></td>
            </tr>
            <tr>
                <td>Total Premi Gross</td>
                <td>:</td>
                <td>Rp. <?= number_format($inforceMember['total_gross_premium']); ?></td>
            </tr>
        </table>
    </div>
    <br>

    <p><b>2. Daftar Peserta Pending Asuransi Kumpulan</b></p>
    <div style="padding-left: 12px;">
        <table>
            <tr>
                <td width="100">Total Peserta</td>
                <td width="10">:</td>
                <td><?= $pendingMember['total_member']; ?></td>
            </tr>
            <tr>
                <td>Total UP</td>
                <td>:</td>
                <td>Rp. <?= number_format($pendingMember['total_si']); ?></td>
            </tr>
            <tr>
                <td>Total Premi Gross</td>
                <td>:</td>
                <td>Rp. <?= number_format($pendingMember['total_gross_premium']); ?></td>
            </tr>
        </table>
    </div>
    <br>

    <p><b>3. Debit Note</b></p>
    <br>

    <p>Dapat diinformasikan bahwa Data Peserta tersebut telah diakseptasi ataupun masih dalam status PENDING
        sesuai dengan ketentuan kepesertaan yang tercantum di dalam Polis. Untuk data pending, hasil akseptasi akan
        disampaikan kembali setelah kelengkapan dokumen diterima dengan diberlakukan kondisi <i>"subject to no claim"</i>
        per tanggal diakseptasi oleh Penanggung.</p>
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
        <h4 class="text-center"><b>DAFTAR PENAMBAHAN PESERTA ASURANSI KUMPULAN</b></h4>
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
                <th>TGL AKHIR</th>
                <th>UP AS POKOK</th>
                <th>PREMI</th>
                <th>EM</th>
                <th>TOTAL PREMI</th>
                <th>TGL STNC</th>
            </tr>
            <?php
            $i = $pageStartNumber + 1;
            $totalUp = 0;
            $totalPremi = 0;
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
                    <td align="center" nowrap><?= Utils::convertDateTodMy($member['end_date']); ?></td>
                    <td align="right"><?= number_format($member['sum_insured']); ?></td>
                    <td align="right"><?= number_format($member['gross_premium']); ?></td>
                    <td align="right"><?= number_format($member['em_premium']); ?></td>
                    <td align="right"><?= number_format($member['gross_premium'] + $member['em_premium']); ?></td>
                    <td align="center" nowrap><?= Utils::convertDateTodMy($member['stnc_date']); ?></td>
                </tr>
            <?php
                $i++;
                $totalUp += $member['sum_insured'];
                $totalPremi += $member['gross_premium'];
                $totalEm += $member['em_premium'];
                $totalNett += $member['gross_premium'] + $member['em_premium'];
            endforeach;
            $grandTotalUp += $totalUp;
            $grandTotalPremi += $totalPremi;
            $grandTotalEm += $totalEm;
            $grandTotalNett += $totalNett;
            if ($currentPage == $totalPage) :
            ?>
                <tr>
                    <th colspan="7">TOTAL</th>
                    <th align="right"><?= number_format($grandTotalUp); ?></th>
                    <th align="right"><?= number_format($grandTotalPremi); ?></th>
                    <th align="right"><?= number_format($grandTotalEm); ?></th>
                    <th align="right"><?= number_format($grandTotalNett); ?></th>
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