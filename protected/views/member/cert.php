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
           background-image: url("background.png");
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

<body>
    <header>
        <img style="width: 200px;float:left;margin-top:6px;" src="reliance-life.png">
        <img style="width: 130px;float:right;margin-right: 20px;" src="mari-berasuransi.png">
        <div style="clear:both"></div>
    </header>

    <footer>
        <table class="w-full">
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
                    <img class="leaderboard" src="leaderboard.png">
                </td>
            </tr>
        </table>
    </footer>

    <main>
        @foreach($pengajuan->kepesertaan->where('status_akseptasi',1) as $kepesertaan)
        <div class="page">
            <table class="w-full">
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
                        No Sertifikat: {{$kepesertaan->no_sertifikat}}
                    </td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{$kepesertaan->nama}}</td>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td>Nomor Peserta</td>
                    <td>:</td>
                    <td>{{$kepesertaan->no_peserta}}</td>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td>Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{format_tanggal($kepesertaan->tanggal_lahir)}}</td>
                    <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                    <td class="pt-1" colspan="7">Adalah Peserta dari Polis Asuransi Jiwa (Pemegang Polis) :</td>
                </tr>
                <tr>
                    <td class="p-1 text-center uppercase subtitle" colspan="7">{{$polis->nama}}</td>
                </tr>
                <tr>
                    <td class="pt-1" colspan="7">Dengan ketentuan pertanggungan sebagai berikut :</td>
                </tr>
                <tr>
                    <td>Jenis Asuransi</td>
                    <td>:</td>
                    <td>{{$polis->produk->nama}}</td>
                    <td>&nbsp;</td>
                    <td>Masa Asuransi</td>
                    <td>:</td>
                    <td>{{$kepesertaan->masa_bulan}} Bulan</td>
                </tr>
                <tr>
                    <td>Manfaat Asuransi</td>
                    <td>:</td>
                    <td>{{$polis->produk->type_manfaat_asuransi}}</td>
                    <td>&nbsp;</td>
                    <td>Uang Pertanggungan</td>
                    <td>:</td>
                    <td>{{format_idr($kepesertaan->basic)}}</td>
                </tr>
                <tr>
                    <td>Periode Asuransi</td>
                    <td>:</td>
                    <td colspan="2">
                        {{format_tanggal($kepesertaan->tanggal_mulai)}}
                        s/d
                        {{format_tanggal($kepesertaan->tanggal_akhir)}}
                    </td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" rowspan="5">&nbsp;</td>
                    <td class="text-center" colspan="3">Jakarta, 22-02-2026</td>
                </tr>
                <tr>
                    <td class="text-center uppercase" colspan="3">PT Asuransi Jiwa Reliance Indonesia</td>
                </tr>
                <tr>
                    <td class="text-center" colspan="3">
                        <img class="signature" src="gideon-heru-prasetya.png">
                    </td>
                </tr>
                <tr>
                    <td class="text-center" colspan="3">
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

</html>