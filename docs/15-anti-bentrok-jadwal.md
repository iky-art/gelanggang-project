# 15. Anti bentrok jadwal

Kode: `jadwal_bentrok()` di `app/logic/jadwal.php`, dipakai oleh `admin/laga.php`.

## Aturan

Saat panitia mengatur waktu dan arena satu laga, sistem menolak bila dalam rentang waktu yang tumpang tindih:

1. **tim yang sama** sudah punya laga lain, atau
2. **arena yang sama** sudah dipakai laga lain.

Dua laga dianggap tumpang tindih bila selisih waktu mulainya **kurang dari durasi laga** turnamen.

## Contoh (durasi 45 menit)

Sudah ada: laga #1 pukul 08.00, Tim 1 vs Tim 2, Arena 1.

| Laga baru | Hasil |
|---|---|
| 08.30, Tim 2 vs Tim 3, Arena 1 | Ditolak: Tim 2 bentrok dan Arena 1 bentrok |
| 08.45, Tim 2 vs Tim 3, Arena 1 | Diterima (selisih tepat 45 menit) |
| 08.10, Tim 5 vs Tim 6, Arena 2 | Diterima |

Perbandingan arena tidak membedakan huruf besar dan kecil ("arena 1" sama dengan "Arena 1").

## Cara kerja

1. Formulir mengirim waktu dan lokasi.
2. Server mengambil laga lain yang waktunya berada dalam rentang durasi di sekitar waktu baru (`DATE_SUB` dan `DATE_ADD`).
3. `jadwal_bentrok()` membandingkan tim dan arena.
4. Bila ada masalah, perubahan **tidak disimpan** dan pesan menyebut tim atau arena penyebabnya beserta nomor laga.

## Cakupan

- Cek arena berlaku lintas turnamen, karena arena fisiknya satu.
- Cek tim berlaku karena satu tim hanya milik satu turnamen.
- Laga yang sedang diedit tidak dianggap bentrok dengan dirinya sendiri.
- Mengosongkan waktu selalu diizinkan.

## Penyusunan otomatis

Jadwal otomatis (dokumen 12) tidak memakai pengecekan ini. Ia aman dari bentrok tim karena rondenya terpisah, tetapi bisa bentrok arena dengan turnamen lain yang memakai nama arena sama pada jam sama. Setelah susun otomatis, ubah manual bila perlu; pengecekan akan berjalan.

## Batasan

Hanya laga yang sudah punya kedua tim yang bisa dicek terhadap tim. Laga bagan yang menunggu pemenang belum diketahui timnya, sehingga bentrok tim baru terdeteksi saat tim terisi.
