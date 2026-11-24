# 17. Tanya jawab: kode

Buka file yang disebutkan di editor saat menjawab. Penguji senang melihat langsung.

**1. Tunjukkan bagian paling penting dari kodemu.**
`simpan_skor()` di `app/model.php`, karena menyentuh skor, pemenang, bagan, status, dan log dalam satu transaksi.

**2. Bagaimana klasemen diurutkan?**
`hitung_klasemen()` di `app/logic/klasemen.php`. Pertama `usort` berdasarkan poin dan selisih. Lalu tim yang masih sama dikelompokkan dalam blok dan diurutkan ulang dengan `urutkan_blok()` memakai mini-liga head-to-head, skor memasukkan, undian, dan nama.

**3. Mengapa `usort` dengan fungsi pembanding?**
PHP memperbolehkan aturan urut majemuk. Pembanding mengembalikan nilai negatif, nol, atau positif; operator `<=>` memudahkan membandingkan angka.

**4. Bagaimana jadwal liga dibuat?**
`buat_pasangan_liga()` di `app/logic/jadwal.php` dengan metode lingkaran: satu tim tetap, sisanya berputar tiap ronde. Jumlah ganjil ditangani dengan tim kosong.

**5. Bagaimana pemenang otomatis maju ke ronde berikutnya?**
Setiap laga menyimpan `menang_ke_id` dan `menang_ke_slot`. Setelah skor disimpan, fungsi `taruh_tim()` memperbarui slot `tim_a_id` atau `tim_b_id` laga berikutnya.

**6. Mengapa bagan disimpan dari ronde tertinggi?**
Agar saat menyimpan laga ronde rendah, laga tujuannya sudah punya ID untuk dirujuk di `menang_ke_id`. Lihat `simpan_bagan()`.

**7. Apa itu `FOR UPDATE`?**
Mengunci baris selama transaksi, sehingga dua penyimpanan serentak antre, bukan saling menimpa.

**8. Apa fungsi kolom `versi`?**
Formulir membawa versi saat dibuka. `UPDATE ... WHERE id = ? AND versi = ?` hanya berhasil bila versi masih sama. Jika tidak, tidak ada baris berubah dan simpan ditolak dengan pesan jelas.

**9. Bagaimana kamu mencegah SQL injection?**
Fungsi `q()` di `app/db.php` selalu memakai prepared statement. Contoh pemakaian: `q('SELECT * FROM pengguna WHERE username = ?', [$username])`. Tidak ada data pengguna yang disambung ke teks SQL. Untuk nama kolom yang dinamis (`tim_a_id` atau `tim_b_id`) nilainya dipilih dari daftar tetap di kode, bukan dari input.

**10. Bagaimana kamu mencegah XSS?**
Setiap keluaran melewati `e()`, pembungkus `htmlspecialchars`. Warna tim divalidasi `#RRGGBB` sebelum masuk atribut style.

**11. Apa itu Post/Redirect/Get dan mengapa dipakai?**
Setelah memproses POST, server mengirim redirect ke halaman GET. Bila pengguna menekan refresh, yang dimuat ulang adalah GET, bukan kirim ulang formulir.

**12. Mengapa fungsi di `app/logic/` tidak memakai database?**
Agar murni: masukan sama, keluaran sama. Mudah diuji dan dijelaskan, dan tidak bergantung pada kondisi database.

**13. Bagaimana kamu menguji logika?**
`tests/uji-logika.php` berisi skenario dengan hasil yang sudah dihitung manual. Jalankan lewat browser atau `php tests/uji-logika.php`.

**14. Jelaskan alur satu permintaan, misalnya membuka klasemen.**
`turnamen.php` memuat `bootstrap.php`, membaca turnamen dari database, `klasemen_turnamen()` mengambil tim dan laga selesai per grup, `hitung_klasemen()` menghitung, `tabel_klasemen()` menyusun HTML, semua keluaran lewat `e()`.

**15. Mengapa `declare(strict_types)` tidak dipakai?**
Data dari formulir dan database datang sebagai string. Saya melakukan konversi dan validasi eksplisit (`bulat_dalam`, `skor_valid`). Menambahkan strict types adalah perbaikan yang masuk akal untuk lanjutan.

**16. Apa yang kamu lakukan bila ada galat tak terduga saat menyimpan?**
Transaksi di-`rollBack`, dan pengguna melihat pesan "Gagal menyimpan" tanpa data setengah tersimpan.

**17. Bagaimana kamu menangani nama tim kembar?**
Ada kunci unik `(turnamen_id, nama)`. Database melempar galat kode 23000, ditangkap, lalu diubah menjadi pesan "Nama tim itu sudah dipakai di turnamen ini."

**18. Bagian mana yang masih bisa diperbaiki?**
Contoh: file `model.php` cukup panjang dan bisa dipecah per domain (turnamen, laga, jadwal); strict types; dan uji integrasi dengan database uji.
