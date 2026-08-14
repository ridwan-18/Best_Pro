<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alteration_endorsement".
 *
 * @property int $id
 * @property string $alteration_no
 * @property string $alteration_date
 * @property string $policy_no
 * @property string $description
 * @property float $total_si
 * @property float $new_total_si
 * @property float $total_premium
 * @property float $new_total_premium
 * @property string $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class AlterationEndorsement extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alteration_endorsement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alteration_no', 'alteration_date', 'policy_no', 'description', 'total_si', 'new_total_si', 'total_premium', 'new_total_premium', 'status'], 'required'],
            [['alteration_date', 'created_at', 'updated_at'], 'safe'],
            [['total_si', 'new_total_si', 'total_premium', 'new_total_premium'], 'number'],
            [['created_by', 'updated_by'], 'integer'],
            [['alteration_no'], 'string', 'max' => 100],
            [['policy_no'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
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
            'description' => 'Description',
            'total_si' => 'Total Si',
            'new_total_si' => 'New Total Si',
            'total_premium' => 'Total Premium',
            'new_total_premium' => 'New Total Premium',
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
        return $params['id'] . '/CNE/AJRI/' . date("Y");
    }
	
	public function callAPIPostMemberLogin()
    {
		
		// $url='http://demo-reliancelife.ajrius.id/api/login';
		$url='http://127.0.0.1:8000/api/login';
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
	
	
	
	public function callAPIPostMemberEndorsementPush($token, $policy_number, $membersNo)
	{
		$headers = [
			'Content-Type: application/json',
			'Authorization: Bearer ' . $token,
		];

		$peserta = [];

		foreach ($membersNo as $memberNo) {
			$peserta[] = [
				'peserta' => [
					'no_peserta' => '111-25050005236-308',
				],
				'before' => [
				'jenis_pengajuan'   => 1,
				'metode_endorse'    => 'REFUND',
				'bank_name'         => 'BCA',
				'bank_no_rekening'  => '1234567890',
				], 
				'after'  => [
				'jenis_pengajuan'   => 1,
				'metode_endorse'    => 'REFUND',
				'bank_name'         => 'BCA',
				'bank_no_rekening'  => '1234567890',
				], // akan menjadi {}
			];
		}

		$data = [
			'no_polis' => '1032301000472',
			'data' => [
				'tanggal_pengajuan' => date('Y-m-d'),
				'jenis_pengajuan'   => 1,
				'metode_endorse'    => 1,
				'jenis_perubahan_id'=> 1,
				'bank_name'         => 'BCA',
				'bank_no_rekening'  => '1234567890',
				'bank_owner'        => 'ABC',
			],
			'peserta' => $peserta,
		];

		$ch = curl_init('http://127.0.0.1:8000/api/memo-endorsement/store');

		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => json_encode($data),
			CURLOPT_HTTPHEADER     => $headers,
		]);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			return [
				'http_code' => 0,
				'error'     => curl_error($ch),
			];
		}

		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		return [
			'http_code'    => $httpCode,
			'request'      => $data,
			'response_raw' => $response,
			'response'     => json_decode($response, true),
		];
	}
}
