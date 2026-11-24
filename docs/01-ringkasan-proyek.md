# 01. Ringkasan proyek

## Apa itu Gelanggang?

Gelanggang membantu panitia turnamen e-sport sekolah mengelola tim, pemain, jadwal, skor, klasemen, dan bagan gugur. Penonton cukup membuka halaman publik untuk melihat klasemen dan hasil terbaru.

Nama "Gelanggang" berarti arena tempat pertandingan berlangsung.

## Masalah yang diselesaikan

Turnamen antar kelas biasanya dicatat di kertas atau spreadsheet. Akibatnya:

- klasemen dihitung manual dan sering salah saat poin sama;
- bagan gugur digambar tangan dan sulit diperbarui;
- dua laga bisa terjadwal di arena atau waktu yang sama;
- skor bisa diubah tanpa jejak.

Gelanggang mengotomatiskan semua itu dengan aturan yang jelas dan bisa dijelaskan.

## Fitur utama

- Tiga format turnamen: **grup lalu gugur**, **liga saja**, **gugur saja**.
- Pendaftaran tim dan pemain (CRUD lengkap).
- Pembuatan jadwal liga otomatis dengan metode lingkaran.
- Klasemen otomatis dengan tie-break berurutan dan penanda seri total.
- Bagan gugur otomatis, mendukung jumlah tim bukan pangkat dua (bye) dan perebutan juara 3.
- Penyusunan waktu dan arena otomatis, serta deteksi bentrok saat jadwal diubah manual.
- Input skor aman: transaksi database, penguncian, koreksi khusus admin dengan alasan, dan log aktivitas.
- Dua peran pengelola (admin dan panitia) dengan halaman publik tanpa login.

## Teknologi

| Lapisan | Pilihan | Alasan |
|---|---|---|
| Backend dan tampilan | PHP polos (tanpa framework) | Sesuai ketentuan UKK, mudah dibaca penguji |
| Database | MySQL lewat phpMyAdmin | Bawaan XAMPP |
| UI | Bootstrap 5.3 (disimpan lokal) | Stabil, tidak butuh internet |
| Ikon | SVG inline | Tidak butuh pustaka ikon |
| Interaksi | JavaScript murni (sekitar 50 baris) | Tanpa pustaka tambahan |

## Prinsip desain

1. **Offline dulu.** Tidak ada CDN, font web, atau API luar saat aplikasi berjalan.
2. **Logika terpisah dari tampilan.** Perhitungan murni ada di `app/logic/` dan bisa diuji tanpa database.
3. **Setiap tombol berfungsi.** Tidak ada data palsu, statistik palsu, atau fitur hiasan.
4. **Aman secara default.** Prepared statement, token CSRF, hash kata sandi, dan tidak ada kata sandi bawaan.
