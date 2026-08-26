<?php

namespace app\models;

use Yii;

class Api
{
    const KEY = 'zXF6srYAwRlshsX9guL3SyDWNu3yhTcT-jEfvwM9srsKJinLvTabjWKi9AB9hHe1l';

    const SECRET = 'CHKk2XVf0bwWaZMf5r8BniI5ehdL1Tk3-GkLPX4sY5VZCzAxBhBCsPD6DB269GSA6';

    const POLICY_NO = '1012307000001';

    /**
     * Validasi API Key dan Secret
     */
    public static function validate($k, $s)
    {
        return hash_equals(self::KEY, $k)
            && hash_equals(self::SECRET, $s);
    }

    /**
     * Generate token baru
     */
    public static function generateToken()
    {
        $token = bin2hex(random_bytes(32));

        $createdAt = date('Y-m-d H:i:s');
        $expiredAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Hapus token lama
        Yii::$app->db->createCommand()
            ->delete('api_token')
            ->execute();

        // Simpan token baru
        Yii::$app->db->createCommand()
            ->insert('api_token', [
                'token'      => $token,
                'created_at' => $createdAt,
                'expired_at' => $expiredAt,
            ])
            ->execute();

        return [
            'token'      => $token,
            'expired_at' => $expiredAt,
        ];
    }

    /**
     * Validasi Bearer Token
     */
    public static function validateBearerToken($token)
    {
        $data = Yii::$app->db
            ->createCommand("
                SELECT *
                FROM api_token
                WHERE token = :token
                AND expired_at > NOW()
                LIMIT 1
            ")
            ->bindValue(':token', $token)
            ->queryOne();

        return !empty($data);
    }
}