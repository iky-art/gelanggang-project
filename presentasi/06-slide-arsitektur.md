# Slide 06: Arsitektur

**Waktu:** 45 detik

## Tampil di layar

```
Halaman (index, turnamen, admin/*)
        │
        ▼
   app/model.php ─── aturan bisnis + database
        │
        ▼
 app/logic/*  ─── hitungan murni (klasemen, jadwal, bagan, validasi)
```

Tiga lapisan: **tampilan → model → logika murni.**

## Naskah

"Kode saya bagi tiga lapisan. Halaman hanya menyusun tampilan. Lapisan model berisi aturan bisnis yang menyentuh database, misalnya menyimpan skor. Paling bawah adalah logika murni: fungsi yang hanya menghitung tanpa menyentuh database. Contohnya fungsi klasemen dan pembuat bagan. Keuntungannya, bagian yang paling mudah salah ini bisa saya uji sendiri tanpa database."

## Yang bisa ditunjukkan

Buka editor, perlihatkan folder `app/logic/`, lalu `tests/uji-logika.php`.

## Pola halaman admin

Tiap halaman: aksi POST di atas, formulir di tengah, daftar di bawah. Setelah POST selalu redirect (Post/Redirect/Get) agar refresh tidak mengirim ulang data.

Rujukan: `docs/07-struktur-folder.md`.
