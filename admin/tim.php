<?php
require __DIR__ . '/../app/bootstrap.php';
wajib_peran('admin', 'panitia');

$self = url('admin/tim.php');
$tid = (int)($_GET['turnamen'] ?? $_POST['turnamen'] ?? 0);
$t = ambil_turnamen($tid);
if (!$t) {
    flash('galat', 'Pilih turnamen terlebih dahulu.');
    redirect(url('admin/turnamen.php'));
}
$draft = $t['status'] === 'draft';

if (adalah_post()) {
    cek_csrf();
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($aksi === 'simpan') {
        $x = $id ? ambil_satu('SELECT * FROM tim WHERE id = ? AND turnamen_id = ?', [$id, $tid]) : null;
        $galat = [];
        if ($id && !$x) { $galat[] = 'Tim tidak ditemukan.'; }
        if (!$id && !$draft) { $galat[] = 'Tim tidak bisa ditambah setelah turnamen dimulai.'; }
        $nama = bersih($_POST['nama'] ?? '', 60);
        if (panjang($nama) < 2) { $galat[] = 'Nama tim minimal 2 karakter.'; }
        $sing = huruf_besar(preg_replace('/[^A-Za-z0-9]/', '', (string)($_POST['singkatan'] ?? '')) ?? '');
        $sing = $sing === '' ? singkatan_dari($nama) : potong($sing, 5);
        if (panjang($sing) < 2) { $galat[] = 'Singkatan 2-5 huruf/angka.'; }
        $warna = (string)($_POST['warna'] ?? '');
        if (!warna_valid($warna)) { $galat[] = 'Warna tidak valid.'; }
        $seedIn = trim((string)($_POST['seed'] ?? ''));
        $seed = $seedIn === '' ? null : bulat_dalam($seedIn, 1, 99);
        if ($seedIn !== '' && $seed === null) { $galat[] = 'Seed 1-99 atau kosongkan.'; }
        $undianIn = trim((string)($_POST['undian'] ?? '0'));
        $undian = bulat_dalam($undianIn === '' ? '0' : $undianIn, -99, 99);
        if ($undian === null) { $galat[] = 'Nilai undian -99 sampai 99.'; }

        if (!$galat) {
            try {
                if ($x) {
                    if ($draft) {
                        q('UPDATE tim SET nama=?, singkatan=?, warna=?, seed=?, undian=? WHERE id=?', [$nama, $sing, $warna, $seed, $undian, $id]);
                    } else {
                        q('UPDATE tim SET nama=?, singkatan=?, warna=?, undian=? WHERE id=?', [$nama, $sing, $warna, $undian, $id]);
                        perbarui_status_turnamen($tid);
                    }
                    catat('ubah_tim', "Tim #$id diubah: $nama");
                } else {
                    q('INSERT INTO tim (turnamen_id, nama, singkatan, warna, seed, undian) VALUES (?,?,?,?,?,?)', [$tid, $nama, $sing, $warna, $seed, $undian]);
                    catat('tambah_tim', "Tim ditambah ke turnamen #$tid: $nama");
                }
                flash('sukses', 'Tim tersimpan.');
                redirect($self . '?turnamen=' . $tid);
            } catch (PDOException $e) {
                $galat[] = $e->getCode() === '23000' ? 'Nama tim itu sudah dipakai di turnamen ini.' : 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
        foreach ($galat as $g) { flash('galat', $g); }
        redirect($self . '?turnamen=' . $tid . '&aksi=' . ($id ? 'ubah&id=' . $id : 'tambah'));
    }

    if ($aksi === 'hapus') {
        if (!$draft) {
            flash('galat', 'Tim tidak bisa dihapus setelah turnamen dimulai.');
        } else {
            $x = ambil_satu('SELECT nama FROM tim WHERE id = ? AND turnamen_id = ?', [$id, $tid]);
            if ($x) {
                q('DELETE FROM tim WHERE id = ?', [$id]);
                catat('hapus_tim', "Tim #$id dihapus: " . $x['nama']);
                flash('sukses', 'Tim dihapus.');
            }
        }
        redirect($self . '?turnamen=' . $tid);
    }
    redirect($self . '?turnamen=' . $tid);
}

$aksi = $_GET['aksi'] ?? 'daftar';
if ($aksi === 'tambah' || $aksi === 'ubah') {
    $id = (int)($_GET['id'] ?? 0);
    $x = $aksi === 'ubah' ? ambil_satu('SELECT * FROM tim WHERE id = ? AND turnamen_id = ?', [$id, $tid]) : null;
    if ($aksi === 'ubah' && !$x) { redirect($self . '?turnamen=' . $tid); }
    if ($aksi === 'tambah' && !$draft) {
        flash('galat', 'Tim tidak bisa ditambah setelah turnamen dimulai.');
        redirect($self . '?turnamen=' . $tid);
    }
    $d = $x ?: ['nama' => '', 'singkatan' => '', 'warna' => '#2F6BFF', 'seed' => '', 'undian' => 0];
    tampil_header($x ? 'Ubah tim' : 'Tambah tim', ['area' => 'admin', 'nav' => 'turnamen']);
?>
<a class="kembali" href="<?= e($self . '?turnamen=' . $tid) ?>"><?= ikon('back', 16) ?> Daftar tim</a>
<h1><?= $x ? 'Ubah tim' : 'Tambah tim' ?></h1>
<form method="post" class="panel form-lebar" novalidate>
  <?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="turnamen" value="<?= $tid ?>"><input type="hidden" name="id" value="<?= (int)$id ?>">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="nama">Nama tim</label><input class="form-control" id="nama" name="nama" maxlength="60" value="<?= e($d['nama']) ?>" required></div>
    <div class="col-6 col-md-3"><label class="form-label" for="singkatan">Singkatan</label><input class="form-control" id="singkatan" name="singkatan" maxlength="5" value="<?= e($d['singkatan']) ?>" aria-describedby="b-sing"><div id="b-sing" class="form-text">Kosongkan untuk otomatis.</div></div>
    <div class="col-6 col-md-3"><label class="form-label" for="warna">Warna tim</label><input class="form-control form-control-color w-100" id="warna" name="warna" type="color" value="<?= e(warna_valid($d['warna']) ? $d['warna'] : '#2F6BFF') ?>"></div>
    <div class="col-6 col-md-3"><label class="form-label" for="seed">Seed</label><input class="form-control" id="seed" name="seed" type="number" min="1" max="99" value="<?= e($d['seed']) ?>"<?= $draft ? '' : ' disabled' ?> aria-describedby="b-seed"><div id="b-seed" class="form-text">1 = unggulan. Opsional.</div></div>
    <div class="col-6 col-md-3"><label class="form-label" for="undian">Nilai undian</label><input class="form-control" id="undian" name="undian" type="number" min="-99" max="99" value="<?= (int)$d['undian'] ?>" aria-describedby="b-und"><div id="b-und" class="form-text">Pemutus terakhir bila seri total.</div></div>
  </div>
  <div class="aksi-form"><button class="btn tombol-utama" type="submit">Simpan</button><a class="btn tombol-garis" href="<?= e($self . '?turnamen=' . $tid) ?>">Batal</a></div>
</form>
<?php
    tampil_footer();
    exit;
}

$tim = daftar_tim($tid);
tampil_header('Tim · ' . $t['nama'], ['area' => 'admin', 'nav' => 'turnamen']);
?>
<a class="kembali" href="<?= e(url('admin/turnamen.php') . '?aksi=kelola&id=' . $tid) ?>"><?= ikon('back', 16) ?> <?= e($t['nama']) ?></a>
<div class="baris-judul"><h1>Tim &amp; pemain</h1>
<?php if ($draft): ?><a class="btn tombol-utama" href="<?= e($self . '?turnamen=' . $tid . '&aksi=tambah') ?>"><?= ikon('plus', 16) ?> Tambah tim</a><?php endif; ?></div>
<?= tab_turnamen($tid, 'tim') ?>
<?php if (!$draft): ?><div class="alert alert-info">Turnamen sudah dimulai: tim tidak bisa ditambah atau dihapus. Nama, warna, dan nilai undian masih bisa diubah.</div><?php endif; ?>
<?php if (!$tim): ?>
  <?= kartu_kosong('Belum ada tim', 'Tambahkan minimal 2 tim untuk bisa memulai turnamen.') ?>
<?php else: ?>
<div class="table-responsive panel"><table class="table align-middle tabel-admin">
  <thead><tr><th scope="col">Tim</th><th scope="col">Grup</th><th scope="col">Seed</th><th scope="col">Undian</th><th scope="col">Pemain</th><th scope="col"><span class="visually-hidden">Aksi</span></th></tr></thead><tbody>
<?php foreach ($tim as $x): ?>
  <tr><td><?= chip_tim($x['nama'], $x['warna'], $x['singkatan']) ?></td>
    <td><?= $x['nama_grup'] ? e($x['nama_grup']) : '<span class="teks-redup">-</span>' ?></td>
    <td><?= $x['seed'] !== null ? (int)$x['seed'] : '<span class="teks-redup">-</span>' ?></td>
    <td><?= (int)$x['undian'] ?></td><td><?= (int)$x['jumlah_pemain'] ?></td>
    <td class="text-end aksi-baris">
      <a class="btn btn-sm tombol-garis" href="<?= e(url('admin/pemain.php') . '?tim=' . (int)$x['id']) ?>"><?= ikon('users', 14) ?> Pemain</a>
      <a class="btn btn-sm tombol-garis" href="<?= e($self . '?turnamen=' . $tid . '&aksi=ubah&id=' . (int)$x['id']) ?>"><?= ikon('edit', 14) ?> Ubah</a>
<?php if ($draft): ?>
      <?= tombol_post($self, ['aksi' => 'hapus', 'turnamen' => $tid, 'id' => $x['id']], ikon('trash', 14) . ' Hapus', 'tombol-bahaya', 'Hapus tim "' . $x['nama'] . '" beserta pemainnya?') ?>
<?php endif; ?>
    </td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif;
tampil_footer();
