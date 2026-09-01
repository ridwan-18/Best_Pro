<?php

require __DIR__ . '/sftp-lib/vendor/autoload.php';

use phpseclib3\Net\SFTP;

$host = '202.152.22.234';
$port = 22;
$username = 'reliance';
$password = 'reliance@brks2026';

echo "Mencoba koneksi SFTP...<br>";

try {

    $sftp = new SFTP($host, $port, 30);

    echo "Koneksi SFTP berhasil.<br>";

    if (!$sftp->login($username, $password)) {
        die("Login SFTP gagal.<br>");
    }

    echo "Login SFTP berhasil.<br>";

    if (!$sftp->chdir('/outgoing')) {
        die("Folder outgoing tidak ditemukan.<br>");
    }

    echo "Folder outgoing berhasil dibuka.<br>";

    $files = $sftp->nlist();

    echo "<br>Isi folder outgoing:<br>";

    if ($files === false) {

        echo "Gagal membaca folder outgoing.<br>";

    } elseif (empty($files)) {

        echo "Folder outgoing kosong.<br>";

    } else {

        foreach ($files as $file) {

            if ($file === '.' || $file === '..') {
                continue;
            }

            echo "- " . htmlspecialchars($file) . "<br>";
        }
    }

    echo "<br><strong>TEST SFTP BERHASIL.</strong>";

} catch (\Throwable $e) {

    echo "<br><strong>TEST SFTP GAGAL.</strong><br>";
    echo "Error: " . htmlspecialchars($e->getMessage());
}