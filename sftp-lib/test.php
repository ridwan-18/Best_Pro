<?php

require __DIR__ . '/vendor/autoload.php';

use phpseclib3\Net\SFTP;

$host = '202.152.22.234';
$port = 22;
$username = 'reliance';
$password = 'reliance@brks2026';

echo "Memulai koneksi SFTP...\n";

$sftp = new SFTP($host, $port);

if (!$sftp->login($username, $password)) {
    echo "LOGIN SFTP GAGAL\n";
    exit;
}

echo "LOGIN SFTP BERHASIL\n";

echo "Current directory: " . $sftp->pwd() . "\n";

echo "Isi folder:\n";

$list = $sftp->nlist();

if ($list === false) {
    echo "Gagal membaca folder SFTP\n";
    exit;
}

foreach ($list as $file) {
    echo "- " . $file . "\n";
}