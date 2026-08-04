<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alteration_endorsement_member".
 *
 * @property int $id
 * @property string|null $alteration_no
 * @property string|null $member_no
 * @property string|null $name
 * @property string|null $birth_date
 * @property string|null $new_birth_date
 * @property int|null $age
 * @property int|null $new_age
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string|null $new_start_date
 * @property string|null $new_end_date
 * @property int|null $term
 * @property int|null $new_term
 * @property float|null $sum_insured
 * @property float|null $new_sum_insured
 * @property float|null $premi
 * @property float|null $new_premi
 * @property float|null $extra_premi
 */
class AlterationEndorsementMember extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alteration_endorsement_member';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['birth_date', 'new_birth_date', 'start_date', 'end_date', 'new_start_date', 'new_end_date'], 'safe'],
            [['age', 'new_age', 'term', 'new_term'], 'integer'],
            [['sum_insured', 'new_sum_insured', 'premi', 'new_premi', 'extra_premi'], 'number'],
            [['alteration_no', 'member_no'], 'string', 'max' => 100],
            [['name','new_name'], 'string', 'max' => 255],
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
            'member_no' => 'Member No',
            'name' => 'Name',
            'birth_date' => 'Birth Date',
            'new_birth_date' => 'New Birth Date',
            'age' => 'Age',
            'new_age' => 'New Age',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'new_start_date' => 'New Start Date',
            'new_end_date' => 'New End Date',
            'term' => 'Term',
            'new_term' => 'New Term',
            'sum_insured' => 'Sum Insured',
            'new_sum_insured' => 'New Sum Insured',
            'premi' => 'Premi',
            'new_premi' => 'New Premi',
            'extra_premi' => 'Extra Premi',
			'new_name' => 'new_name',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->asArray();

        if (isset($params['alteration_no']) && $params['alteration_no'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.alteration_no', $params['alteration_no']]);
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
