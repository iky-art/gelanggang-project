<?php
/* ---------- Teks & keluaran aman ---------- */
function e($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function panjang(string $s): int   { return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s); }
function potong(string $s, int $n): string { return function_exists('mb_substr') ? mb_substr($s, 0, $n, 'UTF-8') : substr($s, 0, $n); }
function huruf_kecil(string $s): string { return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s); }
function huruf_besar(string $s): string { return function_exists('mb_strtoupper') ? mb_strtoupper($s, 'UTF-8') : strtoupper($s); }

/* ---------- URL ---------- */
function base_url(): string
{
    static $b = null;
    if ($b === null) {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        if (substr($dir, -6) === '/admin') {
            $dir = substr($dir, 0, -6);
        }
        $b = rtrim($dir, '/');
    }
    return $b;
}
function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}
function aset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}
function redirect(string $tujuan): void
{
    header('Location: ' . $tujuan);
    exit;
}

/* ---------- Pesan kilat (flash) ---------- */
function flash(string $tipe, string $pesan): void
{
    $_SESSION['flash'][] = ['tipe' => $tipe, 'pesan' => $pesan];
}
function ambil_flash(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}
function cek_csrf(): void
{
    $t = $_POST['_csrf'] ?? '';
    if (!is_string($t) || !hash_equals(csrf_token(), $t)) {
        http_response_code(419);
        galat_halaman('Formulir kedaluwarsa', 'Token keamanan tidak cocok. Muat ulang halaman, lalu coba lagi.');
    }
}
function adalah_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/* ---------- Header keamanan ---------- */
function kirim_header_keamanan(): void
{
    if (headers_sent()) {
        return;
    }
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'; frame-ancestors 'self'");
}

/* ---------- Tanggal ---------- */
function tanggal_id(?string $dt, bool $denganJam = true): string
{
    if (!$dt) {
        return 'Belum dijadwalkan';
    }
    $ts = strtotime($dt);
    if (!$ts) {
        return '-';
    }
    $hari  = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $s = $hari[(int)date('w', $ts)] . ', ' . date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    return $denganJam ? $s . ' · ' . date('H.i', $ts) : $s;
}
function untuk_input_waktu(?string $dt): string
{
    return $dt ? date('Y-m-d\TH:i', strtotime($dt)) : '';
}

/* ---------- Input lama pada formulir ---------- */
function lama(string $kunci, $bawaan = '')
{
    return $_POST[$kunci] ?? $bawaan;
}

/* ---------- Ikon SVG (tanpa pustaka ikon, jadi aman offline) ---------- */
function ikon(string $nama, int $ukuran = 18): string
{
    static $p = [
        'plus'    => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'edit'    => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>',
        'trash'   => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
        'check'   => '<polyline points="20 6 9 17 4 12"/>',
        'users'   => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user'    => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'calendar'=> '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'lock'    => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
        'play'    => '<polygon points="5 3 19 12 5 21 5 3"/>',
        'logout'  => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'trophy'  => '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>',
        'grid'    => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'list'    => '<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>',
        'clock'   => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'back'    => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
        'alert'   => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
        'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
        'eye'     => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
    ];
    $isi = $p[$nama] ?? '';
    return '<svg class="ikon" width="' . $ukuran . '" height="' . $ukuran . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $isi . '</svg>';
}
