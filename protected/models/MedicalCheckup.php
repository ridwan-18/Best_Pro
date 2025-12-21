<?php

namespace app\models;

class MedicalCheckup
{
    const MC_CMP = 'Candidate Member Policy';
    const MC_PH = 'Policy Holder';
    const MC_INSURER = 'Insurer';
    const MC_NONE = 'None';

    public static function medicalCheckups()
    {
        return [
            self::MC_CMP => self::MC_CMP,
            self::MC_PH => self::MC_PH,
            self::MC_INSURER => self::MC_INSURER,
            self::MC_NONE => self::MC_NONE,
        ];
    }
}
