<?php

namespace app\models;

class AgeCalculate
{
    const LAST_BIRTHDAY = 'Last Birthday';
    const NEXT_BIRTHDAY = 'Next Birthday';
    const NEAREST_BIRTHDAY = 'Nearest Birthday';

    public static function ageCalculates()
    {
        return [
            self::LAST_BIRTHDAY => self::LAST_BIRTHDAY,
            self::NEXT_BIRTHDAY => self::NEXT_BIRTHDAY,
            self::NEAREST_BIRTHDAY => self::NEAREST_BIRTHDAY,
        ];
    }
}
