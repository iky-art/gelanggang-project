# 17. CRUD per modul

CRUD = Create, Read, Update, Delete. Tabel ini memetakan setiap operasi ke halaman dan aturannya.

## Turnamen (`admin/turnamen.php`)

| Operasi | Cara | Aturan |
|---|---|---|
| Create | Tombol **Turnamen baru** | Nama ≥ 3 huruf, game ≥ 2, durasi 10–240, aturan poin valid |
| Read | Daftar turnamen, halaman Kelola | Menampilkan status, jumlah tim, progres |
| Update | **Ubah pengaturan** | Setelah dimulai hanya nama, game, durasi |
| Delete | Zona hapus (admin) | Konfirmasi, menghapus semua data turunan |

## Tim (`admin/tim.php`)

| Operasi | Aturan |
|---|---|
| Create | Hanya saat draft. Nama unik per turnamen. Singkatan 2–5 karakter (otomatis bila kosong). Warna harus `#RRGGBB` |
| Read | Daftar dengan grup, seed, undian, jumlah pemain |
| Update | Nama, singkatan, warna, undian kapan saja. Seed hanya saat draft |
| Delete | Hanya saat draft, ikut menghapus pemain |

## Pemain (`admin/pemain.php`)

| Operasi | Aturan |
|---|---|
| Create | Nickname unik per tim. Maksimal 12 pemain per tim (`maks_pemain_per_tim` di `config.php`) |
| Read | Daftar per tim dengan lencana kapten |
| Update | Memilih kapten otomatis menurunkan kapten lama (dalam transaksi) |
| Delete | Dengan konfirmasi |

## Laga (`admin/laga.php`)

Laga dibuat otomatis, bukan lewat formulir.

| Operasi | Cara |
|---|---|
| Create | Otomatis saat turnamen dimulai atau babak gugur dibuat |
| Read | Daftar per fase, dengan status dan kunci |
| Update | **Isi skor** / **Koreksi**, dan **Jadwal** (waktu dan arena) |
| Delete | Tidak ada. Laga hanya hilang bersama turnamen, supaya bagan tidak rusak |

## Pengguna (`admin/pengguna.php`, admin)

| Operasi | Aturan |
|---|---|
| Create | Username 3–40 karakter (huruf, angka, titik, `_`, `-`). Sandi kuat |
| Read | Daftar dengan peran dan status |
| Update | Kata sandi opsional. Tidak bisa menurunkan atau menonaktifkan diri sendiri |
| Delete | Tidak bisa menghapus diri sendiri atau admin aktif terakhir |

## Log (`admin/log.php`, admin)

Read saja, dengan pencarian. Log sengaja tidak bisa diubah atau dihapus dari aplikasi.

## Pola yang sama di tiap modul

1. Formulir mengirim POST dengan token CSRF.
2. Server memvalidasi dan menyimpan. Galat ditampilkan sebagai pesan jelas.
3. Sukses: `redirect` ke daftar dengan pesan "tersimpan" (Post/Redirect/Get).
4. Aksi berbahaya (hapus, mulai) meminta konfirmasi lewat dialog.
