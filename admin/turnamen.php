<?php
require __DIR__ . '/../app/bootstrap.php';
wajib_peran('admin', 'panitia');

$self = url('admin/turnamen.php');

/* ---------------- Aksi POST ---------------- */
if (adalah_post()) {
    cek_csrf();
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($aksi === 'simpan') {
        $t = $id ? ambil_turnamen($id) : null;
        $mulai = $t && $t['status'] !== 'draft';
        $nama = bersih($_POST['nama'] ?? '', 120);
        $game = bersih($_POST['game'] ?? '', 60);
        $durasi = bulat_dalam($_POST['durasi_menit'] ?? '', 10, 240);
        $galat = [];
        if (panjang($nama) < 3) { $galat[] = 'Nama turnamen minimal 3 karakter.'; }
        if (panjang($game) < 2) { $galat[] = 'Nama game minimal 2 karakter.'; }
        if ($durasi === null) { $galat[] = 'Durasi laga 10-240 menit.'; }

        if ($mulai) {
            if (!$galat) {
                q('UPDATE turnamen SET nama = ?, game = ?, durasi_menit = ? WHERE id = ?', [$nama, $game, $durasi, $id]);
                catat('ubah_turnamen', "Turnamen #$id diubah (nama/game/durasi)");
                flash('sukses', 'Turnamen diperbarui. Aturan lain terkunci karena turnamen sudah dimulai.');
                redirect($self . '?aksi=kelola&id=' . $id);
            }
        } else {
            $format = in_array($_POST['format'] ?? '', ['liga_gugur', 'liga', 'gugur'], true) ? $_POST['format'] : 'liga_gugur';
            $grup = bulat_dalam($_POST['jumlah_grup'] ?? '', 1, 8);
            $lolos = bulat_dalam($_POST['lolos_per_grup'] ?? '', 1, 4);
            $pm = bulat_dalam($_POST['poin_menang'] ?? '', 0, 9);
            $ps = bulat_dalam($_POST['poin_seri'] ?? '', 0, 9);
            $pk = bulat_dalam($_POST['poin_kalah'] ?? '', 0, 9);
            if ($grup === null || $lolos === null) { $galat[] = 'Jumlah grup 1-8 dan tim lolos 1-4.'; }
            if ($pm === null || $ps === null || $pk === null) { $galat[] = 'Poin menang/seri/kalah harus 0-9.'; }
            if ($pm !== null && $ps !== null && $pk !== null && !($pm > $ps && $ps >= $pk)) { $galat[] = 'Poin menang harus lebih besar dari seri, dan seri tidak lebih kecil dari kalah.'; }
            if ($format === 'liga') { $grup = 1; $lolos = 1; }
            if ($format === 'gugur') { $grup = 1; $lolos = 1; }
            if ($format === 'liga_gugur' && $grup !== null && $lolos !== null && $grup * $lolos < 2) { $galat[] = 'Jumlah tim lolos total minimal 2 (grup × lolos per grup).'; }
            if (!$galat) {
                $nilai = [$nama, $game, $format, $grup, $lolos, isset($_POST['pulang_pergi']) ? 1 : 0, isset($_POST['izinkan_seri']) ? 1 : 0, $pm, $ps, $pk, isset($_POST['juara_tiga']) ? 1 : 0, $durasi];
                if ($id && $t) {
                    q('UPDATE turnamen SET nama=?, game=?, format=?, jumlah_grup=?, lolos_per_grup=?, pulang_pergi=?, izinkan_seri=?, poin_menang=?, poin_seri=?, poin_kalah=?, juara_tiga=?, durasi_menit=? WHERE id=?', array_merge($nilai, [$id]));
                    catat('ubah_turnamen', "Turnamen #$id diubah");
                } else {
                    q('INSERT INTO turnamen (nama, game, format, jumlah_grup, lolos_per_grup, pulang_pergi, izinkan_seri, poin_menang, poin_seri, poin_kalah, juara_tiga, durasi_menit) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', $nilai);
                    $id = (int)db()->lastInsertId();
                    catat('buat_turnamen', "Turnamen #$id dibuat: $nama");
                }
                flash('sukses', 'Turnamen tersimpan. Sekarang daftarkan timnya.');
                redirect($self . '?aksi=kelola&id=' . $id);
            }
        }
        foreach ($galat as $g) { flash('galat', $g); }
        redirect($self . '?aksi=' . ($id ? 'ubah&id=' . $id : 'tambah'));
    }

    if ($aksi === 'mulai') {
        $r = mulai_turnamen($id, ($_POST['mode'] ?? '') === 'acak' ? 'acak' : 'seed');
        flash($r['ok'] ? 'sukses' : 'galat', $r['pesan']);
        redirect($self . '?aksi=kelola&id=' . $id);
    }
    if ($aksi === 'mulai_gugur') {
        $r = mulai_gugur($id);
        flash($r['ok'] ? 'sukses' : 'galat', $r['pesan']);
        redirect($self . '?aksi=kelola&id=' . $id);
    }
    if ($aksi === 'jadwal_auto') {
        $fase = ($_POST['fase'] ?? '') === 'gugur' ? 'gugur' : 'grup';
        $w = waktu_valid($_POST['mulai'] ?? '');
        $slot = bulat_dalam($_POST['slot_menit'] ?? '', 10, 480);
        $arena = bulat_dalam($_POST['arena'] ?? '', 1, 8);
        if ($w === null || $slot === null || $arena === null) {
            flash('galat', 'Isi waktu mulai, durasi slot (10-480 menit), dan jumlah arena (1-8).');
        } else {
            $r = susun_jadwal_otomatis($id, $fase, $w, $slot, $arena);
            flash($r['ok'] ? 'sukses' : 'galat', $r['pesan']);
        }
        redirect($self . '?aksi=kelola&id=' . $id);
    }
    if ($aksi === 'hapus') {
        wajib_peran('admin');
        $t = ambil_turnamen($id);
        if ($t) {
            q('DELETE FROM turnamen WHERE id = ?', [$id]);
            catat('hapus_turnamen', "Turnamen #$id dihapus: " . $t['nama']);
            flash('sukses', 'Turnamen beserta tim, pemain, dan laganya dihapus.');
        }
        redirect($self);
    }
    redirect($self);
}

