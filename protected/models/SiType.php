<?php

namespace app\models;

class SiType
{
    const FIXED = 'Fixed';
    const DECREASED = 'Decreased';

    public static function siTypes()
    {
        return [
            self::FIXED => self::FIXED,
            self::DECREASED => self::DECREASED,
        ];
    }
}
