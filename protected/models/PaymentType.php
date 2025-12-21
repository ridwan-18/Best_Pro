<?php

namespace app\models;

class PaymentType
{
    const SINGLE = 'Single';
    const MONTHLY = 'Monthly';
    const QUARTERLY = 'Quarterly';
    const ANNUALLY = 'Annually';
    const SEMI_ANNUALLY = 'Semi Annually';

    public static function paymentTypes()
    {
        return [
            self::SINGLE => self::SINGLE,
            self::MONTHLY => self::MONTHLY,
            self::QUARTERLY => self::QUARTERLY,
            self::ANNUALLY => self::ANNUALLY,
            self::SEMI_ANNUALLY => self::SEMI_ANNUALLY,
        ];
    }
}
