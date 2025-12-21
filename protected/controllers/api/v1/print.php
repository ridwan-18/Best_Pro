<?php

use app\models\Utils;
use app\models\PaymentMethod;
use app\models\Member;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Print Policy';
?>

<style>
    * {
        margin: 0;
        padding: 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13px;
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
<?php echo Html::img("@web/images/img-Reliance-life.jpg") ?> 
    <h4 class="text-center"><b>SERTIFIKAT ASURANSI</b></h4>
	<h4 class="text-center">NOMOR: <?=  $member->id_loan; ?> </h4>
    <br>

    <table width="100%" cellpadding="5">
        <tr>
            <td width="50%">
                <table width="100%">
				<tr>
                        <td width="150">Nomor Polis</td>
                        <td>: <?= $member['policy_no']; ?></td>
                    </tr>
                    <tr>
                        <td width="150">Nama</td>
                        <td>: <?= $personal['name']; ?></td>
                    </tr>
                    <tr>
                        <td>Nomor Peserta</td>
                        <td>: <?= $member['member_no']; ; ?></td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir</td>
                        <td>: <?= Utils::convertDateTodMyPrint($personal['birth_date'])?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
	<br>
	
	<p>Adalah Peserta Polis Dari Asuransi Jiwa (Pemegang Polis) </p>
	<br>
	<br>
	<h4 class="text-center"><b>BANK JAWA TIMUR</b></h4>
	<br>
	<br>
	<p>Dengan Ketentuan Sebagai Berikut :</p>
	<br>
	
	<table width="100%" cellpadding="5">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td width="150">Jenis Asuransi</td>
                        <td>: <?= $member->produk; ?></td>
                    </tr>
                    <tr>
                        <td>Masa Asuransi</td>
                        <td>: <?= $member['term'] ?> Bulan </td>
                    </tr>
                    <tr>
                        <td>Periode Asuransi</td>
                        <td>: <?= Utils::convertDateTodMyPrint($member['start_date'])?> s/d  <?= Utils::convertDateTodMyPrint($member['end_date'])?> </td>
                    </tr>
					
					<tr>
                        <td>Uang Pertanggungan</td>
                        <td>: <?= number_format($member['sum_insured']); ?></td>
                    </tr>
					
					<tr>
                        <td>Premi Gross</td>
                        <td>: <?= number_format($member['gross_premium']); ?></td>
                    </tr>
					
					<tr>
                        <td>Extra Premi </td>
                        <td>: <?= number_format($member['em_premium']); ?></td>
                    </tr>
					
					<tr>
                        <td>Total Premi</td>
                        <td>: <?= number_format($member['nett_premium']); ?></td>
                    </tr>
					
                </table>
            </td>
        </tr>
    </table>
	<br>
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

    <br>

    
</section>