# Situs resmi Gelanggang

Halaman statis (HTML, CSS, dan JavaScript murni, tanpa build) yang memperkenalkan Gelanggang: video demo, fitur, cara kerja logika, kalkulator bagan dan jadwal liga, cara pasang, dan tautan dokumen.

Aplikasi Gelanggang sendiri (PHP + MySQL) **tidak bisa dihosting di sini**. Situs ini hanya etalase; aplikasinya dijalankan lokal.

## Isi

| Berkas | Fungsi |
|---|---|
| `index.html` | Seluruh halaman |
| `assets/style.css` | Gaya (terang dan gelap mengikuti pengaturan perangkat) |
| `assets/app.js` | Kalkulator bagan dan liga, tombol salin, hitung naik pada papan skor |
| `assets/favicon.svg` | Ikon |
| `404.html` | Halaman galat |
| `media/demo.mp4` | **Tidak ada di folder ini.** Disalin dari `video/demo.mp4` saat deploy |

## Melihat di komputer

Buka `index.html` langsung di peramban, atau jalankan server statis:

```
cd situs
python3 -m http.server 8000
```

Lalu buka `http://localhost:8000`. Video baru tampil bila kamu menyalin `video/demo.mp4` ke `situs/media/demo.mp4`; tanpa itu halaman menampilkan pesan bahwa video belum tersedia.

## Deploy ke GitHub Pages

1. Di GitHub: **Settings → Pages → Build and deployment → Source: GitHub Actions**.
2. Push ke `main` (perubahan di `situs/` memicu workflow `Deploy situs`), atau jalankan manual dari tab **Actions → Deploy situs → Run workflow**.
3. Alamat biasanya `https://iky-art.github.io/gelanggang-project/`. Alamat sebenarnya tampil di hasil workflow.

Workflow menyalin `video/demo.mp4` ke `media/demo.mp4` di hasil deploy, dan otomatis jalan lagi setelah workflow `Render video demo` selesai.

## Mengubah isi

- Tautan repo: cari `iky-art/gelanggang-project` di `index.html`.
- Warna: variabel di bagian atas `assets/style.css`.
- Logika kalkulator: bagian atas `assets/app.js`. Fungsinya bisa diuji di Node: `node -e "console.log(require('./assets/app.js').hitungBagan(6))"`.

## Catatan

- Tidak ada font eksternal, pelacak, atau pustaka JavaScript.
- Tidak ada gambar OG untuk pratinjau tautan; tambahkan `og:image` bila diperlukan.
