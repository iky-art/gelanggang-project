# 15. Data demo dan skenario

## Data dasar

Impor `database/data_contoh.sql` setelah `gelanggang.sql`. Hasilnya satu turnamen "Contoh Cup: Antar Kelas" berstatus pendaftaran dengan 8 tim (seed 1–8) dan 5 pemain per tim. Semua nama adalah contoh fiktif; ganti dengan nama kelas kalian bila mau.

Pengaturan turnamen contoh: grup + gugur, 2 grup, 2 lolos per grup, poin 3/1/0, seri tidak diizinkan, juara 3 aktif, durasi 45 menit.

## Skenario 1: alur utama (untuk demo)

1. Mulai turnamen dengan pembagian seed.
2. Hasil: grup A = seed 1, 4, 5, 8. Grup B = seed 2, 3, 6, 7. Masing-masing 6 laga.
3. Isi skor beberapa laga, lihat klasemen berubah.

## Skenario 2: seri total (membuktikan tie-break)

Siapkan grup yang hasilnya membuat dua tim sama poin dan sama selisih:

- Isi skor agar tim X dan Y sama-sama 3 poin, selisih sama, dan di antara mereka X mengalahkan Y. Klasemen menempatkan X di atas karena head-to-head.
- Untuk seri total: buat X dan Y sama pada semua kriteria. Klasemen menampilkan tanda "=". Tombol babak gugur ditolak di batas lolos. Isi nilai undian pada X, coba lagi: berhasil.

Catat skor yang kamu pakai di lembar bukti (berkas 22) agar bisa diulang.

## Skenario 3: bye (format gugur)

Buat turnamen format **Gugur** dengan 6 tim. Setelah dimulai, bagan berukuran 8 dengan dua bye: seed 1 dan 2 langsung ada di semifinal.

## Skenario 4: koreksi dan penguncian

1. Isi semifinal 1: A menang atas B.
2. Koreksi sebagai admin menjadi B menang, sebelum final dimainkan: diizinkan.
3. Isi final, lalu koreksi semifinal 1 lagi: **ditolak** karena final sudah selesai.

## Skenario 5: jadwal bentrok

1. Atur laga 1: 08.00, Arena 1.
2. Atur laga lain dengan salah satu tim yang sama pada 08.30 (durasi 45): ditolak.
3. Atur laga lain di Arena 1 pada 08.30 dengan tim berbeda: ditolak karena arena.
4. Atur pada 08.45: diterima.

## Mengatur ulang demo

Hapus turnamen demo (admin) dan impor ulang `data_contoh.sql`. Impor kedua akan menambah turnamen contoh baru, jadi hapus yang lama bila tidak ingin dobel.

## Mempercepat persiapan

Siapkan dua turnamen: satu baru dimulai (untuk demo input skor dan jadwal), satu hampir selesai grup (untuk demo babak gugur dan juara). Dengan begitu demo tidak perlu mengisi puluhan skor di depan penguji.
