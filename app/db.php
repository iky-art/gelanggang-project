<?php
/* Koneksi database (PDO) + pembantu query. Semua query memakai prepared statement. */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $c = $GLOBALS['config']['db'];
    $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['nama']};charset=utf8mb4";
    try {
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec("SET time_zone = '+07:00'");
    } catch (PDOException $e) {
        tampilkan_galat_db($e);
    }
    return $pdo;
}

function tampilkan_galat_db(PDOException $e): void
{
    http_response_code(500);
    $c = $GLOBALS['config']['db'];
    echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Database belum siap · Gelanggang</title>';
    echo '<style>body{font-family:system-ui,"Segoe UI",sans-serif;background:#F4F7FB;color:#1B2540;margin:0;padding:24px}main{max-width:620px;margin:8vh auto;background:#fff;border:2px solid #1B2540;border-radius:14px;padding:28px;box-shadow:6px 6px 0 #FFE08A}h1{margin:0 0 8px;font-size:1.5rem}li{margin:6px 0}code{background:#EEF2FA;padding:2px 6px;border-radius:6px}details{margin-top:16px;color:#55607A}</style></head><body><main>';
    echo '<h1>Database belum siap</h1><p>Aplikasi belum bisa terhubung ke MySQL. Periksa urutan ini:</p><ol>';
    echo '<li>Buka XAMPP, pastikan <b>MySQL</b> berstatus Running.</li>';
    echo '<li>Buka <code>localhost/phpmyadmin</code>, lalu impor <code>database/gelanggang.sql</code>.</li>';
    echo '<li>Cocokkan <code>app/config.php</code> (sekarang: host <code>' . e($c['host']) . '</code>, database <code>' . e($c['nama']) . '</code>, user <code>' . e($c['user']) . '</code>).</li>';
    echo '</ol><details><summary>Pesan teknis</summary><p>' . e($e->getMessage()) . '</p></details></main></body></html>';
    exit;
}

function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}
function ambil_semua(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}
function ambil_satu(string $sql, array $params = []): ?array
{
    $r = q($sql, $params)->fetch();
    return $r === false ? null : $r;
}
function ambil_nilai(string $sql, array $params = [])
{
    $r = q($sql, $params)->fetchColumn();
    return $r === false ? null : $r;
}
