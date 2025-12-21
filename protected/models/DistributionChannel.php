<?php

namespace app\models;

class DistributionChannel
{
    const DIRECT_MARKETING = 'DIRECT MARKETING';
    const BROKER = 'BROKER';
    const CO_INSURANCE = 'CO-INSURANCE';
    const TELEMARKETING = 'TELEMARKETING';
    const AGENCY = 'AGENCY';
    const BANCCASURANCE = 'BANCCASURANCE';
    const CROSS_SELLING = 'CROSS SELLING';
    const DIGITAL_MARKETING = 'DIGITAL MARKETING';
    const NOT_ANALYSIS = 'NOT ANALYSIS';

    public static function distributionChannels()
    {
        return [
            self::DIRECT_MARKETING => self::DIRECT_MARKETING,
            self::BROKER => self::BROKER,
            self::CO_INSURANCE => self::CO_INSURANCE,
            self::TELEMARKETING => self::TELEMARKETING,
            self::AGENCY => self::AGENCY,
            self::BANCCASURANCE => self::BANCCASURANCE,
            self::CROSS_SELLING => self::CROSS_SELLING,
            self::DIGITAL_MARKETING => self::DIGITAL_MARKETING,
            self::NOT_ANALYSIS => self::NOT_ANALYSIS,
        ];
    }
}
