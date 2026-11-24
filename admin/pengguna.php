<?php
require __DIR__ . '/../app/bootstrap.php';
wajib_peran('admin');

$self = url('admin/pengguna.php');
$saya = pengguna();

if (adalah_post()) {
    cek_csrf();
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($aksi === 'simpan') {
        $x = $id ? ambil_satu('SELECT * FROM pengguna WHERE id = ?', [$id]) : null;
        $galat = [];
        if ($id && !$x) { $galat[] = 'Pengguna tidak ditemukan.'; }
        $nama = bersih($_POST['nama'] ?? '', 80);
        $username = bersih($_POST['username'] ?? '', 40);
        $peran = ($_POST['peran'] ?? '') === 'admin' ? 'admin' : 'panitia';
        $aktif = isset($_POST['aktif']) ? 1 : 0;
        $sandi = (string)($_POST['sandi'] ?? '');
        if (panjang($nama) < 2) { $galat[] = 'Nama minimal 2 karakter.'; }
        if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $username)) { $galat[] = 'Username 3-40 karakter: huruf, angka, titik, garis bawah, atau strip.'; }
        if (!$x || $sandi !== '') {
            if ($m = cek_kata_sandi($sandi)) { $galat[] = $m; }
        }
        if ($x && (int)$x['id'] === (int)$saya['id'] && (!$aktif || $peran !== 'admin')) {
            $galat[] = 'Kamu tidak bisa menonaktifkan atau menurunkan peran akunmu sendiri.';
        }
        if ($x && $x['peran'] === 'admin' && (int)$x['aktif'] === 1 && ($peran !== 'admin' || !$aktif)) {
            $sisa = (int)ambil_nilai("SELECT COUNT(*) FROM pengguna WHERE peran = 'admin' AND aktif = 1 AND id <> ?", [$id]);
            if ($sisa < 1) { $galat[] = 'Harus tersisa minimal satu admin aktif.'; }
        }
        if (!$galat) {
            try {
                if ($x) {
                    if ($sandi !== '') {
                        q('UPDATE pengguna SET nama=?, username=?, peran=?, aktif=?, kata_sandi=? WHERE id=?', [$nama, $username, $peran, $aktif, password_hash($sandi, PASSWORD_DEFAULT), $id]);
                    } else {
                        q('UPDATE pengguna SET nama=?, username=?, peran=?, aktif=? WHERE id=?', [$nama, $username, $peran, $aktif, $id]);
                    }
                    catat('ubah_pengguna', "Pengguna #$id ($username) diubah");
                } else {
                    q('INSERT INTO pengguna (nama, username, kata_sandi, peran, aktif) VALUES (?,?,?,?,?)', [$nama, $username, password_hash($sandi, PASSWORD_DEFAULT), $peran, $aktif]);
                    catat('tambah_pengguna', "Pengguna baru: $username ($peran)");
                }
                flash('sukses', 'Pengguna tersimpan.');
                redirect($self);
            } catch (PDOException $e) {
                $galat[] = $e->getCode() === '23000' ? 'Username itu sudah dipakai.' : 'Gagal menyimpan: ' . $e->getMessage();
            }
        }
        foreach ($galat as $g) { flash('galat', $g); }
        redirect($self . '?aksi=' . ($id ? 'ubah&id=' . $id : 'tambah'));
    }

    if ($aksi === 'hapus') {
        $x = ambil_satu('SELECT * FROM pengguna WHERE id = ?', [$id]);
        if (!$x) {
            flash('galat', 'Pengguna tidak ditemukan.');
        } elseif ((int)$x['id'] === (int)$saya['id']) {
            flash('galat', 'Kamu tidak bisa menghapus akunmu sendiri.');
        } elseif ($x['peran'] === 'admin' && (int)ambil_nilai("SELECT COUNT(*) FROM pengguna WHERE peran = 'admin' AND aktif = 1 AND id <> ?", [$id]) < 1) {
            flash('galat', 'Harus tersisa minimal satu admin aktif.');
        } else {
            q('DELETE FROM pengguna WHERE id = ?', [$id]);
            catat('hapus_pengguna', "Pengguna #$id (" . $x['username'] . ') dihapus');
            flash('sukses', 'Pengguna dihapus.');
        }
    }
    redirect($self);
}

