<?php
require __DIR__ . '/../app/bootstrap.php';
wajib_login();

$jml = [
    'turnamen' => (int)ambil_nilai('SELECT COUNT(*) FROM turnamen'),
    'aktif'    => (int)ambil_nilai("SELECT COUNT(*) FROM turnamen WHERE status IN ('grup','gugur')"),
    'tim'      => (int)ambil_nilai('SELECT COUNT(*) FROM tim'),
    'pemain'   => (int)ambil_nilai('SELECT COUNT(*) FROM pemain'),
];
$menunggu = ambil_semua(SQL_LAGA . " WHERE m.status = 'terjadwal' AND m.bye = 0 AND m.tim_a_id IS NOT NULL AND m.tim_b_id IS NOT NULL
        AND m.waktu IS NOT NULL AND m.waktu < NOW() ORDER BY m.waktu ASC LIMIT 8");
$siapGugur = [];
foreach (ambil_semua("SELECT * FROM turnamen WHERE status = 'grup' AND format = 'liga_gugur'") as $t) {
    if ((int)ambil_nilai("SELECT COUNT(*) FROM pertandingan WHERE turnamen_id = ? AND fase = 'grup' AND status <> 'selesai'", [$t['id']]) === 0) {
        $siapGugur[] = $t;
    }
}
$belumMulai = ambil_semua("SELECT t.*, (SELECT COUNT(*) FROM tim WHERE turnamen_id = t.id) AS jml_tim FROM turnamen t WHERE status = 'draft' ORDER BY id DESC LIMIT 5");

tampil_header('Ringkasan', ['area' => 'admin', 'nav' => 'ringkasan']);
?>
<h1>Halo, <?= e(pengguna()['nama']) ?></h1>
<p class="teks-redup">Ringkasan yang perlu kamu tindak lanjuti hari ini.</p>

<div class="kisi-angka">
  <div class="angka"><b><?= $jml['turnamen'] ?></b><span>Turnamen</span></div>
  <div class="angka"><b><?= $jml['aktif'] ?></b><span>Sedang berjalan</span></div>
  <div class="angka"><b><?= $jml['tim'] ?></b><span>Tim</span></div>
  <div class="angka"><b><?= $jml['pemain'] ?></b><span>Pemain</span></div>
</div>

<div class="dua-kolom">
  <section class="panel">
    <h2 class="judul-panel">Menunggu skor</h2>
<?php if (!$menunggu): ?>
    <p class="teks-redup">Tidak ada laga yang waktunya sudah lewat tanpa skor.</p>
<?php else: foreach ($menunggu as $m):
        echo baris_laga($m, ['turnamen' => true, 'aksi' => '<a class="btn btn-sm tombol-utama" href="' . e(url('admin/laga.php') . '?aksi=skor&id=' . (int)$m['id']) . '">Isi skor</a>']);
    endforeach; endif; ?>
  </section>
  <section class="panel">
    <h2 class="judul-panel">Perlu tindakan</h2>
<?php if (!$siapGugur && !$belumMulai): ?>
    <p class="teks-redup">Tidak ada yang menunggu. Buat turnamen baru bila diperlukan.</p>
<?php endif; ?>
<?php foreach ($siapGugur as $t): ?>
    <div class="catatan-aksi"><b><?= e($t['nama']) ?></b>: semua laga grup selesai.
      <a href="<?= e(url('admin/turnamen.php') . '?aksi=kelola&id=' . (int)$t['id']) ?>">Mulai babak gugur</a></div>
<?php endforeach; ?>
<?php foreach ($belumMulai as $t): ?>
    <div class="catatan-aksi"><b><?= e($t['nama']) ?></b>: pendaftaran, <?= (int)$t['jml_tim'] ?> tim terdaftar.
      <a href="<?= e(url('admin/turnamen.php') . '?aksi=kelola&id=' . (int)$t['id']) ?>">Kelola</a></div>
<?php endforeach; ?>
    <a class="btn tombol-garis mt-2" href="<?= e(url('admin/turnamen.php') . '?aksi=tambah') ?>"><?= ikon('plus', 16) ?> Turnamen baru</a>
  </section>
</div>
<?php tampil_footer();
