# Video demo Gelanggang (Remotion)

Video ±42 detik, 1920×1080, 30 fps, dibuat **dengan kode** memakai [Remotion](https://www.remotion.dev/) (React). Tidak ada rekaman layar dan tidak ada aset gambar: semuanya digambar dari komponen React dengan warna yang sama seperti aplikasi.

## Isi video

| Adegan | Durasi | Isi |
|---|---|---|
| Judul | 3,5 dtk | Nama, teknologi |
| Masalah | 5 dtk | Empat masalah turnamen manual |
| Fitur | 4,5 dtk | Enam fitur utama |
| Alur | 4,5 dtk | Dari buat turnamen sampai juara |
| Klasemen | 6 dtk | Contoh tie-break head-to-head |
| Bagan | 6 dtk | Seed standar, pemenang maju sendiri |
| Skor aman | 5 dtk | Satu simpan, satu transaksi |
| Keamanan | 4 dtk | Kontrol keamanan dan offline |
| Penutup | 4 dtk | Tautan repo |

Data pada adegan klasemen adalah contoh yang sama dengan skenario uji di `tests/uji-logika.php`. Bagan adalah ilustrasi 8 tim, bukan hasil turnamen nyata (tertulis di layar).

## Cara 1: otomatis lewat GitHub Actions (disarankan, termasuk dari Termux)

1. Push repo ke GitHub.
2. Buka tab **Actions** → **Render video demo** → **Run workflow**.
3. Setelah selesai, file `video/demo.mp4` muncul di repo. Tautan di README langsung berfungsi.

Workflow ini juga jalan otomatis setiap `video/src/**` berubah.

## Cara 2: render sendiri di komputer (Windows/macOS/Linux)

Butuh Node.js 18+ dan internet (untuk mengunduh paket dan Chromium milik Remotion).

```
cd video
npm install
npm run start     # pratinjau interaktif (Remotion Studio)
npm run build     # hasil: video/demo.mp4
npm run poster    # gambar sampul: video/poster.png
```

## Tidak bisa dirender di Termux

Remotion merender lewat Chromium headless, yang tidak tersedia di Termux/Android. Gunakan Cara 1 (GitHub Actions) atau komputer.

## Mengubah isi

- Teks, durasi, dan data: `src/Demo.tsx` (objek `DURASI` mengatur panjang tiap adegan).
- Warna: `src/theme.ts`.
- Ukuran dan fps: `src/Root.tsx`.

## Catatan versi

`package.json` memakai `^4.0.0` untuk `remotion` dan `@remotion/cli`. Remotion mewajibkan versi kedua paket itu sama. Jika muncul peringatan versi berbeda, jalankan `npm run upgrade`.

## Status verifikasi

Kode TypeScript diperiksa tipenya dengan deklarasi pengganti (stub) untuk `react` dan `remotion`. **Belum pernah dirender** karena lingkungan penyusun tidak punya akses internet untuk memasang Remotion. Jalankan sekali dan periksa hasilnya; bila ada galat, perbaiki sebelum dipakai di README.

## Lisensi

Remotion punya ketentuan lisensi sendiri (gratis untuk individu dan tim kecil). Periksa di https://www.remotion.dev/license sebelum memakai untuk keperluan komersial.
