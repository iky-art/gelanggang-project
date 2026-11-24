# 14. Naskah demo (±3,5 menit)

Latih sampai lancar tanpa melihat catatan. Siapkan data sebelum mulai (lihat berkas 15).

## Persiapan sebelum masuk ruangan

- XAMPP Apache dan MySQL sudah hijau.
- Turnamen demo sudah dibuat dan **baru dimulai**, 8 tim, jadwal belum diisi.
- Dua tab browser: satu halaman publik (`/turnamen.php?id=...`), satu panel admin (sudah login).
- Satu tab lagi untuk akun panitia (jendela privat/incognito).

## Langkah demo

### 0:00 Halaman publik (20 detik)
"Ini tampilan penonton, tanpa login. Ada daftar turnamen, laga berikutnya, dan hasil terbaru."

### 0:20 Mulai turnamen (20 detik)
Di panel admin, buka turnamen. "Turnamen dimulai: sistem membagi tim ke dua grup dengan pola ular dan membuat 12 laga."
Tunjukkan tab **Jadwal** publik: laga sudah ada.

### 0:40 Jadwal otomatis (25 detik)
Isi waktu mulai, slot 55 menit, 2 arena, klik **Susun jadwal**. "Tiap slot memuat dua laga, dan ronde baru selalu di slot baru, sehingga satu tim tidak main dua kali bersamaan."

### 1:05 Anti bentrok (20 detik)
Buka satu laga, ubah waktu ke jam yang sama dengan laga lain yang tim-nya sama. "Ditolak, dan pesannya menyebut penyebabnya."

### 1:25 Input skor (30 detik)
Isi skor 2–1 pada satu laga. Beralih ke tab publik, muat ulang: klasemen berubah.

### 1:55 Penguncian dan koreksi (30 detik)
Di jendela panitia, buka laga tadi: "Terkunci". Kembali sebagai admin, **Koreksi** tanpa alasan → ditolak. Isi alasan → tersimpan. Buka menu **Log**, tunjukkan baris koreksinya.

### 2:25 Skor yang ditolak (15 detik)
Isi `-1` atau `100` → ditolak. Isi skor seri saat seri dimatikan → ditolak.

### 2:40 Selesaikan grup, mulai gugur (30 detik)
Gunakan turnamen kedua yang laga grupnya sudah hampir selesai (lihat berkas 15). Selesaikan laga terakhir, klik **Mulai babak gugur**. Tunjukkan bagan: "Ronde 1 tidak mempertemukan tim satu grup."

### 3:10 Juara (20 detik)
Isi semifinal dan final. Pemenang otomatis maju. Tunjukkan banner juara.

## Kalimat penutup demo

"Itu alur dari pendaftaran sampai juara. Selanjutnya saya jelaskan logika di balik beberapa bagian tadi."

## Bila ada yang gagal saat demo

Tetap tenang, buka `21-rencana-cadangan.md`. Jangan mengulang dari awal; lompat ke langkah berikutnya dan pakai tangkapan layar.

## Larangan

- Jangan membuat data baru dari nol saat demo (terlalu lama).
- Jangan menunjukkan kata sandi saat mengetik.
- Jangan membuka halaman yang belum kamu uji.
