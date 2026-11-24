# Slide 10: Input skor yang aman

**Waktu:** 45 detik

## Tampil di layar

> **Satu simpan = satu transaksi**
> 1. Validasi skor (0–99)
> 2. Kunci baris + cek versi
> 3. Tentukan pemenang (aturan seri)
> 4. Simpan skor, kunci laga
> 5. Majukan pemenang ke laga berikutnya
> 6. Perbarui status & juara
> 7. Catat log
>
> Gagal di mana pun → **semua dibatalkan**

## Naskah

"Menyimpan skor itu menyentuh banyak hal: skor, pemenang, laga berikutnya, status turnamen, dan log. Karena itu semuanya dalam satu transaksi. Kalau salah satu gagal, semuanya dibatalkan, jadi bagan tidak mungkin setengah rusak. Setelah disimpan, laga dikunci. Panitia tidak bisa mengubahnya, hanya admin yang bisa mengoreksi dengan alasan wajib, dan alasan itu masuk log. Ada juga nomor versi: kalau dua panitia membuka laga yang sama dan satu sudah menyimpan, yang kedua ditolak agar tidak menimpa."

## Bukti

- Demo: isi skor, lalu coba buka lagi sebagai panitia (terkunci).
- Demo koreksi sebagai admin, lalu tunjukkan baris di **Log**.
- Kode: fungsi `simpan_skor()` di `app/model.php`.

## Contoh yang jelas

Semifinal 1: A menang atas B. Admin koreksi karena salah input. Final belum dimainkan, jadi koreksi diizinkan dan slot final berganti. Bila final sudah selesai, koreksi ditolak sampai final dikoreksi dulu.

Rujukan: `docs/14-logika-input-skor.md`.
