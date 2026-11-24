# 11. Logika klasemen

Kode: `app/logic/klasemen.php`. Fungsi murni `hitung_klasemen($tim, $laga, $aturan)`.

## Statistik dasar

Untuk tiap laga selesai:

- Main +1 untuk kedua tim.
- GM (skor memasukkan) dan GK (skor kemasukan) ditambahkan.
- Pemenang mendapat `poin_menang`, pecundang `poin_kalah`. Skor sama: keduanya `poin_seri`.
- Selisih = GM − GK.

## Urutan peringkat

1. **Poin**, terbesar di atas.
2. **Selisih skor**.
3. **Head-to-head** antar tim yang masih sama (dijelaskan di bawah).
4. **Total skor memasukkan**.
5. **Nilai undian** (diisi panitia).
6. **Nama**, hanya agar urutan stabil.

## Head-to-head (mini-liga)

Setelah mengurutkan berdasarkan poin dan selisih, tim yang masih sama dikelompokkan dalam satu blok. Untuk blok berisi lebih dari satu tim, dibuat **mini-liga**: hanya laga antar anggota blok yang dihitung, lalu diurutkan berdasarkan poin mini-liga dan selisih mini-liga.

Contoh dari `tests/uji-logika.php`: Dewa dan Bima sama-sama 3 poin dengan selisih −1. Dewa mengalahkan Bima 1–0 langsung, sehingga Dewa di atas Bima, walau secara abjad Bima lebih dulu.

## Tanda seri total

Setiap baris diberi `kunci` gabungan: poin, selisih, poin h2h, selisih h2h, GM, undian. Bila dua baris punya kunci sama, keduanya ditandai `terikat`. Tanda "=" muncul di tabel, dan panitia diminta mengisi nilai undian.

Peringkat tetap diurutkan lewat nama agar tampilan stabil, tetapi keputusan penting diblokir:

- "Mulai babak gugur" ditolak bila seri total jatuh di batas lolos.
- Juara liga tidak ditetapkan bila peringkat 1 dan 2 seri total.

## Catatan batasan

Pada blok berisi 3 tim atau lebih, mini-liga dihitung **sekali**, tidak diulang secara rekursif bila hasilnya memecah blok lagi. Cara ini cukup untuk turnamen sekolah, dan perilakunya konsisten. Aturan resmi liga profesional bisa berbeda, jadi aturan ini tertulis di halaman publik agar peserta tahu.

## Mengapa fungsi murni?

Masukan sama selalu menghasilkan keluaran sama, tanpa membaca database. Karena itu mudah diuji (lihat dokumen 19) dan mudah dijelaskan di depan penguji.

## Alur penggunaan

`klasemen_turnamen()` di `model.php` mengambil tim dan laga selesai tiap grup dari database, lalu memanggil `hitung_klasemen()`. Hasilnya dikirim ke `tabel_klasemen()` di `view.php`. Baris yang lolos diberi garis hijau.
