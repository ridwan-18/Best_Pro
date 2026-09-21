<?php

namespace app\models;

use Yii;

class MemberClaim extends \yii\db\ActiveRecord
{
    const STATUS_ANALISA  = '0';
    const STATUS_APPROVED = '1';
    const STATUS_DITOLAK  = '2';

    const PAGE_SIZE = 10;

    /**
     * Nama tabel
     */
    public static function tableName()
    {
        return 'tbl_claim_riau';
    }

    /**
     * Rules sesuai struktur database
     */
    public function rules()
    {
        return [

            [['id'], 'integer'],

            [
                [
                    'id_transaksi',
                    'tenor',
                    'premi',
                    'tenor_berjalan',
                    'sisa_tenor',
                    'jumlah_diajukan'
                ],
                'number'
            ],

            [
                [
                    'id_pengajuan',
                    'kode_broker',
                    'ktp',
                    'nama',
                    'kode_cabang',
                    'nomor_rekening',
                    'no_akad',
                    'benefit',
                    'id_agunan',
                    'nomor_bukti',
                    'jenis_klaim',
                    'penyebab_klaim',
                    'tempat_kejadian',
                    'tujuan_pembayaran',
                    'status_claim',
                    'alasan_batal_klaim',
                    'id_pengajuan_klaim_riau'
                ],
                'string'
            ],

            [
                [
                    'periode_awal',
                    'periode_akhir',
                    'tanggal_kejadian',
                    'tanggal_kirim',
                    'created_at'
                ],
                'safe'
            ],
        ];
    }

    /**
     * Label field
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_transaksi' => 'ID Transaksi',
            'id_pengajuan' => 'ID Pengajuan',
            'kode_broker' => 'Kode Broker',
            'ktp' => 'KTP',
            'nama' => 'Nama',
            'kode_cabang' => 'Kode Cabang',
            'nomor_rekening' => 'Nomor Rekening',
            'no_akad' => 'No Akad',
            'tenor' => 'Tenor',
            'premi' => 'Premi',
            'periode_awal' => 'Periode Awal',
            'periode_akhir' => 'Periode Akhir',
            'tenor_berjalan' => 'Tenor Berjalan',
            'sisa_tenor' => 'Sisa Tenor',
            'benefit' => 'Benefit',
            'id_agunan' => 'ID Agunan',
            'nomor_bukti' => 'Nomor Bukti',
            'jenis_klaim' => 'Jenis Klaim',
            'penyebab_klaim' => 'Penyebab Klaim',
            'tanggal_kejadian' => 'Tanggal Kejadian',
            'tempat_kejadian' => 'Tempat Kejadian',
            'jumlah_diajukan' => 'Jumlah Diajukan',
            'tujuan_pembayaran' => 'Tujuan Pembayaran',
            'tanggal_kirim' => 'Tanggal Kirim',
            'status_claim' => 'Status Claim',
            'alasan_batal_klaim' => 'Alasan Batal Klaim',
            'created_at' => 'Created At',
            'id_pengajuan_klaim_riau' => 'ID Pengajuan Klaim Riau',
        ];
    }

    /**
     * ============================================================
     * GET ALL
     * ============================================================
     */
    public static function getAll($params = [])
    {
        $query = self::find()->asArray();

        if (!empty($params['id_pengajuan'])) {
            $query->andWhere([
                'id_pengajuan' => $params['id_pengajuan']
            ]);
        }

        if (!empty($params['ktp'])) {
            $query->andWhere([
                'ktp' => $params['ktp']
            ]);
        }

        if (!empty($params['nama'])) {
            $query->andWhere([
                'like',
                'nama',
                $params['nama']
            ]);
        }

        if (!empty($params['id_transaksi'])) {
            $query->andWhere([
                'id_transaksi' => $params['id_transaksi']
            ]);
        }

        if (
            isset($params['status_claim']) &&
            $params['status_claim'] !== '' &&
            $params['status_claim'] !== null
        ) {
            $query->andWhere([
                'status_claim' => $params['status_claim']
            ]);
        }

        if (
            isset($params['offset']) &&
            $params['offset'] !== null
        ) {
            $query->offset($params['offset']);
        }

        if (
            isset($params['limit']) &&
            $params['limit'] !== null
        ) {
            $query->limit($params['limit']);
        }

        $sort = isset($params['sort'])
            ? $params['sort']
            : SORT_DESC;

        $query->orderBy([
            'id' => $sort
        ]);

        return $query->all();
    }

