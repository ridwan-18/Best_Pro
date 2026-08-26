<?php

namespace app\controllers\api;

use Yii;
use yii\rest\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    public function actionToken()
    {
        $request = Yii::$app->request;

        // Ambil JSON body
        $body = $request->getBodyParams();

        // Jika body tidak terbaca
        if (!is_array($body) || empty($body)) {
            return [
                'Result' => [
                    'status' => '400',
                    'kode_response' => '01',
                    'message' => 'Request body tidak valid',
                ]
            ];
        }

        // Ambil parameter
        $clientId = $body['client_id'] ?? null;
        $clientSecret = $body['client_secret'] ?? null;
        $username = $body['username'] ?? null;
        $password = $body['password'] ?? null;
        $grantType = $body['grand_type'] ?? null;
        $scope = $body['scope'] ?? null;

        // Validasi parameter
        if (
            empty($clientId) ||
            empty($clientSecret) ||
            empty($username) ||
            empty($password)
        ) {
            return [
                'Result' => [
                    'status' => '400',
                    'kode_response' => '02',
                    'message' => 'Parameter tidak lengkap',
                ]
            ];
        }

        // Validasi client
        if (
            $clientId !== 'BRKSH2HDEV' ||
            $clientSecret !== 'BRKSH2HDEV'
        ) {
            return [
                'Result' => [
                    'status' => '401',
                    'kode_response' => '03',
                    'message' => 'Client ID atau Client Secret salah',
                ]
            ];
        }

        // Validasi grant type
        if ($grantType !== 'password credential') {
            return [
                'Result' => [
                    'status' => '400',
                    'kode_response' => '04',
                    'message' => 'Grant type tidak valid',
                ]
            ];
        }

        // Validasi username dan password
        if (
            $username !== 'devbrks' ||
            $password !== 'BRKSH2H2341'
        ) {
            return [
                'Result' => [
                    'status' => '401',
                    'kode_response' => '05',
                    'message' => 'Username atau password salah',
                ]
            ];
        }

        // Generate JWT
        $token = $this->generateToken(
            $username,
            $clientId,
            $scope
        );

        return [
            'Result' => [
                'status' => '200',
                'kode_response' => '00',
                'message' => 'Request berhasil',
                'token' => $token,
            ]
        ];
    }

    private function generateToken($username, $clientId, $scope)
{
    $header = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    $issuedAt = time();
    $expiredAt = $issuedAt + 86400; // 24 jam

    $payload = [
        'iss' => 'BRKS H2H',
        'sub' => $username,
        'client_id' => $clientId,
        'scope' => $scope,
        'iat' => $issuedAt,
        'exp' => $expiredAt
    ];

    $base64UrlEncode = function ($data) {
        return rtrim(
            strtr(
                base64_encode(
                    json_encode($data)
                ),
                '+/',
                '-_'
            ),
            '='
        );
    };

    $encodedHeader = $base64UrlEncode($header);
    $encodedPayload = $base64UrlEncode($payload);

    $secret = 'BRKS-H2H-SECRET-2026';

    $signature = hash_hmac(
        'sha256',
        $encodedHeader . '.' . $encodedPayload,
        $secret,
        true
    );

    $encodedSignature = rtrim(
        strtr(
            base64_encode($signature),
            '+/',
            '-_'
        ),
        '='
    );

    return $encodedHeader
        . '.'
        . $encodedPayload
        . '.'
        . $encodedSignature;
}
}