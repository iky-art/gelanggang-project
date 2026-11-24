# 21. Kustomisasi

## Mengganti nama aplikasi
`app/config.php`, kunci `app.nama`. Nama ini muncul di judul halaman dan footer. Teks "Gelanggang" pada logo ikut berubah karena membaca nilai yang sama.

## Mengganti warna
Semua warna ada sebagai variabel di bagian atas `assets/css/app.css`:

```css
--biru: #2F6BFF;      /* warna utama, tombol utama */
--sun: #FFE08A;       /* penanda menu aktif, kapten */
--mint: #C6F1E0;      /* lolos, pemenang */
--koral: #FFD3CB;     /* peringatan lembut */
--ink: #1B2540;       /* teks dan garis */
```

Setelah mengubah, periksa kontras: teks di atas warna latar sebaiknya minimal rasio 4,5:1.

## Mengubah skema poin bawaan
Per turnamen, lewat formulir. Untuk mengganti nilai bawaan formulir baru, ubah `$d` di `admin/turnamen.php` (cari `'poin_menang' => 3`) dan `DEFAULT` pada `database/gelanggang.sql`.

## Mengubah jumlah pemain maksimum
`app/config.php`, kunci `app.maks_pemain_per_tim`.

## Mengubah zona waktu
`app/config.php`, kunci `app.zona_waktu`, dan offset pada `SET time_zone` di `app/db.php`.

## Menambah game atau peran pemain
Nama game dan peran adalah teks bebas, jadi tidak perlu diubah di kode. Untuk daftar pilihan tetap, ganti input dengan `<select>` pada `admin/turnamen.php` dan `admin/pemain.php`.

## Menambah kolom pada tabel
1. Tambahkan kolom di `database/gelanggang.sql` (dan jalankan `ALTER TABLE` pada database yang sudah ada).
2. Tambahkan field pada formulir, validasi, dan query `INSERT`/`UPDATE` halaman terkait.
3. Tampilkan di `view.php` atau halaman publik.

## Menambah bahasa Inggris
Teks tersebar langsung di halaman (belum ada berkas terjemahan). Pendekatan paling sederhana: salin proyek, terjemahkan teks. Untuk dukungan dua bahasa sungguhan, kumpulkan teks ke larik `app/lang/id.php` dan `en.php` serta fungsi `t('kunci')`.

## Mengganti logo
Logo dibuat dengan CSS (`.merek-tanda`). Untuk memakai gambar, letakkan berkas di `assets/img/` dan ganti elemen `<span class="merek-tanda">` pada `app/layout.php`.

## Mengubah tampilan bagan
Gaya ada di bagian "Bagan gugur" pada `app.css`; markup di fungsi `bagan_html()` pada `app/view.php`.

## Menambahkan format baru (misal Swiss atau double elimination)
Logika bagan murni ada di `app/logic/bagan.php`. Format baru berarti: fungsi pembuat laga baru, nilai enum baru pada `turnamen.format`, dan percabangan pada `mulai_turnamen()`. Mulailah dengan menulis uji di `tests/uji-logika.php`.
