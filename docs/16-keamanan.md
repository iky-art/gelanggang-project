# 16. Keamanan

Daftar kontrol keamanan yang ada, dan batasannya.

## Ringkasan

| Ancaman | Perlindungan | Berkas |
|---|---|---|
| SQL injection | Semua query memakai prepared statement (PDO, emulasi dimatikan) | `app/db.php` |
| XSS | Semua keluaran lewat `e()` (`htmlspecialchars`) | seluruh tampilan |
| CSRF | Token acak di setiap formulir POST, dibandingkan dengan `hash_equals` | `helpers.php` |
| Kata sandi bocor | `password_hash` (bcrypt), tanpa sandi bawaan | `auth.php`, `setup.php` |
| Tebakan kata sandi | Maksimal 5 percobaan gagal per username dan IP per 15 menit | `auth.php` |
| Pembajakan sesi | Cookie `HttpOnly`, `SameSite=Lax`, ID sesi diganti saat login | `auth.php` |
| Akses tanpa izin | Pengecekan peran di server pada setiap halaman admin | `wajib_peran()` |
| Open redirect | Parameter `kembali` hanya menerima jalur di dalam aplikasi | `aman_kembali()` |
| Clickjacking dan sniffing | `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` | `helpers.php` |
| Skrip tidak sah | `Content-Security-Policy` membatasi skrip ke asal sendiri | `helpers.php` |
| Perubahan tanpa jejak | Log aktivitas untuk aksi penting | `catat()` |
| Akses file internal | `.htaccess` menutup `app/`, `database/`, `docker/` | `.htaccess` |

## Penjelasan singkat

**Prepared statement.** Data pengguna tidak pernah disambung ke teks SQL. Contoh: `q('SELECT * FROM pengguna WHERE username = ?', [$username])`.

**Escape keluaran.** Nama tim berisi `<script>` akan tampil sebagai teks biasa. Warna tim divalidasi dengan pola `#RRGGBB` sebelum masuk ke atribut `style`.

**CSRF.** Tanpa token, situs lain bisa membuat browser korban mengirim formulir ke Gelanggang. Token tersimpan di sesi dan harus cocok.

**Logout via POST.** Agar tidak bisa dipicu lewat tautan atau gambar.

**Log tidak boleh menggagalkan aksi.** `catat()` dibungkus try/catch.

## Hal yang sengaja tidak ada

- **Reset kata sandi lewat email.** Aplikasi berjalan offline, tidak ada pengiriman email. Admin dapat mengganti kata sandi pengguna lewat menu Pengguna.
- **HTTPS.** Aplikasi dirancang untuk localhost. Bila dipasang di server publik, aktifkan HTTPS; cookie otomatis menjadi `Secure` bila koneksi HTTPS.
- **Pembatasan permintaan umum.** Hanya login yang dibatasi.

## Batasan yang jujur

- Pembatasan login berbasis IP; di belakang proxy, semua pengguna bisa terlihat satu IP.
- Tidak ada pengecekan waktu respons untuk username yang tidak ada (user enumeration lewat waktu tidak ditutup sepenuhnya, pesan galatnya sudah sama).
- `siapkan-aset.php` bisa dipakai siapa saja selama Bootstrap belum terpasang; setelah terpasang, ia otomatis menolak. Hapus berkas itu di server publik.
- Belum ada audit keamanan pihak ketiga.

## Saran penerapan

1. Ganti kata sandi MySQL bila server bisa diakses jaringan.
2. Hapus `siapkan-aset.php` dan `tests/` di server publik.
3. Pasang HTTPS.
4. Cadangkan database secara berkala (ekspor lewat phpMyAdmin).