/* ---------------- Tampilan ---------------- */
$aksi = $_GET['aksi'] ?? 'daftar';
$id = (int)($_GET['id'] ?? 0);

if ($aksi === 'tambah' || $aksi === 'ubah') {
    $t = $aksi === 'ubah' ? ambil_turnamen($id) : null;
    if ($aksi === 'ubah' && !$t) { redirect($self); }
    $d = $t ?: ['nama' => '', 'game' => '', 'format' => 'liga_gugur', 'jumlah_grup' => 2, 'lolos_per_grup' => 2, 'pulang_pergi' => 0,
        'izinkan_seri' => 0, 'poin_menang' => 3, 'poin_seri' => 1, 'poin_kalah' => 0, 'juara_tiga' => 0, 'durasi_menit' => 60, 'status' => 'draft'];
    $kunci = $t && $t['status'] !== 'draft';
    $dis = $kunci ? ' disabled' : '';
    tampil_header($t ? 'Ubah turnamen' : 'Turnamen baru', ['area' => 'admin', 'nav' => 'turnamen']);
?>
<a class="kembali" href="<?= e($t ? $self . '?aksi=kelola&id=' . $id : $self) ?>"><?= ikon('back', 16) ?> Kembali</a>
<h1><?= $t ? 'Ubah turnamen' : 'Turnamen baru' ?></h1>
<?php if ($kunci): ?><div class="alert alert-info">Turnamen sudah dimulai, jadi hanya nama, game, dan durasi laga yang bisa diubah. Aturan format dikunci agar hasil tetap adil.</div><?php endif; ?>
<form method="post" class="panel form-lebar" novalidate>
  <?= csrf_field() ?>
  <input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= (int)$id ?>">
  <div class="row g-3">
    <div class="col-md-8"><label class="form-label" for="nama">Nama turnamen</label>
      <input class="form-control" id="nama" name="nama" maxlength="120" value="<?= e($d['nama']) ?>" required></div>
    <div class="col-md-4"><label class="form-label" for="game">Game</label>
      <input class="form-control" id="game" name="game" maxlength="60" value="<?= e($d['game']) ?>" placeholder="mis. Mobile Legends" required></div>
    <div class="col-md-6"><label class="form-label" for="format">Format</label>
      <select class="form-select" id="format" name="format"<?= $dis ?>>
<?php foreach (['liga_gugur' => 'Fase grup lalu gugur', 'liga' => 'Liga saja (satu klasemen)', 'gugur' => 'Gugur saja (bagan)'] as $v => $l): ?>
        <option value="<?= e($v) ?>"<?= $d['format'] === $v ? ' selected' : '' ?>><?= e($l) ?></option>
<?php endforeach; ?></select></div>
    <div class="col-md-3" data-hanya-format="liga_gugur"><label class="form-label" for="jumlah_grup">Jumlah grup</label>
      <input class="form-control" id="jumlah_grup" name="jumlah_grup" type="number" min="1" max="8" value="<?= (int)$d['jumlah_grup'] ?>"<?= $dis ?>></div>
    <div class="col-md-3" data-hanya-format="liga_gugur"><label class="form-label" for="lolos_per_grup">Lolos per grup</label>
      <input class="form-control" id="lolos_per_grup" name="lolos_per_grup" type="number" min="1" max="4" value="<?= (int)$d['lolos_per_grup'] ?>"<?= $dis ?>></div>
    <div class="col-4 col-md-2"><label class="form-label" for="poin_menang">Poin menang</label>
      <input class="form-control" id="poin_menang" name="poin_menang" type="number" min="0" max="9" value="<?= (int)$d['poin_menang'] ?>"<?= $dis ?>></div>
    <div class="col-4 col-md-2"><label class="form-label" for="poin_seri">Poin seri</label>
      <input class="form-control" id="poin_seri" name="poin_seri" type="number" min="0" max="9" value="<?= (int)$d['poin_seri'] ?>"<?= $dis ?>></div>
    <div class="col-4 col-md-2"><label class="form-label" for="poin_kalah">Poin kalah</label>
      <input class="form-control" id="poin_kalah" name="poin_kalah" type="number" min="0" max="9" value="<?= (int)$d['poin_kalah'] ?>"<?= $dis ?>></div>
    <div class="col-md-3"><label class="form-label" for="durasi_menit">Durasi satu laga (menit)</label>
      <input class="form-control" id="durasi_menit" name="durasi_menit" type="number" min="10" max="240" value="<?= (int)$d['durasi_menit'] ?>" required></div>
    <div class="col-12">
      <div class="form-check"><input class="form-check-input" type="checkbox" id="pulang_pergi" name="pulang_pergi" value="1"<?= $d['pulang_pergi'] ? ' checked' : '' ?><?= $dis ?>><label class="form-check-label" for="pulang_pergi">Pulang-pergi di babak grup/liga (setiap tim bertemu dua kali)</label></div>
      <div class="form-check"><input class="form-check-input" type="checkbox" id="izinkan_seri" name="izinkan_seri" value="1"<?= $d['izinkan_seri'] ? ' checked' : '' ?><?= $dis ?>><label class="form-check-label" for="izinkan_seri">Izinkan skor seri di babak grup/liga</label></div>
      <div class="form-check" data-hanya-format="liga_gugur gugur"><input class="form-check-input" type="checkbox" id="juara_tiga" name="juara_tiga" value="1"<?= $d['juara_tiga'] ? ' checked' : '' ?><?= $dis ?>><label class="form-check-label" for="juara_tiga">Buat perebutan juara 3 (hanya jika kedua semifinal tanpa bye)</label></div>
    </div>
  </div>
  <div class="aksi-form"><button class="btn tombol-utama" type="submit">Simpan</button>
    <a class="btn tombol-garis" href="<?= e($t ? $self . '?aksi=kelola&id=' . $id : $self) ?>">Batal</a></div>
</form>
<?php
    tampil_footer();
    exit;
}

