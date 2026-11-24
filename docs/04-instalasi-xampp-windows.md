# 04. Instalasi di XAMPP (Windows), tanpa terminal

Panduan ini sengaja hanya memakai klik. Cocok untuk laptop sekolah yang tidak boleh memakai terminal dan tidak punya internet.

## Sebelum berangkat (di rumah, ada internet)

1. Siapkan folder `gelanggang` lengkap dengan Bootstrap di `assets/vendor/` (dokumen 03).
2. Salin ke flashdisk.

## Langkah di laptop sekolah

### 1. Jalankan XAMPP
Buka **XAMPP Control Panel**, klik **Start** pada **Apache** dan **MySQL**. Keduanya harus berwarna hijau.

### 2. Taruh folder proyek
Salin folder `gelanggang` ke `C:\xampp\htdocs\`. Hasil akhirnya: `C:\xampp\htdocs\gelanggang\index.php`.

### 3. Buat database
1. Buka `http://localhost/phpmyadmin`.
2. Klik tab **Impor**.
3. **Pilih File**: `gelanggang\database\gelanggang.sql`.
4. Klik **Kirim** (atau Go) di bagian bawah. Database `gelanggang` dengan 8 tabel akan terbentuk.

Opsional: ulangi dengan `data_contoh.sql` untuk mengisi satu turnamen contoh berisi 8 tim. Impor ini harus setelah `gelanggang.sql`.

### 4. Buka aplikasi
Buka `http://localhost/gelanggang/`. Klik **Masuk**. Karena belum ada akun, kamu otomatis dibawa ke halaman **Setup awal**.

### 5. Buat akun admin
Isi nama, username, dan kata sandi (minimal 8 karakter, ada huruf dan angka). Tidak ada sandi bawaan, jadi tidak ada akun yang bisa ditebak.

### 6. Masuk dan mulai
Masuk dengan akun tadi lalu ikuti dokumen 18.

## Jika XAMPP memakai kata sandi MySQL

Buka `app/config.php` dengan Notepad, lalu ubah baris `pass` agar sesuai. Pada XAMPP bawaan, user `root` tanpa kata sandi, jadi tidak perlu diubah.

## Jika port 80 bentrok

Jika Apache gagal start karena port 80 dipakai aplikasi lain (Skype, IIS), ubah port di `Config` lalu `httpd.conf` menjadi 8080, dan buka `http://localhost:8080/gelanggang/`.

## Mengapa tidak perlu terminal?

Semua langkah (menaruh folder, impor SQL, setup akun) dilakukan lewat Explorer, phpMyAdmin, dan browser.
