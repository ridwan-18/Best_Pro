<?php

namespace app\models;

use Yii;

class MemberClaim extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 'Analisa';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECT = 'Ditolak';

    const PAGE_SIZE = 10;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_claim_jatim';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tanggal_pengajuan', 'tanggal_kejadian', 'create_at'], 'safe'],
            [['id_loan'], 'string', 'max' => 40],
            [['name', 'jenis_claim', 'sebab_claim', 'estimasi_nilai_claim', 'keterangan'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_loan' => 'Id Loan',
            'name' => 'Name',
            'tanggal_pengajuan' => 'Tanggal Pengajuan',
            'tanggal_kejadian' => 'Tanggal Kejadian',
            'jenis_claim' => 'Jenis Claim',
            'sebab_claim' => 'Sebab Claim',
            'estimasi_nilai_claim' => 'Estimasi Nilai Claim',
            'keterangan' => 'Keterangan',
            'create_at' => 'Create At',
            'status' => 'Status',
        ];
    }

    public static function getAll($params = [])
    {
        $query = self::find()
            ->asArray();


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

    public function callAPIPostStatus($description)
    {
        $url = 'http://45.64.1.151/api/klaim/bankjatim/post-status';
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode('bjtm:bjtm!@##@!')
        ];
        $data = json_encode([
            'ID_Loan' => $this->id_loan,
            'Status' => $this->status,
            'Keterangan' => $description
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
}
