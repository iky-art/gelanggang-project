<?php
require __DIR__ . '/../app/bootstrap.php';
wajib_peran('admin');

$cari = bersih($_GET['q'] ?? '', 40);
if ($cari !== '') {
    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $cari) . '%';
    $baris = ambil_semua('SELECT * FROM log_aktivitas WHERE aksi LIKE ? OR nama_pengguna LIKE ? OR detail LIKE ? ORDER BY id DESC LIMIT 200', [$like, $like, $like]);
} else {
    $baris = ambil_semua('SELECT * FROM log_aktivitas ORDER BY id DESC LIMIT 200');
}

tampil_header('Log aktivitas', ['area' => 'admin', 'nav' => 'log']);
?>
<div class="baris-judul"><h1>Log aktivitas</h1></div>
<form method="get" class="pencarian" role="search">
  <label class="visually-hidden" for="q">Cari log</label>
  <input class="form-control" id="q" name="q" value="<?= e($cari) ?>" placeholder="Cari aksi, pengguna, atau detail">
  <button class="btn tombol-garis" type="submit">Cari</button>
</form>
<p class="teks-redup">Menampilkan maksimal 200 catatan terbaru. Setiap input dan koreksi skor tercatat di sini.</p>
<?php if (!$baris): ?>
  <?= kartu_kosong('Tidak ada catatan', 'Belum ada aktivitas yang cocok.') ?>
<?php else: ?>
<div class="table-responsive panel"><table class="table align-middle tabel-admin tabel-log">
  <thead><tr><th scope="col">Waktu</th><th scope="col">Pengguna</th><th scope="col">Aksi</th><th scope="col">Detail</th></tr></thead><tbody>
<?php foreach ($baris as $b): ?>
  <tr><td class="nowrap"><?= e(tanggal_id($b['waktu'])) ?></td><td><?= e($b['nama_pengguna']) ?></td><td><code><?= e($b['aksi']) ?></code></td><td><?= e($b['detail']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;
tampil_footer();
