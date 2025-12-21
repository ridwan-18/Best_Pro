<?php

namespace app\models;

class PremiumType
{
    const AMOUNT = 'Amount';
    const REGULER = 'Reguler';
    const SINGLE = 'Single';

    public static function premiumTypes()
    {
        return [
            self::AMOUNT => self::AMOUNT,
            self::REGULER => self::REGULER,
            self::SINGLE => self::SINGLE,
        ];
    }
}
