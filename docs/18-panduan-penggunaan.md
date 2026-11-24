# 18. Panduan penggunaan: dari nol sampai juara

Skenario: turnamen "Contoh Cup", 8 tim, 2 grup, 2 lolos per grup, dengan juara 3. Kamu bisa memakai `database/data_contoh.sql` agar tim dan pemain sudah terisi, lalu mulai dari langkah 4.

## 1. Masuk
Buka `/login.php`. Pertama kali kamu dibawa ke setup untuk membuat admin.

## 2. Buat turnamen
Menu **Turnamen → Turnamen baru**.

- Nama: `Contoh Cup`, game: `Mobile Legends`.
- Format: **Fase grup lalu gugur**, jumlah grup 2, lolos per grup 2.
- Poin 3/1/0. Durasi laga 45 menit.
- Centang **Buat perebutan juara 3**.

## 3. Daftarkan tim dan pemain
Dari halaman Kelola, buka **Tim & pemain → Tambah tim**. Isi nama, warna, dan seed (1 = terkuat). Tambahkan pemain lewat tombol **Pemain** di baris tim.

## 4. Mulai turnamen
Di **Ringkasan** turnamen, pilih pembagian grup (seed atau acak), klik **Mulai turnamen**, lalu konfirmasi. Sistem membagi grup dan membuat 12 laga grup (2 grup × 6 laga).

## 5. Susun jadwal
Di kotak **Susun jadwal otomatis**: fase babak grup, isi waktu mulai, slot 55 menit, jumlah arena 2. Klik **Susun jadwal**.

## 6. Isi skor babak grup
Menu **Laga & skor → Babak grup**. Klik **Isi skor** pada laga, isi angka, lalu **Simpan & kunci skor**. Klasemen halaman publik langsung berubah.

Jika salah input, admin memakai **Koreksi** dan menulis alasan.

## 7. Mulai babak gugur
Saat semua laga grup selesai, halaman Kelola menampilkan tombol **Mulai babak gugur**. Jika ada seri total di batas lolos, sistem menolak dan meminta nilai undian: buka **Tim**, ubah salah satu tim, isi nilai undian lebih tinggi, simpan, lalu coba lagi.

## 8. Susun jadwal babak gugur
Ulangi langkah 5 dengan fase **Babak gugur**.

## 9. Isi skor sampai final
Isi skor semifinal. Pemenang otomatis masuk final. Jika skor seri, pilih pemenang pada kolom overtime. Isi juga laga juara 3.

## 10. Juara
Setelah final dan juara 3 selesai, status menjadi **Selesai** dan banner juara muncul di halaman publik.

## 11. Lihat sebagai penonton
Buka tab **Tampilan publik** atau halaman `index.php`. Penonton melihat klasemen, bagan, jadwal, dan daftar tim tanpa login.

## Tips

- Cetak klasemen: tombol cetak browser (`Ctrl+P`). Navigasi dan tombol otomatis disembunyikan saat cetak.
- Salah mulai turnamen sebelum waktunya? Hapus turnamen (admin) dan buat ulang, atau gunakan turnamen contoh lain. Aplikasi sengaja tidak punya "batalkan mulai" agar hasil tidak rusak.
- Cek log untuk menelusuri siapa mengubah apa.
