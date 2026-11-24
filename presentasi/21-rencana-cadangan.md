# 21. Rencana cadangan

Masalah bisa terjadi di hari ujian. Tenang, ikuti tabel ini.

| Masalah | Tindakan cepat | Jika masih gagal |
|---|---|---|
| Apache tidak mau start (port 80) | Tutup program lain (Skype/IIS), start ulang | Pakai port 8080 (docs/04), atau demo dari tangkapan layar |
| MySQL tidak mau start | Start ulang dari XAMPP | Pakai cadangan: pindahkan folder `mysql\data` yang lama, atau ekspor SQL ke laptop lain |
| "Database belum siap" | Cek config dan nama database | Impor ulang `gelanggang.sql` (+ `data_contoh.sql`) |
| Tampilan berantakan | File Bootstrap hilang. Salin ulang ke `assets/vendor/` | Lanjutkan demo; tampilan buruk tapi fungsi jalan, jelaskan penyebabnya |
| Lupa sandi admin | Kosongkan tabel `pengguna`, buka `setup.php` (docs/20) | Gunakan akun lain yang sudah disiapkan |
| Layar putih | Aktifkan `display_errors`, baca pesan | Buka tangkapan layar dan jelaskan alurnya |
| Data demo kacau | Hapus turnamen, impor ulang `data_contoh.sql` | Gunakan turnamen kedua yang sudah disiapkan |
| Satu fitur galat saat demo | Jangan panik, jangan debug di depan penguji | Lewati ke langkah berikutnya, tunjukkan tangkapan layar atau video |
| Laptop mati | Siapkan laptop kedua atau pakai laptop sekolah dengan flashdisk | Presentasikan dari PDF dan video demo |
| Proyektor tidak cocok | Ubah resolusi atau mode duplikat | Presentasi dari layar laptop dekat penguji |
| Pertanyaan yang tidak kamu tahu | Jujur: "Itu belum saya uji, saya perkirakan [..] dan akan saya cek" | Catat sebagai bahan pengembangan |

## Prinsip

1. **Jangan debug di depan penguji** kecuali diminta. Catat masalahnya, lanjutkan, dan perbaiki setelahnya.
2. **Selalu punya bukti alternatif**: tangkapan layar dan video demo.
3. **Jujur lebih baik daripada mengarang.** "Saya belum menguji skenario itu" lebih dihargai daripada jawaban yang salah.
4. **Tenang dan tersenyum.** Masalah teknis dianggap wajar. Reaksimu yang dinilai.

## Paket cadangan (siapkan di satu folder)

```
cadangan/
├── gelanggang-folder/        salinan proyek
├── gelanggang-2026-xx-xx.sql ekspor database
├── slide.pdf
├── demo.mp4
├── tangkapan-layar/
└── xampp-portable (opsional)
```

## Ekspor dan impor cadangan database

- Ekspor: phpMyAdmin → pilih `gelanggang` → **Ekspor** → format SQL → **Kirim**.
- Impor: phpMyAdmin → **Impor** → pilih berkas → **Kirim**. Database lama akan ditimpa karena berkas berisi `DROP TABLE IF EXISTS`.
