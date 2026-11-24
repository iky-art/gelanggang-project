# 13. Logika bagan gugur

Kode: `app/logic/bagan.php` (hitungan murni) dan `simpan_bagan()` di `model.php` (menyimpan).

## Ukuran bagan dan bye

Ukuran bagan P = pangkat dua terkecil yang ≥ jumlah tim. 6 tim → P = 8, artinya ada 2 "bye". Tim dengan seed tertinggi mendapat bye dan langsung maju.

## Urutan seed standar

`urutan_seed(8)` = [1, 8, 4, 5, 2, 7, 3, 6]. Pasangan ronde 1 adalah elemen berurutan: 1 lawan 8, 4 lawan 5, 2 lawan 7, 3 lawan 6.

Akibatnya seed 1 dan 2 hanya bisa bertemu di final, seed 1 dan 4 paling cepat di semifinal.

Cara membuatnya: mulai dari [1]. Setiap putaran, setiap angka s diikuti pasangannya (2m+1−s), dengan m panjang daftar sekarang.

## Seed dari hasil grup

`peserta_gugur_dari_grup()`:

1. Juara grup diurutkan berdasarkan poin, selisih, skor memasukkan.
2. Peringkat kedua dan seterusnya mengikuti **urutan grup juara**.

Contoh 4 grup × 2 lolos: urutan seed = 1A, 1B, 1C, 1D, lalu 2A, 2B, 2C, 2D (dalam urutan juara). Pasangan ronde 1 menjadi [1 v 8], [4 v 5], dst., dan terbukti tidak ada pertemuan satu grup. Hal ini diuji otomatis.

Untuk konfigurasi lain, `hindari_bentrok_grup()` menukar lawan agar dua tim satu grup tidak bertemu di ronde 1. Ini heuristik terbaik, bukan jaminan matematis untuk semua kombinasi.

## Bentuk data bagan

Setiap laga punya kunci seperti `R1-2` (ronde 1, urutan 2). Pemenang laga ronde r urutan i menuju `R(r+1)-ceil(i/2)`; slot `a` bila i ganjil, `b` bila genap.

Disimpan ke database dari ronde tertinggi ke terendah, supaya `menang_ke_id` selalu menunjuk laga yang sudah ada.

## Bye

Laga bye ditandai `bye = 1`, langsung `selesai`, pemenangnya otomatis dimasukkan ke laga berikutnya saat bagan disimpan. Bye tidak punya skor dan tidak dihitung pada progres.

## Perebutan juara 3

Dibuat hanya bila opsi aktif **dan** kedua semifinal pasti punya pecundang. Bila ada bye di babak semifinal (contoh 3 tim), laga juara 3 tidak dibuat. Pecundang semifinal masuk slot `a` dan `b` laga tersebut lewat `kalah_ke_id`.

## Kemajuan pemenang

Saat skor disimpan (dokumen 14), pemenang otomatis ditempatkan di slot laga berikutnya. Bila hasil dikoreksi dan pemenang berubah, penempatan diganti, **kecuali** laga berikutnya sudah selesai. Dalam kasus itu koreksi ditolak, dan admin harus mengoreksi laga berikutnya terlebih dulu.

## Juara otomatis

Setelah skor tersimpan, `perbarui_status_turnamen()` memeriksa final. Pemenang final menjadi juara, dan status menjadi `selesai` bila tidak ada laga yang tersisa (termasuk juara 3).
