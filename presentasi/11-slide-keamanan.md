# Slide 11: Keamanan

**Waktu:** 30 detik

## Tampil di layar

| Ancaman | Perlindungan |
|---|---|
| SQL injection | Prepared statement (PDO) |
| XSS | Escape keluaran `e()` |
| CSRF | Token di semua formulir |
| Sandi bocor | `password_hash`, tanpa sandi bawaan |
| Tebak sandi | Maks. 5 gagal / 15 menit |
| Akses ilegal | Cek peran di server |

## Naskah

"Untuk keamanan: semua query memakai prepared statement, semua keluaran di-escape, setiap formulir punya token CSRF, kata sandi disimpan sebagai hash dan tidak ada akun bawaan, jadi akun admin pertama dibuat sendiri. Login dibatasi lima kali gagal per lima belas menit, dan hak akses dicek di server, bukan hanya dengan menyembunyikan tombol."

## Bukti singkat

- Ketik `<b>uji</b>` sebagai nama tim: tampil sebagai teks.
- Buka `/admin/pengguna.php` sebagai panitia: "Akses ditolak".

## Batasan yang jujur

Tidak ada reset sandi lewat email karena aplikasi berjalan offline. Pemulihan lewat admin atau database. Belum ada audit pihak ketiga.

Rujukan: `docs/16-keamanan.md`.
