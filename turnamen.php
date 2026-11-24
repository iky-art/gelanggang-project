<?php
require __DIR__ . '/app/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
$t = ambil_turnamen($id);
if (!$t) {
    http_response_code(404);
    galat_halaman('Turnamen tidak ditemukan', 'Turnamen yang kamu cari tidak ada atau sudah dihapus.');
}

$punyaGrup = $t['format'] !== 'gugur';
$punyaBagan = $t['format'] !== 'liga';
$tersedia = ['jadwal' => 'Jadwal & hasil', 'tim' => 'Tim'];
if ($punyaBagan) { $tersedia = ['bagan' => 'Bagan gugur'] + $tersedia; }
if ($punyaGrup) { $tersedia = ['klasemen' => $t['format'] === 'liga' ? 'Klasemen' : 'Klasemen grup'] + $tersedia; }

$tab = $_GET['tab'] ?? '';
if (!isset($tersedia[$tab])) {
    $tab = ($punyaBagan && in_array($t['status'], ['gugur', 'selesai'], true)) ? 'bagan' : ($punyaGrup ? 'klasemen' : 'bagan');
}
$juara = $t['juara_id'] ? ambil_satu('SELECT nama, warna FROM tim WHERE id = ?', [$t['juara_id']]) : null;
$prog = progres_laga($id);

tampil_header($t['nama'], ['nav' => 'beranda']);
?>
<nav aria-label="Jejak halaman" class="jejak"><a href="<?= e(url('index.php')) ?>">Beranda</a> / <span>Turnamen</span></nav>
<header class="kepala-turnamen">
  <div>
    <?= lencana_status($t['status']) ?>
    <h1><?= e($t['nama']) ?></h1>
    <p class="teks-redup"><?= e($t['game']) ?> · <?= e(label_format($t['format'])) ?> · <?= $prog['selesai'] ?> dari <?= $prog['total'] ?> laga selesai</p>
  </div>
<?php if ($juara): ?>
  <div class="spanduk-juara"><?= ikon('trophy', 28) ?><div><small>Juara</small><?= chip_tim($juara['nama'], $juara['warna']) ?></div></div>
<?php endif; ?>
</header>

<nav class="tab-turnamen" aria-label="Bagian turnamen">
<?php foreach ($tersedia as $k => $label): ?>
  <a href="<?= e(url('turnamen.php') . '?id=' . $id . '&tab=' . $k) ?>"<?= $k === $tab ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
<?php endforeach; ?>
</nav>

<?php if ($tab === 'klasemen'): ?>
<?php if ($t['status'] === 'draft'): ?>
  <?= kartu_kosong('Turnamen belum dimulai', 'Klasemen muncul setelah panitia memulai turnamen dan pertandingan pertama selesai.') ?>
<?php else:
    $lolos = $t['format'] === 'liga_gugur' ? (int)$t['lolos_per_grup'] : 0;
    foreach (klasemen_turnamen($t) as $g): ?>
  <section class="panel">
    <h2 class="judul-panel"><?= e(nama_babak_grup($g['grup']['nama'])) ?></h2>
    <?= tabel_klasemen($g['baris'], $lolos) ?>
  </section>
<?php endforeach; ?>
  <p class="catatan">
    Urutan: poin → selisih skor → head-to-head → total skor memasukkan → undian.
<?php if ($lolos): ?> Baris dengan garis hijau lolos ke babak gugur.<?php endif; ?>
    Tanda <span class="tanda-seri">=</span> berarti dua tim seri total dan perlu keputusan panitia.
  </p>
<?php endif; ?>

<?php elseif ($tab === 'bagan'): ?>
<?php $bagan = laga_turnamen($id, 'gugur'); ?>
<?php if (!$bagan): ?>
  <?= kartu_kosong('Bagan belum dibuat', $t['format'] === 'liga_gugur' ? 'Bagan gugur dibuat setelah semua laga grup selesai.' : 'Bagan dibuat saat turnamen dimulai.') ?>
<?php else: ?>
  <section class="panel"><?= bagan_html($bagan) ?></section>
<?php endif; ?>

<?php elseif ($tab === 'jadwal'): ?>
<?php $semua = laga_turnamen($id); ?>
<?php if (!$semua): ?>
  <?= kartu_kosong('Belum ada jadwal', 'Jadwal dibuat otomatis saat panitia memulai turnamen.') ?>
<?php else:
    $kepala = null;
    foreach ($semua as $m) {
        $k = $m['fase'] === 'grup' ? nama_babak_grup($m['nama_grup']) . ' · Ronde ' . (int)$m['ronde'] : ($m['label'] ?: 'Babak gugur');
        if ($k !== $kepala) {
            if ($kepala !== null) { echo '</section>'; }
            echo '<section class="blok-ronde"><h2 class="judul-ronde">' . e($k) . '</h2>';
            $kepala = $k;
        }
        echo baris_laga($m);
    }
    echo '</section>';
endif; ?>

<?php else: ?>
<?php
    $tim = daftar_tim($id);
    $pemain = ambil_semua('SELECT p.* FROM pemain p JOIN tim t ON t.id = p.tim_id WHERE t.turnamen_id = ? ORDER BY p.kapten DESC, p.nama', [$id]);
    $perTim = [];
    foreach ($pemain as $p) { $perTim[(int)$p['tim_id']][] = $p; }
?>
<?php if (!$tim): ?>
  <?= kartu_kosong('Belum ada tim', 'Tim didaftarkan oleh panitia.') ?>
<?php else: ?>
  <div class="kisi-kartu">
<?php foreach ($tim as $x): ?>
    <article class="kartu-tim" style="--w:<?= e(warna_valid($x['warna']) ? $x['warna'] : '#2F6BFF') ?>">
      <h2><?= e($x['nama']) ?> <small><?= e($x['singkatan']) ?></small></h2>
      <p class="teks-redup"><?= $x['nama_grup'] ? e(nama_babak_grup($x['nama_grup'])) : 'Belum masuk grup' ?></p>
      <ul class="daftar-pemain">
<?php foreach ($perTim[(int)$x['id']] ?? [] as $p): ?>
        <li><b><?= e($p['nickname']) ?></b> <span class="teks-redup"><?= e($p['nama']) ?><?= $p['peran'] ? ' · ' . e($p['peran']) : '' ?></span><?= $p['kapten'] ? ' <span class="lencana lencana-sun">Kapten</span>' : '' ?></li>
<?php endforeach; ?>
<?php if (empty($perTim[(int)$x['id']])): ?><li class="teks-redup">Belum ada pemain terdaftar.</li><?php endif; ?>
      </ul>
    </article>
<?php endforeach; ?>
  </div>
<?php endif; ?>
<?php endif; ?>
<?php tampil_footer();
