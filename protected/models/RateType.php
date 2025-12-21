<?php

namespace app\models;

class RateType
{
    const PRORATE_MONTH = 'Prorate Month';
    const PRORATE_DAY = 'Prorate Day';
    const RATE_ROUND_UP = 'Rate Round Up';

    public static function rateTypes()
    {
        return [
            self::PRORATE_MONTH => self::PRORATE_MONTH,
            self::PRORATE_DAY => self::PRORATE_DAY,
            self::RATE_ROUND_UP => self::RATE_ROUND_UP,
        ];
    }
}
