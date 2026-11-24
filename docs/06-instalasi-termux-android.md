# 06. Instalasi di Termux (Android)

Berguna untuk berlatih atau demo dari ponsel. Di Termux, "MySQL" adalah **MariaDB** yang kompatibel penuh dengan skema Gelanggang.

## 1. Pasang paket

```
pkg update
pkg install php mariadb
```

## 2. Siapkan database

```
mysql_install_db
mysqld_safe --datadir=$PREFIX/var/lib/mysql &
```

Tunggu beberapa detik, lalu:

```
mysql -u root < ~/gelanggang/database/gelanggang.sql
```

Opsional: `mysql -u root < ~/gelanggang/database/data_contoh.sql`.

Jika perintah `mysql` meminta sandi, tekan Enter (root Termux awalnya tanpa sandi).

## 3. Jalankan server PHP

Di Termux, koneksi lewat nama `localhost` memakai socket yang sering tidak ditemukan PHP, jadi paksa TCP dengan variabel lingkungan:

```
cd ~/gelanggang
DB_HOST=127.0.0.1 php -S 0.0.0.0:8080
```

Buka `http://localhost:8080` di browser ponsel, atau `http://IP-PONSEL:8080` dari perangkat lain di Wi-Fi yang sama.

## 4. Bootstrap

Salin dua file Bootstrap ke `assets/vendor/` (dokumen 03). Di Termux, `siapkan-aset.php` juga bisa dipakai bila ada internet.

## Catatan penting

- Server bawaan PHP **tidak membaca `.htaccess`**. Folder `app/`, `database/` dan `docker/` bisa dibuka lewat URL. Isinya tidak berbahaya (kode PHP tidak menampilkan apa pun, dan `.sql` tidak berisi data pribadi), tetapi jangan membuka port ini ke jaringan umum.
- Hentikan server dengan `Ctrl + C`. Hentikan database dengan `mysqladmin -u root shutdown`.
- Jam di Termux mengikuti ponsel. Zona waktu aplikasi diatur ke Asia/Jakarta di `app/config.php`.

## Jalankan ulang tiap sesi

```
mysqld_safe --datadir=$PREFIX/var/lib/mysql &
cd ~/gelanggang && DB_HOST=127.0.0.1 php -S 0.0.0.0:8080
```
