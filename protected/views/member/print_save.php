<?php

use app\models\Utils;
use app\models\PaymentMethod;
use app\models\Member;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Print Policy';
?>

<html>
<head>
<style>
    * {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: 50% 25%;
            background-size: 20%;
			background-image: url("http://localhost/BestPro/images/background.png");
			background-color: #FFFFFF;
        }

        header,
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

        footer {
            bottom: 300px;
        }

        footer td {
            font-size: 10px;
        }

        .page {
            margin: 8rem 10rem 0 10rem;
        }

        .page+.page {
            page-break-before: always;
        }

        .title {
            font-size: 18px;
        }

        .subtitle {
            font-size: 16px;
        }

        .leaderboard {
            height: 60px;
        }

        .signature {
            margin-left: auto;
            margin-right: auto;
            max-height: 100px;
        }

        .col-colon {
            width: 1%;
        }

        .col-label {
            width: 15%;
        }

        .col-value {
            width: 21%;
        }

        .col-space {
            width: 26%;
        }

        .w-full {
            width: 100%;
        }

        .align-bottom {
            vertical-align: bottom;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-sm {
            font-size: 12px;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .border {
            border: 1px solid;
        }

        .border-bottom {
            border-bottom: 1px solid;
        }

        .p-1 {
            padding: 8px;
        }

        .pt-2 {
            padding-top: 16px;
        }

        .pl-2 {
            padding-left: 16px;
        }

        .pr-2 {
            padding-right: 16px;
        }
		
		
		
</style>
</head>
<section class="sheet padding-10mm">

<body>
    <header>
	
		<img style="width: 200px;float:left;margin-top:6px;" src="http://localhost/BestPro/images/reliance-life.png">
		
        <img style="width: 130px;float:right;margin-right: 20px;" src="http://localhost/BestPro/images/mari-berasuransi.png">
        <div style="clear:both"></div>
    </header>

    <footer>
       <table width="100%" cellpadding="5">
            <tr class="align-bottom text-center">
                <td class="text-left pl-2">
                    <span class="font-bold">PT Asuransi Jiwa Reliance Indonesia</span><br>
                    Gedung Soho West Point, Kota Kedoya<br>
                    Jl. Macan, Kav. 4-5, Kedoya Utara, Kebon Jeruk <br>
					Jakarta Barat 11510
                </td>
                <td>
                    Telp. +62 21 2119 2288 (Hotline)
                </td>
                <td>
                    www.reliance-life.com<br>
                    info@reliance-life.com
                </td>
                <td class="text-right pr-2">
				
                    <img class="leaderboard" src="http://localhost/BestPro/images/leaderboard.png">
                </td>
            </tr>
        </table>
    </footer>

    <main>
      
        <div class="page">
            <table width="100%" cellpadding="5">
                <tr>
                    <td class="col-label"></td>
                    <td class="col-colon"></td>
                    <td class="col-value"></td>
                    <td class="col-space"></td>
                    <td class="col-label"></td>
                    <td class="col-colon"></td>
                    <td class="col-value"></td>
                </tr>
                <tr>
                    <td class="w-full text-center" colspan="7">
                        <span class="title font-bold">SERTIFIKAT ASURANSI</span><br>
                        No Sertifikat: 3763736736
                    </td>
                </tr>
                <tr>
				 
                    <td>Nama</td>
                    <td>:</td>
                    <td>ytrtrtr</td>
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td>Nomor Peserta</td>
                    <td>:</td>
                    <td>6325765276537657625</td>
                    <td colspan="8">&nbsp;</td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td>07-07-2026</td>
                    <td colspan="">&nbsp;</td>
                </tr>
				
				
				
				
                <tr>
                    <td class="pt-1"  colspan="7">Adalah Peserta dari Polis Asuransi Jiwa (Pemegang Polis) :</td>
                </tr>
                <tr>
                    <td class="p-1 text-center uppercase subtitle" colspan="7">tesss</td>
                </tr>
                <tr>
                    <td class="pt-1" colspan="7">Dengan ketentuan pertanggungan sebagai berikut :</td>
                </tr>
				
				<table width="100%" cellpadding="5">
                <tr>
                    <td>Jenis Asuransi</td>
					<td>: <?= $members->produk; ?></td>
                    
                    <td>&nbsp;</td>
                    <td>Masa Asuransi</td>
                    <td>:</td>
                    <td>4 Bulan</td>
                </tr>
                <tr>
                    <td>Manfaat Asuransi</td>
                    <td>:</td>
                    <td>teee</td>
                    <td>&nbsp;</td>
                    <td>Uang Pertanggungan</td>
                    <td>:</td>
                    <td>80.000.000</td>
                </tr>
                <tr>
                    <td>Periode Asuransi</td>
                    <td>:</td>
                    <td colspan="2">
                       09-09-2029
                        s/d
                        09-09-2029
                    </td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" rowspan="5">&nbsp;</td>
                    <td class="text-right" colspan="3">Jakarta, 22-02-2026</td>
                </tr>
                <tr>
                    <td class="text-right uppercase" colspan="3">PT Asuransi Jiwa Reliance Indonesia</td>
                </tr>
                <tr>
                    <td class="text-right" colspan="3">
                        <img class="signature" src="http://localhost/BestPro/images/gideon-heru-prasetya.png">
						
                    </td>
                </tr>
                <tr>
                    <td class="text-right" colspan="3">
                        <span class="border-bottom">Gideon Heru Prasetya</span>
                    </td>
                </tr>
                <tr>
                    <td class="text-center" colspan="3">Directur</td>
                </tr>
                <tr>
                    <td class="border text-sm" colspan="4" style="padding:5px;">
                        Sertifikat ini tunduk pada Ketentuan Umum Polis Asuransi dan ketentuanketentuan lain yang tercantum di dalam atau melekat pada Polis dan merupakan bagian yang tidak terpisahkan dari Perjanjian Asuransi.
                    </td>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-sm" colspan="7">
                        Keterangan :<br>
                        Pertanggungan pada sertifikat ini berlaku apabila pembayaran sudah dilakukan dan efektif masuk ke dalam rekening {{$companyName}}
                    </td>
                    <td colspan="4">&nbsp;</td>
                </tr>
            </table>
        </div>
       
    </main>
</body>
</section>
</html>