<?php
require __DIR__ . '/../app/bootstrap.php';
wajib_peran('admin', 'panitia');

$self = url('admin/pemain.php');
$timId = (int)($_GET['tim'] ?? $_POST['tim'] ?? 0);
$tim = ambil_satu('SELECT * FROM tim WHERE id = ?', [$timId]);
if (!$tim) {
    flash('galat', 'Tim tidak ditemukan.');
    redirect(url('admin/turnamen.php'));
}
$t = ambil_turnamen((int)$tim['turnamen_id']);
$maks = (int)$GLOBALS['config']['app']['maks_pemain_per_tim'];

if (adalah_post()) {
    cek_csrf();
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($aksi === 'simpan') {
        $x = $id ? ambil_satu('SELECT * FROM pemain WHERE id = ? AND tim_id = ?', [$id, $timId]) : null;
        $galat = [];
        if ($id && !$x) { $galat[] = 'Pemain tidak ditemukan.'; }
        $nama = bersih($_POST['nama'] ?? '', 80);
        $nick = bersih($_POST['nickname'] ?? '', 40);
        $peran = bersih($_POST['peran'] ?? '', 30);
        $kapten = isset($_POST['kapten']) ? 1 : 0;
        if (panjang($nama) < 2) { $galat[] = 'Nama pemain minimal 2 karakter.'; }
        if (panjang($nick) < 2) { $galat[] = 'Nickname minimal 2 karakter.'; }
        if (!$id && (int)ambil_nilai('SELECT COUNT(*) FROM pemain WHERE tim_id = ?', [$timId]) >= $maks) {
            $galat[] = "Satu tim maksimal $maks pemain.";
        }
        if (!$galat) {
            $db = db();
            try {
                $db->beginTransaction();
                if ($x) {
                    q('UPDATE pemain SET nama=?, nickname=?, peran=?, kapten=? WHERE id=?', [$nama, $nick, $peran ?: null, $kapten, $id]);
                } else {
                    q('INSERT INTO pemain (tim_id, nama, nickname, peran, kapten) VALUES (?,?,?,?,?)', [$timId, $nama, $nick, $peran ?: null, $kapten]);
                    $id = (int)$db->lastInsertId();
                }
                if ($kapten) {
                    q('UPDATE pemain SET kapten = 0 WHERE tim_id = ? AND id <> ?', [$timId, $id]);
                }
                $db->commit();
                catat($x ? 'ubah_pemain' : 'tambah_pemain', "Pemain #$id ($nick) di tim #$timId");
                flash('sukses', 'Pemain tersimpan.');
                redirect($self . '?tim=' . $timId);
            } catch (PDOException $e) {
                if ($db->inTransaction()) { $db->rollBack(); }
                $galat[] = $e->getCode() === '23000' ? 'Nickname itu sudah dipakai di tim ini.' : 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
        foreach ($galat as $g) { flash('galat', $g); }
        redirect($self . '?tim=' . $timId . '&aksi=' . ($id ? 'ubah&id=' . $id : 'tambah'));
    }
    if ($aksi === 'hapus') {
        $x = ambil_satu('SELECT nickname FROM pemain WHERE id = ? AND tim_id = ?', [$id, $timId]);
        if ($x) {
            q('DELETE FROM pemain WHERE id = ?', [$id]);
            catat('hapus_pemain', "Pemain #$id (" . $x['nickname'] . ') dihapus');
            flash('sukses', 'Pemain dihapus.');
        }
    }
    redirect($self . '?tim=' . $timId);
}

$aksi = $_GET['aksi'] ?? 'daftar';
if ($aksi === 'tambah' || $aksi === 'ubah') {
    $id = (int)($_GET['id'] ?? 0);
    $x = $aksi === 'ubah' ? ambil_satu('SELECT * FROM pemain WHERE id = ? AND tim_id = ?', [$id, $timId]) : null;
    if ($aksi === 'ubah' && !$x) { redirect($self . '?tim=' . $timId); }
    $d = $x ?: ['nama' => '', 'nickname' => '', 'peran' => '', 'kapten' => 0];
    tampil_header($x ? 'Ubah pemain' : 'Tambah pemain', ['area' => 'admin', 'nav' => 'turnamen']);
?>
<a class="kembali" href="<?= e($self . '?tim=' . $timId) ?>"><?= ikon('back', 16) ?> <?= e($tim['nama']) ?></a>
<h1><?= $x ? 'Ubah pemain' : 'Tambah pemain' ?></h1>
<form method="post" class="panel form-lebar" novalidate>
  <?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="tim" value="<?= $timId ?>"><input type="hidden" name="id" value="<?= (int)$id ?>">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="nama">Nama lengkap</label><input class="form-control" id="nama" name="nama" maxlength="80" value="<?= e($d['nama']) ?>" required></div>
    <div class="col-md-3"><label class="form-label" for="nickname">Nickname</label><input class="form-control" id="nickname" name="nickname" maxlength="40" value="<?= e($d['nickname']) ?>" required></div>
    <div class="col-md-3"><label class="form-label" for="peran">Peran (opsional)</label><input class="form-control" id="peran" name="peran" maxlength="30" value="<?= e($d['peran']) ?>" placeholder="mis. Jungler"></div>
    <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="kapten" name="kapten" value="1"<?= $d['kapten'] ? ' checked' : '' ?>><label class="form-check-label" for="kapten">Kapten tim (kapten lama otomatis diganti)</label></div></div>
  </div>
  <div class="aksi-form"><button class="btn tombol-utama" type="submit">Simpan</button><a class="btn tombol-garis" href="<?= e($self . '?tim=' . $timId) ?>">Batal</a></div>
</form>
<?php
    tampil_footer();
    exit;
}

$pemain = ambil_semua('SELECT * FROM pemain WHERE tim_id = ? ORDER BY kapten DESC, nama', [$timId]);
tampil_header('Pemain · ' . $tim['nama'], ['area' => 'admin', 'nav' => 'turnamen']);
?>
<a class="kembali" href="<?= e(url('admin/tim.php') . '?turnamen=' . (int)$t['id']) ?>"><?= ikon('back', 16) ?> Daftar tim</a>
<div class="baris-judul"><div><h1>Pemain</h1><?= chip_tim($tim['nama'], $tim['warna'], $tim['singkatan']) ?> <span class="teks-redup"><?= count($pemain) ?>/<?= $maks ?> pemain</span></div>
  <a class="btn tombol-utama" href="<?= e($self . '?tim=' . $timId . '&aksi=tambah') ?>"><?= ikon('plus', 16) ?> Tambah pemain</a></div>
<?php if (!$pemain): ?>
  <?= kartu_kosong('Belum ada pemain', 'Tambahkan pemain inti dan cadangan tim ini.') ?>
<?php else: ?>
<div class="table-responsive panel"><table class="table align-middle tabel-admin">
  <thead><tr><th scope="col">Nickname</th><th scope="col">Nama</th><th scope="col">Peran</th><th scope="col"><span class="visually-hidden">Aksi</span></th></tr></thead><tbody>
<?php foreach ($pemain as $p): ?>
  <tr><td><b><?= e($p['nickname']) ?></b><?= $p['kapten'] ? ' <span class="lencana lencana-sun">Kapten</span>' : '' ?></td>
    <td><?= e($p['nama']) ?></td><td><?= $p['peran'] ? e($p['peran']) : '<span class="teks-redup">-</span>' ?></td>
    <td class="text-end aksi-baris">
      <a class="btn btn-sm tombol-garis" href="<?= e($self . '?tim=' . $timId . '&aksi=ubah&id=' . (int)$p['id']) ?>"><?= ikon('edit', 14) ?> Ubah</a>
      <?= tombol_post($self, ['aksi' => 'hapus', 'tim' => $timId, 'id' => $p['id']], ikon('trash', 14) . ' Hapus', 'tombol-bahaya', 'Hapus pemain "' . $p['nickname'] . '"?') ?>
    </td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;
tampil_footer();
