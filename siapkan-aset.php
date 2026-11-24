<?php
/**
 * Pemasang Bootstrap sekali pakai.
 * Jalankan di komputer yang PUNYA internet, lalu salin seluruh folder ke laptop sekolah.
 * Halaman ini mati sendiri setelah file Bootstrap ada.
 */
require __DIR__ . '/app/bootstrap.php';

const VERSI_BS = '5.3.3';
$berkas = [
    'bootstrap.min.css'        => 'https://cdn.jsdelivr.net/npm/bootstrap@' . VERSI_BS . '/dist/css/bootstrap.min.css',
    'bootstrap.bundle.min.js'  => 'https://cdn.jsdelivr.net/npm/bootstrap@' . VERSI_BS . '/dist/js/bootstrap.bundle.min.js',
];
$tujuan = ROOT_DIR . '/assets/vendor/';
$sudahAda = is_file($tujuan . 'bootstrap.min.css') && is_file($tujuan . 'bootstrap.bundle.min.js');

function unduh(string $u): ?string
{
    if (function_exists('curl_init')) {
        $c = curl_init($u);
        curl_setopt_array($c, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => true]);
        $d = curl_exec($c);
        $kode = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);
        return ($d !== false && $kode === 200) ? $d : null;
    }
    $d = @file_get_contents($u, false, stream_context_create(['http' => ['timeout' => 30]]));
    return $d === false ? null : $d;
}

$pesan = [];
if (!$sudahAda && adalah_post()) {
    cek_csrf();
    if (!is_dir($tujuan)) { @mkdir($tujuan, 0775, true); }
    foreach ($berkas as $nama => $u) {
        $d = unduh($u);
        if ($d === null || strlen($d) < 50000 || strpos(substr($d, 0, 400), 'Bootstrap') === false) {
            $pesan[] = ['galat', "Gagal mengunduh $nama. Pastikan komputer ini terhubung ke internet."];
            continue;
        }
        if (@file_put_contents($tujuan . $nama, $d) === false) {
            $pesan[] = ['galat', "Tidak bisa menulis $nama ke assets/vendor/. Periksa izin folder."];
            continue;
        }
        $pesan[] = ['sukses', "$nama terpasang (" . round(strlen($d) / 1024) . ' KB).'];
    }
    $sudahAda = is_file($tujuan . 'bootstrap.min.css') && is_file($tujuan . 'bootstrap.bundle.min.js');
}

tampil_header('Pasang Bootstrap', ['nav' => 'masuk']);
?>
<div class="panel panel-sempit">
  <h1>Pasang Bootstrap offline</h1>
<?php foreach ($pesan as $p): ?><div class="alert alert-<?= $p[0] === 'sukses' ? 'success' : 'danger' ?>"><?= e($p[1]) ?></div><?php endforeach; ?>
<?php if ($sudahAda): ?>
  <p>Bootstrap <?= e(VERSI_BS) ?> sudah ada di <code>assets/vendor/</code>. Halaman ini tidak perlu dipakai lagi dan boleh dihapus.</p>
  <a class="btn tombol-utama" href="<?= e(url('index.php')) ?>">Ke beranda</a>
<?php else: ?>
  <p>File Bootstrap <?= e(VERSI_BS) ?> belum ada. Klik tombol di bawah <b>sekali</b> saat komputer ini punya internet. Setelah itu aplikasi berjalan penuh offline.</p>
  <form method="post"><?= csrf_field() ?><button class="btn tombol-utama" type="submit">Unduh &amp; pasang Bootstrap <?= e(VERSI_BS) ?></button></form>
  <p class="catatan">Tidak bisa online? Unduh dua file itu dari komputer lain, lalu salin manual. Langkahnya ada di <code>docs/03-memasang-bootstrap-offline.md</code>.</p>
<?php endif; ?>
</div>
<?php tampil_footer();
