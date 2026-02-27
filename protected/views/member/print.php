<?php

use app\models\Utils;
use app\models\PaymentMethod;
use app\models\Member;
use app\models\Personal;
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
         font-size: 11px;
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
	
	
        footer {
            width: 100%;
            text-align: center;
            position: fixed;
        }

        header {
            top: 0px;
            background-color: #2d2c6e;
            border-bottom: 3px solid #e03425;
            padding:0;
        }
		
		 body {
            margin: 0;
            padding: 0;
        }

        body {
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: 50% 25%;
            background-size: 20%;
			background-image: url("https://devweb.bestpro-id.com/images/background.png");
			background-color: #FFFFFF;
        }
		
		 .text-sm {
            font-size: 11px;
        }
		
		.text-ft {
            font-size: 10px;
        }
		
		.border {
            border: 1px solid;
        }
		
		 .leaderboard {
            height: 60px;
        }
	 
	
</style>

<section class="sheet padding-10mm">
<body>

 <header>
	
		<img style="width: 200px;float:left;margin-top:6px;" src="https://devweb.bestpro-id.com/images/reliance-life.png">
		
        <img style="width: 130px;float:right;margin-right: 20px;" src="https://devweb.bestpro-id.com/images/mari-berasuransi.png">
        <div style="clear:both"></div>
    </header>


    <h4 class="text-center"><b>SERTIFIKAT ASURANSI</b></h4>
	<h4 class="text-center">NOMOR: <?=  $member->id_loan; ?> </h4>


    <table width="100%" cellpadding="5">
        <tr>
            <td width="50%">
                <table width="100%">
				<tr>
                        <td width="150" >Nomor Polis</td>
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
	<h4 class="text-center"><b>JAMKRIDA JABAR</b></h4>
	<br>
	<p>Dengan Ketentuan pertanggungan Sebagai Berikut :</p>
	<table width="110%" cellpadding="6" border="1">
        <tr>
            <td width="50%">
                <table width="100%">
					<tr>
                        <td width="80" >Jenis Asuransi</td>
                        <td>:  Reliance Credit Life</td>
                    </tr>
                    <tr>
                        <td width="80">Manfaat Asuransi </td>
                        <td><p>: Kepada Pemegang Polis dibayarkan
						
							sejumlah sisa hutang pokok kredit 
							tanpa biaya tunggakan dan bunga
							</p></td>
					</td>
                    </tr>
                </table>
            </td>
			
			<td width="50%">
                <table width="100%">
				
				
                    <tr>
                       <td width="30%">Periode Asuransi</td>
                        <td>: <?= Utils::convertDateTodMyPrint($member['start_date'])?> s/d  <?= Utils::convertDateTodMyPrint($member['end_date'])?> </td>
                    </tr>
                    <tr>
                        <td>Masa Asuransi</td>
                        <td>: <?= $member['term'] ?> Bulan </td>
                    </tr>
					<tr>
                        <td>Uang Pertanggungan</td>
                        <td>: <?= number_format($member['sum_insured']); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
		
		<tr>
            <td width="50%" class="border text-ft" >
                <table width="100%" >
				<p>
				1. Sertifikat ini tunduk pada ketentuan umum Polis Asuransi dan 
					ketentuan lain yang tercantum atau melekat pada polis dan 
					merupakan bagian yang tidak terpisahkan dari perjanjian asuransi.
					<br>
					2. Pertanggungan ini sewaktu – waktu dapat berubah dengan terlebih 
					dahulu ada pemberitahuan maksimal 30 hari sebelum tanggal 
					perubahan kondisi pertanggungan.
				</p>
				<br>
                </table>
            </td>
			
			<table width="100%">
        <tr>
            <td width="70%"></td>
            <td width="30%" align="center">
                 <p>Jakarta, <?= Utils::convertDateTodMyPrint($member['updated_at']); ?></p>
				 <p>PT Asuransi Jiwa Reliance Indonesia</p>
                <img src="https://devweb.bestpro-id.com/images/gideon-heru-prasetya.png" alt="Signature" class="text-center" height="75">
               
                <p><b><u>Gideon Heru Prasetya</u></b></p>
                <p>Directur</p>
            </td>
        </tr>
    </table>
	
       <table width="100%"  cellpadding="5" class=" text-sm" >
                <td>
				<br>
				<br>
				<br>
				<br>
				<br>
				<br>
                    <span class="font-bold">PT Asuransi Jiwa Reliance Indonesia</span><br>
                    Gedung Soho West Point, Kota Kedoya<br>
                    Jl. Macan, Kav. 4-5, Kedoya Utara, Kebon Jeruk <br>
					Jakarta Barat 11510
                </td>
          
		<td style="width:60%;">
		<table style="width:100%;">
        <tr>
            <td style="padding-top:40px;">
               
            </td>
			
			<table style="width:100%;">
			<td style="padding-top:40px;">
				
                    <img class="leaderboard" src="https://devweb.bestpro-id.com/images/leaderboard.png">
                </td>
				 </table>
        </tr>
    </table>
		</td>
			
		
		
			
	
        </tr>
    </table>
   </body> 
</section>