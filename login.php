<?php
require __DIR__ . '/app/bootstrap.php';

if (pengguna()) {
    redirect(url('admin/index.php'));
}
if ((int)ambil_nilai('SELECT COUNT(*) FROM pengguna') === 0) {
    redirect(url('setup.php'));
}

$galat = '';
if (adalah_post()) {
    cek_csrf();
    $r = coba_masuk((string)($_POST['username'] ?? ''), (string)($_POST['sandi'] ?? ''));
    if ($r['ok']) {
        redirect(aman_kembali($_GET['kembali'] ?? ''));
    }
    $galat = $r['pesan'];
}

tampil_header('Masuk', ['nav' => 'masuk']);
?>
<div class="panel panel-sempit">
  <h1>Masuk panel</h1>
  <p class="teks-redup">Khusus admin dan panitia. Penonton tidak perlu masuk untuk melihat klasemen.</p>
<?php if ($galat): ?><div class="alert alert-danger" role="alert"><?= e($galat) ?></div><?php endif; ?>
  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label" for="username">Username</label>
      <input class="form-control" id="username" name="username" value="<?= e(lama('username')) ?>" autocomplete="username" autofocus required>
    </div>
    <div class="mb-3">
      <label class="form-label" for="sandi">Kata sandi</label>
      <input class="form-control" id="sandi" name="sandi" type="password" autocomplete="current-password" required>
    </div>
    <button class="btn tombol-utama w-100" type="submit">Masuk</button>
  </form>
</div>
<?php tampil_footer();
