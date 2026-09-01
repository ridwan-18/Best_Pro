<?php

// require 'C:/xampp7.4/htdocs/sftp-lib/vendor/autoload.php';
require __DIR__ . '/../vendor/autoload.php';

use phpseclib3\Net\SFTP;

$host = '202.152.22.234';
$port = 22;
$username = 'reliance';
$password = 'reliance@brks2026';

echo "Mencoba koneksi SFTP...\n";

try {

    $sftp = new SFTP($host, $port, 30);

    echo "Koneksi SFTP berhasil.\n";

    if (!$sftp->login($username, $password)) {
        die("Login SFTP gagal.\n");
    }

    echo "Login SFTP berhasil.\n";

    if (!$sftp->chdir('Outgoing')) {
        die("Folder Outgoing tidak ditemukan.\n");
    }

    echo "Folder Outgoing berhasil dibuka.\n";

    $files = $sftp->nlist();

    echo "\nIsi folder Outgoing:\n";

    if ($files === false) {

        echo "Gagal membaca folder Outgoing.\n";

    } elseif (empty($files)) {

        echo "Folder Outgoing kosong.\n";

    } else {

        foreach ($files as $file) {

            if ($file === '.' || $file === '..') {
                continue;
            }

            echo "- " . $file . "\n";
        }
    }

    echo "\nTEST SFTP BERHASIL.\n";

} catch (\Throwable $e) {

    echo "\nTEST SFTP GAGAL.\n";
    echo "Error: " . $e->getMessage() . "\n";
}