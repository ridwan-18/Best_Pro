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
}