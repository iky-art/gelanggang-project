# Slide 07: Database

**Waktu:** 45 detik

## Tampil di layar

```
turnamen ─┬─< grup ─< tim ─< pemain
          └─< pertandingan >─ tim
pengguna     log_aktivitas     percobaan_login
```

- 8 tabel, InnoDB, utf8mb4
- Foreign key + `ON DELETE CASCADE`
- Kunci unik: username, nama tim per turnamen, nickname per tim

(Gambar ERD dari phpMyAdmin: tab **Desainer**, atau gunakan tangkapan layar.)

## Naskah

"Database memiliki delapan tabel. Turnamen punya grup dan tim, tim punya pemain, dan pertandingan menghubungkan dua tim dalam satu turnamen. Tabel pengguna menyimpan akun dengan kata sandi ter-hash. Log aktivitas mencatat aksi penting. Relasinya memakai foreign key sehingga menghapus turnamen ikut menghapus data turunannya. Tabel pertandingan paling penting karena menyimpan skor, pemenang, jadwal, dan penunjuk ke laga berikutnya di bagan."

## Poin teknis yang bisa disebut

- `menang_ke_id` menunjuk laga berikutnya, sehingga bagan bisa maju otomatis.
- `versi` pada pertandingan dipakai untuk mendeteksi dua orang mengubah laga yang sama.
- Kolom penunjuk laga sengaja tanpa foreign key (referensi ke diri sendiri) agar penghapusan berantai bersih.

Rujukan: `docs/08-struktur-database.md`.
