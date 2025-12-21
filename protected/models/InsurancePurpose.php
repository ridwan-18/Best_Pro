<?php

namespace app\models;

class InsurancePurpose
{
    const RISK_COVER = 'Risk Cover';
    const OTHERS = 'Others';

    public static function insurancePurposes()
    {
        return [
            self::RISK_COVER => self::RISK_COVER,
            self::OTHERS => self::OTHERS,
        ];
    }
}
