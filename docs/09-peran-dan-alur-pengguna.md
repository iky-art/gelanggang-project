# 09. Peran dan alur pengguna

## Tiga jenis pengguna

| Pengguna | Perlu login? | Bisa melakukan |
|---|---|---|
| Penonton | Tidak | Melihat beranda, klasemen, jadwal, bagan, daftar tim |
| Panitia | Ya | Mengelola tim dan pemain, jadwal, input skor pertama kali |
| Admin | Ya | Semua milik panitia, ditambah: koreksi skor terkunci, hapus turnamen, kelola pengguna, lihat log |

## Matriks hak akses

| Aksi | Penonton | Panitia | Admin |
|---|:-:|:-:|:-:|
| Lihat halaman publik | ✔ | ✔ | ✔ |
| Buat dan ubah turnamen | | ✔ | ✔ |
| Tambah/ubah/hapus tim dan pemain | | ✔ | ✔ |
| Mulai turnamen, mulai babak gugur | | ✔ | ✔ |
| Atur jadwal | | ✔ | ✔ |
| Isi skor (pertama kali) | | ✔ | ✔ |
| Koreksi skor terkunci | | | ✔ |
| Hapus turnamen | | | ✔ |
| Kelola pengguna | | | ✔ |
| Lihat log aktivitas | | | ✔ |

Aturan ini dijaga di server (`wajib_peran()`), bukan hanya dengan menyembunyikan tombol.

## Alur akun

1. Pertama kali: `setup.php` membuat admin pertama. Halaman ini otomatis menutup diri setelah ada satu akun.
2. Admin menambah panitia lewat menu Pengguna.
3. Login dibatasi: 5 kali gagal untuk kombinasi username dan alamat IP mengunci selama 15 menit.
4. Setelah login, sesi diganti ID-nya (`session_regenerate_id`) untuk mencegah pembajakan sesi.

## Perlindungan akun

- Admin tidak bisa menonaktifkan, menurunkan, atau menghapus akunnya sendiri.
- Harus selalu tersisa minimal satu admin aktif.
- Kata sandi minimal 8 karakter dengan huruf dan angka.

## Alur kerja panitia (ringkas)

Buat turnamen → daftarkan tim dan pemain → mulai turnamen → susun jadwal → isi skor tiap laga → (grup + gugur) mulai babak gugur → isi skor sampai final → juara terisi otomatis.

Detail langkah ada di dokumen 18.
