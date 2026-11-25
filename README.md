# Gelanggang

Aplikasi manajemen turnamen e-sport sekolah: tim, pemain, jadwal, skor, klasemen, dan bagan gugur otomatis. Dibuat dengan **PHP polos + MySQL (phpMyAdmin) + Bootstrap 5.3**, berjalan **penuh offline** di localhost, dan disiapkan sebagai contoh proyek UKK RPL.

## Video demo

[▶ Tonton video demo](https://github.com/user-attachments/assets/188923b1-3962-4846-8a57-a8c5eafc7dc6)

Video ±42 detik ini dibuat **dengan kode** memakai [Remotion](https://www.remotion.dev/) (folder `video/`): masalah turnamen manual, fitur, alur, klasemen dengan head-to-head, bagan gugur, input skor yang aman, dan keamanan. Isinya data contoh, bukan hasil turnamen nyata.

- File `video/demo.mp4` dibuat otomatis oleh GitHub Actions (`.github/workflows/render-video.yml`). Jalankan manual dari tab **Actions → Render video demo → Run workflow**. Sebelum workflow pertama selesai, tautan di atas belum aktif.
- Merender sendiri atau mengubah isi video: lihat [`video/README.md`](video/README.md).
- Ingin video tampil langsung di halaman README? Di GitHub, klik ikon pensil pada README lalu seret `demo.mp4` ke editor; GitHub membuat pemutar bawaan.

## Mulai cepat (XAMPP, tanpa terminal)

1. Salin folder ini ke `C:\xampp\htdocs\gelanggang`.
2. Start **Apache** dan **MySQL** di XAMPP.
3. Buka `localhost/phpmyadmin` → **Impor** → `database/gelanggang.sql` (opsional: `database/data_contoh.sql`).
4. Pasang Bootstrap: taruh `bootstrap.min.css` dan `bootstrap.bundle.min.js` (v5.3.3) di `assets/vendor/`, atau buka `localhost/gelanggang/siapkan-aset.php` saat ada internet (sekali saja).
5. Buka `localhost/gelanggang/` → **Masuk** → buat akun admin pertama.

Jalur lain: Docker (`docker/`), Termux (`docs/06-instalasi-termux-android.md`).

## Isi proyek

| Folder | Isi |
|---|---|
| `admin/`, `*.php` | Halaman aplikasi |
| `app/` | Konfigurasi, model, tampilan, dan `logic/` (hitungan murni) |
| `assets/` | CSS, JS, dan `vendor/` untuk Bootstrap |
| `database/` | Skema dan data contoh |
| `docker/` | Dockerfile dan docker-compose |
| `tests/` | Uji logika inti (`tests/uji-logika.php`) |
| `video/` | Video demo dibuat dengan kode (Remotion) |
| `docs/` | Dokumentasi lengkap (25 berkas) |
| `presentasi/` | Bahan presentasi UKK (25 berkas) |

## Fitur

- Format **grup + gugur**, **liga**, dan **gugur**
- CRUD turnamen, tim, pemain, pengguna
- Jadwal liga otomatis (metode lingkaran), penyusunan waktu dan arena, deteksi bentrok
- Klasemen dengan tie-break: poin → selisih → head-to-head → skor memasukkan → undian
- Bagan gugur dengan bye, seed dari grup, dan perebutan juara 3
- Input skor dalam transaksi, penguncian, koreksi admin beralasan, log aktivitas
- Peran admin dan panitia; halaman publik tanpa login

## Status verifikasi (baca sebelum memakai)

- Algoritma klasemen, jadwal liga, seed bagan, dan penyusunan waktu sudah diperiksa lewat prototipe terpisah; nilai harapan uji berasal dari sana.
- **Kode PHP, SQL, dan tampilan belum dijalankan** oleh penyusun (lingkungan penyusun tidak punya PHP/MySQL/internet). Jalankan `tests/uji-logika.php` dan daftar uji di `docs/19-pengujian.md` sebelum dipakai, lalu perbaiki bila ada galat.
- **Bootstrap tidak ikut terbundel** karena tidak bisa diunduh di lingkungan penyusun. Lihat `docs/03-memasang-bootstrap-offline.md`.

## Lisensi

MIT. Lihat `LICENSE`.
