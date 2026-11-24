# 19. Pengujian

## Status verifikasi (baca ini dulu)

Dokumen ini jujur tentang apa yang sudah dan belum diperiksa saat proyek disusun.

| Hal | Status |
|---|---|
| Algoritma klasemen, pasangan liga, seed bagan, penyusunan waktu | **Sudah diperiksa** lewat prototipe terpisah dengan skenario berhitung manual. Nilai harapan di `tests/uji-logika.php` berasal dari sana |
| Kode PHP seluruh aplikasi | **Belum dijalankan.** Lingkungan penyusun tidak punya PHP maupun MySQL. Kode ditulis hati-hati dan keseimbangan kurung dicek, tetapi kesalahan ketik atau sintaks masih mungkin ada |
| Query SQL dan skema | **Belum dijalankan** pada MySQL sungguhan |
| Tampilan Bootstrap | **Belum dilihat.** File Bootstrap tidak ikut terbundel (dokumen 03) |
| Docker | **Belum dibangun** |

Artinya: **jalankan uji di bawah ini di XAMPP sebelum hari ujian**, dan perbaiki bila ada galat. Jangan mengklaim "sudah teruji penuh" di depan penguji. Klaim yang aman: "logika inti sudah diuji otomatis; aplikasi diuji manual dengan skenario di dokumen ini."

## Uji otomatis (logika inti)

Tanpa database. Dua cara:

- Browser: `http://localhost/gelanggang/tests/uji-logika.php`
- Terminal: `php tests/uji-logika.php`

Hasil yang diharapkan: seluruh baris hijau, "xx dari xx uji lulus". Cakupan:

- urutan seed bagan (2, 8, 16);
- pasangan liga (jumlah, keunikan, tidak ada tim main dua kali per ronde, ganjil, pulang-pergi);
- pembagian grup pola ular;
- klasemen dasar, head-to-head, lingkaran tiga tim, seri total, nilai undian;
- bagan 3, 4, 6 tim, bye, juara 3, penolakan 1 tim;
- seed dari 4 grup dan tidak ada pertemuan satu grup;
- penyusunan waktu dan deteksi bentrok;
- validasi skor, waktu, warna, kata sandi.

Bila ada yang merah, baca nama ujinya: itu langsung menunjukkan fungsi yang bermasalah.

## Uji manual (daftar periksa)

Kerjakan berurutan di XAMPP. Centang saat lulus.

### Dasar
- [ ] Impor `gelanggang.sql` berhasil, 8 tabel terbentuk.
- [ ] `localhost/gelanggang/` tampil tanpa galat, tanpa peringatan Bootstrap.
- [ ] Setup membuat admin; setup tertutup setelahnya.
- [ ] Login benar masuk; login salah menampilkan pesan umum; 5 kali salah mengunci.
- [ ] Logout memutus sesi; membuka `/admin/` tanpa login diarahkan ke login.

### Turnamen dan tim
- [ ] Buat turnamen format grup + gugur dengan poin 1/3/0 → ditolak (menang harus lebih besar dari seri).
- [ ] Nama turnamen 2 huruf → ditolak.
- [ ] Tambah 8 tim; nama kembar ditolak.
- [ ] Mulai dengan 3 tim, 2 grup, lolos 2 → ditolak dengan pesan jumlah tim.
- [ ] Mulai dengan 8 tim → 12 laga grup terbentuk; tim tidak bisa ditambah lagi.

### Skor dan klasemen
- [ ] Isi skor 2–1 → klasemen publik berubah, laga terkunci.
- [ ] Panitia membuka laga terkunci → hanya melihat "Terkunci".
- [ ] Admin koreksi tanpa alasan → ditolak; dengan alasan → tersimpan dan ada di log.
- [ ] Skor seri saat opsi seri mati → ditolak.
- [ ] Skor `-1`, `abc`, `100` → ditolak.
- [ ] Dua tab browser: simpan di tab 1, lalu simpan laga sama di tab 2 → tab 2 ditolak (versi).

### Bagan
- [ ] Selesaikan semua laga grup → tombol babak gugur berhasil.
- [ ] Buat seri total di batas lolos → ditolak dengan petunjuk undian; isi undian → berhasil.
- [ ] Bagan ronde 1 tidak mempertemukan tim satu grup.
- [ ] Skor semifinal membuat pemenang muncul di final.
- [ ] Skor seri di gugur tanpa memilih pemenang → ditolak.
- [ ] Koreksi semifinal setelah final selesai → ditolak.
- [ ] Final selesai → banner juara muncul, status "Selesai".

### Jadwal
- [ ] Susun otomatis 2 arena → tiap slot maksimal 2 laga, ronde baru di slot baru.
- [ ] Atur manual dua laga dengan tim sama dalam 45 menit → ditolak.
- [ ] Atur dua laga arena sama dalam 45 menit → ditolak.

### Keamanan dan peran
- [ ] Panitia membuka `/admin/pengguna.php` → "Akses ditolak".
- [ ] Nama tim `<b>uji</b>` tampil sebagai teks.
- [ ] Mengirim formulir tanpa token (hapus kolom `_csrf` lewat inspeksi) → ditolak.

### Tampilan
- [ ] Lebar layar 360 px: tidak ada gulir horizontal pada halaman, kecuali tabel dan bagan yang menggulir sendiri.
- [ ] Navigasi bisa dioperasikan dengan keyboard (Tab, Enter), fokus terlihat.
- [ ] Cetak (`Ctrl+P`) menyembunyikan navigasi dan tombol.

## Mencatat hasil

Gunakan lembar bukti di `presentasi/22-lembar-bukti-pengujian.md`. Tangkapan layar uji otomatis dan beberapa uji manual membuat presentasi jauh lebih meyakinkan.

## Bila ada galat

Aktifkan tampilan galat PHP sementara: di `php.ini` set `display_errors=On`, restart Apache, ulangi langkah, dan baca pesannya. Lihat dokumen 20.
