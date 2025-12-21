<?php

namespace app\models;

class Term
{
    const _1_MONTH = '1 Bulan';
    const _2_MONTH = '2 Bulan';
    const _3_MONTH = '3 Bulan';
    const _4_MONTH = '4 Bulan';
    const _5_MONTH = '5 Bulan';
    const _6_MONTH = '6 Bulan';
    const _7_MONTH = '7 Bulan';
    const _8_MONTH = '8 Bulan';
    const _9_MONTH = '9 Bulan';
    const _10_MONTH = '10 Bulan';
    const _11_MONTH = '11 Bulan';
    const _12_MONTH = '12 Bulan';
    const OVER_12_MONTH = '> 12 Bulan';
    const UNDER_1_MONTH = '< 1 Bulan';
    const UNDER_2_WEEK = '< 2 Minggu';
    const UNDER_1_WEEK = '< 1 Minggu';
    const OPEN_POLIS = 'Open Polis';

    public static function terms()
    {
        return [
            self::_1_MONTH => self::_1_MONTH,
            self::_2_MONTH => self::_2_MONTH,
            self::_3_MONTH => self::_3_MONTH,
            self::_4_MONTH => self::_4_MONTH,
            self::_5_MONTH => self::_5_MONTH,
            self::_6_MONTH => self::_6_MONTH,
            self::_7_MONTH => self::_7_MONTH,
            self::_8_MONTH => self::_8_MONTH,
            self::_9_MONTH => self::_9_MONTH,
            self::_10_MONTH => self::_10_MONTH,
            self::_11_MONTH => self::_11_MONTH,
            self::_12_MONTH => self::_12_MONTH,
            self::OVER_12_MONTH => self::OVER_12_MONTH,
            self::UNDER_1_MONTH => self::UNDER_1_MONTH,
            self::UNDER_2_WEEK => self::UNDER_2_WEEK,
            self::UNDER_1_WEEK => self::UNDER_1_WEEK,
            self::OPEN_POLIS => self::OPEN_POLIS,
        ];
    }
}