$aksi = $_GET['aksi'] ?? 'daftar';
if ($aksi === 'tambah' || $aksi === 'ubah') {
    $id = (int)($_GET['id'] ?? 0);
    $x = $aksi === 'ubah' ? ambil_satu('SELECT * FROM pengguna WHERE id = ?', [$id]) : null;
    if ($aksi === 'ubah' && !$x) { redirect($self); }
    $d = $x ?: ['nama' => '', 'username' => '', 'peran' => 'panitia', 'aktif' => 1];
    tampil_header($x ? 'Ubah pengguna' : 'Tambah pengguna', ['area' => 'admin', 'nav' => 'pengguna']);
?>
<a class="kembali" href="<?= e($self) ?>"><?= ikon('back', 16) ?> Daftar pengguna</a>
<h1><?= $x ? 'Ubah pengguna' : 'Tambah pengguna' ?></h1>
<form method="post" class="panel form-lebar" novalidate>
  <?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="<?= (int)$id ?>">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="nama">Nama</label><input class="form-control" id="nama" name="nama" maxlength="80" value="<?= e($d['nama']) ?>" required></div>
    <div class="col-md-6"><label class="form-label" for="username">Username</label><input class="form-control" id="username" name="username" maxlength="40" value="<?= e($d['username']) ?>" autocomplete="off" required></div>
    <div class="col-md-6"><label class="form-label" for="sandi"><?= $x ? 'Kata sandi baru (kosongkan bila tidak diganti)' : 'Kata sandi' ?></label>
      <input class="form-control" id="sandi" name="sandi" type="password" autocomplete="new-password"<?= $x ? '' : ' required' ?> aria-describedby="b-sandi"><div id="b-sandi" class="form-text">Minimal 8 karakter, ada huruf dan angka.</div></div>
    <div class="col-md-3"><label class="form-label" for="peran">Peran</label><select class="form-select" id="peran" name="peran">
      <option value="panitia"<?= $d['peran'] === 'panitia' ? ' selected' : '' ?>>Panitia</option><option value="admin"<?= $d['peran'] === 'admin' ? ' selected' : '' ?>>Admin</option></select></div>
    <div class="col-md-3 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" id="aktif" name="aktif" value="1"<?= $d['aktif'] ? ' checked' : '' ?>><label class="form-check-label" for="aktif">Akun aktif</label></div></div>
  </div>
  <p class="catatan"><b>Admin</b> bisa semuanya. <b>Panitia</b> mengelola tim, pemain, jadwal, dan skor, tetapi tidak bisa mengoreksi skor terkunci, menghapus turnamen, atau mengelola pengguna.</p>
  <div class="aksi-form"><button class="btn tombol-utama" type="submit">Simpan</button><a class="btn tombol-garis" href="<?= e($self) ?>">Batal</a></div>
</form>
<?php
    tampil_footer();
    exit;
}

$daftar = ambil_semua('SELECT * FROM pengguna ORDER BY peran, nama');
tampil_header('Pengguna', ['area' => 'admin', 'nav' => 'pengguna']);
?>
<div class="baris-judul"><h1>Pengguna</h1><a class="btn tombol-utama" href="<?= e($self . '?aksi=tambah') ?>"><?= ikon('plus', 16) ?> Tambah pengguna</a></div>
<div class="table-responsive panel"><table class="table align-middle tabel-admin">
  <thead><tr><th scope="col">Nama</th><th scope="col">Username</th><th scope="col">Peran</th><th scope="col">Status</th><th scope="col"><span class="visually-hidden">Aksi</span></th></tr></thead><tbody>
<?php foreach ($daftar as $u): ?>
  <tr><td><b><?= e($u['nama']) ?></b><?= (int)$u['id'] === (int)$saya['id'] ? ' <span class="lencana lencana-sky">Kamu</span>' : '' ?></td>
    <td><?= e($u['username']) ?></td><td><?= e(ucfirst($u['peran'])) ?></td>
    <td><?= $u['aktif'] ? '<span class="lencana lencana-mint">Aktif</span>' : '<span class="lencana lencana-coral">Nonaktif</span>' ?></td>
    <td class="text-end aksi-baris">
      <a class="btn btn-sm tombol-garis" href="<?= e($self . '?aksi=ubah&id=' . (int)$u['id']) ?>"><?= ikon('edit', 14) ?> Ubah</a>
<?php if ((int)$u['id'] !== (int)$saya['id']): ?>
      <?= tombol_post($self, ['aksi' => 'hapus', 'id' => $u['id']], ikon('trash', 14) . ' Hapus', 'tombol-bahaya', 'Hapus pengguna "' . $u['username'] . '"?') ?>
<?php endif; ?>
    </td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php tampil_footer();
