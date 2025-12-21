<?php

namespace app\models;

class Api
{
    const KEY = 'zXF6srYAwRlshsX9guL3SyDWNu3yhTcT-jEfvwM9srsKJinLvTabjWKi9AB9hHe1l';
    const SECRET = 'CHKk2XVf0bwWaZMf5r8BniI5ehdL1Tk3-GkLPX4sY5VZCzAxBhBCsPD6DB269GSA6';
    const POLICY_NO = '1012307000001';

    public static function validate($k, $s)
    {
        return (self::KEY == $k && self::SECRET == $s);
    }
}
