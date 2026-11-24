<?php
/* Kerangka halaman: header, navigasi, footer, halaman galat, tab turnamen. */

function tampil_header(string $judul, array $o = []): void
{
    $area = $o['area'] ?? 'publik';
    $nav  = $o['nav'] ?? '';
    $nama = $GLOBALS['config']['app']['nama'];
    $p    = pengguna();
    $bootstrapAda = is_file(ROOT_DIR . '/assets/vendor/bootstrap.min.css');
    ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> · <?= e($nama) ?></title>
<meta name="theme-color" content="#2F6BFF">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%232F6BFF'/%3E%3Ccircle cx='16' cy='16' r='7' fill='none' stroke='%23FFE08A' stroke-width='3'/%3E%3C/svg%3E">
<link rel="stylesheet" href="<?= e(aset('vendor/bootstrap.min.css')) ?>">
<link rel="stylesheet" href="<?= e(aset('css/app.css')) ?>">
</head>
<body class="area-<?= e($area) ?>">
<a class="lompat" href="#isi">Lewati ke konten</a>
<header class="atas">
  <div class="wadah atas-dalam">
    <a class="merek" href="<?= e($area === 'admin' ? url('admin/index.php') : url('index.php')) ?>">
      <span class="merek-tanda" aria-hidden="true"></span>
      <span class="merek-nama"><?= e($nama) ?></span>
    </a>
    <nav class="nav-utama" aria-label="Navigasi utama">
<?php if ($area === 'admin' && $p): ?>
      <a href="<?= e(url('admin/index.php')) ?>"<?= $nav === 'ringkasan' ? ' aria-current="page"' : '' ?>>Ringkasan</a>
      <a href="<?= e(url('admin/turnamen.php')) ?>"<?= $nav === 'turnamen' ? ' aria-current="page"' : '' ?>>Turnamen</a>
<?php if (adalah_admin()): ?>
      <a href="<?= e(url('admin/pengguna.php')) ?>"<?= $nav === 'pengguna' ? ' aria-current="page"' : '' ?>>Pengguna</a>
      <a href="<?= e(url('admin/log.php')) ?>"<?= $nav === 'log' ? ' aria-current="page"' : '' ?>>Log</a>
<?php endif; ?>
      <a href="<?= e(url('index.php')) ?>">Lihat situs</a>
<?php else: ?>
      <a href="<?= e(url('index.php')) ?>"<?= $nav === 'beranda' ? ' aria-current="page"' : '' ?>>Beranda</a>
<?php if ($p): ?>
      <a href="<?= e(url('admin/index.php')) ?>">Panel</a>
<?php else: ?>
      <a href="<?= e(url('login.php')) ?>"<?= $nav === 'masuk' ? ' aria-current="page"' : '' ?>>Masuk</a>
<?php endif; ?>
<?php endif; ?>
    </nav>
<?php if ($p): ?>
    <form class="atas-pengguna" method="post" action="<?= e(url('logout.php')) ?>">
      <?= csrf_field() ?>
      <span class="atas-nama" title="<?= e($p['peran']) ?>"><?= e($p['nama']) ?> <small><?= e($p['peran']) ?></small></span>
      <button class="btn btn-sm tombol-garis" type="submit"><?= ikon('logout', 16) ?> Keluar</button>
    </form>
<?php endif; ?>
  </div>
</header>
<main id="isi" class="wadah utama">
<?php if (!$bootstrapAda): ?>
  <div class="alert alert-warning" role="alert">
    <b>Bootstrap belum terpasang.</b> Tampilan akan berantakan sampai file Bootstrap ada di <code>assets/vendor/</code>.
    Buka <a href="<?= e(url('siapkan-aset.php')) ?>">siapkan-aset.php</a> (butuh internet satu kali) atau ikuti <code>docs/03-memasang-bootstrap-offline.md</code>.
  </div>
<?php endif; ?>
<?php foreach (ambil_flash() as $f):
        $kelas = ['sukses' => 'success', 'galat' => 'danger', 'peringatan' => 'warning', 'info' => 'info'][$f['tipe']] ?? 'info'; ?>
  <div class="alert alert-<?= e($kelas) ?>" role="<?= $f['tipe'] === 'galat' ? 'alert' : 'status' ?>"><?= e($f['pesan']) ?></div>
<?php endforeach;
}

function tampil_footer(): void
{
    $c = $GLOBALS['config']['app'];
    ?>
</main>
<footer class="kaki">
  <div class="wadah kaki-dalam">
    <span><b><?= e($c['nama']) ?></b> v<?= e($c['versi']) ?> · aplikasi contoh UKK RPL</span>
    <span>PHP · MySQL · Bootstrap · berjalan penuh offline</span>
  </div>
</footer>
<?php if (is_file(ROOT_DIR . '/assets/vendor/bootstrap.bundle.min.js')): ?>
<script src="<?= e(aset('vendor/bootstrap.bundle.min.js')) ?>"></script>
<?php endif; ?>
<script src="<?= e(aset('js/app.js')) ?>"></script>
</body>
</html>
<?php
}

function galat_halaman(string $judul, string $pesan): void
{
    tampil_header($judul, ['area' => pengguna() ? 'admin' : 'publik']);
    echo '<div class="panel panel-tengah"><h1>' . e($judul) . '</h1><p>' . e($pesan) . '</p>';
    echo '<a class="btn tombol-utama" href="' . e(url('index.php')) . '">Kembali ke beranda</a></div>';
    tampil_footer();
    exit;
}

/** Tab navigasi dalam satu turnamen (panel admin). */
function tab_turnamen(int $id, string $aktif): string
{
    $tab = [
        'kelola' => ['Ringkasan', url('admin/turnamen.php') . '?aksi=kelola&id=' . $id, 'grid'],
        'tim'    => ['Tim & pemain', url('admin/tim.php') . '?turnamen=' . $id, 'users'],
        'laga'   => ['Laga & skor', url('admin/laga.php') . '?turnamen=' . $id, 'calendar'],
    ];
    $h = '<nav class="tab-turnamen" aria-label="Bagian turnamen">';
    foreach ($tab as $k => $t) {
        $h .= '<a href="' . e($t[1]) . '"' . ($k === $aktif ? ' aria-current="page"' : '') . '>' . ikon($t[2], 16) . ' ' . e($t[0]) . '</a>';
    }
    $h .= '<a href="' . e(url('turnamen.php') . '?id=' . $id) . '">' . ikon('eye', 16) . ' Tampilan publik</a>';
    return $h . '</nav>';
}

/** Formulir kecil berisi tombol POST + konfirmasi opsional. */
function tombol_post(string $aksiUrl, array $hidden, string $label, string $kelas = 'tombol-garis', string $konfirmasi = ''): string
{
    $h = '<form method="post" action="' . e($aksiUrl) . '" class="d-inline"' . ($konfirmasi !== '' ? ' data-konfirmasi="' . e($konfirmasi) . '"' : '') . '>' . csrf_field();
    foreach ($hidden as $k => $v) {
        $h .= '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">';
    }
    return $h . '<button type="submit" class="btn btn-sm ' . e($kelas) . '">' . $label . '</button></form>';
}
