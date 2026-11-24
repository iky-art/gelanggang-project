# Slide 08: Logika klasemen

**Waktu:** 45 detik (bersama slide 09: 1,5 menit total)

## Tampil di layar

> ## Urutan peringkat
> 1. Poin
> 2. Selisih skor
> 3. Head-to-head
> 4. Total skor memasukkan
> 5. Nilai undian
>
> Seri total → ditandai "=" dan keputusan diblokir sampai panitia mengisi undian

## Naskah

"Klasemen diurutkan berdasarkan poin, lalu selisih skor. Kalau masih sama, dipakai head-to-head: hanya pertandingan di antara tim-tim yang sama itu yang dihitung. Kalau masih sama, total skor memasukkan, lalu nilai undian. Kalau semuanya sama, tim ditandai seri total, dan sistem menolak menentukan siapa yang lolos sampai panitia mengisi nilai undian. Jadi aplikasi tidak memutuskan secara diam-diam."

## Contoh yang bisa disebut (dari uji otomatis)

Dewa dan Bima sama-sama 3 poin dan selisih −1. Dewa menang langsung 1–0 atas Bima, jadi Dewa di atas, walau secara abjad Bima lebih dulu.

## Bukti

- Tampilkan halaman klasemen publik dengan baris lolos berwarna hijau.
- Buka `app/logic/klasemen.php`, fungsi `hitung_klasemen()`.
- Jalankan `tests/uji-logika.php`, tunjukkan baris "head-to-head".

## Batasan yang jujur

Mini-liga dihitung sekali (tidak rekursif). Ini tertulis di dokumen 11 dan pada halaman klasemen.
