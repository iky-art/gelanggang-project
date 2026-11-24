# 14. Logika input skor

Kode: `simpan_skor()` di `app/model.php`. Ini fungsi paling penting karena menyentuh banyak tabel sekaligus.

## Tujuan

Skor harus: valid, tidak bisa tertimpa diam-diam, tercatat siapa yang mengubah, dan memajukan bagan dengan benar. Kalau salah satu gagal, **tidak ada yang berubah**.

## Langkah-langkah

1. **Validasi masukan.** Skor harus bilangan bulat 0 sampai 99 (`skor_valid`).
2. **Mulai transaksi** dan kunci baris laga serta turnamen (`SELECT ... FOR UPDATE`). Dua panitia yang menyimpan di waktu bersamaan akan antre, bukan saling menimpa.
3. **Cek aturan:**
   - kedua tim sudah ada, dan laga bukan bye;
   - turnamen sudah dimulai;
   - **versi** laga di formulir sama dengan versi di database (anti konflik);
   - laga yang sudah terkunci hanya boleh diubah admin dalam mode koreksi, dengan alasan wajib;
   - hasil babak grup dibekukan setelah babak gugur dimulai (format grup + gugur).
4. **Tentukan pemenang:**
   - skor tidak sama → pemenang dari skor;
   - skor sama di grup → diperbolehkan hanya bila opsi seri aktif, pemenang kosong;
   - skor sama di gugur → wajib memilih pemenang.
5. **Cek koreksi bagan.** Bila hasil lama diubah dan pemenang berubah, laga tujuan (pemenang dan pecundang) tidak boleh sudah selesai.
6. **Simpan** dengan `UPDATE ... WHERE id = ? AND versi = ?`. Jika 0 baris berubah, transaksi dibatalkan.
7. **Majukan bagan** (babak gugur): pemenang ke `menang_ke_id`, pecundang ke `kalah_ke_id` bila ada.
8. **Perbarui status turnamen** dan juara.
9. **Catat log**, termasuk nilai lama, nilai baru, dan alasan koreksi.
10. **Commit.** Bila ada kesalahan di langkah mana pun, `rollBack`.

## Penguncian

Setelah skor pertama disimpan, `dikunci = 1`. Panitia melihat tulisan "Terkunci". Admin melihat tombol "Koreksi" dan wajib mengisi alasan. Alasan masuk ke log aktivitas.

Mengapa dikunci? Skor yang bisa diubah bebas membuka peluang kecurangan dan sengketa. Dengan kunci dan log, setiap perubahan bisa ditelusuri.

## Versi (optimistic locking)

Setiap perubahan laga menaikkan `versi`. Formulir membawa versi saat dibuka. Contoh masalah yang dicegah:

1. Panitia A dan B membuka form laga yang sama.
2. A menyimpan 2–1. Versi naik.
3. B menyimpan 1–2 dengan versi lama. Ditolak: "Data laga sudah diubah orang lain."

## Mengapa transaksi?

Bayangkan skor tersimpan tetapi pemenang gagal masuk ke final karena error. Tanpa transaksi, bagan menjadi rusak. Dengan transaksi, kedua langkah berhasil bersama atau gagal bersama.

## Pesan galat

Semua penolakan memakai bahasa jelas ("Skor sudah dikunci. Hanya admin yang bisa mengoreksi, dengan alasan."), bukan kode teknis.

## Contoh skenario koreksi

Semifinal 1 selesai, A menang lawan B. Final belum dimainkan. Admin menemukan salah input: ternyata B menang.

- Koreksi diizinkan, karena final belum selesai.
- Slot final berganti dari A ke B, dan pecundang pada juara 3 ikut berganti.
- Log mencatat "2-1 → 1-2 (koreksi: ...)".

Jika final sudah selesai, koreksi ditolak sampai final dikoreksi dulu.
