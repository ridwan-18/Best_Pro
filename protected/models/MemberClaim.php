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
	
	
	public function callAPIPostDebitur($token, $model, $document = null, $restitusi = null)
	{
		$url = '202.152.22.234:5008/callback/debitur';

		try {

			// =========================================================
			// 1. DEBUG PARAMETER AWAL
			// =========================================================
			Yii::error(
				"========================================\n" .
				"DEBUG callAPIPostDebitur - START\n" .
				"========================================\n" .
				"TOKEN ADA      : " . (!empty($token) ? 'YA' : 'TIDAK') . "\n" .
				"MODEL ADA      : " . ($model ? 'YA' : 'TIDAK') . "\n" .
				"DOCUMENT ADA   : " . ($document ? 'YA' : 'TIDAK') . "\n" .
				"RESTITUSI ADA  : " . ($restitusi ? 'YA' : 'TIDAK') . "\n" .
				"URL            : " . $url . "\n",
				'api'
			);


			// =========================================================
			// 2. VALIDASI
			// =========================================================
			if (empty($token)) {
				throw new \Exception('Token Bank kosong');
			}

			if (!$model) {
				throw new \Exception('Data member/debitur tidak ditemukan');
			}

			if (!$restitusi) {
				throw new \Exception('Data restitusi/debitur tidak ditemukan');
			}


			// =========================================================
			// 3. DEBUG DATA MEMBER
			// =========================================================
			Yii::error(
				"===== DEBUG MODEL MEMBER =====\n" .
				"Nama           : " . var_export($model->nama, true) . "\n" .
				"KTP            : " . var_export($model->ktp, true) . "\n" .
				"Benefit        : " . var_export($model->benefit, true) . "\n" .
				"ID Transaksi   : " . var_export($model->id_transaksi, true) . "\n" .
				"ID Pengajuan   : " . var_export($model->id_pengajuan, true) . "\n" .
				"Term           : " . var_export($model->term, true) . "\n" .
				"Gross Premium   : " . var_export($model->gross_premium, true) . "\n" .
				"Start Date     : " . var_export($model->start_date, true) . "\n" .
				"End Date       : " . var_export($model->end_date, true) . "\n" .
				"================================",
				'api'
			);


			// =========================================================
			// 4. DEBUG DATA RESTITUSI
			// =========================================================
			Yii::error(
				"===== DEBUG MODEL RESTITUSI =====\n" .
				"ID Transaksi       : " . var_export($restitusi->id_transaksi, true) . "\n" .
				"ID Pengajuan       : " . var_export($restitusi->id_pengajuan, true) . "\n" .
				"Status Restitusi   : " . var_export($restitusi->status_restitusi, true) . "\n" .
				"Tenor Berjalan    : " . var_export($restitusi->tenor_berjalan, true) . "\n" .
				"Sisa Tenor        : " . var_export($restitusi->sisa_tenor, true) . "\n" .
				"Status Bayar      : " . var_export($restitusi->status_bayar, true) . "\n" .
				"Premi             : " . var_export($restitusi->premi, true) . "\n" .
				"Nomor Rekening    : " . var_export($restitusi->nomor_rekening, true) . "\n" .
				"Kode Broker       : " . var_export($restitusi->kode_broker, true) . "\n" .
				"Nomor Akad        : " . var_export($restitusi->nomor_akad, true) . "\n" .
				"Kode Cabang       : " . var_export($restitusi->kode_cabang, true) . "\n" .
				"================================",
				'api'
			);


			// =========================================================
			// 5. DEBUG DOCUMENT
			// =========================================================
			$statusDokumen = '1';
			$keterangan = '';

			if ($document) {

				$statusDokumen = (string) $document->approve;
				$keterangan = (string) $document->keterangan;

				Yii::error(
					"===== DEBUG DOCUMENT =====\n" .
					"ID              : " . var_export($document->id, true) . "\n" .
					"ID Loan         : " . var_export($document->id_loan, true) . "\n" .
					"Approve         : " . var_export($document->approve, true) . "\n" .
					"Status Dokumen  : " . var_export($statusDokumen, true) . "\n" .
					"Keterangan      : " . var_export($document->keterangan, true) . "\n" .
					"File            : " . var_export($document->files, true) . "\n" .
					"Jenis Dokumen   : " . var_export($document->jenis_dokumen, true) . "\n" .
					"================================",
					'api'
				);
			}


			// =========================================================
			// 6. VALIDASI STATUS BAYAR
			// =========================================================
			$statusBayar = (string) $restitusi->status_bayar;

			Yii::error(
				"===== DEBUG STATUS =====\n" .
				"STATUS BAYAR RAW : " . var_export($restitusi->status_bayar, true) . "\n" .
				"STATUS BAYAR STR : " . var_export($statusBayar, true) . "\n" .
				"STATUS DOKUMEN   : " . var_export($statusDokumen, true) . "\n" .
				"================================",
				'api'
			);

			$payload = [
				'nama' => $model->nama,

				'ktp' => $model->ktp,

				'benefit' => (string) $model->benefit,

				'restitusi' => [
					'id_transaksi_bank' => (string) $model->id_transaksi,

					'id_pengajuan' => (string) $model->id_pengajuan,

					'status_restitusi' => (string) $restitusi->status_restitusi,

					'tenor' => (string) $model->term,

					'premi' => (string) $model->gross_premium,

					'periode_awal' => $model->start_date,

					'periode_akhir' => $model->end_date,

					'tenor_berjalan' => (string) $restitusi->tenor_berjalan,

					'sisa_tenor' => (string) $restitusi->sisa_tenor,

					'status_bayar' => $statusBayar,

					'premi_dikembalikan' => (string) $restitusi->premi,

					'asuransi' => 'Reliance Life Unit Syariah',

					'keterangan' => $keterangan,
				],

				'klaim' => null,

				'id_transaksi' => (string) $restitusi->id_transaksi,

				'status_callback' => '1',

				'nomor_rekening' => $restitusi->nomor_rekening,

				'kode_broker' => $restitusi->kode_broker,

				'no_akad' => $restitusi->nomor_akad,

				'kode_cabang' => $restitusi->kode_cabang,
			];


			// =========================================================
			// 8. DEBUG PAYLOAD ARRAY
			// =========================================================
			Yii::error(
				"========================================\n" .
				"DEBUG PAYLOAD ARRAY\n" .
				"========================================\n" .
				print_r($payload, true),
				'api'
			);


			// =========================================================
			// 9. JSON ENCODE
			// =========================================================
			$jsonData = json_encode(
				$payload,
				JSON_UNESCAPED_UNICODE
			);


			// =========================================================
			// 10. CEK JSON ERROR
			// =========================================================
			if ($jsonData === false) {

				Yii::error(
					"===== JSON ENCODE ERROR =====\n" .
					"ERROR : " . json_last_error_msg(),
					'api'
				);

				throw new \Exception(
					'Gagal membuat JSON payload: ' .
					json_last_error_msg()
				);
			}


			// =========================================================
			// 11. DEBUG JSON FINAL
			// =========================================================
			Yii::error(
				"========================================\n" .
				"DEBUG JSON YANG DIKIRIM KE BANK\n" .
				"========================================\n" .
				$jsonData . "\n" .
				"========================================",
				'api'
			);


			// =========================================================
			// 12. CURL
			// =========================================================
			$ch = curl_init();

			curl_setopt_array($ch, [
				CURLOPT_URL => $url,

				CURLOPT_POST => true,

				CURLOPT_POSTFIELDS => $jsonData,

				CURLOPT_HTTPHEADER => [
					'Content-Type: application/json',
					'Accept: application/json',
					'Authorization: Bearer ' . $token,
				],

				CURLOPT_RETURNTRANSFER => true,

				CURLOPT_CONNECTTIMEOUT => 10,

				CURLOPT_TIMEOUT => 30,

				CURLOPT_HEADER => false,
			]);


			// =========================================================
			// 13. EXECUTE CURL
			// =========================================================
			$body = curl_exec($ch);

			$httpCode = curl_getinfo(
				$ch,
				CURLINFO_HTTP_CODE
			);

			$curlNo = curl_errno($ch);

			$curlErr = curl_error($ch);

			curl_close($ch);


			// =========================================================
			// 14. DEBUG RESPONSE BANK
			// =========================================================
			Yii::error(
				"========================================\n" .
				"DEBUG RESPONSE BANK\n" .
				"========================================\n" .
				"HTTP CODE  : " . $httpCode . "\n" .
				"CURL NO    : " . $curlNo . "\n" .
				"CURL ERROR : " . $curlErr . "\n" .
				"RESPONSE   :\n" .
				$body . "\n" .
				"========================================",
				'api'
			);


			// =========================================================
			// 15. CURL ERROR
			// =========================================================
			if ($curlNo !== 0) {

				return [
					'success' => false,

					'http_code' => $httpCode,

					'curl_errno' => $curlNo,

					'curl_error' => $curlErr,

					'body' => $body,

					'payload' => $payload,
				];
			}


			// =========================================================
			// 16. DECODE RESPONSE
			// =========================================================
			$response = json_decode(
				$body,
				true
			);


			if (!is_array($response)) {

				Yii::error(
					"===== RESPONSE BUKAN JSON =====\n" .
					"JSON ERROR : " . json_last_error_msg() . "\n" .
					"BODY       : " . $body,
					'api'
				);

				return [
					'success' => false,

					'http_code' => $httpCode,

					'body' => $body,

					'payload' => $payload,

					'json_error' => json_last_error_msg(),
				];
			}


			// =========================================================
			// 17. DEBUG RESPONSE ARRAY
			// =========================================================
			Yii::error(
				"===== RESPONSE ARRAY BANK =====\n" .
				print_r($response, true),
				'api'
			);


			// =========================================================
			// 18. AMBIL RESULT
			// =========================================================
			$result = isset($response['Result'])
				? $response['Result']
				: [];


			$kodeResponse = isset($result['kode_response'])
				? (string) $result['kode_response']
				: null;


			$statusResponse = isset($result['status'])
				? (string) $result['status']
				: null;


			$success = (
				$httpCode >= 200 &&
				$httpCode < 300 &&
				$kodeResponse === '00' &&
				$statusResponse === '200'
			);


			// =========================================================
			// 19. DEBUG HASIL AKHIR
			// =========================================================
			Yii::error(
				"========================================\n" .
				"DEBUG HASIL CALLBACK\n" .
				"========================================\n" .
				"SUCCESS        : " . ($success ? 'TRUE' : 'FALSE') . "\n" .
				"HTTP CODE      : " . var_export($httpCode, true) . "\n" .
				"KODE RESPONSE  : " . var_export($kodeResponse, true) . "\n" .
				"STATUS         : " . var_export($statusResponse, true) . "\n" .
				"MESSAGE        : " . var_export(
					isset($result['message'])
						? $result['message']
						: null,
					true
				) . "\n" .
				"========================================",
				'api'
			);


			// =========================================================
			// 20. RETURN
			// =========================================================
			return [
				'success' => $success,

				'http_code' => $httpCode,

				'kode_response' => $kodeResponse,

				'status' => $statusResponse,

				'message' => isset($result['message'])
					? $result['message']
					: null,

				'response' => $response,

				'body' => $body,

				'payload' => $payload,
			];

		} catch (\Throwable $e) {

			Yii::error(
				"========================================\n" .
				"DEBUG EXCEPTION callAPIPostDebitur\n" .
				"========================================\n" .
				"MESSAGE : " . $e->getMessage() . "\n" .
				"FILE    : " . $e->getFile() . "\n" .
				"LINE    : " . $e->getLine() . "\n" .
				"TRACE   :\n" . $e->getTraceAsString() . "\n" .
				"========================================",
				'api'
			);

			return [
				'success' => false,

				'http_code' => 500,

				'message' => $e->getMessage(),

				'payload' => isset($payload)
					? $payload
					: null,
			];
		}
	}
}