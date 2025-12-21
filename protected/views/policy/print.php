<?php

use app\models\Utils;
use app\models\PaymentMethod;

$this->title = 'Print Policy';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    p {
        font-size: 12px;
    }

    .text-center {
        text-align: center;
    }

    td {
        padding-top: 10px;
        padding-bottom: 10px;
        vertical-align: top;
        font-size: 12px;
    }
</style>

<section class="sheet padding-10mm">
    <h4 class="text-center"><b>POLIS ASURANSI JIWA KUMPULAN</b></h4>

    <br>

    <h5 class="text-center"><b><?= $product['name']; ?></b></h5>

    <br>

    <p class="text-center"><b>Nomor Polis :</b></p>
    <p class="text-center"><?= $policy['policy_no']; ?></p>

    <br>

    <p class="text-center">antara</p>

    <br>

    <h5 class="text-center"><b>PT. ASURANSI JIWA RELIANCEINDONESIA</b></h5>
    <p class="text-center"><i>(selanjutnya disebut "Penanggung")</i></p>

    <br>

    <p class="text-center">dengan</p>

    <br>

    <h5 class="text-center"><b><?= $partner['name']; ?></b></h5>
    <p class="text-center"><?= $partner['address']; ?></p>
    <p class="text-center"><i>(selanjutnya disebut "Pemegang Polis")</i></p>

    <br>

    <p>Berdasarkan Surat Permintaan Asuransi Jiwa Kumpulan tanggal <?= Utils::convertDateTodMyPrint($policy['spa_date']); ?> yang merupakan bagian dari Polis, serta pembayaran Premi Pemegang Polis kepada Penanggung, maka Penanggung menyetujui untuk membayar kepada Pemegang Polis sejumlah uang sebagai Manfaat Asuransi dalam Polis ini, dengan tunduk kepada seluruh syarat dan ketentuan yang ada didalamnya.</p>

    <br>

    <p class="text-center">Jakarta, <?= Utils::convertDateTodMyPrint(date("Y-m-d")); ?></p>

    <br>
    <br>

    <p class="text-center"><b>Penanggung,</b></p>
    <p class="text-center">PT. Asuransi Jiwa Reliance Indonesia</p>

    <div class="text-center">
        <img src="<?= $qrCodeUrl; ?>" alt="Signature" class="text-center" height="75">
    </div>

    <p class="text-center"><b><?= $signature->policy_name; ?></b></p>
    <p class="text-center"><?= $signature->policy_position; ?></p>
</section>

<section class="sheet padding-10mm">
    <h4 class="text-center"><b>IKHTISAR POLIS</b></h4>

    <br>

    <table width="100%" cellpadding="5">
        <tr>
            <td width="250">Jenis Asuransi</td>
            <td width="20">:</td>
            <td><?= $product['name']; ?></td>
        </tr>
        <tr>
            <td>Pemegang Polis</td>
            <td>:</td>
            <td><?= $partner['name']; ?></td>
        </tr>
        <tr>
            <td>Peserta</td>
            <td>:</td>
            <td>Sebagaimana tercantum dalam daftar peserta</td>
        </tr>
        <tr>
            <td>Manfaat Asuransi</td>
            <td>:</td>
            <td>Manfaat Asuransi kepada Penerima Manfaat sebesar sisa pinjaman/kredit pokok tidak termasuk tunggakan angsuran pokok, bunga dan denda (bila ada) apabila Tertanggung/Peserta meninggal dunia karena sakit maupun kecelakaan dalam masa pertanggungan Asuransi.</td>
        </tr>
        <tr>
            <td>Mata Uang</td>
            <td>:</td>
            <td>Rupiah</td>
        </tr>
        <tr>
            <td>Cara Pembayaran Premi</td>
            <td>:</td>
            <td><?= PaymentMethod::translate($policy['payment_method']); ?></td>
        </tr>
        <tr>
            <td>Masa Asuransi</td>
            <td>:</td>
            <td>Sebagaimana tercantum dalam daftar peserta</td>
        </tr>
    </table>

    <br>

    <p>Ikhtisar Polis ini merupakan bagian yang tidak terpisahkan dari Polis Asuransi Jiwa <?= $product['name']; ?> Nomor : <b><?= $policy['policy_no']; ?></b></p>
</section>