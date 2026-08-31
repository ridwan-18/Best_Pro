<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alteration_refund".
 *
 * @property int $id
 * @property string $alteration_no
 * @property string $alteration_date
 * @property string $policy_no
 * @property float $total_si
 * @property float $total_premium
 * @property float $total_premium_refund
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class AlterationRefund extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alteration_refund';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alteration_no', 'alteration_date', 'policy_no', 'total_si', 'total_premium', 'total_premium_refund', 'status'], 'required'],
            [['alteration_date', 'created_at', 'updated_at'], 'safe'],
            [['total_si', 'total_premium', 'total_premium_refund'], 'number'],
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
            'total_premium_refund' => 'Total Premium Refund',
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
        return $params['id'] . '/CNR/AJRI/' . date("Y");
    }
	
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
	
	public function callAPIPostMemberRefundPush(
    $token,
    $policy_number,
    $newEndDates,
    $membersNo,
    $remainingTerm,
    $premiRefunds,
	$totalPremium
	) 
	{
			$url = 'https://reliancelife.ajrius.id/api/memo-refund/store';

			$tanggalEfektif = is_array($newEndDates)
				? $newEndDates[0]
				: $newEndDates;

			$noPeserta = is_array($membersNo)
				? $membersNo[0]
				: $membersNo;

			$data = [
				'no_polis' => $policy_number,

				'data' => [
					'tanggal_pengajuan'    => date('Y-m-d'),
					'tanggal_efektif'      => $tanggalEfektif,
					'tujuan_pembayaran'    => 'Transfer',
					'nama_bank'            => 'BCA',
					'nomor_peserta_awal'   => $noPeserta,
					'nomor_peserta_akhir'  => $noPeserta
				],

				'peserta' => [
					[
						'no_peserta' => $noPeserta,

						'refund_tanggal_efektif' => $tanggalEfektif,

						'refund_sisa_masa_asuransi' => $remainingTerm,

						'total_kontribusi_dibayar' => $totalPremium,

						'refund_kontribusi' => $premiRefunds
					]
				]
			];

			$jsonData = json_encode($data);

			if ($jsonData === false) {
				return [
					'success' => false,
					'message' => 'JSON Encode Error: ' . json_last_error_msg()
				];
			}

			$headers = [
				'Content-Type: application/json',
				'Accept: application/json',
				'Authorization: Bearer ' . trim($token)
			];

			$ch = curl_init();

			curl_setopt_array($ch, [
				CURLOPT_URL            => $url,
				CURLOPT_POST           => true,
				CURLOPT_POSTFIELDS     => $jsonData,

				CURLOPT_HTTPHEADER     => $headers,

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_CONNECTTIMEOUT => 10,
				CURLOPT_TIMEOUT        => 60,

				// sementara untuk debugging SSL
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,

				CURLOPT_FOLLOWLOCATION => true,

				CURLOPT_VERBOSE        => false,
			]);

			$response = curl_exec($ch);

			$curlNo  = curl_errno($ch);
			$curlErr = curl_error($ch);

			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

			curl_close($ch);

			// ================= DEBUG =================

			// echo "<pre>";

			// echo "========================================\n";
			// echo "REFUND API DEBUG\n";
			// echo "========================================\n\n";

			// echo "URL:\n";
			// echo $url . "\n\n";

			// echo "HTTP CODE:\n";
			// var_dump($httpCode);

			// echo "\nCURL ERRNO:\n";
			// var_dump($curlNo);

			// echo "\nCURL ERROR:\n";
			// var_dump($curlErr);

			// echo "\nCONTENT TYPE:\n";
			// var_dump($contentType);

			// echo "\nTOKEN:\n";
			// var_dump(
				// empty($token)
					// ? 'TOKEN KOSONG'
					// : substr($token, 0, 30) . '...'
			// );

			// echo "\nREQUEST JSON:\n";
			// echo $jsonData;

			// echo "\n\nRAW RESPONSE:\n";
			// var_dump($response);

			// echo "\n\nJSON DECODE:\n";

			// $decode = json_decode($response, true);

			// var_dump($decode);

			// echo "\nJSON ERROR:\n";
			// var_dump(json_last_error_msg());

			// echo "</pre>";

			// die;

			// =========================================

			if ($curlNo !== 0) {
				return [
					'success'    => false,
					'http_code'  => $httpCode,
					'curl_errno' => $curlNo,
					'curl_error' => $curlErr,
					'body'       => $response
				];
			}

			return [
				'success'    => true,
				'http_code'  => $httpCode,
				'body'       => $response,
				'data'       => $decode
			];
		}
	
}
