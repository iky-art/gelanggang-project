# 08. Struktur database

Database `gelanggang`, mesin InnoDB, karakter `utf8mb4`. Skema ada di `database/gelanggang.sql`.

## Relasi antar tabel

```
turnamen 1──* grup 1──* tim 1──* pemain
    │                    │
    │                    ├──* pertandingan (tim_a_id, tim_b_id, pemenang_id)
    └──* pertandingan *──┘  (turnamen_id, grup_id)

pengguna 1──* log_aktivitas (pengguna_id, tanpa foreign key)
percobaan_login (berdiri sendiri, untuk pembatasan percobaan)
```

## Tabel

### pengguna
| Kolom | Keterangan |
|---|---|
| id, nama, username (unik) | Identitas |
| kata_sandi | Hasil `password_hash` (bcrypt), tidak pernah disimpan polos |
| peran | `admin` atau `panitia` |
| aktif | 1 = boleh masuk |

### turnamen
| Kolom | Keterangan |
|---|---|
| nama, game | Identitas turnamen |
| format | `liga_gugur`, `liga`, atau `gugur` |
| jumlah_grup, lolos_per_grup | Dipakai format grup + gugur |
| pulang_pergi, izinkan_seri | Aturan babak grup/liga |
| poin_menang, poin_seri, poin_kalah | Skema poin (bawaan 3/1/0) |
| juara_tiga | Minta laga perebutan juara 3 |
| durasi_menit | Lama satu laga, dipakai deteksi bentrok |
| status | `draft`, `grup`, `gugur`, `selesai` |
| juara_id | Diisi otomatis saat turnamen selesai |

### grup
Satu baris per grup (A, B, ...). Untuk format liga namanya `Liga`. Kunci unik `(turnamen_id, nama)`.

### tim
`nama` unik per turnamen. `warna` kode heksadesimal. `seed` (1 = unggulan, opsional). `undian` pemutus terakhir saat seri total. `grup_id` diisi saat turnamen dimulai.

### pemain
`nickname` unik per tim. `kapten` hanya satu per tim (dijaga di kode).

### pertandingan
Tabel paling penting.

| Kolom | Keterangan |
|---|---|
| fase | `grup` atau `gugur` |
| ronde, urutan, label | Posisi laga. Label contoh: Semifinal, Final, Perebutan Juara 3 |
| tim_a_id, tim_b_id | Boleh kosong di bagan (menunggu pemenang) |
| skor_a, skor_b, pemenang_id | Kosong sampai laga selesai |
| bye | 1 = tim lolos otomatis, tanpa skor |
| status, dikunci | `terjadwal`/`selesai`, dan kunci skor |
| waktu, lokasi | Jadwal dan arena |
| menang_ke_id, menang_ke_slot | Laga berikutnya bagi pemenang, dan slot `a`/`b` |
| kalah_ke_id, kalah_ke_slot | Dipakai perebutan juara 3 |
| versi | Penghitung perubahan untuk pengecekan konflik |

### log_aktivitas dan percobaan_login
Log mencatat siapa, aksi, detail, IP, waktu. Percobaan login menyimpan upaya gagal untuk pembatasan 5 kali per 15 menit.

## Perilaku hapus (foreign key)

- Hapus turnamen: ikut terhapus grup, tim, pemain, dan pertandingan (`ON DELETE CASCADE`).
- Hapus tim (hanya saat draft): pemainnya ikut terhapus; referensi di pertandingan menjadi NULL.
- Kolom `menang_ke_id` dan `kalah_ke_id` sengaja tanpa foreign key. Referensi ke tabel sendiri akan mempersulit penghapusan turnamen secara berantai.
- `turnamen.juara_id` juga tanpa foreign key, untuk menghindari referensi melingkar dengan `tim`.

## Indeks

- `idx_laga_turnamen (turnamen_id, fase, ronde, urutan)`: mempercepat daftar laga.
- `idx_laga_waktu (waktu)`: mempercepat pengecekan bentrok dan daftar laga berikutnya.
- Kunci unik pada username, nama tim per turnamen, nickname per tim, dan nama grup.