    /**
     * ============================================================
     * COUNT ALL
     * ============================================================
     */
    public static function countAll($params = [])
    {
        $query = self::find();

        if (!empty($params['id_pengajuan'])) {
            $query->andWhere([
                'id_pengajuan' => $params['id_pengajuan']
            ]);
        }

        if (!empty($params['ktp'])) {
            $query->andWhere([
                'ktp' => $params['ktp']
            ]);
        }

        if (!empty($params['nama'])) {
            $query->andWhere([
                'like',
                'nama',
                $params['nama']
            ]);
        }

        if (!empty($params['id_transaksi'])) {
            $query->andWhere([
                'id_transaksi' => $params['id_transaksi']
            ]);
        }

        if (
            isset($params['status_claim']) &&
            $params['status_claim'] !== '' &&
            $params['status_claim'] !== null
        ) {
            $query->andWhere([
                'status_claim' => $params['status_claim']
            ]);
        }

        return $query->count();
    }

    /**
     * ============================================================
     * STATUS
     * ============================================================
     */
    public static function statuses()
    {
        return [
            self::STATUS_ANALISA  => 'Analisa',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_DITOLAK  => 'Ditolak',
        ];
    }

    /**
     * ============================================================
     * STATUS LABEL
     * ============================================================
     */
    public static function statusLabel($status)
    {
        $statuses = self::statuses();

        return isset($statuses[$status])
            ? $statuses[$status]
            : ($status ?: '-');
    }

    /**
     * ============================================================
     * STATUS BADGE
     * ============================================================
     */
    public static function statusClass($status)
    {
        switch ($status) {

            case self::STATUS_ANALISA:
                return 'badge-warning';

            case self::STATUS_APPROVED:
                return 'badge-success';

            case self::STATUS_DITOLAK:
                return 'badge-danger';

            default:
                return 'badge-secondary';
        }
    }

    /**
     * ============================================================
     * GENERATE ALTERATION NO
     * ============================================================
     */
    public static function generateAlterationNo($params)
    {
        return $params['id']
            . '/CNR/AJRI/'
            . date('Y');
    }

    /**
     * ============================================================
     * CALL API POST STATUS
     * ============================================================
     */
    public function callAPIPostStatus($description)
    {
        $url = 'http://45.64.1.151/api/klaim/bankjatim/post-status';

        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' .
                base64_encode('USERNAME:PASSWORD')
        ];

        $data = json_encode([
            'ID_Loan' => $this->id_pengajuan,
            'Status' => $this->status_claim,
            'Keterangan' => $description
        ]);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($response === false) {

            $error = curl_error($ch);

            curl_close($ch);

            return [
                'success' => false,
                'message' => $error
            ];
        }

        $headerSize = curl_getinfo(
            $ch,
            CURLINFO_HEADER_SIZE
        );

        $httpCode = curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

        $body = substr(
            $response,
            $headerSize
        );

        curl_close($ch);

        $result = json_decode(
            $body,
            true
        );

