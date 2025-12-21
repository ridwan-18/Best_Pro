<?php

namespace app\models;

class PeriodType
{
    const ANNUALLY = 'Annually';
    const MONTHLY = 'Monthly';

    public static function periodTypes()
    {
        return [
            self::ANNUALLY => self::ANNUALLY,
            self::MONTHLY => self::MONTHLY,
        ];
    }
}
