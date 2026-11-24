<?php
require __DIR__ . '/app/bootstrap.php';

$daftar = ambil_semua(
    "SELECT t.*, (SELECT COUNT(*) FROM tim WHERE turnamen_id = t.id) AS jml_tim, j.nama AS nama_juara, j.warna AS warna_juara
       FROM turnamen t LEFT JOIN tim j ON j.id = t.juara_id
      ORDER BY FIELD(t.status,'grup','gugur','draft','selesai'), t.id DESC"
);
$berikut = ambil_semua(SQL_LAGA . " WHERE m.status = 'terjadwal' AND m.bye = 0 AND m.tim_a_id IS NOT NULL AND m.tim_b_id IS NOT NULL
        AND m.waktu IS NOT NULL AND m.waktu >= (NOW() - INTERVAL 3 HOUR) ORDER BY m.waktu ASC LIMIT 6");
$terbaru = ambil_semua(SQL_LAGA . " WHERE m.status = 'selesai' AND m.bye = 0 ORDER BY m.diperbarui_pada DESC LIMIT 6");

tampil_header('Beranda', ['nav' => 'beranda']);
?>
<section class="pembuka">
  <p class="kicker">Turnamen e-sport sekolah</p>
  <h1>Jadwal, klasemen, dan bagan dalam satu papan skor</h1>
  <p class="pembuka-teks">Klasemen dan bagan dihitung otomatis dari skor yang dimasukkan panitia. Tidak perlu hitung manual.</p>
</section>

<section aria-labelledby="h-turnamen">
  <h2 id="h-turnamen" class="judul-seksi">Turnamen</h2>
<?php if (!$daftar): ?>
  <?= kartu_kosong('Belum ada turnamen', 'Panitia belum membuat turnamen. Setelah dibuat, jadwal dan klasemennya muncul di sini.',
      pengguna() ? '<a class="btn tombol-utama" href="' . e(url('admin/turnamen.php')) . '?aksi=tambah">Buat turnamen</a>' : '<a class="btn tombol-utama" href="' . e(url('login.php')) . '">Masuk sebagai panitia</a>') ?>
<?php else: ?>
  <div class="kisi-kartu">
<?php foreach ($daftar as $t): ?>
    <a class="kartu-turnamen" href="<?= e(url('turnamen.php') . '?id=' . (int)$t['id']) ?>">
      <div class="kartu-atas"><?= lencana_status($t['status']) ?><span class="teks-redup"><?= e(label_format($t['format'])) ?></span></div>
      <h3><?= e($t['nama']) ?></h3>
      <p class="teks-redup"><?= e($t['game']) ?> · <?= (int)$t['jml_tim'] ?> tim</p>
<?php if ($t['nama_juara']): ?>
      <p class="juara"><?= ikon('trophy', 18) ?> Juara: <?= chip_tim($t['nama_juara'], $t['warna_juara']) ?></p>
<?php endif; ?>
    </a>
<?php endforeach; ?>
  </div>
<?php endif; ?>
</section>

<div class="dua-kolom">
  <section aria-labelledby="h-berikut">
    <h2 id="h-berikut" class="judul-seksi">Laga berikutnya</h2>
<?php if (!$berikut): ?>
    <p class="teks-redup">Belum ada laga terjadwal dengan waktu yang sudah ditentukan.</p>
<?php else: foreach ($berikut as $m) { echo baris_laga($m, ['turnamen' => true]); } endif; ?>
  </section>
  <section aria-labelledby="h-terbaru">
    <h2 id="h-terbaru" class="judul-seksi">Hasil terbaru</h2>
<?php if (!$terbaru): ?>
    <p class="teks-redup">Belum ada pertandingan yang selesai.</p>
<?php else: foreach ($terbaru as $m) { echo baris_laga($m, ['turnamen' => true]); } endif; ?>
  </section>
</div>
<?php tampil_footer();
