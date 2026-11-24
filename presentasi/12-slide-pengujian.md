# Slide 12: Pengujian

**Waktu:** 45 detik

## Tampil di layar

> ## Uji otomatis (logika inti)
> **[ISI] dari [ISI] uji lulus**
> (klasemen · jadwal · bagan · validasi)
>
> ## Uji manual
> Daftar periksa di `docs/19-pengujian.md`
> [ISI] dari [ISI] butir lulus

**Isi angka sesuai hasil nyata dari `22-lembar-bukti-pengujian.md`.** Jangan menulis angka yang belum kamu dapatkan.

## Naskah

"Logika inti saya uji otomatis tanpa database. Misalnya klasemen dengan head-to-head, bagan dengan bye, dan deteksi bentrok jadwal. Hasilnya [sebutkan angka nyata]. Selain itu saya menjalankan daftar uji manual untuk alur lengkap, dari login sampai juara, termasuk skenario yang seharusnya ditolak, seperti skor negatif atau jadwal bentrok."

## Bukti

- Buka `localhost/gelanggang/tests/uji-logika.php`, tunjukkan semua baris hijau.
- Perlihatkan satu uji manual yang ditolak dengan pesan jelas.

## Jika ada uji yang gagal

Jangan disembunyikan. Katakan: "Satu uji gagal, penyebabnya [..], sudah saya perbaiki dan hasil ulangnya lulus." Penguji menghargai proses debugging yang jujur.

Rujukan: `docs/19-pengujian.md`.
