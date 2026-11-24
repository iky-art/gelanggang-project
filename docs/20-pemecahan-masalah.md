# 20. Pemecahan masalah

## Halaman "Database belum siap"
Aplikasi gagal terhubung ke MySQL.

1. XAMPP: MySQL berstatus hijau?
2. Database `gelanggang` ada di phpMyAdmin? Bila tidak, impor `database/gelanggang.sql`.
3. `app/config.php`: user dan kata sandi sesuai?
4. Termux/Docker: host harus `127.0.0.1` atau `db`, bukan `localhost` (lihat dokumen 05 dan 06).
5. Buka "Pesan teknis" pada halaman itu untuk kode galat sebenarnya.

## Tampilan berantakan, ada peringatan kuning
File Bootstrap belum ada. Lihat dokumen 03.

## Layar putih kosong
Ada galat PHP yang disembunyikan. Aktifkan `display_errors` di `php.ini`, restart Apache, muat ulang halaman. Atau periksa `C:\xampp\apache\logs\error.log`.

## Galat "Class PDO not found" atau "could not find driver"
Ekstensi `pdo_mysql` belum aktif. Di `php.ini`, hilangkan tanda `;` pada `extension=pdo_mysql`, lalu restart Apache.

## Setelah impor SQL, tabel tidak muncul
- Pastikan file yang diimpor `gelanggang.sql`, bukan `data_contoh.sql`.
- Berkas besar? Naikkan `upload_max_filesize` di `php.ini` (berkas ini kecil, jarang jadi masalah).

## Impor `data_contoh.sql` gagal
Impor `gelanggang.sql` dulu. Berkas contoh membutuhkan tabel itu.

## "Formulir kedaluwarsa" (419)
Sesi habis atau cookie diblokir. Muat ulang halaman, lalu kirim lagi. Bila terjadi terus, pastikan browser menerima cookie dari `localhost`.

## Tidak bisa login padahal sandi benar
- Sudah 5 kali gagal? Tunggu 15 menit atau kosongkan tabel `percobaan_login` lewat phpMyAdmin.
- Akun nonaktif? Aktifkan lewat admin lain, atau ubah kolom `aktif` menjadi 1 di phpMyAdmin.
- Lupa sandi satu-satunya admin: lihat bagian berikut.

## Lupa kata sandi admin
Tanpa email, pemulihan dilakukan lewat database:

1. Kosongkan tabel `pengguna` di phpMyAdmin (Operasi → Kosongkan).
2. Buka `/setup.php` dan buat admin baru.

Tabel turnamen, tim, dan laga tidak ikut terhapus. Log lama tetap ada tetapi tidak terhubung ke akun baru.

## "Tim tidak bisa ditambah"
Turnamen sudah dimulai. Buat turnamen baru, atau hapus turnamen (admin) bila memang salah mulai.

## "Peringkat seri total, isi nilai undian"
Normal. Buka Tim, ubah salah satu tim yang seri, isi nilai undian lebih tinggi (misal 1), simpan.

## Jam laga berbeda beberapa jam
Zona waktu server diatur `Asia/Jakarta` di `app/config.php`, dan koneksi database memakai `+07:00`. Bila lokasimu WITA atau WIT, ubah keduanya (zona di `config.php`, offset di `app/db.php`).

## Folder `app/` bisa dibuka lewat URL (server PHP bawaan)
Server `php -S` tidak membaca `.htaccess`. Jangan bukakan ke jaringan umum. Di Apache/XAMPP tidak terjadi.

## Port 80 bentrok
Ganti port Apache (dokumen 04) atau tutup program yang memakai port 80.

## Docker: "port is already allocated"
Ubah `8080:80` atau `8081:80` di `docker-compose.yml` ke port lain.

## Docker: aplikasi tidak bisa terhubung ke database
Tunggu healthcheck MySQL selesai. Lihat `docker compose logs db`. Bila skema tidak termuat, jalankan `docker compose down -v` lalu `up -d` lagi.
