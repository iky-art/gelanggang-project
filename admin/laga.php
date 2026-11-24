<?php
require __DIR__ . '/../app/bootstrap.php';
wajib_peran('admin', 'panitia');

$self = url('admin/laga.php');

/* ---------------- Aksi POST ---------------- */
if (adalah_post()) {
    cek_csrf();
    $aksi = $_POST['aksi'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $m = ambil_laga($id);
    if (!$m) {
        flash('galat', 'Laga tidak ditemukan.');
        redirect(url('admin/turnamen.php'));
    }
    $tid = (int)$m['turnamen_id'];

    if ($aksi === 'skor') {
        $koreksi = adalah_admin() && ($_POST['koreksi'] ?? '') === '1';
        $r = simpan_skor($id, $_POST['skor_a'] ?? '', $_POST['skor_b'] ?? '', $_POST['pemenang'] ?? 0, (int)($_POST['versi'] ?? -1), $koreksi, (string)($_POST['alasan'] ?? ''));
        flash($r['ok'] ? 'sukses' : 'galat', $r['pesan']);
        redirect($r['ok'] ? $self . '?turnamen=' . $tid . '&fase=' . $m['fase'] : $self . '?aksi=skor&id=' . $id);
    }

    if ($aksi === 'jadwal') {
        $t = ambil_turnamen($tid);
        $mentah = trim((string)($_POST['waktu'] ?? ''));
        $waktu = $mentah === '' ? null : waktu_valid($mentah);
        $lokasi = bersih($_POST['lokasi'] ?? '', 60);
        if ($mentah !== '' && $waktu === null) {
            flash('galat', 'Format waktu tidak valid.');
            redirect($self . '?aksi=jadwal&id=' . $id);
        }
        if ($waktu !== null) {
            $durasi = (int)$t['durasi_menit'];
            $lain = ambil_semua(
                'SELECT id, waktu, tim_a_id, tim_b_id, lokasi FROM pertandingan
                  WHERE id <> ? AND waktu IS NOT NULL AND waktu > DATE_SUB(?, INTERVAL ? MINUTE) AND waktu < DATE_ADD(?, INTERVAL ? MINUTE)',
                [$id, $waktu, $durasi, $waktu, $durasi]
            );
            $masalah = jadwal_bentrok($lain, ['id' => $id, 'waktu' => $waktu, 'tim_a_id' => $m['tim_a_id'], 'tim_b_id' => $m['tim_b_id'], 'lokasi' => $lokasi], $durasi);
            if ($masalah) {
                $nama = [];
                foreach (ambil_semua('SELECT id, nama FROM tim WHERE id IN (' . implode(',', array_map('intval', array_filter([$m['tim_a_id'], $m['tim_b_id']]) ?: [0])) . ')') as $x) {
                    $nama[(int)$x['id']] = $x['nama'];
                }
                foreach ($masalah as $b) {
                    flash('galat', $b['jenis'] === 'tim'
                        ? 'Bentrok: ' . ($nama[$b['tim_id']] ?? 'tim ini') . ' sudah punya laga #' . $b['laga_id'] . ' pada rentang waktu itu.'
                        : 'Bentrok: ' . $lokasi . ' sudah dipakai laga #' . $b['laga_id'] . ' pada rentang waktu itu.');
                }
                redirect($self . '?aksi=jadwal&id=' . $id);
            }
        }
        q('UPDATE pertandingan SET waktu = ?, lokasi = ?, versi = versi + 1 WHERE id = ?', [$waktu, $lokasi ?: null, $id]);
        catat('ubah_jadwal', "Laga #$id: " . ($waktu ?? 'tanpa waktu') . ($lokasi ? " @ $lokasi" : ''));
        flash('sukses', 'Jadwal laga tersimpan.');
        redirect($self . '?turnamen=' . $tid . '&fase=' . $m['fase']);
    }
    redirect($self . '?turnamen=' . $tid);
}

/* ---------------- Formulir skor ---------------- */
$aksi = $_GET['aksi'] ?? 'daftar';
if ($aksi === 'skor' || $aksi === 'jadwal') {
    $m = ambil_laga((int)($_GET['id'] ?? 0));
    if (!$m) { flash('galat', 'Laga tidak ditemukan.'); redirect(url('admin/turnamen.php')); }
    $t = ambil_turnamen((int)$m['turnamen_id']);
    $kembali = $self . '?turnamen=' . (int)$m['turnamen_id'] . '&fase=' . $m['fase'];
    tampil_header($aksi === 'skor' ? 'Isi skor' : 'Atur jadwal', ['area' => 'admin', 'nav' => 'turnamen']);
    echo '<a class="kembali" href="' . e($kembali) . '">' . ikon('back', 16) . ' Daftar laga</a>';
    echo '<h1>' . ($aksi === 'skor' ? 'Isi skor' : 'Atur jadwal') . '</h1>';
    echo baris_laga($m, ['turnamen' => true]);

    if ($aksi === 'skor'):
        $siap = !empty($m['tim_a_id']) && !empty($m['tim_b_id']) && !(int)$m['bye'];
        $selesai = $m['status'] === 'selesai';
        $terkunci = $selesai && (int)$m['dikunci'];
        if ($t['status'] === 'draft') { $siap = false; }
        if (!$siap): ?>
<div class="alert alert-warning mt-3">Skor belum bisa diisi: kedua tim belum ditentukan, laga ini bye, atau turnamen belum dimulai.</div>
<?php   elseif ($terkunci && !adalah_admin()): ?>
<div class="alert alert-info mt-3"><?= ikon('lock', 16) ?> Skor sudah dikunci. Hanya admin yang bisa mengoreksi dengan alasan yang tercatat.</div>
<?php   else: ?>
<form method="post" class="panel form-lebar mt-3" novalidate>
  <?= csrf_field() ?><input type="hidden" name="aksi" value="skor"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
  <input type="hidden" name="versi" value="<?= (int)$m['versi'] ?>">
<?php if ($terkunci): ?><input type="hidden" name="koreksi" value="1"><div class="alert alert-warning"><?= ikon('alert', 16) ?> Mode koreksi: perubahan dicatat di log beserta alasannya.</div><?php endif; ?>
  <div class="papan-input">
    <div><label class="form-label" for="skor_a"><?= e($m['nama_a']) ?></label>
      <input class="form-control input-skor" id="skor_a" name="skor_a" type="number" inputmode="numeric" min="0" max="99" value="<?= $selesai ? (int)$m['skor_a'] : '' ?>" required autofocus></div>
    <span class="pisah" aria-hidden="true">:</span>
    <div><label class="form-label" for="skor_b"><?= e($m['nama_b']) ?></label>
      <input class="form-control input-skor" id="skor_b" name="skor_b" type="number" inputmode="numeric" min="0" max="99" value="<?= $selesai ? (int)$m['skor_b'] : '' ?>" required></div>
  </div>
<?php if ($m['fase'] === 'gugur'): ?>
  <fieldset class="mt-3"><legend class="form-label">Jika skor seri, siapa pemenangnya? (overtime/tiebreak)</legend>
    <div class="form-check"><input class="form-check-input" type="radio" name="pemenang" id="pm_a" value="<?= (int)$m['tim_a_id'] ?>"<?= (int)$m['pemenang_id'] === (int)$m['tim_a_id'] && $selesai ? ' checked' : '' ?>><label class="form-check-label" for="pm_a"><?= e($m['nama_a']) ?></label></div>
    <div class="form-check"><input class="form-check-input" type="radio" name="pemenang" id="pm_b" value="<?= (int)$m['tim_b_id'] ?>"<?= (int)$m['pemenang_id'] === (int)$m['tim_b_id'] && $selesai ? ' checked' : '' ?>><label class="form-check-label" for="pm_b"><?= e($m['nama_b']) ?></label></div>
  </fieldset>
<?php elseif (!(int)$t['izinkan_seri']): ?>
  <p class="catatan">Turnamen ini tidak mengizinkan skor seri di babak grup.</p>
<?php endif; ?>
<?php if ($terkunci): ?>
  <div class="mt-3"><label class="form-label" for="alasan">Alasan koreksi</label><textarea class="form-control" id="alasan" name="alasan" rows="2" maxlength="300" required></textarea></div>
<?php endif; ?>
  <div class="aksi-form"><button class="btn tombol-utama" type="submit"><?= ikon('check', 16) ?> <?= $terkunci ? 'Simpan koreksi' : 'Simpan & kunci skor' ?></button>
    <a class="btn tombol-garis" href="<?= e($kembali) ?>">Batal</a></div>
</form>
<?php   endif;
    else: /* jadwal */ ?>
<form method="post" class="panel form-lebar mt-3" novalidate>
  <?= csrf_field() ?><input type="hidden" name="aksi" value="jadwal"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
  <div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="waktu">Waktu</label><input class="form-control" id="waktu" name="waktu" type="datetime-local" value="<?= e(untuk_input_waktu($m['waktu'])) ?>"></div>
    <div class="col-md-6"><label class="form-label" for="lokasi">Arena / lokasi</label><input class="form-control" id="lokasi" name="lokasi" maxlength="60" value="<?= e($m['lokasi']) ?>" placeholder="mis. Arena 1"></div>
  </div>
  <p class="catatan">Sistem menolak jadwal bila tim yang sama atau arena yang sama sudah dipakai laga lain dalam rentang <?= (int)$t['durasi_menit'] ?> menit.</p>
  <div class="aksi-form"><button class="btn tombol-utama" type="submit">Simpan jadwal</button><a class="btn tombol-garis" href="<?= e($kembali) ?>">Batal</a></div>
</form>
<?php endif;
    tampil_footer();
    exit;
}

/* ---------------- Daftar laga ---------------- */
$tid = (int)($_GET['turnamen'] ?? 0);
$t = ambil_turnamen($tid);
if (!$t) {
    flash('galat', 'Pilih turnamen terlebih dahulu.');
    redirect(url('admin/turnamen.php'));
}
$fase = $_GET['fase'] ?? '';
if (!in_array($fase, ['grup', 'gugur'], true)) {
    if ($t['format'] === 'gugur') {
        $fase = 'gugur';
    } elseif ($t['format'] === 'liga') {
        $fase = 'grup';
    } else {
        $fase = in_array($t['status'], ['gugur', 'selesai'], true) ? 'gugur' : 'grup';
    }
}
$laga = laga_turnamen($tid, $fase);

tampil_header('Laga · ' . $t['nama'], ['area' => 'admin', 'nav' => 'turnamen']);
?>
<a class="kembali" href="<?= e(url('admin/turnamen.php') . '?aksi=kelola&id=' . $tid) ?>"><?= ikon('back', 16) ?> <?= e($t['nama']) ?></a>
<div class="baris-judul"><h1>Laga &amp; skor</h1></div>
<?= tab_turnamen($tid, 'laga') ?>
<?php if ($t['format'] === 'liga_gugur'): ?>
<nav class="tab-turnamen" aria-label="Fase">
  <a href="<?= e($self . '?turnamen=' . $tid . '&fase=grup') ?>"<?= $fase === 'grup' ? ' aria-current="page"' : '' ?>>Babak grup</a>
  <a href="<?= e($self . '?turnamen=' . $tid . '&fase=gugur') ?>"<?= $fase === 'gugur' ? ' aria-current="page"' : '' ?>>Babak gugur</a>
</nav>
<?php endif; ?>
<?php if ($t['status'] === 'draft'): ?>
  <?= kartu_kosong('Turnamen belum dimulai', 'Jadwal dibuat otomatis saat turnamen dimulai dari halaman Ringkasan.') ?>
<?php elseif (!$laga): ?>
  <?= kartu_kosong('Belum ada laga di fase ini', $fase === 'gugur' ? 'Babak gugur dibuat setelah semua laga grup selesai.' : 'Tidak ada laga grup.') ?>
<?php else: foreach ($laga as $m):
    $bisa = !empty($m['tim_a_id']) && !empty($m['tim_b_id']) && !(int)$m['bye'];
    $aksiHtml = '';
    if ($bisa) {
        $terkunci = $m['status'] === 'selesai' && (int)$m['dikunci'];
        $labelSkor = $m['status'] !== 'selesai' ? 'Isi skor' : (adalah_admin() ? 'Koreksi' : 'Terkunci');
        $aksiHtml .= ($terkunci && !adalah_admin())
            ? '<span class="teks-redup">' . ikon('lock', 14) . ' Terkunci</span> '
            : '<a class="btn btn-sm tombol-utama" href="' . e($self . '?aksi=skor&id=' . (int)$m['id']) . '">' . e($labelSkor) . '</a> ';
        $aksiHtml .= '<a class="btn btn-sm tombol-garis" href="' . e($self . '?aksi=jadwal&id=' . (int)$m['id']) . '">' . ikon('clock', 14) . ' Jadwal</a>';
    }
    echo baris_laga($m, ['aksi' => $aksiHtml]);
endforeach; endif;
tampil_footer();
