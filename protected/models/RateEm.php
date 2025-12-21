<?php

namespace app\models;

class RateEm
{
    const RATE_EM_PRORATE = 'Prorate';
    const RATE_EM_ROUND_UP = 'Round Up';

    public static function rateEms()
    {
        return [
            self::RATE_EM_PRORATE => self::RATE_EM_PRORATE,
            self::RATE_EM_ROUND_UP => self::RATE_EM_ROUND_UP,
        ];
    }
}
