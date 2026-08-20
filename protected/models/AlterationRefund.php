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
		
		$url='http://demo-reliancelife.ajrius.id/api/login';
        $headers = [
            'Content-Type: application/json',
        ];
		$data = json_encode([
			'email' => 'api@gmail.com',
            'password' => '12345678',
			
			
        ]);
		
         $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));

        curl_close($ch);

        return json_decode($body, true);
    }
	
	public function callAPIPostMemberRefundPush($token,$policy_number,$newEndDates,$membersNo,$remainingTerm,$premiRefund,$totalPremium)
	{
		$headers = [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $token
		];

		$data = [
			'no_polis' => $policy_number ,

			'data' => [
				'tanggal_pengajuan'   => date('Y-m-d'),
				'tanggal_efektif'     => is_array($newEndDates) ? $newEndDates[0] : $newEndDates,
				'tujuan_pembayaran'   => 'Transfer',
				'nama_bank'           => 'BCA',
				'nomor_peserta_awal' => $membersNo,
				'nomor_peserta_akhir' =>  $membersNo
			],

			'peserta' => [
				[
					'no_peserta' => $membersNo,

					'refund_tanggal_efektif' =>
						is_array($newEndDates) ? $newEndDates[0] : $newEndDates,

					'refund_sisa_masa_asuransi' => $remainingTerm,

					'total_kontribusi_dibayar' => $totalPremium,

					'refund_kontribusi' => $premiRefund
				]
			]
		];

		$ch = curl_init('http://demo-reliancelife.ajrius.id/api/memo-refund/store');


		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => $headers
		]);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			return [
				'success' => false,
				'message' => curl_error($ch)
			];
		}

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		echo "<pre>";
		echo "HTTP CODE : ".$httpCode."<br><br>";

		echo "REQUEST<br>";
		print_r($data);

		echo "<br><br>RESPONSE<br>";
		var_dump($response);

		$decode = json_decode($response, true);

		// echo "<br><br>JSON DECODE<br>";
		// var_dump($decode);

		// die;

		return $decode;
	}
	
}
