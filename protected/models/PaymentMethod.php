<?php

namespace app\models;

class PaymentMethod
{
    const SINGLE = 'Single';
    const MONTHLY = 'Monthly';
    const QUARTERLY = 'Quarterly';
    const ANNUALLY = 'Annually';
    const SEMI_ANNUALLY = 'Semi Annually';

    public static function paymentMethods()
    {
        return [
            self::SINGLE => self::SINGLE,
            self::MONTHLY => self::MONTHLY,
            self::QUARTERLY => self::QUARTERLY,
            self::ANNUALLY => self::ANNUALLY,
            self::SEMI_ANNUALLY => self::SEMI_ANNUALLY,
        ];
    }

    public static function translate($value)
    {
        if ($value == self::SINGLE) {
            return 'Sekaligus';
        }

        if ($value == self::MONTHLY) {
            return 'Bulanan';
        }

        if ($value == self::QUARTERLY) {
            return 'Tiga Bulan';
        }

        if ($value == self::ANNUALLY) {
            return 'Tahunan';
        }

        return '';
    }
}
