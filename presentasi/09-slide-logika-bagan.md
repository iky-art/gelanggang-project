# Slide 09: Logika bagan gugur

**Waktu:** 45 detik

## Tampil di layar

```
8 tim:   1v8   4v5   2v7   3v6
          └──┬──┘     └──┬──┘
           Semifinal   Semifinal
                 └──┬──┘
                  Final
```

- Seed standar: seed 1 dan 2 baru bertemu di final
- 6 tim → ukuran 8 → seed 1 dan 2 mendapat **bye**
- Ronde 1 menghindari pertemuan satu grup

## Naskah

"Bagan dibuat dengan urutan seed standar, jadi unggulan satu dan dua baru bisa bertemu di final. Kalau jumlah tim bukan pangkat dua, misalnya enam, bagan dibulatkan ke delapan dan tim unggulan mendapat bye, artinya lolos otomatis. Untuk format grup, juara grup diurutkan dulu lalu runner-up mengikuti urutan itu, sehingga di ronde pertama tidak ada dua tim dari grup yang sama bertemu. Pemenang tiap laga otomatis pindah ke laga berikutnya lewat kolom `menang_ke_id`."

## Cara membuat urutan seed (jika ditanya)

Mulai dari [1]. Tiap angka s diikuti pasangannya (2m+1−s), dengan m panjang daftar sekarang: [1,2] → [1,4,2,3] → [1,8,4,5,2,7,3,6].

## Bukti

- Halaman bagan publik.
- `app/logic/bagan.php`, fungsi `urutan_seed()` dan `buat_bagan()`.
- Uji otomatis: "4 grup × 2 lolos: ronde 1 = [1v8, 7v2, 3v6, 5v4]".

## Batasan yang jujur

Penghindaran satu grup adalah heuristik: terbukti untuk 2 dan 4 grup dengan 2 lolos, tetapi tidak dijamin untuk semua kombinasi.
