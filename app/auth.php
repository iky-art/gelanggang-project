<?php
/* Sesi, login, peran, dan log aktivitas. */

function mulai_sesi(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('gelanggang_sesi');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

function pengguna(): ?array
{
    return $_SESSION['pengguna'] ?? null;
}
function adalah_admin(): bool
{
    return (pengguna()['peran'] ?? '') === 'admin';
}

function aman_kembali(?string $tujuan): string
{
    $tujuan = (string)$tujuan;
    if ($tujuan === '' || $tujuan[0] !== '/' || strpos($tujuan, '//') === 0 || strpos($tujuan, '\\') !== false
        || strpos($tujuan, base_url() . '/') !== 0) {
        return url('admin/index.php');
    }
    return $tujuan;
}

function wajib_login(): void
{
    if (!pengguna()) {
        flash('peringatan', 'Silakan masuk dulu untuk membuka panel.');
        redirect(url('login.php') . '?kembali=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
    }
}
function wajib_peran(string ...$peran): void
{
    wajib_login();
    if (!in_array(pengguna()['peran'], $peran, true)) {
        http_response_code(403);
        galat_halaman('Akses ditolak', 'Peran kamu tidak punya izin untuk membuka halaman ini.');
    }
}

function catat(string $aksi, string $detail = ''): void
{
    try {
        $p = pengguna();
        q('INSERT INTO log_aktivitas (pengguna_id, nama_pengguna, aksi, detail, ip, waktu) VALUES (?,?,?,?,?,NOW())', [
            $p['id'] ?? null,
            $p['nama'] ?? 'Tamu',
            potong($aksi, 40),
            $detail,
            $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    } catch (Throwable $e) {
        /* log tidak boleh menggagalkan aksi utama */
    }
}

function jumlah_gagal_login(string $username, string $ip): int
{
    return (int)ambil_nilai(
        'SELECT COUNT(*) FROM percobaan_login WHERE username = ? AND ip = ? AND waktu > (NOW() - INTERVAL 15 MINUTE)',
        [$username, $ip]
    );
}

function coba_masuk(string $username, string $sandi): array
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $username = trim($username);
    if ($username === '' || $sandi === '') {
        return ['ok' => false, 'pesan' => 'Isi username dan kata sandi.'];
    }
    if (jumlah_gagal_login($username, $ip) >= 5) {
        return ['ok' => false, 'pesan' => 'Terlalu banyak percobaan gagal. Coba lagi dalam 15 menit.'];
    }
    $u = ambil_satu('SELECT * FROM pengguna WHERE username = ? AND aktif = 1', [$username]);
    if (!$u || !password_verify($sandi, $u['kata_sandi'])) {
        q('INSERT INTO percobaan_login (username, ip, waktu) VALUES (?,?,NOW())', [$username, $ip]);
        return ['ok' => false, 'pesan' => 'Username atau kata sandi salah.'];
    }
    if (password_needs_rehash($u['kata_sandi'], PASSWORD_DEFAULT)) {
        q('UPDATE pengguna SET kata_sandi = ? WHERE id = ?', [password_hash($sandi, PASSWORD_DEFAULT), $u['id']]);
    }
    q('DELETE FROM percobaan_login WHERE username = ? AND ip = ?', [$username, $ip]);
    session_regenerate_id(true);
    $_SESSION['pengguna'] = [
        'id' => (int)$u['id'], 'nama' => $u['nama'], 'username' => $u['username'], 'peran' => $u['peran'],
    ];
    catat('masuk', 'Login berhasil');
    return ['ok' => true, 'pesan' => ''];
}

function keluar(): void
{
    catat('keluar', 'Logout');
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $c = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $c['path'], $c['domain'] ?? '', $c['secure'], $c['httponly']);
    }
    session_destroy();
}

/* Aturan kata sandi: minimal 8 karakter, ada huruf dan angka. */
function cek_kata_sandi(string $s): ?string
{
    if (panjang($s) < 8) {
        return 'Kata sandi minimal 8 karakter.';
    }
    if (!preg_match('/[A-Za-z]/', $s) || !preg_match('/\d/', $s)) {
        return 'Kata sandi harus mengandung huruf dan angka.';
    }
    return null;
}
