# 19. Analogi untuk menjelaskan dengan sederhana

Pakai analogi ketika penguji meminta "jelaskan dengan bahasa awam" atau ketika kamu bingung merangkai kata.

## Transaksi database
**Seperti transfer uang antar rekening.** Uang keluar dari rekening A dan masuk ke rekening B harus terjadi bersama. Jika uang keluar tetapi tidak masuk, transfer dibatalkan seluruhnya. Begitu juga simpan skor: skor, pemenang, dan bagan berubah bersama atau tidak sama sekali.

## Nomor versi (optimistic locking)
**Seperti dua orang mengedit dokumen yang sama dari salinan lama.** Setiap kali dokumen disimpan, nomor revisinya naik. Orang kedua yang masih memegang revisi lama diberi tahu bahwa dokumen sudah berubah, sehingga ia tidak menimpa pekerjaan orang pertama.

## Prepared statement
**Seperti formulir dengan kotak isian.** Kalimat SQL ditulis dulu dengan kotak kosong, lalu data dimasukkan ke kotak. Data tidak pernah bisa mengubah isi kalimatnya.

## Token CSRF
**Seperti nomor antrean yang hanya kamu pegang.** Server hanya melayani formulir yang membawa nomor yang ia berikan kepadamu. Situs lain tidak tahu nomormu.

## Hash kata sandi
**Seperti menghancurkan kunci menjadi serbuk dan menyimpan serbuknya.** Kita tidak bisa mengembalikan kunci dari serbuk, tetapi kita bisa mengecek apakah kunci yang dibawa menghasilkan serbuk yang sama.

## Metode lingkaran (jadwal liga)
**Seperti meja bundar dengan satu kursi tetap.** Satu orang duduk diam, yang lain bergeser satu kursi tiap ronde. Lawan tiap orang berubah, dan setelah semua bergeser, semua sudah bertemu semua.

## Pola ular (pembagian grup)
**Seperti memilih anggota tim saat main bola di lapangan.** Kapten A memilih pemain terbaik, kapten B memilih dua berikutnya, lalu bergantian arah, supaya kekuatan kedua tim seimbang.

## Seed dan bye
**Seperti undangan lomba lari.** Pelari tercepat tidak langsung berhadapan dengan sesama pelari tercepat di babak awal. Bila peserta kurang, pelari terbaik dapat "tiket langsung" ke babak berikutnya. Itu bye.

## Head-to-head
**Seperti dua tim yang sama-sama 3 kemenangan, lalu dilihat siapa yang menang saat mereka bertemu.** Hanya pertemuan mereka berdua yang dihitung.

## Logika murni vs model
**Seperti kalkulator dan buku kas.** Kalkulator (logika murni) hanya menghitung, tidak mencatat. Buku kas (model/database) mencatat hasilnya. Memisahkan keduanya membuat kalkulator mudah dites.

## Post/Redirect/Get
**Seperti kasir yang memberi struk setelah bayar.** Setelah kamu bayar (POST), kamu diberi struk dan diarahkan ke halaman lain (GET). Kalau kamu menekan refresh, yang dimuat adalah halaman struk, bukan membayar lagi.

## Log aktivitas
**Seperti CCTV catatan.** Bukan untuk mencurigai, melainkan agar setiap perubahan penting bisa ditelusuri bila ada perselisihan.

## Cara memakai analogi

1. Jawab pertanyaan dengan istilah teknis dulu (satu kalimat).
2. Tambahkan analogi bila penguji tampak butuh penjelasan lebih sederhana.
3. Kembali ke kode: "Di aplikasi saya, ini ada di `simpan_skor()`."
