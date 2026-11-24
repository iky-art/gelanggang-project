# 10. Aturan turnamen

## Tiga format

| Format | Isi | Contoh |
|---|---|---|
| Grup + gugur | Tim dibagi ke beberapa grup, bermain liga di grup, juara dan runner-up lanjut ke bagan | 8 tim, 2 grup, 2 lolos per grup |
| Liga | Satu klasemen, semua tim bertemu semua tim | 6 tim, juara = peringkat 1 |
| Gugur | Langsung bagan, kalah langsung tersingkir | 8 tim |

## Siklus status

```
draft ──(Mulai turnamen)──▶ grup ──(Mulai babak gugur)──▶ gugur ──(final selesai)──▶ selesai
   │                          │
   │                          └─(format liga, semua laga selesai)──▶ selesai
   └─(format gugur)───────────────────────────────────────────────▶ gugur
```

- **draft**: pendaftaran. Tim boleh ditambah, diubah, dan dihapus.
- **grup**: jadwal liga sudah dibuat. Tim dikunci.
- **gugur**: bagan sudah dibuat. Hasil grup dibekukan.
- **selesai**: juara terisi otomatis. Admin masih bisa mengoreksi skor dengan alasan.

## Yang dikunci setelah dimulai

Hanya nama, game, dan durasi laga yang masih bisa diubah. Format, jumlah grup, skema poin, dan daftar tim dikunci agar hasil tetap adil. Nama, warna, dan nilai undian tim masih bisa diubah.

## Validasi saat membuat turnamen

- Poin menang harus lebih besar dari poin seri, dan poin seri tidak boleh lebih kecil dari poin kalah.
- Format grup + gugur: jumlah grup × lolos per grup minimal 2.
- Saat dimulai, setiap grup harus punya minimal 2 tim dan minimal sebanyak jumlah yang lolos. Contoh: 2 grup × 2 lolos butuh minimal 4 tim.
- Format liga dan gugur otomatis memakai 1 grup.

## Pembagian grup

- **Berdasarkan seed (pola ular)**: seed 1 ke grup A, seed 2 ke B, seed 3 ke B, seed 4 ke A, dan seterusnya. Grup menjadi seimbang.
- **Acak**: urutan diacak penuh.
- Ukuran grup berbeda paling banyak 1 tim.

## Babak grup dan seri

- Seri hanya boleh bila opsi "izinkan seri" aktif.
- Bila tidak aktif, skor sama ditolak dengan pesan jelas.

## Babak gugur dan seri

Skor sama pada babak gugur wajib disertai pilihan pemenang (overtime atau tiebreak). Tanpa itu skor ditolak.

## Menutup babak grup

Tombol "Mulai babak gugur" hanya berhasil bila:

1. semua laga grup selesai;
2. tiap grup punya cukup tim;
3. tidak ada seri total tepat di batas lolos. Bila ada, panitia mengisi nilai undian pada salah satu tim.

## Juara

- Gugur: pemenang final.
- Liga: peringkat 1 bila tidak seri total dengan peringkat 2.
