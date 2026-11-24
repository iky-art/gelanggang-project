# 05. Instalasi lewat Docker

Jalur ini untuk yang terbiasa dengan terminal. Hasilnya tiga layanan: aplikasi PHP-Apache, MySQL 8, dan phpMyAdmin.

## Prasyarat

- Docker Desktop (Windows/macOS) atau Docker Engine + Compose plugin (Linux).

## Menjalankan

```
cd gelanggang/docker
docker compose up -d --build
```

Tunggu sekitar satu menit pada pertama kali (MySQL menyiapkan data awal). Lalu buka:

| Layanan | Alamat |
|---|---|
| Aplikasi | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 (user `root`, sandi `gelanggang_lokal`) |

Skema `gelanggang.sql` dimuat otomatis saat volume database masih kosong. Akun admin dibuat lewat halaman setup, seperti pada XAMPP.

## Data contoh

Buka `docker/docker-compose.yml`, hapus tanda `#` pada baris `02-data-contoh.sql`, lalu hapus volume agar skema dimuat ulang:

```
docker compose down -v
docker compose up -d
```

## Mengubah kata sandi database

Ganti `gelanggang_lokal` di tiga tempat pada `docker-compose.yml`: `DB_PASS`, `MYSQL_ROOT_PASSWORD`, dan `-pgelanggang_lokal` pada healthcheck. Karena ini aplikasi latihan lokal, sandi bawaan boleh dipakai selama port database tidak dibuka ke jaringan (memang tidak dibuka).

## Menghentikan

```
docker compose down        # data tetap tersimpan
docker compose down -v     # hapus juga data database
```

## Memakai Docker tanpa internet

Image hanya perlu diunduh sekali di komputer yang online:

```
docker pull php:8.3-apache
docker pull mysql:8.0
docker pull phpmyadmin:5
docker save -o gelanggang-images.tar php:8.3-apache mysql:8.0 phpmyadmin:5
```

Di komputer offline:

```
docker load -i gelanggang-images.tar
docker compose up -d --build
```

Langkah `build` hanya memasang ekstensi `pdo_mysql` dari sumber yang sudah ada di image PHP, sehingga seharusnya tidak butuh internet. Alur ini belum diuji di lingkungan tanpa internet; uji sekali sebelum hari ujian.

## Struktur kontainer

- `web`: membaca kode dari folder proyek (bind mount), jadi perubahan file langsung terlihat.
- `db`: data disimpan di volume `dbdata`.
- Aplikasi membaca konfigurasi database dari variabel lingkungan `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` (lihat `app/config.php`).
