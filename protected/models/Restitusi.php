<?php

namespace app\models;

use Yii;

class Restitusi extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'restitusi';
    }

    public function rules()
    {
        return [
            [
                [
                    'id_transaksi',
                    'id_pengajuan',
                    'kode_broker',
                    'kode_cabang',
                    'nomor_rekening',
                    'old_nomor_akad',
                    'tanggal_pembiayaan',
                    'plafon_pembiayaan',
                    'tenor',
                    'benefit',
                ],
                'required'
            ],

            [
                [
                    'id_transaksi',
                    'plafon_pembiayaan',
                    'tenor'
                ],
                'integer'
            ],

            [
                [
                    'id_pengajuan',
                    'kode_broker',
                    'kode_cabang',
                    'nomor_rekening',
                    'old_nomor_akad',
                    'benefit'
                ],
                'string'
            ],

            [
                'tanggal_pembiayaan',
                'safe'
            ],

            [
                'restitusi_jiwa',
                'string'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_transaksi' => 'ID Transaksi',
            'id_pengajuan' => 'ID Pengajuan',
            'kode_broker' => 'Kode Broker',
            'kode_cabang' => 'Kode Cabang',
            'nomor_rekening' => 'Nomor Rekening',
            'old_nomor_akad' => 'Old Nomor Akad',
            'tanggal_pembiayaan' => 'Tanggal Pembiayaan',
            'plafon_pembiayaan' => 'Plafon Pembiayaan',
            'tenor' => 'Tenor',
            'benefit' => 'Benefit',
            'restitusi_jiwa' => 'Restitusi Jiwa',
        ];
    }
}