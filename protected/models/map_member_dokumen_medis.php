<?php

namespace app\models;

use DateTime;
use Yii;

/**
 * This is the model class for table "member".
 *
 * @property int $id
 * @property string $policy_no
 * @property string $batch_no
 * @property string|null $member_no
 * @property string $personal_no
 * @property int|null $age
 * @property string|null $branch
 * @property string|null $branch_code
 * @property string|null $account_no
 * @property string|null $bank_branch
 * @property int $term
 * @property string $start_date
 * @property string $end_date
 * @property float $sum_insured
 * @property float|null $total_si
 * @property float|null $total_premium
 * @property float|null $rate_premi
 * @property float|null $rate_saving
 * @property float|null $gross_premium
 * @property float|null $basic_premium
 * @property float|null $saving_premium
 * @property float|null $percentage_discount
 * @property float|null $discount_premium
 * @property float|null $nett_premium
 * @property string|null $medical_code
 * @property string|null $status
 * @property string|null $member_status
 * @property string|null $reas_status
 * @property string|null $status_reason
 * @property string|null $stnc_date
 * @property string|null $stnc_status
 * @property string|null $stnc_reason
 * @property string|null $acc_status
 * @property float|null $percentage_extra_premium
 * @property float|null $extra_premium
 * @property int|null $em_type
 * @property float|null $percentage_em
 * @property float|null $rate_em
 * @property float|null $em_premium
 * @property string|null $em_notes
 * @property string|null $uw_notes
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class map_member_dokumen_medis extends \yii\db\ActiveRecord
{
    const STATUS_INFORCE = 'Inforce';

    const EM_MANUAL = 1;
    const EM_FROM_PRODUCT = 2;

    const PAGE_SIZE = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_map_member_medis';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['policy_no', 'batch_no', 'personal_no', 'term', 'start_date', 'end_date', 'sum_insured'], 'required'],
            [['age', 'term', 'em_type', 'created_by', 'updated_by'], 'integer'],
            [['start_date', 'end_date', 'stnc_date', 'created_at', 'updated_at'], 'safe'],
            [['sum_insured', 'total_si', 'total_premium', 'rate_premi', 'rate_saving', 'gross_premium', 'basic_premium', 'saving_premium', 'percentage_discount', 'discount_premium', 'nett_premium', 'percentage_extra_premium', 'extra_premium', 'percentage_em', 'rate_em', 'em_premium'], 'number'],
            [['policy_no', 'batch_no', 'medical_code'], 'string', 'max' => 50],
            [['member_no'], 'string', 'max' => 100],
            [['personal_no', 'branch', 'branch_code', 'account_no', 'bank_branch', 'status_reason', 'stnc_status', 'stnc_reason', 'acc_status', 'em_notes', 'uw_notes'], 'string', 'max' => 255],
            [['status', 'member_status', 'reas_status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_loan' => 'id_loan',
            'kode_dokumen' => 'kode_dokumen',
            'files' => 'files',
      
        ];
    }

	
	  public static function getAll($params = [])
    {
         $query = self::find()
            ->asArray();
		
		 if (isset($params['medis']) && $params['medis'] != null) {
            $query->andFilterWhere(['=', self::tableName() . '.medis', $params['medis']]);
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
	
	 public function callAPIPostConfirmationDocument()
    {
        // $ch = curl_init('https://api-gateway.aapialang.co.id/sandbox/bankjatim-service/h2h/webhook/insurance/akseptasi/draft/dokumen-underwriting/confirmation');
		$ch = curl_init ('https://api-gateway.aapialang.co.id/bankjatim-service/h2h/webhook/insurance/akseptasi/draft/dokumen-underwriting/confirmation');

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer EiSsYqSqEJGv2EEiIYXP6d4HL3FBMFD2tdassSIEqje9p7TD0oFPcXkG1at7osvVZFzZJ0hlkWBxZEAaxfHYFvY0CiisK8S688y8xMhILidFO7IVCLxB7w1gHfb0O7oaesw7a0F0K5cTxaSdZ47T5YgF0XURbAeOYTtcKMcGOVJ3h5JlqavuWEQMVvbPjEOIKjwQ7ycf7WGLbii1Uz2qpTR9R40MuAUk0mVq0lzF'
        ];

        $data = json_encode([
            'nomor_transaksi' => $this->nomor_transaksi,
            'code_dokumen' => $this->kode_dokumen,
            'status_dokumen' => $this->approve,
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
        // var_dump($response);
        return json_decode($body, true);
    }
}
