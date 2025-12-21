<?php

namespace app\models;

class ProductRateType
{
    const AGE_ONLY = 'Age Only';
    const AGE_TERM = 'Age Term';
    const TERM_ONLY = 'Term Only';

    public static function productRateTypes()
    {
        return [
            self::AGE_ONLY => self::AGE_ONLY,
            self::AGE_TERM => self::AGE_TERM,
            self::TERM_ONLY => self::TERM_ONLY,
        ];
    }
}
