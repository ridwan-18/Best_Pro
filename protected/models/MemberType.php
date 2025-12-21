<?php

namespace app\models;

class MemberType
{
    const EMPLOYEE = 'EMPLOYEE';
    const ANGGOTA = 'ANGGOTA';
    const EMPLOYEE_SPOUSE_CHILD = 'EMPLOYEE SPOUSE CHILD';

    public static function memberTypes()
    {
        return [
            self::EMPLOYEE => self::EMPLOYEE,
            self::ANGGOTA => self::ANGGOTA,
            self::EMPLOYEE_SPOUSE_CHILD => self::EMPLOYEE_SPOUSE_CHILD,
        ];
    }
}
