```php
<?php

namespace app\components;

use Yii;
use phpseclib3\Net\SFTP;

class SftpBankService
{
    private $host = '202.152.22.234';
    private $port = 22;
    private $username = 'reliance';
    private $password = 'reliance@brks2026';

    private $remotePath = '/outgoing';

    /**
     * Connect ke SFTP Bank
     */
    public function connect()
    {
        $sftp = new SFTP($this->host, $this->port, 30);

        if (!$sftp->login($this->username, $this->password)) {
            throw new \Exception('Login SFTP Bank gagal.');
        }

        if (!$sftp->chdir($this->remotePath)) {
            throw new \Exception(
                'Folder SFTP tidak ditemukan: ' . $this->remotePath
            );
        }

        return $sftp;
    }

    /**
     * Ambil daftar file di folder Outgoing
     */
    public function listFiles()
    {
        $sftp = $this->connect();

        $files = $sftp->nlist();

        if ($files === false) {
            throw new \Exception(
                'Gagal membaca folder ' . $this->remotePath
            );
        }

        return array_values(array_filter($files, function ($file) {
            return $file !== '.'
                && $file !== '..';
        }));
    }

    /**
     * Cari file CBC berdasarkan NIK
     *
     * Format:
     * nik_ddmmyy_codedoc_benefit_sequence.zip
     */
    public function findFilesByNik($nik)
    {
        $files = $this->listFiles();

        $result = [];

        foreach ($files as $file) {

            if (stripos($file, '.zip') === false) {
                continue;
            }

            if (strpos($file, $nik . '_') !== 0) {
                continue;
            }

            $result[] = $file;
        }

        return $result;
    }

    /**
     * Download satu file dari SFTP Bank
     */
    public function downloadFile($fileName)
    {
        $sftp = $this->connect();

        $localDirectory = Yii::getAlias(
            '@runtime/cbc'
        );

        if (!is_dir($localDirectory)) {
            if (!mkdir($localDirectory, 0777, true)) {
                throw new \Exception(
                    'Gagal membuat folder: ' . $localDirectory
                );
            }
        }

        $localFile = $localDirectory . DIRECTORY_SEPARATOR . $fileName;

        $remoteFile = $this->remotePath . '/' . $fileName;

        if (!$sftp->file_exists($remoteFile)) {
            throw new \Exception(
                'File tidak ditemukan di SFTP: ' . $fileName
            );
        }

        if (!$sftp->get($remoteFile, $localFile)) {
            throw new \Exception(
                'Gagal download file: ' . $fileName
            );
        }

        if (!file_exists($localFile)) {
            throw new \Exception(
                'File hasil download tidak ditemukan: ' . $localFile
            );
        }

        return $localFile;
    }

    /**
     * Download seluruh file berdasarkan NIK
     */
    public function downloadFilesByNik($nik)
    {
        $files = $this->findFilesByNik($nik);

        $downloaded = [];

        foreach ($files as $file) {

            try {

                $localFile = $this->downloadFile($file);

                $downloaded[] = [
                    'file_name' => $file,
                    'local_file' => $localFile,
                    'status' => 'success',
                ];

            } catch (\Throwable $e) {

                $downloaded[] = [
                    'file_name' => $file,
                    'local_file' => null,
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return $downloaded;
    }
}
```
