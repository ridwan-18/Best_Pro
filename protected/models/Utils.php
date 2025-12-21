<?php

namespace app\models;

use yii\helpers\HtmlPurifier;

class Utils
{
    public static function trueBirthDate($val)
    {
        $dob = date('Y-m-d', strtotime($val));
        $dobs = explode("-", $dob);
        $birthYear = $dobs[0];
        if ($dobs[0] > date("Y")) {
            $birthYear = $dobs[0] - 100;
        }
        return date('Y-m-d', strtotime($birthYear . '-' . $dobs[1] . '-' . $dobs[2]));
    }

    public static function convertDateTodMy($val)
    {
        if ($val == '' || $val == null) {
            return '';
        }
        return date('d-M-y', strtotime($val));
    }

    public static function convertDateToYmd($val)
    {
        return date('Y-m-d', strtotime($val));
    }

    public static function convertDateTodMyPrint($val)
    {
        if ($val == '' || $val == null) {
            return '';
        }
        return date('d F Y', strtotime($val));
    }

    public static function numberToText($number)
    {
        function convert($number)
        {
            $number = abs($number);
            $string = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            $temp = "";
            if ($number < 12) {
                $temp = $string[$number];
            } else if ($number < 20) {
                $temp = convert($number - 10) . " Belas ";
            } else if ($number < 100) {
                $temp = convert($number / 10) . " Puluh " . convert($number % 10);
            } else if ($number < 200) {
                $temp = " Seratus " . convert($number - 100);
            } else if ($number < 1000) {
                $temp = convert($number / 100) . " Ratus " . convert($number % 100);
            } else if ($number < 2000) {
                $temp = " Seribu" . convert($number - 1000);
            } else if ($number < 1000000) {
                $temp = convert($number / 1000) . " Ribu " . convert($number % 1000);
            } else if ($number < 1000000000) {
                $temp = convert($number / 1000000) . " Juta " . convert($number % 1000000);
            } else if ($number < 1000000000000) {
                $temp = convert($number / 1000000000) . " Milyar " . convert(fmod($number, 1000000000));
            } else if ($number < 1000000000000000) {
                $temp = convert($number / 1000000000000) . " Trilyun " . convert(fmod($number, 1000000000000));
            }
            return $temp;
        }

        function comma($number)
        {
            $str = stristr($number, ".");
            $ex = explode('.', $number);

            if (($ex[1] / 10) >= 1) {
                $a = abs($ex[1]);
            }
            $string = array("Nol", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            $temp = "";

            $a2 = $ex[1] / 10;
            $pjg = strlen($str);
            $i = 1;

            if ($a >= 1 && $a < 12) {
                $temp .= " " . $string[$a];
            } else if ($a > 12 && $a < 20) {
                $temp .= convert($a - 10) . " Belas ";
            } else if ($a > 20 && $a < 100) {
                $temp .= convert($a / 10) . " Puluh " . convert($a % 10);
            } else {
                if ($a2 < 1) {
                    while ($i < $pjg) {
                        $char = substr($str, $i, 1);
                        $i++;
                        $temp .= " " . $string[$char];
                    }
                }
            }
            return $temp;
        }

        if ($number < 0) {
            $result = "minus " . trim(convert($number));
        } else {
            $poin = trim(comma($number));
            $result = trim(convert($number));
        }

        if ($poin) {
            $result = $result . " Koma " . $poin;
        } else {
            $result = $result;
        }
        return $result;
    }

    public static function getRomanNumeral($month)
    {
        switch ($month) {
            case 1:
                return "I";
                break;
            case 2:
                return "II";
                break;
            case 3:
                return "III";
                break;
            case 4:
                return "IV";
                break;
            case 5:
                return "V";
                break;
            case 6:
                return "VI";
                break;
            case 7:
                return "VII";
                break;
            case 8:
                return "VIII";
                break;
            case 9:
                return "IX";
                break;
            case 10:
                return "X";
                break;
            case 11:
                return "XI";
                break;
            case 12:
                return "XII";
                break;
        }
    }

    public static function removeComma($val)
    {
        return intval(preg_replace('/[^\d.]/', '', $val));
    }

    public static function sanitize($value)
    {
        if ($value == null) {
            return '';
        }
        return strip_tags(HtmlPurifier::process($value));
    }
	
	public static function tgl_indo($tanggal){
	$bulan = array (
		1 =>   'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);
	$pecahkan = explode('-', $tanggal);
	
	// variabel pecahkan 0 = tanggal
	// variabel pecahkan 1 = bulan
	// variabel pecahkan 2 = tahun
 
	return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
	}
}
