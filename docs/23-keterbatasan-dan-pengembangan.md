# 23. Keterbatasan dan pengembangan lanjutan

## Keterbatasan saat ini

| Area | Batasan |
|---|---|
| Format | Hanya grup + gugur, liga, dan gugur tunggal. Belum ada Swiss, double elimination, atau best-of-N otomatis |
| Skor | Satu skor per laga. Belum ada skor per game dalam seri Bo3/Bo5 (skor seri bisa diisi sebagai 2–1) |
| Head-to-head | Mini-liga dihitung sekali, tidak rekursif untuk blok besar |
| Bagan | Penghindaran pertemuan satu grup bersifat heuristik |
| Pembatalan | Tidak ada "batalkan mulai turnamen"; solusinya hapus dan buat ulang |
| Notifikasi | Tidak ada pemberitahuan ke pemain |
| Bahasa | Hanya Bahasa Indonesia |
| Unggah | Belum ada logo tim atau foto pemain |
| Pemulihan sandi | Tanpa email; lewat admin atau database |
| Verifikasi | Kode belum dijalankan oleh penyusun (lihat dokumen 19) |

## Pengembangan yang masuk akal

### Mudah
- Ekspor klasemen ke CSV.
- Filter laga berdasarkan tanggal atau arena.
- Pilihan game dari daftar tetap.
- Halaman profil pengguna untuk ganti sandi sendiri.

### Sedang
- Skor per game untuk seri Bo3/Bo5, dengan pemenang dihitung otomatis.
- Logo tim (unggah gambar ke `assets/uploads/` dengan validasi tipe dan ukuran).
- Halaman layar besar (`tv.php`) untuk menampilkan klasemen di proyektor, dengan penyegaran otomatis.
- Dua bahasa.

### Lanjut
- Format Swiss dan double elimination.
- Statistik pemain (kill, assist) dan MVP.
- API JSON baca-saja untuk papan skor eksternal.
- Pembaruan langsung (Server-Sent Events) tanpa muat ulang.

## Prinsip saat mengembangkan

1. Tulis logika baru sebagai fungsi murni di `app/logic/` terlebih dahulu.
2. Tambahkan uji di `tests/uji-logika.php` dengan nilai yang dihitung manual.
3. Baru hubungkan ke database dan tampilan.
4. Setiap aksi yang mengubah data harus punya validasi, token CSRF, dan baris log.