if ($aksi === 'kelola') {
    $t = ambil_turnamen($id);
    if (!$t) { flash('galat', 'Turnamen tidak ditemukan.'); redirect($self); }
    $tim = daftar_tim($id);
    $prog = progres_laga($id);
    $peringatan = peringatan_turnamen($t);
    $juara = $t['juara_id'] ? ambil_satu('SELECT nama, warna FROM tim WHERE id = ?', [$t['juara_id']]) : null;
    $grupBelum = (int)ambil_nilai("SELECT COUNT(*) FROM pertandingan WHERE turnamen_id = ? AND fase = 'grup' AND status <> 'selesai'", [$id]);
    tampil_header($t['nama'], ['area' => 'admin', 'nav' => 'turnamen']);
?>
<a class="kembali" href="<?= e($self) ?>"><?= ikon('back', 16) ?> Semua turnamen</a>
<div class="kepala-turnamen">
  <div><?= lencana_status($t['status']) ?><h1><?= e($t['nama']) ?></h1>
    <p class="teks-redup"><?= e($t['game']) ?> · <?= e(label_format($t['format'])) ?> · <?= count($tim) ?> tim · <?= $prog['selesai'] ?>/<?= $prog['total'] ?> laga selesai</p></div>
<?php if ($juara): ?><div class="spanduk-juara"><?= ikon('trophy', 28) ?><div><small>Juara</small><?= chip_tim($juara['nama'], $juara['warna']) ?></div></div><?php endif; ?>
</div>
<?= tab_turnamen($id, 'kelola') ?>
<?php foreach ($peringatan as $p): ?><div class="alert alert-warning" role="alert"><?= ikon('alert', 16) ?> <?= e($p) ?></div><?php endforeach; ?>

<div class="dua-kolom">
  <section class="panel">
    <h2 class="judul-panel">Langkah turnamen</h2>
<?php if ($t['status'] === 'draft'): ?>
    <p>Daftarkan semua tim dulu, lalu mulai. Setelah dimulai, jumlah tim dan aturan dikunci.</p>
    <form method="post" class="baris-form" data-konfirmasi="Mulai turnamen sekarang? Tim dan aturan akan dikunci.">
      <?= csrf_field() ?><input type="hidden" name="aksi" value="mulai"><input type="hidden" name="id" value="<?= $id ?>">
<?php if ($t['format'] !== 'gugur'): ?>
      <label class="form-label" for="mode">Pembagian grup</label>
      <select class="form-select" id="mode" name="mode"><option value="seed">Berdasarkan seed (pola ular)</option><option value="acak">Acak</option></select>
<?php endif; ?>
      <button class="btn tombol-utama mt-2" type="submit"<?= count($tim) < 2 ? ' disabled' : '' ?>><?= ikon('play', 16) ?> Mulai turnamen</button>
<?php if (count($tim) < 2): ?><div class="form-text">Minimal 2 tim terdaftar.</div><?php endif; ?>
    </form>
<?php elseif ($t['status'] === 'grup' && $t['format'] === 'liga_gugur'): ?>
    <p><?= $grupBelum === 0 ? 'Semua laga grup selesai. Bagan gugur siap dibuat.' : "Masih $grupBelum laga grup yang belum selesai." ?></p>
    <?= tombol_post($self, ['aksi' => 'mulai_gugur', 'id' => $id], ikon('play', 16) . ' Mulai babak gugur', 'tombol-utama', 'Buat bagan gugur dari hasil grup? Hasil grup akan dibekukan.') ?>
<?php elseif ($t['status'] === 'selesai'): ?>
    <p>Turnamen selesai. Koreksi skor tetap bisa dilakukan admin lewat menu Laga & skor, dengan alasan yang tercatat.</p>
<?php else: ?>
    <p>Turnamen berjalan. Isi skor lewat menu <a href="<?= e(url('admin/laga.php') . '?turnamen=' . $id) ?>">Laga & skor</a>.</p>
<?php endif; ?>
    <p class="mt-3"><a class="btn tombol-garis btn-sm" href="<?= e($self . '?aksi=ubah&id=' . $id) ?>"><?= ikon('edit', 16) ?> Ubah pengaturan</a></p>
  </section>

<?php if ($t['status'] !== 'draft'): ?>
  <section class="panel">
    <h2 class="judul-panel">Susun jadwal otomatis</h2>
    <p class="teks-redup">Mengisi waktu dan arena untuk laga yang belum selesai. Satu tim tidak akan main dua kali di slot yang sama.</p>
    <form method="post" class="row g-2">
      <?= csrf_field() ?><input type="hidden" name="aksi" value="jadwal_auto"><input type="hidden" name="id" value="<?= $id ?>">
      <div class="col-12"><label class="form-label" for="fase">Fase</label>
        <select class="form-select" id="fase" name="fase">
<?php if ($t['format'] !== 'gugur'): ?><option value="grup">Babak grup / liga</option><?php endif; ?>
<?php if ($t['format'] !== 'liga'): ?><option value="gugur">Babak gugur</option><?php endif; ?></select></div>
      <div class="col-12"><label class="form-label" for="mulai">Mulai</label><input class="form-control" id="mulai" name="mulai" type="datetime-local" required></div>
      <div class="col-6"><label class="form-label" for="slot_menit">Slot (menit)</label><input class="form-control" id="slot_menit" name="slot_menit" type="number" min="10" max="480" value="<?= (int)$t['durasi_menit'] + 10 ?>" required></div>
      <div class="col-6"><label class="form-label" for="arena">Jumlah arena</label><input class="form-control" id="arena" name="arena" type="number" min="1" max="8" value="1" required></div>
      <div class="col-12"><button class="btn tombol-garis" type="submit"><?= ikon('calendar', 16) ?> Susun jadwal</button></div>
    </form>
  </section>
<?php endif; ?>
</div>

<?php if (adalah_admin()): ?>
<section class="panel panel-bahaya">
  <h2 class="judul-panel">Zona hapus</h2>
  <p>Menghapus turnamen ikut menghapus semua tim, pemain, dan laganya. Tidak bisa dibatalkan.</p>
  <?= tombol_post($self, ['aksi' => 'hapus', 'id' => $id], ikon('trash', 16) . ' Hapus turnamen', 'tombol-bahaya', 'Hapus turnamen "' . $t['nama'] . '" beserta seluruh datanya?') ?>
</section>
<?php endif; ?>
<?php
    tampil_footer();
    exit;
}

