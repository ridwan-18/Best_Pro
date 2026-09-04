<?php

namespace app\models;

use Yii;

class claim_riau extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'tbl_claim_riau';
    }

    public function rules()
    {
        return [
            [
                [
                    'id_transaksi',
                    'id_pengajuan',
                    'kode_broker',
                    'ktp',
                    'nama',
                    'kode_cabang',
                    'nomor_rekening',
                    'no_akad',
                    'tenor',
                    'premi',
                    'periode_awal',
                    'periode_akhir',
                    'tenor_berjalan',
                    'sisa_tenor',
                    'benefit',
                    'jenis_klaim',
                    'penyebab_klaim',
                    'tanggal_kejadian',
                    'tempat_kejadian',
                    'jumlah_diajukan',
                    'tujuan_pembayaran',
                    'tanggal_kirim',
                ],
                'required'
            ],

            [
                [
                    'id_transaksi',
                    'tenor',
                    'premi',
                    'tenor_berjalan',
                    'sisa_tenor',
                    'jumlah_diajukan',
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
                ],
                'string'
            ],

            [
                [
                    'periode_awal',
                    'periode_akhir',
                    'tanggal_kejadian',
                    'tanggal_kirim',
                ],
                'safe'
            ],

            [
                [
                    'id_agunan',
                    'nomor_bukti',
                ],
                'string'
            ],

            // Jika benefit = 4 (Kebakaran),
            // id_agunan dan nomor_bukti wajib diisi
            [
                [
                    'id_agunan',
                    'nomor_bukti',
                ],
                'required',
                'when' => function ($model) {
                    return $model->benefit == '4';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#claim_riau-benefit').val() == '4';
                }"
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
            'ktp' => 'KTP',
            'nama' => 'Nama',
            'kode_cabang' => 'Kode Cabang',
            'nomor_rekening' => 'Nomor Rekening',
            'no_akad' => 'Nomor Akad',
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
        ];
    }
}