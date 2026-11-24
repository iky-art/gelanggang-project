# 22. Lembar bukti pengujian

Isi lembar ini dengan **hasil nyata** dari uji yang kamu jalankan. Lembar kosong adalah pengingat bahwa pengujian belum dilakukan. Jangan mengisi centang yang belum dikerjakan.

## Identitas uji

| Item | Isi |
|---|---|
| Tanggal uji | |
| Penguji | |
| Versi PHP (`phpinfo`) | |
| Versi MySQL/MariaDB | |
| Browser dan versinya | |
| Lingkungan | XAMPP / Docker / Termux |

## Uji otomatis (`tests/uji-logika.php`)

| Item | Hasil |
|---|---|
| Jumlah uji | |
| Lulus | |
| Gagal | |
| Tangkapan layar | `bukti/uji-otomatis.png` |

Uji yang sempat gagal dan perbaikannya (jika ada):

| Uji | Penyebab | Perbaikan | Hasil ulang |
|---|---|---|---|
| | | | |

## Uji manual (turunan dari `docs/19-pengujian.md`)

| No | Skenario | Hasil yang diharapkan | Hasil nyata | Lulus? | Bukti |
|---|---|---|---|:-:|---|
| 1 | Setup admin pertama | Akun dibuat, setup tertutup | | | |
| 2 | Login sandi salah 5 kali | Dikunci 15 menit | | | |
| 3 | Poin 1/3/0 | Ditolak | | | |
| 4 | Mulai dengan 3 tim, 2 grup | Ditolak dengan pesan | | | |
| 5 | Mulai dengan 8 tim | 12 laga grup terbentuk | | | |
| 6 | Skor `-1` | Ditolak | | | |
| 7 | Skor seri (seri dimatikan) | Ditolak | | | |
| 8 | Panitia buka laga terkunci | "Terkunci" | | | |
| 9 | Koreksi tanpa alasan | Ditolak | | | |
| 10 | Koreksi dengan alasan | Tersimpan, ada di log | | | |
| 11 | Simpan laga sama dari dua tab | Tab kedua ditolak | | | |
| 12 | Seri total di batas lolos | Babak gugur ditolak, minta undian | | | |
| 13 | Ronde 1 bagan | Tidak ada pertemuan satu grup | | | |
| 14 | Skor seri di gugur tanpa pemenang | Ditolak | | | |
| 15 | Koreksi semifinal setelah final | Ditolak | | | |
| 16 | Final selesai | Juara terisi, status Selesai | | | |
| 17 | Jadwal tim sama dalam 45 menit | Ditolak | | | |
| 18 | Jadwal arena sama dalam 45 menit | Ditolak | | | |
| 19 | Panitia buka `/admin/pengguna.php` | Akses ditolak | | | |
| 20 | Nama tim berisi `<b>` | Tampil sebagai teks | | | |
| 21 | Layar 360 px | Tidak ada gulir horizontal halaman | | | |
| 22 | Navigasi keyboard | Fokus terlihat, semua bisa dioperasikan | | | |

Ringkasan: **[..] dari 22 lulus.**

## Skor yang dipakai pada skenario seri total

Catat agar bisa diulang saat demo:

| Laga | Tim A | Tim B | Skor |
|---|---|---|---|
| | | | |

## Temuan dan perbaikan

| Temuan | Tingkat (rendah/sedang/tinggi) | Perbaikan | Status |
|---|---|---|---|
| | | | |

## Catatan akhir

Tuliskan satu kalimat jujur tentang kondisi aplikasi hari ini, misalnya: "Seluruh uji otomatis lulus dan 21 dari 22 uji manual lulus; satu temuan tampilan kecil sudah diperbaiki."
