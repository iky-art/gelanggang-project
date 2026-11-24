# 18. Tanya jawab: database dan keamanan

**1. Mengapa memilih InnoDB?**
Mendukung transaksi dan foreign key, yang dibutuhkan untuk input skor dan penghapusan berantai.

**2. Apa itu normalisasi dan bagaimana tabelmu?**
Data dipecah agar tidak duplikat: tim di tabel tim, pemain di tabel pemain, pertandingan menunjuk tim lewat ID. Nama tim tidak disalin ke pertandingan. Perkecualiannya hasil turunan (klasemen) yang tidak disimpan, tetapi dihitung dari laga setiap kali dibuka, supaya tidak pernah tidak sinkron.

**3. Mengapa klasemen tidak disimpan di tabel?**
Klasemen adalah hasil hitungan dari laga. Menyimpannya berarti harus menyinkronkannya setiap skor berubah atau dikoreksi, dan itu sumber kesalahan. Dihitung ulang sudah cukup cepat untuk skala sekolah.

**4. Jelaskan relasi antar tabelmu.**
Turnamen memiliki banyak grup, tim, dan pertandingan. Grup memiliki banyak tim. Tim memiliki banyak pemain dan terlibat dalam banyak pertandingan sebagai A atau B. Semua relasi memakai foreign key.

**5. Apa yang terjadi bila turnamen dihapus?**
Grup, tim, pemain, dan pertandingannya ikut terhapus (`ON DELETE CASCADE`). Hanya admin yang bisa, dengan konfirmasi.

**6. Mengapa `menang_ke_id` tidak pakai foreign key?**
Referensi ke tabel yang sama mempersulit penghapusan berantai. Konsistensinya dijaga oleh kode yang membuat bagan dalam satu transaksi.

**7. Bagaimana kata sandi disimpan?**
Sebagai hasil `password_hash` (bcrypt) dengan garam otomatis. Tidak ada kata sandi polos di database atau di kode. Saat login dipakai `password_verify`.

**8. Mengapa tidak ada akun admin bawaan?**
Akun bawaan seperti admin/admin123 adalah celah paling umum. Akun pertama dibuat lewat `setup.php` dan halaman itu menutup diri setelahnya.

**9. Bagaimana mencegah brute force login?**
Percobaan gagal dicatat di `percobaan_login`. Lima kali gagal untuk username dan IP yang sama dalam 15 menit akan dikunci.

**10. Apa itu CSRF dan bagaimana mencegahnya?**
Serangan yang membuat browser korban mengirim formulir ke aplikasi tanpa disadari. Setiap formulir membawa token acak yang tersimpan di sesi, dan server membandingkannya dengan `hash_equals`.

**11. Apa itu session fixation dan bagaimana dicegah?**
Penyerang memaksa korban memakai ID sesi tertentu. Saya memanggil `session_regenerate_id(true)` setiap login berhasil.

**12. Bagaimana otorisasi dijaga?**
Setiap halaman admin memanggil `wajib_peran(...)` di awal. Menyembunyikan tombol saja tidak cukup, karena orang bisa membuka URL langsung.

**13. Bagaimana mencegah penghapusan semua admin?**
Admin tidak bisa menghapus, menonaktifkan, atau menurunkan dirinya sendiri, dan harus selalu tersisa minimal satu admin aktif.

**14. Apa itu `Content-Security-Policy` yang kamu pasang?**
Header yang membatasi dari mana skrip dan gaya boleh dimuat. Saya membatasi ke asal sendiri, sehingga skrip dari luar tidak dijalankan.

**15. Bagaimana cadangan data?**
Ekspor database lewat phpMyAdmin (tab Ekspor, format SQL). Simpan berkas bertanggal.

**16. Apa yang kamu lakukan jika database rusak saat ujian?**
Impor ulang `gelanggang.sql` dan `data_contoh.sql`; atau pakai cadangan ekspor yang sudah disiapkan. Lihat `21-rencana-cadangan.md`.

**17. Apa itu indeks dan apa yang kamu indeks?**
Indeks mempercepat pencarian. Saya membuat indeks pada `pertandingan (turnamen_id, fase, ronde, urutan)` untuk daftar laga, dan pada `waktu` untuk pengecekan bentrok.

**18. Bagaimana log aktivitas dijaga agar tidak diubah?**
Aplikasi tidak menyediakan fitur ubah atau hapus log. Batasan: orang dengan akses langsung ke database masih bisa mengubahnya, jadi di lingkungan nyata dibutuhkan pengaturan hak akses database.

**19. Apakah ini aman untuk internet publik?**
Belum disiapkan untuk itu. Perlu HTTPS, kata sandi database khusus, penghapusan `siapkan-aset.php` dan `tests/`, dan audit keamanan. Dirancang untuk jaringan lokal.

**20. Bagaimana kamu memastikan input tanggal valid?**
`waktu_valid()` memakai `DateTime::createFromFormat` dan memeriksa bahwa format ulang hasilnya sama persis, sehingga tanggal mustahil seperti 30 Februari ditolak.