/* Daftar turnamen */
$daftar = ambil_semua("SELECT t.*, (SELECT COUNT(*) FROM tim WHERE turnamen_id = t.id) AS jml_tim FROM turnamen t ORDER BY t.id DESC");
tampil_header('Turnamen', ['area' => 'admin', 'nav' => 'turnamen']);
?>
<div class="baris-judul"><h1>Turnamen</h1>
  <a class="btn tombol-utama" href="<?= e($self . '?aksi=tambah') ?>"><?= ikon('plus', 16) ?> Turnamen baru</a></div>
<?php if (!$daftar): ?>
  <?= kartu_kosong('Belum ada turnamen', 'Buat turnamen pertama, lalu daftarkan timnya.') ?>
<?php else: ?>
<div class="table-responsive panel"><table class="table align-middle tabel-admin">
  <thead><tr><th scope="col">Turnamen</th><th scope="col">Format</th><th scope="col">Tim</th><th scope="col">Status</th><th scope="col"><span class="visually-hidden">Aksi</span></th></tr></thead><tbody>
<?php foreach ($daftar as $t): ?>
  <tr><td><b><?= e($t['nama']) ?></b><br><small class="teks-redup"><?= e($t['game']) ?></small></td>
    <td><?= e(label_format($t['format'])) ?></td><td><?= (int)$t['jml_tim'] ?></td><td><?= lencana_status($t['status']) ?></td>
    <td class="text-end"><a class="btn btn-sm tombol-garis" href="<?= e($self . '?aksi=kelola&id=' . (int)$t['id']) ?>">Kelola</a></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;
tampil_footer();
