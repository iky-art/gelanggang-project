<?php
/**
 * Pintu masuk bersama: dimuat oleh setiap halaman.
 * Urutan: konfigurasi -> fungsi bantu -> logika murni -> database -> sesi.
 */
define('APP_DIR', __DIR__);
define('ROOT_DIR', dirname(__DIR__));

$GLOBALS['config'] = require APP_DIR . '/config.php';
date_default_timezone_set($GLOBALS['config']['app']['zona_waktu']);

require APP_DIR . '/helpers.php';
require APP_DIR . '/logic/validasi.php';
require APP_DIR . '/logic/klasemen.php';
require APP_DIR . '/logic/jadwal.php';
require APP_DIR . '/logic/bagan.php';
require APP_DIR . '/db.php';
require APP_DIR . '/auth.php';
require APP_DIR . '/view.php';
require APP_DIR . '/layout.php';
require APP_DIR . '/model.php';

mulai_sesi();
kirim_header_keamanan();
