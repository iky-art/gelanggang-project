<?php
require __DIR__ . '/app/bootstrap.php';

if (adalah_post()) {
    cek_csrf();
    if (pengguna()) {
        keluar();
    }
    mulai_sesi();
    flash('sukses', 'Kamu sudah keluar.');
}
redirect(url('index.php'));
