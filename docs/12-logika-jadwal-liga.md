# 12. Logika jadwal liga

Kode: `app/logic/jadwal.php`.

## Metode lingkaran (circle method)

Agar setiap tim bertemu tim lain tepat sekali:

1. Bila jumlah tim ganjil, tambahkan "tim kosong". Tim yang berpasangan dengan tim kosong istirahat pada ronde itu.
2. Tim di posisi pertama tetap diam. Tim lain berputar satu langkah tiap ronde.
3. Pada tiap ronde, posisi ke-i berpasangan dengan posisi ke-(n−1−i).

Contoh 4 tim (A, B, C, D):

| Ronde | Pasangan |
|---|---|
| 1 | A–D, B–C |
| 2 | A–C, D–B |
| 3 | A–B, C–D |

Jumlah ronde = n − 1 (n genap) atau n (n ganjil). Jumlah laga = n(n−1)/2.

## Pulang-pergi

Bila diaktifkan, seluruh putaran diulang dengan posisi tim A dan B ditukar, ronde dilanjutkan. 6 tim: 15 laga ×2 = 30 laga.

## Pembagian grup (pola ular)

Tim diurutkan berdasarkan seed (yang tanpa seed di belakang), lalu dibagikan seperti ular: baris pertama ke kanan, baris kedua ke kiri, dan seterusnya.

8 tim, 2 grup: grup A = seed 1, 4, 5, 8. Grup B = seed 2, 3, 6, 7. Jumlah kekuatan kedua grup hampir sama.

## Penyusunan waktu otomatis

Fungsi `susun_waktu()`:

- Laga diurutkan per ronde.
- Setiap slot waktu menampung sebanyak jumlah arena.
- **Ronde baru selalu mulai di slot baru.** Dengan begitu satu tim tidak pernah main dua kali pada slot yang sama, karena dalam satu ronde setiap tim hanya muncul sekali.

Contoh 2 arena, slot 45 menit, mulai 08.00:

| Laga | Waktu | Arena |
|---|---|---|
| 1 | 08.00 | Arena 1 |
| 2 | 08.00 | Arena 2 |
| 3 | 08.45 | Arena 1 |
| 4 (ronde 2) | 09.30 | Arena 1 |

Laga 3 dan 4 sengaja tidak dirapatkan karena berbeda ronde.

Hanya laga yang belum selesai yang dijadwalkan ulang.

## Rekomendasi slot

Isi slot dengan durasi laga ditambah jeda (formulir mengisi otomatis durasi + 10 menit).

## Pengujian

`tests/uji-logika.php` memeriksa: jumlah laga, tiap pasangan tepat sekali, tidak ada tim bermain dua kali dalam satu ronde, dan hasil `susun_waktu` yang pasti.
