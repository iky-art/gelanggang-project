# 02. Persyaratan sistem

## Perangkat lunak

| Komponen | Minimum | Disarankan |
|---|---|---|
| PHP | 7.4 | 8.1 atau lebih baru |
| MySQL / MariaDB | MySQL 5.7 / MariaDB 10.3 | MySQL 8 / MariaDB 10.6+ |
| Web server | Apache (XAMPP) atau server bawaan PHP | Apache |
| Browser | Chrome, Edge, Firefox, atau Safari versi 2 tahun terakhir | Terbaru |

## Ekstensi PHP

- **pdo_mysql** (wajib). Aktif bawaan di XAMPP.
- **mbstring** (disarankan). Kode punya cadangan bila tidak ada.
- **curl** (opsional). Hanya dipakai `siapkan-aset.php` untuk mengunduh Bootstrap.
- **session** (wajib, bawaan).

## Cek cepat di XAMPP

Buka `http://localhost/dashboard/phpinfo.php` lalu cari `PDO`, `pdo_mysql`, dan `mbstring`.

## Perangkat keras

Sangat ringan. Laptop sekolah biasa sudah cukup. Aplikasi ini tidak memakai proses latar belakang atau layanan tambahan.

## Browser dan fitur yang dipakai

- `<input type="datetime-local">` untuk memilih waktu (didukung semua browser modern).
- `<dialog>` untuk jendela konfirmasi. Browser lama otomatis memakai `confirm()` biasa.
- CSS Grid dan Flexbox untuk tata letak.

## Jaringan

- Saat dipakai: **tidak perlu internet.**
- Saat pemasangan awal: perlu internet **satu kali** hanya untuk mengunduh dua file Bootstrap (lihat dokumen 03). Unduhan itu bisa dilakukan di komputer lain.
