# 16. Tanya jawab: konsep dan keputusan

Jawab dengan kata-katamu sendiri. Contoh jawaban di bawah hanya kerangka.

**1. Mengapa memilih topik turnamen e-sport?**
Karena ada logika yang nyata: peringkat dengan tie-break, bagan dengan bye, dan jadwal tanpa bentrok. CRUD biasa saja tidak menunjukkan kemampuan menyusun algoritma.

**2. Siapa penggunanya dan apa bedanya?**
Penonton hanya melihat. Panitia mengelola dan mengisi skor pertama kali. Admin juga bisa mengoreksi skor terkunci, menghapus turnamen, mengelola pengguna, dan melihat log. Pemisahan ini dijaga di server.

**3. Mengapa tanpa framework?**
Ketentuan menggunakan PHP, dan tanpa framework setiap alur terlihat jelas: dari formulir, validasi, sampai query. Konsekuensinya saya harus menulis sendiri pengamanan seperti CSRF dan validasi, dan itu saya lakukan di `helpers.php` dan `validasi.php`. Dengan framework seperti Laravel, banyak bagian itu sudah tersedia, dan itu pilihan yang saya pertimbangkan untuk pengembangan lanjut.

**4. Mengapa semuanya offline?**
Laptop ujian tidak tersambung internet. Karena itu Bootstrap disimpan lokal, ikon memakai SVG inline, dan tidak ada font web atau API luar.

**5. Bagaimana kalau jumlah tim ganjil?**
Di liga, satu tim istirahat tiap ronde (tim kosong di metode lingkaran). Di bagan gugur, tim mendapat bye hingga ukuran bagan menjadi pangkat dua.

**6. Bagaimana kalau dua tim seri total?**
Sistem menandainya dan memblokir keputusan penting (lolos atau juara) sampai panitia mengisi nilai undian. Aplikasi tidak memilih secara diam-diam.

**7. Mengapa skor dikunci?**
Agar tidak ada perubahan tanpa jejak. Koreksi hanya oleh admin dan wajib beralasan, lalu tercatat di log.

**8. Apa yang terjadi kalau skor salah input?**
Admin memakai Koreksi dengan alasan. Jika pemenang berubah dan laga berikutnya sudah selesai, koreksi ditolak sampai laga berikutnya dikoreksi dulu, supaya bagan tetap konsisten.

**9. Apakah bisa dipakai banyak pengguna bersamaan?**
Ya, untuk skala sekolah. Konflik pengubahan laga yang sama dicegah dengan kunci baris dan nomor versi. Aplikasi belum diuji beban untuk ratusan pengguna sekaligus.

**10. Apa kekurangan aplikasi ini?**
Belum ada format Swiss, skor per game untuk Bo3, dan logo tim. Pemulihan sandi tanpa email. Head-to-head dihitung sekali, tidak rekursif. Semua ada di `docs/23-keterbatasan-dan-pengembangan.md`.

**11. Apa yang paling sulit?**
Pilih satu yang benar-benar kamu alami. Contoh: menjaga bagan tetap konsisten saat skor dikoreksi, karena pemenang sudah berpindah ke laga berikutnya. Solusinya aturan penolakan bila laga berikutnya sudah selesai dan semua perubahan dalam satu transaksi.

**12. Apa yang kamu pelajari?**
Contoh: memisahkan logika murni dari database sehingga bisa diuji; pentingnya transaksi; dan menulis pesan galat yang menjelaskan penyebabnya.

**13. Bagaimana pengembangan selanjutnya?**
Ekspor CSV, tampilan layar proyektor, skor per game, dan format Swiss. Strukturnya sudah memisahkan logika, sehingga format baru cukup menambah fungsi di `app/logic/bagan.php`.

**14. Bagaimana jika ada kesalahan hitung klasemen?**
Logikanya murni dan punya uji otomatis. Bila ada kasus baru, saya tambahkan sebagai uji, perbaiki fungsi, dan pastikan semua uji lama tetap lulus.
