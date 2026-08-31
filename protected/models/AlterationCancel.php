<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alteration_cancel".
 *
 * @property int $id
 * @property string $alteration_no
 * @property string $alteration_date
 * @property string $policy_no
 * @property float $total_si
 * @property float $total_premium
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class AlterationCancel extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alteration_cancel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alteration_no', 'alteration_date', 'policy_no', 'total_si', 'total_premium', 'status'], 'required'],
            [['alteration_date', 'created_at', 'updated_at'], 'safe'],
            [['total_si', 'total_premium'], 'number'],
            [['created_by', 'updated_by'], 'integer'],
            [['alteration_no'], 'string', 'max' => 100],
            [['policy_no'], 'string', 'max' => 50],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alteration_no' => 'Alteration No',
            'alteration_date' => 'Alteration Date',
            'policy_no' => 'Policy No',
            'total_si' => 'Total Si',
            'total_premium' => 'Total Premium',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->asArray();

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        if (isset($params['offset']) && $params['offset'] != null) {
            $query->offset($params['offset']);
        }

        if (isset($params['limit']) && $params['limit'] != null) {
            $query->limit($params['limit']);
        }

        $query->orderBy(['id' => $params['sort']]);

        return $query->all();
    }

    public static function countAll($params = [])
    {
        $query = self::find();

        if (isset($params['policy_no']) && $params['policy_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.policy_no', $params['policy_no']]);
        }

        return $query->count();
    }

    public static function generateAlterationNo($params)
    {
        return $params['id'] . '/CNC/AJRI/' . date("Y");
    }
	
	// public function callAPIPostMemberLogin()
    // {
		
		// $url='http://demo-reliancelife.ajrius.id/api/login';
        // $headers = [
            // 'Content-Type: application/json',
        // ];
		// $data = json_encode([
			// 'email' => 'api@gmail.com',
            // 'password' => '12345678',
			
			
        // ]);
		
         // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_HEADER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // $response = curl_exec($ch);
        // $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

        // curl_close($ch);

        // return json_decode($body, true);
    // }
	
	public function callAPIPostMemberLogin()
    {
		
		// $url='http://demo-reliancelife.ajrius.id/api/login';
        // $headers = [
            // 'Content-Type: application/json',
        // ];
		// $data = json_encode([
			// 'email' => 'api@gmail.com',
            // 'password' => '12345678',
			
			
        // ]);
		
		
			 $url = 'https://reliancelife.ajrius.id/api/login';

		$data = [
			'email'    => 'adminapi@gmail.com',
			'password' => '12345678',
		];

		$jsonData = json_encode($data);

		$ch = curl_init();

		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $jsonData,

			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json',
				'Accept: application/json',
			],

			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CONNECTTIMEOUT => 10,

			// Untuk mengatasi SSL error
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
		]);

		$body = curl_exec($ch);

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlNo   = curl_errno($ch);
		$curlErr  = curl_error($ch);

		curl_close($ch);

		if ($curlNo !== 0) {
			return [
				'token'      => null,
				'http_code'  => $httpCode,
				'curl_errno' => $curlNo,
				'curl_error' => $curlErr,
			];
		}

		$response = json_decode($body, true);

		return [
			'token'      => isset($response['token']) ? $response['token'] : null,
			'expired'    => isset($response['expired']) ? $response['expired'] : null,
			'success'    => isset($response['success']) ? $response['success'] : false,
			'user'       => isset($response['user']) ? $response['user'] : null,
			'http_code'  => $httpCode,
			'curl_errno' => $curlNo,
			'curl_error' => $curlErr,
			'body'       => $body,
		];

    }
	
	
	
	public function callAPIPostMemberCancelPush(
    $token,
    $policy_number,
    $membersNo,
    $cancel_premi
	)
	{
    $url = 'https://reliancelife.ajrius.id/api/memo-cancel/store';

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token,
    ];

    $peserta = [];

    // Pastikan membersNo selalu array
    if (!is_array($membersNo)) {
        $membersNo = [$membersNo];
    }

    foreach ($membersNo as $memberNo) {
        $peserta[] = [
            'no_peserta' => $memberNo,
            'cancel_kontribusi_netto' => (float) $cancel_premi,
            'total_kontribusi_dibayar' => (float) $cancel_premi,
        ];
    }

    $data = [
        'no_polis' => $policy_number,
        'source' => 2,
        'peserta' => $peserta,
        'data' => [
            'tanggal_efektif'   => date('Y-m-d'),
            'tanggal_pengajuan' => date('Y-m-d'),
            'tujuan_pembayaran' => 'ABC',
            'nama_bank'         => 'ABC',
            'no_rekening'       => 'ABC',
        ],
    ];

    // Convert ke JSON
    $jsonData = json_encode($data);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $jsonData,

        CURLOPT_HTTPHEADER     => $headers,

        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,

        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlNo   = curl_errno($ch);
    $curlErr  = curl_error($ch);

    curl_close($ch);

    // =========================
    // DEBUG
    // =========================
    Yii::info([
        'url'        => $url,
        'http_code'  => $httpCode,
        'curl_errno' => $curlNo,
        'curl_error' => $curlErr,
        'request'    => $data,
        'json'       => $jsonData,
        'response'   => $response,
    ], 'cancel-api');

    // Jika CURL error
    if ($curlNo !== 0) {
        return [
            'code'       => '0',
            'http_code'  => $httpCode,
            'curl_errno' => $curlNo,
            'curl_error' => $curlErr,
            'message'    => 'CURL Error',
            'body'       => $response,
        ];
    }

    // Decode response
    $decode = json_decode($response, true);

    // Kalau response bukan JSON
    if (!is_array($decode)) {
        return [
            'code'      => (string) $httpCode,
            'http_code' => $httpCode,
            'message'   => 'Response bukan JSON',
            'body'      => $response,
        ];
    }

    // Tambahkan HTTP code supaya mudah dicek
    $decode['http_code'] = $httpCode;

    return $decode;
	}

}
