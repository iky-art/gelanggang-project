# 03. Memasang Bootstrap offline

Gelanggang memakai Bootstrap 5.3 yang **disimpan di dalam proyek**, bukan dari CDN. Dua file ini wajib ada di `assets/vendor/`:

- `bootstrap.min.css`
- `bootstrap.bundle.min.js`

Tanpa keduanya, halaman tetap terbuka tetapi tampilannya berantakan dan muncul peringatan kuning di bagian atas.

## Cara 1: otomatis (butuh internet satu kali)

1. Jalankan aplikasi (lihat dokumen 04).
2. Buka `http://localhost/gelanggang/siapkan-aset.php`.
3. Klik **Unduh & pasang Bootstrap**.
4. Setelah muncul tulisan terpasang, selesai. Halaman itu menonaktifkan dirinya sendiri dan boleh dihapus.

Server mengunduh dari `cdn.jsdelivr.net`, versi 5.3.3 yang dikunci. Ukuran file dicek agar tidak menyimpan halaman galat.

## Cara 2: manual (komputer lain yang online)

1. Buka `https://getbootstrap.com/docs/5.3/getting-started/download/`.
2. Pilih **Download** pada "Compiled CSS and JS". Hasilnya berkas ZIP.
3. Ekstrak. Salin dua file ini:
   - `css/bootstrap.min.css`
   - `js/bootstrap.bundle.min.js`
4. Tempel ke `gelanggang/assets/vendor/`.
5. Bawa seluruh folder `gelanggang` ke laptop sekolah (flashdisk).

## Cara 3: lewat npm (untuk yang terbiasa)

```
npm pack bootstrap@5.3.3
tar -xzf bootstrap-5.3.3.tgz
copy package/dist/css/bootstrap.min.css  gelanggang/assets/vendor/
copy package/dist/js/bootstrap.bundle.min.js  gelanggang/assets/vendor/
```

## Cara memeriksa

Folder `assets/vendor/` harus berisi dua file di atas (ukurannya sekitar 230 KB dan 80 KB). Setelah itu, peringatan kuning di atas halaman hilang.

## Mengapa tidak dibundel langsung?

Berkas Bootstrap tidak bisa diunduh di lingkungan tempat proyek ini disusun (tidak ada akses internet), dan menuliskannya ulang dari ingatan akan menghasilkan file yang salah. Karena itu pemasangnya disediakan sebagai langkah satu kali.

## Mengunci versi

Jika ingin versi lain, ubah konstanta `VERSI_BS` di `siapkan-aset.php`, atau pasang file manual. Tampilan sudah diuji rancangannya untuk Bootstrap 5.3.x.
