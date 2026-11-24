# 07. Struktur folder

```
gelanggang/
├── index.php              Beranda publik (daftar turnamen, laga berikutnya, hasil terbaru)
├── turnamen.php           Halaman publik satu turnamen (klasemen, bagan, jadwal, tim)
├── login.php, logout.php  Masuk dan keluar
├── setup.php              Pembuatan akun admin pertama
├── siapkan-aset.php       Pemasang Bootstrap sekali pakai
├── .htaccess              Menutup folder internal (Apache)
├── admin/                 Panel pengelola (wajib login)
│   ├── index.php          Ringkasan dan tugas yang menunggu
│   ├── turnamen.php       CRUD turnamen, mulai, babak gugur, jadwal otomatis
│   ├── tim.php            CRUD tim
│   ├── pemain.php         CRUD pemain
│   ├── laga.php           Daftar laga, input skor, atur jadwal
│   ├── pengguna.php       CRUD pengguna (admin)
│   └── log.php            Log aktivitas (admin)
├── app/
│   ├── bootstrap.php      Pintu masuk bersama, memuat semua modul
│   ├── config.php         Pengaturan database dan aplikasi
│   ├── helpers.php        e(), url(), flash, CSRF, tanggal, ikon
│   ├── db.php             Koneksi PDO dan pembantu query
│   ├── auth.php           Sesi, login, peran, log, aturan kata sandi
│   ├── model.php          Aturan bisnis yang menyentuh database
│   ├── view.php           Potongan tampilan (chip, laga, klasemen, bagan)
│   ├── layout.php         Kerangka halaman, navigasi, tab
│   └── logic/             Fungsi murni, tanpa database (bisa diuji sendiri)
│       ├── validasi.php
│       ├── klasemen.php
│       ├── jadwal.php
│       └── bagan.php
├── assets/
│   ├── css/app.css        Gaya khusus Gelanggang
│   ├── js/app.js          Dialog konfirmasi dan formulir dinamis
│   └── vendor/            Bootstrap (dipasang sendiri, lihat dokumen 03)
├── database/
│   ├── gelanggang.sql     Skema
│   └── data_contoh.sql    Data contoh (opsional)
├── docker/                Dockerfile dan docker-compose.yml
├── tests/uji-logika.php   Uji otomatis logika inti
├── docs/                  Dokumentasi
└── presentasi/            Bahan presentasi UKK
```

## Pola yang dipakai

**Satu halaman, satu berkas.** Setiap halaman `admin/*.php` menangani tiga hal: aksi POST (di atas), formulir (tengah), dan daftar (bawah). Pola ini dikenal sebagai Post/Redirect/Get: setelah POST selalu `redirect`, sehingga refresh browser tidak mengirim ulang formulir.

**Tiga lapisan.**

| Lapisan | Berkas | Tugas |
|---|---|---|
| Logika murni | `app/logic/*` | Menghitung tanpa menyentuh database |
| Model | `app/model.php`, `db.php` | Membaca dan menulis database, menjaga aturan bisnis |
| Tampilan | halaman, `view.php`, `layout.php` | Menyusun HTML |

**Mengapa logika dipisah?** Klasemen dan bagan adalah bagian paling mudah salah. Karena murni (masukan sama, keluaran sama), keduanya bisa diuji di `tests/uji-logika.php` tanpa database.

## Urutan pemuatan

`bootstrap.php` memuat: config, helpers, logic, db, auth, view, layout, model, lalu memulai sesi dan mengirim header keamanan. Setiap halaman cukup memanggil `require 'app/bootstrap.php'`.
