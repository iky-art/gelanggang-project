# 22. Pemetaan ke penilaian UKK

> Rubrik UKK berbeda antar sekolah dan tahun. Tabel di bawah adalah **contoh pemetaan umum**. Cocokkan dengan lembar penilaian resmi dari gurumu sebelum ujian.

## Kompetensi yang umum dinilai dan buktinya

| Aspek umum | Bukti di Gelanggang |
|---|---|
| Analisis kebutuhan | Dokumen 01 (masalah dan tujuan), alur pengguna di dokumen 09 |
| Perancangan basis data | Dokumen 08: relasi, kunci, indeks, perilaku hapus. File `database/gelanggang.sql` |
| Perancangan antarmuka | Tampilan publik dan panel; responsif; aksesibilitas dasar (label, fokus, kontras) |
| Implementasi basis data | Impor SQL lewat phpMyAdmin; relasi foreign key |
| Implementasi pemrograman | PHP terstruktur: logika murni, model, tampilan terpisah |
| CRUD | Dokumen 17: turnamen, tim, pemain, pengguna |
| Autentikasi dan otorisasi | Login, peran admin/panitia, pembatasan percobaan |
| Validasi dan keamanan | Dokumen 16 |
| Pengujian | Dokumen 19: uji otomatis dan daftar uji manual |
| Dokumentasi | Folder `docs/` |
| Presentasi | Folder `presentasi/` |
| Deploy | XAMPP (dokumen 04), Docker (dokumen 05) |

## Hal yang menonjol (bisa disebut sebagai nilai tambah)

1. **Logika non-sepele yang bisa dijelaskan:** klasemen dengan head-to-head, bagan dengan bye, seed dari grup.
2. **Transaksi database** dan kunci versi pada input skor.
3. **Log audit** dan koreksi dengan alasan.
4. **Anti bentrok jadwal.**
5. **Offline penuh:** tidak ada CDN, font web, atau API luar.
6. **Uji otomatis** untuk logika inti.

## Cara menyampaikan batasan tanpa terlihat lemah

Penguji menghargai kejujuran. Sebutkan batasan dengan tenang dan siapkan rencana lanjutan (dokumen 23):

- "Format yang didukung ada tiga. Format Swiss belum, dan strukturnya sudah memungkinkan penambahan."
- "Reset sandi tidak lewat email karena aplikasi berjalan offline; pemulihannya lewat admin atau database."

## Menyesuaikan dengan tugas yang diberikan

Bila soal UKK mewajibkan modul tertentu (misal "laporan" atau "cetak"), tunjukkan fitur terdekat: klasemen cetak (CSS khusus cetak), log aktivitas, dan tampilan publik. Tambahkan modul baru dengan pola yang sama seperti `admin/tim.php`.

## Catatan etika

Aplikasi ini disediakan sebagai bahan belajar dan contoh. Bila kamu memakainya untuk UKK, pahami setiap bagian dan siapkan jawaban sendiri (lihat folder `presentasi/`). Penguji biasanya meminta penjelasan kode, bukan hanya demo.
