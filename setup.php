<?php
require __DIR__ . '/app/bootstrap.php';

if ((int)ambil_nilai('SELECT COUNT(*) FROM pengguna') > 0) {
    redirect(url('login.php'));
}

$galat = [];
if (adalah_post()) {
    cek_csrf();
    $nama = bersih($_POST['nama'] ?? '', 80);
    $username = bersih($_POST['username'] ?? '', 40);
    $sandi = (string)($_POST['sandi'] ?? '');
    $ulang = (string)($_POST['ulang'] ?? '');

    if (panjang($nama) < 2) { $galat[] = 'Nama minimal 2 karakter.'; }
    if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $username)) { $galat[] = 'Username 3-40 karakter: huruf, angka, titik, garis bawah, atau strip.'; }
    if ($m = cek_kata_sandi($sandi)) { $galat[] = $m; }
    if ($sandi !== $ulang) { $galat[] = 'Pengulangan kata sandi tidak sama.'; }

    if (!$galat) {
        q("INSERT INTO pengguna (nama, username, kata_sandi, peran) VALUES (?,?,?, 'admin')", [$nama, $username, password_hash($sandi, PASSWORD_DEFAULT)]);
        catat('setup_admin', "Admin pertama dibuat: $username");
        flash('sukses', 'Akun admin dibuat. Silakan masuk.');
        redirect(url('login.php'));
    }
}

tampil_header('Setup awal', ['nav' => 'masuk']);
?>
<div class="panel panel-sempit">
  <h1>Setup awal</h1>
  <p class="teks-redup">Belum ada akun. Buat akun admin pertama. Tidak ada kata sandi bawaan, jadi tidak ada yang bisa menebaknya.</p>
<?php foreach ($galat as $g): ?><div class="alert alert-danger" role="alert"><?= e($g) ?></div><?php endforeach; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label" for="nama">Nama lengkap</label>
      <input class="form-control" id="nama" name="nama" value="<?= e(lama('nama')) ?>" required></div>
    <div class="mb-3"><label class="form-label" for="username">Username</label>
      <input class="form-control" id="username" name="username" value="<?= e(lama('username')) ?>" autocomplete="username" required></div>
    <div class="mb-3"><label class="form-label" for="sandi">Kata sandi</label>
      <input class="form-control" id="sandi" name="sandi" type="password" autocomplete="new-password" aria-describedby="b-sandi" required>
      <div id="b-sandi" class="form-text">Minimal 8 karakter, ada huruf dan angka.</div></div>
    <div class="mb-3"><label class="form-label" for="ulang">Ulangi kata sandi</label>
      <input class="form-control" id="ulang" name="ulang" type="password" autocomplete="new-password" required></div>
    <button class="btn tombol-utama w-100" type="submit">Buat akun admin</button>
  </form>
</div>
<?php tampil_footer();
