<?php
/**
 * Pengaturan Gelanggang.
 * Di XAMPP bawaan, cukup biarkan seperti ini (user root, tanpa kata sandi).
 * Di Docker, nilainya diambil dari variabel lingkungan (lihat docker-compose.yml).
 */
$sandi = getenv('DB_PASS');

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'port' => getenv('DB_PORT') ?: '3306',
        'nama' => getenv('DB_NAME') ?: 'gelanggang',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => $sandi === false ? '' : $sandi,
    ],
    'app' => [
        'nama'       => 'Gelanggang',
        'versi'      => '1.0.0',
        'zona_waktu' => 'Asia/Jakarta',
        'maks_pemain_per_tim' => 12,
    ],
];