        return [
            'success' => (
                $httpCode >= 200 &&
                $httpCode < 300
            ),
            'http_code' => $httpCode,
            'response' => $result !== null
                ? $result
                : $body
        ];
    }
	
	public function callAPIPostMemberLoginRiau()
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
	
	
	public function callAPIPostDebitur($token, $model, $document = null, $klaim = null)
{
    $url = 'http://202.152.22.234:5008/callback/debitur';

    try {

        if (empty($token)) {
            return [
                'success' => false,
                'http_code' => 401,
                'message' => 'Token Bank kosong',
                'response' => null,
            ];
        }

        if (!$model) {
            return [
                'success' => false,
                'http_code' => 404,
                'message' => 'Data member/debitur tidak ditemukan',
                'response' => null,
            ];
        }

        if (!$klaim) {
            return [
                'success' => false,
                'http_code' => 404,
                'message' => 'Data klaim tidak ditemukan',
                'response' => null,
            ];
        }

        /*
         * PAYLOAD
         * PERHATIKAN: gunakan $klaim, bukan $claim
         */
        $payload = [
            'nama' => (string) $model->nama,
            'ktp' => (string) $model->ktp,
            'benefit' => (string) $model->benefit,

            'restitusi' => null,

            'klaim' => [
                'id_transaksi_bank' => (string) $klaim->id,
                'id_pengajuan' => (string) $klaim->id_pengajuan,
                'status_klaim' => (string) $klaim->status_claim,
                'status_bayar' => (string) $klaim->status_bayar,
                'klaim_dibayarkan' => (string) $klaim->jumlah_diajukan,
                'asuransi' => 'alamin',
                'keterangan' => $document
                    ? (string) $document->keterangan
                    : '-',
            ],

            'id_transaksi' => (string) $klaim->id_transaksi,
            'status_callback' => '2',
            'nomor_rekening' => (string) $klaim->nomor_rekening,
            'kode_broker' => (string) $klaim->kode_broker,
            'no_akad' => (string) $klaim->no_akad,
            'kode_cabang' => (string) $klaim->kode_cabang,
        ];

        $jsonData = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE
        );

        if ($jsonData === false) {
            return [
                'success' => false,
                'http_code' => 500,
                'message' => 'JSON payload gagal dibuat: ' . json_last_error_msg(),
                'response' => null,
                'payload' => $payload,
            ];
        }

        /*
         * CURL
         */
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonData,

            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token,
                'Content-Length: ' . strlen($jsonData),
            ],

            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,

            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $body = curl_exec($ch);

        $httpCode = curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);

        curl_close($ch);

        /*
         * CURL ERROR
         */
        if ($curlErrno !== 0) {

            return [
                'success' => false,
                'http_code' => $httpCode,
                'message' => 'CURL ERROR: ' . $curlError,
                'curl_errno' => $curlErrno,
                'curl_error' => $curlError,
                'body' => $body,
                'response' => null,
                'payload' => $payload,
            ];
        }

        /*
         * RESPONSE KOSONG
         */
        if ($body === false || trim($body) === '') {

            return [
                'success' => false,
                'http_code' => $httpCode,
                'message' => 'Bank mengembalikan response kosong',
                'body' => $body,
                'response' => null,
                'payload' => $payload,
            ];
        }

        /*
         * DECODE JSON
         */
        $response = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            return [
                'success' => false,
                'http_code' => $httpCode,
                'message' => 'Response Bank bukan JSON: ' . json_last_error_msg(),
                'body' => $body,
                'response' => null,
                'payload' => $payload,
            ];
        }

        /*
         * CARI RESULT
         *
         * Support:
         *
         * {
         *   "Result": {...}
         * }
         *
         * maupun:
         *
         * {
         *   "response": {
         *      "Result": {...}
         *   }
         * }
         */
        if (isset($response['Result'])) {

            $result = $response['Result'];

        } elseif (
            isset($response['response']) &&
            isset($response['response']['Result'])
        ) {

            $result = $response['response']['Result'];

        } else {

            return [
                'success' => false,
                'http_code' => $httpCode,
                'message' => 'Bank tidak memberikan response Result',
                'response' => $response,
                'body' => $body,
                'payload' => $payload,
            ];
        }

        $kodeResponse = isset($result['kode_response'])
            ? (string) $result['kode_response']
            : null;

        $statusResponse = isset($result['status'])
            ? (string) $result['status']
            : null;

        $message = isset($result['message'])
            ? (string) $result['message']
            : 'Response Bank';

        $success = (
            $httpCode >= 200 &&
            $httpCode < 300 &&
            $kodeResponse === '00' &&
            $statusResponse === '200'
        );

        return [
            'success' => $success,

            'http_code' => $httpCode,

            'kode_response' => $kodeResponse,

            'status' => $statusResponse,

            'message' => $message,

            'response' => $response,

            'body' => $body,

            'payload' => $payload,
        ];

    } catch (\Throwable $e) {

        Yii::error(
            'callAPIPostDebitur ERROR: ' .
            $e->getMessage() .
            "\nFILE: " . $e->getFile() .
            "\nLINE: " . $e->getLine() .
            "\nTRACE:\n" . $e->getTraceAsString(),
            'api'
        );

        return [
            'success' => false,
            'http_code' => 500,
            'message' => $e->getMessage(),
            'response' => null,
        ];
    }
}

}