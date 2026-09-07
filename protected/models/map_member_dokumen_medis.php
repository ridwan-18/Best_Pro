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
    $url = 'http://202.152.22.234:5005/token';

    $data = [
        'client_id'     => 'SIAP',
        'client_secret' => '62bb0a61-1eaf-489e-b3f2-6a60ff8c8ffa',
        'username'      => 'reliance',
        'password'      => 'Brk$reliance',
        'grand_type'    => 'password',
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
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HEADER         => false,
    ]);

    $body = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlNo   = curl_errno($ch);
    $curlErr  = curl_error($ch);

    curl_close($ch);


    // ==========================================
    // CURL ERROR
    // ==========================================
    if ($curlNo !== 0) {

        return [
            'success'    => false,
            'token'      => null,
            'http_code'  => $httpCode,
            'curl_errno' => $curlNo,
            'curl_error' => $curlErr,
            'body'       => $body,
        ];
    }


    // ==========================================
    // DEBUG RESPONSE RAW
    // ==========================================
    Yii::error(
        "===== DEBUG TOKEN BANK =====\n" .
        "HTTP CODE : " . $httpCode . "\n" .
        "RAW BODY  : " . $body,
        'api'
    );


    // ==========================================
    // JSON DECODE
    // ==========================================
    $response = json_decode($body, true);


    if (!is_array($response)) {

        return [
            'success'    => false,
            'token'      => null,
            'http_code'  => $httpCode,
            'curl_errno' => $curlNo,
            'curl_error' => $curlErr,
            'body'       => $body,
            'json_error' => json_last_error_msg(),
        ];
    }


    // ==========================================
    // AMBIL TOKEN
    // RESPONSE BANK:
    //
    // {
    //   "result": {
    //      "pesan": "BERHASIL",
    //      "kode": "00",
    //      "token": "JWT..."
    //   }
    // }
    // ==========================================

    $token = null;

    if (
        isset($response['result']) &&
        is_array($response['result']) &&
        isset($response['result']['token'])
    ) {
        $token = $response['result']['token'];
    }


    // ==========================================
    // DEBUG TOKEN
    // ==========================================
    Yii::error(
        "===== HASIL PARSING TOKEN =====\n" .
        "TOKEN ADA : " . (!empty($token) ? 'YA' : 'TIDAK') . "\n" .
        "TOKEN     : " . (!empty($token) ? 'ADA' : 'NULL') . "\n" .
        "KODE      : " .
            (isset($response['result']['kode'])
                ? $response['result']['kode']
                : 'NULL') . "\n" .
        "PESAN     : " .
            (isset($response['result']['pesan'])
                ? $response['result']['pesan']
                : 'NULL'),
        'api'
    );


    // ==========================================
    // RETURN
    // PENTING:
    // TOKEN HARUS DI LEVEL INI
    //
    // $loginResponse['token']
    // ==========================================

    return [
        'success' => !empty($token),

        'token' => $token,

        'http_code' => $httpCode,

        'curl_errno' => $curlNo,

        'curl_error' => $curlErr,

        'kode' => isset($response['result']['kode'])
            ? $response['result']['kode']
            : null,

        'pesan' => isset($response['result']['pesan'])
            ? $response['result']['pesan']
            : null,

        'body' => $body,

        'response' => $response,
    ];
}






	
	
public function callAPIPostConfirmationDocumentRiau($token, $document)
{
    $url = 'http://202.152.22.234:5008/callback/document';

    if (empty($token)) {
        throw new \Exception('Token Bank kosong');
    }

    if (!$document) {
        throw new \Exception('Document tidak ditemukan');
    }

    $payload = [
        'id_transaksi'      => $document->id_loan,
        'id_transaksi_bank' => $document->id_transaksi_bank,
        'id_pengajuan'      => $document->id_pengajuan,
        'kode_cabang'       => $document->kode_cabang,
        'kode_broker'       => $document->kode_broker,
        'nama'              => $document->nama,
        'ktp'               => $document->ktp,
        'status_dokumen'    => ($document->approve === 'DISETUJUI') ? '1' : '0',
        'premi_disetujui'   => $document->premi_disetujui,
        'keterangan'        => $document->keterangan,
        'benefit'           => $document->benefit,
    ];

    $jsonData = json_encode($payload);

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $jsonData,

        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ],

        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HEADER         => false,
    ]);

    $body = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlNo   = curl_errno($ch);
    $curlErr  = curl_error($ch);

    curl_close($ch);

    /*
     * DEBUG
     */
    Yii::error(
        "===== DEBUG BANK CBC =====\n" .
        "URL:\n" . $url . "\n\n" .
        "HTTP CODE:\n" . $httpCode . "\n\n" .
        "CURL ERROR:\n" . $curlErr . "\n\n" .
        "PAYLOAD:\n" . json_encode($payload, JSON_PRETTY_PRINT) . "\n\n" .
        "RESPONSE RAW:\n" . $body . "\n" .
        "==========================",
        'api'
    );

    /*
     * Jika CURL error
     */
    if ($curlNo !== 0) {
        return [
            'success'    => false,
            'http_code'  => $httpCode,
            'curl_errno' => $curlNo,
            'curl_error' => $curlErr,
            'body'       => $body,
            'payload'    => $payload,
        ];
    }

    /*
     * Decode response Bank
     */
    $response = json_decode($body, true);

    /*
     * Response bukan JSON
     */
    if (!is_array($response)) {
        return [
            'success'   => ($httpCode >= 200 && $httpCode < 300),
            'http_code' => $httpCode,
            'body'      => $body,
            'payload'   => $payload,
            'json_error' => json_last_error_msg(),
        ];
    }

    /*
     * RETURN HASIL API BANK
     */
    return [
        'success'   => ($httpCode >= 200 && $httpCode < 300),
        'http_code' => $httpCode,
        'response'  => $response,
        'body'      => $body,
        'payload'   => $payload,
    ];
}




}
