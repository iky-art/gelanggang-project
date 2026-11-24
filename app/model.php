<?php
/**
 * Lapisan data & aturan bisnis turnamen.
 * Fungsi di sini boleh menyentuh database; hitungan murninya ada di app/logic/.
 */

function hasil(bool $ok, string $pesan): array
{
    return ['ok' => $ok, 'pesan' => $pesan];
}

function ambil_turnamen(int $id): ?array
{
    return ambil_satu('SELECT * FROM turnamen WHERE id = ?', [$id]);
}

function aturan_poin(array $t): array
{
    return [
        'poin_menang' => (int)$t['poin_menang'],
        'poin_seri'   => (int)$t['poin_seri'],
        'poin_kalah'  => (int)$t['poin_kalah'],
    ];
}

function daftar_tim(int $turnamenId): array
{
    return ambil_semua(
        'SELECT t.*, g.nama AS nama_grup, (SELECT COUNT(*) FROM pemain p WHERE p.tim_id = t.id) AS jumlah_pemain
           FROM tim t LEFT JOIN grup g ON g.id = t.grup_id
          WHERE t.turnamen_id = ? ORDER BY t.nama',
        [$turnamenId]
    );
}

const SQL_LAGA = 'SELECT m.*, a.nama AS nama_a, a.singkatan AS sing_a, a.warna AS warna_a,
                         b.nama AS nama_b, b.singkatan AS sing_b, b.warna AS warna_b,
                         g.nama AS nama_grup, t.nama AS nama_turnamen
                    FROM pertandingan m
                    JOIN turnamen t ON t.id = m.turnamen_id
                    LEFT JOIN tim a ON a.id = m.tim_a_id
                    LEFT JOIN tim b ON b.id = m.tim_b_id
                    LEFT JOIN grup g ON g.id = m.grup_id';

function laga_turnamen(int $turnamenId, ?string $fase = null): array
{
    $sql = SQL_LAGA . ' WHERE m.turnamen_id = ?';
    $p = [$turnamenId];
    if ($fase !== null) {
        $sql .= ' AND m.fase = ?';
        $p[] = $fase;
    }
    return ambil_semua($sql . ' ORDER BY m.fase, m.ronde, m.urutan, m.id', $p);
}

function ambil_laga(int $id): ?array
{
    return ambil_satu(SQL_LAGA . ' WHERE m.id = ?', [$id]);
}

/** Klasemen semua grup: [['grup'=>row,'baris'=>[...]], ...] */
function klasemen_turnamen(array $t): array
{
    $hasil = [];
    foreach (ambil_semua('SELECT * FROM grup WHERE turnamen_id = ? ORDER BY nama', [$t['id']]) as $g) {
        $tim  = ambil_semua('SELECT id, nama, singkatan, warna, undian FROM tim WHERE grup_id = ?', [$g['id']]);
        $laga = ambil_semua("SELECT tim_a_id, tim_b_id, skor_a, skor_b FROM pertandingan WHERE grup_id = ? AND fase = 'grup' AND status = 'selesai'", [$g['id']]);
        $baris = hitung_klasemen($tim, $laga, aturan_poin($t));
        $info = [];
        foreach ($tim as $x) {
            $info[(int)$x['id']] = $x;
        }
        foreach ($baris as $i => $b) {
            $baris[$i]['warna'] = $info[$b['id']]['warna'];
            $baris[$i]['singkatan'] = $info[$b['id']]['singkatan'];
        }
        $hasil[] = ['grup' => $g, 'baris' => $baris];
    }
    return $hasil;
}

function progres_laga(int $turnamenId): array
{
    $r = ambil_satu(
        "SELECT SUM(bye = 0) AS total, SUM(bye = 0 AND status = 'selesai') AS selesai FROM pertandingan WHERE turnamen_id = ?",
        [$turnamenId]
    );
    return ['total' => (int)($r['total'] ?? 0), 'selesai' => (int)($r['selesai'] ?? 0)];
}

/* ---------- Penempatan tim pada slot laga berikutnya ---------- */
function taruh_tim(int $idLaga, string $slot, ?int $idTim): void
{
    $kol = $slot === 'a' ? 'tim_a_id' : 'tim_b_id';
    $now = ambil_satu("SELECT $kol AS t FROM pertandingan WHERE id = ?", [$idLaga]);
    if ($now && (int)$now['t'] === (int)$idTim) {
        return;
    }
    q("UPDATE pertandingan SET $kol = ?, versi = versi + 1 WHERE id = ?", [$idTim, $idLaga]);
}

/* ---------- Menyimpan bagan hasil buat_bagan() ---------- */
function simpan_bagan(int $turnamenId, array $bagan): void
{
    $laga = $bagan['laga'];
    usort($laga, function ($x, $y) {
        return [$y['ronde'], $x['urutan']] <=> [$x['ronde'], $y['urutan']];
    });
    $peta = [];
    foreach ($laga as $l) {
        q(
            'INSERT INTO pertandingan (turnamen_id, fase, ronde, urutan, label, tim_a_id, tim_b_id, bye, menang_ke_id, menang_ke_slot, kalah_ke_id, kalah_ke_slot)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
            [
                $turnamenId, 'gugur', $l['ronde'], $l['urutan'], $l['label'], $l['a'], $l['b'], $l['bye'] ? 1 : 0,
                $l['menang_ke'] ? $peta[$l['menang_ke']] : null, $l['menang_slot'],
                $l['kalah_ke'] ? $peta[$l['kalah_ke']] : null, $l['kalah_slot'],
            ]
        );
        $peta[$l['kunci']] = (int)db()->lastInsertId();
    }
    foreach ($laga as $l) {
        if ($l['bye']) {
            $pemenang = $l['a'] ?? $l['b'];
            q("UPDATE pertandingan SET status = 'selesai', pemenang_id = ?, dikunci = 1 WHERE id = ?", [$pemenang, $peta[$l['kunci']]]);
            if ($l['menang_ke']) {
                taruh_tim($peta[$l['menang_ke']], $l['menang_slot'], (int)$pemenang);
            }
        }
    }
}

function urutkan_seed_tim(array $tim): array
{
    usort($tim, function ($a, $b) {
        $sa = $a['seed'] !== null ? (int)$a['seed'] : PHP_INT_MAX;
        $sb = $b['seed'] !== null ? (int)$b['seed'] : PHP_INT_MAX;
        return $sa <=> $sb ?: ((int)$a['id'] <=> (int)$b['id']);
    });
    $hasil = [];
    foreach ($tim as $t) {
        $hasil[] = ['id' => (int)$t['id'], 'grup' => null];
    }
    return $hasil;
}

/* ---------- Memulai turnamen ---------- */
function mulai_turnamen(int $id, string $mode): array
{
    $t = ambil_turnamen($id);
    if (!$t) {
        return hasil(false, 'Turnamen tidak ditemukan.');
    }
    if ($t['status'] !== 'draft') {
        return hasil(false, 'Turnamen ini sudah dimulai.');
    }
    $tim = ambil_semua('SELECT * FROM tim WHERE turnamen_id = ? ORDER BY id', [$id]);
    $n = count($tim);
    if ($n < 2) {
        return hasil(false, 'Minimal 2 tim untuk memulai turnamen.');
    }
    $g = $t['format'] === 'liga' ? 1 : (int)$t['jumlah_grup'];
    if ($t['format'] !== 'gugur') {
        $minUkuran = intdiv($n, $g);
        $perlu = $t['format'] === 'liga_gugur' ? max(2, (int)$t['lolos_per_grup']) : 2;
        if ($minUkuran < $perlu) {
            return hasil(false, "Dengan $g grup, setiap grup minimal $perlu tim (butuh sedikitnya " . ($g * $perlu) . " tim, sekarang $n).");
        }
    }
    $db = db();
    try {
        $db->beginTransaction();
        if ($t['format'] === 'gugur') {
            simpan_bagan($id, buat_bagan(urutkan_seed_tim($tim), (bool)$t['juara_tiga']));
            q("UPDATE turnamen SET status = 'gugur' WHERE id = ?", [$id]);
        } else {
            q('DELETE FROM grup WHERE turnamen_id = ?', [$id]);
            $kelompok = bagi_grup($tim, $g, $mode === 'acak');
            foreach ($kelompok as $gi => $anggota) {
                q('INSERT INTO grup (turnamen_id, nama) VALUES (?, ?)', [$id, $g === 1 ? 'Liga' : chr(65 + $gi)]);
                $gid = (int)$db->lastInsertId();
                $ids = [];
                foreach ($anggota as $x) {
                    q('UPDATE tim SET grup_id = ? WHERE id = ?', [$gid, $x['id']]);
                    $ids[] = (int)$x['id'];
                }
                $u = 0;
                foreach (buat_pasangan_liga($ids, (bool)$t['pulang_pergi']) as $p) {
                    $u++;
                    q("INSERT INTO pertandingan (turnamen_id, fase, grup_id, ronde, urutan, tim_a_id, tim_b_id) VALUES (?,?,?,?,?,?,?)",
                        [$id, 'grup', $gid, $p['ronde'], $u, $p['a'], $p['b']]);
                }
            }
            q("UPDATE turnamen SET status = 'grup' WHERE id = ?", [$id]);
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return hasil(false, 'Gagal memulai turnamen: ' . $e->getMessage());
    }
    catat('mulai_turnamen', "Turnamen #$id dimulai (mode " . ($mode === 'acak' ? 'acak' : 'seed') . ')');
    return hasil(true, 'Turnamen dimulai. Jadwal pertandingan sudah dibuat.');
}

/* ---------- Dari babak grup ke babak gugur ---------- */
function mulai_gugur(int $id): array
{
    $t = ambil_turnamen($id);
    if (!$t || $t['format'] !== 'liga_gugur' || $t['status'] !== 'grup') {
        return hasil(false, 'Babak gugur hanya bisa dimulai dari babak grup pada format liga + gugur.');
    }
    $belum = (int)ambil_nilai("SELECT COUNT(*) FROM pertandingan WHERE turnamen_id = ? AND fase = 'grup' AND status <> 'selesai'", [$id]);
    if ($belum > 0) {
        return hasil(false, "Masih ada $belum laga grup yang belum selesai.");
    }
    $lolos = (int)$t['lolos_per_grup'];
    $semua = klasemen_turnamen($t);
    foreach ($semua as $g) {
        if (count($g['baris']) < $lolos) {
            return hasil(false, 'Grup ' . $g['grup']['nama'] . ' punya tim lebih sedikit dari jumlah yang lolos.');
        }
        if (seri_di_batas($g['baris'], $lolos)) {
            return hasil(false, 'Grup ' . $g['grup']['nama'] . ": peringkat $lolos dan " . ($lolos + 1) . ' seri total. Isi nilai undian pada salah satu tim (menu Tim) lalu coba lagi.');
        }
    }
    $data = [];
    foreach ($semua as $g) {
        $data[] = ['grup' => $g['grup']['nama'], 'baris' => $g['baris']];
    }
    $db = db();
    try {
        $db->beginTransaction();
        simpan_bagan($id, buat_bagan(peserta_gugur_dari_grup($data, $lolos), (bool)$t['juara_tiga']));
        q("UPDATE turnamen SET status = 'gugur' WHERE id = ?", [$id]);
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return hasil(false, 'Gagal membuat bagan: ' . $e->getMessage());
    }
    catat('mulai_gugur', "Babak gugur turnamen #$id dibuat");
    return hasil(true, 'Babak gugur dimulai. Bagan sudah dibuat dari hasil grup.');
}

/* ---------- Menyimpan skor ---------- */
function simpan_skor(int $idLaga, $sa, $sb, $pemenangManual, int $versi, bool $koreksi, string $alasan): array
{
    $skorA = skor_valid($sa);
    $skorB = skor_valid($sb);
    if ($skorA === null || $skorB === null) {
        return hasil(false, 'Skor harus berupa angka 0 sampai 99.');
    }
    $db = db();
    $batal = function (string $pesan) use ($db): array {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return hasil(false, $pesan);
    };
    try {
        $db->beginTransaction();
        $m = ambil_satu('SELECT * FROM pertandingan WHERE id = ? FOR UPDATE', [$idLaga]);
        if (!$m) {
            return $batal('Laga tidak ditemukan.');
        }
        $t = ambil_satu('SELECT * FROM turnamen WHERE id = ? FOR UPDATE', [$m['turnamen_id']]);
        if (empty($m['tim_a_id']) || empty($m['tim_b_id'])) {
            return $batal('Kedua tim belum ditentukan. Selesaikan laga sebelumnya dulu.');
        }
        if ((int)$m['bye']) {
            return $batal('Laga bye tidak memakai skor.');
        }
        if ($t['status'] === 'draft') {
            return $batal('Turnamen belum dimulai.');
        }
        if ((int)$m['versi'] !== $versi) {
            return $batal('Data laga sudah diubah orang lain. Muat ulang halaman lalu isi lagi.');
        }
        $sudahSelesai = $m['status'] === 'selesai';
        if ($sudahSelesai && (int)$m['dikunci'] && !$koreksi) {
            return $batal('Skor sudah dikunci. Hanya admin yang bisa mengoreksi, dengan alasan.');
        }
        if ($sudahSelesai && bersih($alasan, 300) === '') {
            return $batal('Alasan koreksi wajib diisi.');
        }
        if ($m['fase'] === 'grup' && $t['format'] === 'liga_gugur' && $t['status'] !== 'grup') {
            return $batal('Hasil babak grup dibekukan karena babak gugur sudah dimulai.');
        }
        if ($m['fase'] === 'gugur' && $t['status'] === 'draft') {
            return $batal('Babak gugur belum dimulai.');
        }

        $pemenang = null;
        if ($skorA > $skorB) {
            $pemenang = (int)$m['tim_a_id'];
        } elseif ($skorB > $skorA) {
            $pemenang = (int)$m['tim_b_id'];
        } elseif ($m['fase'] === 'grup') {
            if (!(int)$t['izinkan_seri']) {
                return $batal('Seri tidak diizinkan di turnamen ini. Isi skor yang menentukan pemenang.');
            }
        } else {
            $pm = (int)$pemenangManual;
            if ($pm !== (int)$m['tim_a_id'] && $pm !== (int)$m['tim_b_id']) {
                return $batal('Skor seri di babak gugur: pilih pemenang (overtime/tiebreak).');
            }
            $pemenang = $pm;
        }

        if ($m['fase'] === 'gugur' && $sudahSelesai && (int)$m['pemenang_id'] !== (int)$pemenang) {
            foreach ([$m['menang_ke_id'], $m['kalah_ke_id']] as $tujuan) {
                if ($tujuan) {
                    $x = ambil_satu('SELECT status FROM pertandingan WHERE id = ?', [$tujuan]);
                    if ($x && $x['status'] === 'selesai') {
                        return $batal('Pemenang berubah, tetapi laga berikutnya sudah selesai. Koreksi laga berikutnya terlebih dahulu.');
                    }
                }
            }
        }

        $st = q(
            "UPDATE pertandingan SET skor_a = ?, skor_b = ?, pemenang_id = ?, status = 'selesai', dikunci = 1, versi = versi + 1 WHERE id = ? AND versi = ?",
            [$skorA, $skorB, $pemenang, $idLaga, $versi]
        );
        if ($st->rowCount() !== 1) {
            return $batal('Data laga berubah saat disimpan. Muat ulang lalu coba lagi.');
        }

        if ($m['fase'] === 'gugur') {
            $kalah = $pemenang === (int)$m['tim_a_id'] ? (int)$m['tim_b_id'] : (int)$m['tim_a_id'];
            if ($m['menang_ke_id']) {
                taruh_tim((int)$m['menang_ke_id'], $m['menang_ke_slot'], $pemenang);
            }
            if ($m['kalah_ke_id']) {
                taruh_tim((int)$m['kalah_ke_id'], $m['kalah_ke_slot'], $kalah);
            }
        }
        perbarui_status_turnamen((int)$m['turnamen_id']);

        $detail = "Laga #$idLaga: " . ($sudahSelesai ? (int)$m['skor_a'] . '-' . (int)$m['skor_b'] . ' → ' : '') . "$skorA-$skorB";
        if ($sudahSelesai) {
            $detail .= ' (koreksi: ' . bersih($alasan, 300) . ')';
        }
        catat($sudahSelesai ? 'koreksi_skor' : 'input_skor', $detail);
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return hasil(false, 'Gagal menyimpan skor: ' . $e->getMessage());
    }
    return hasil(true, $sudahSelesai ? 'Koreksi skor tersimpan dan dicatat di log.' : 'Skor tersimpan dan laga dikunci.');
}

/* ---------- Status & juara otomatis ---------- */
function perbarui_status_turnamen(int $id): void
{
    $t = ambil_turnamen($id);
    if (!$t || $t['status'] === 'draft') {
        return;
    }
    $belum = (int)ambil_nilai("SELECT COUNT(*) FROM pertandingan WHERE turnamen_id = ? AND status <> 'selesai' AND bye = 0", [$id]);
    $status = $t['status'];
    $juara = $t['juara_id'] !== null ? (int)$t['juara_id'] : null;

    if ($t['format'] === 'liga') {
        $status = 'grup';
        $juara = null;
        if ($belum === 0) {
            $k = klasemen_turnamen($t);
            $baris = $k[0]['baris'] ?? [];
            if (count($baris) > 0 && !seri_di_batas($baris, 1)) {
                $status = 'selesai';
                $juara = (int)$baris[0]['id'];
            }
        }
    } elseif ($t['status'] !== 'grup') {
        $final = ambil_satu("SELECT status, pemenang_id FROM pertandingan WHERE turnamen_id = ? AND fase = 'gugur' AND menang_ke_id IS NULL AND label <> 'Perebutan Juara 3' LIMIT 1", [$id]);
        if ($final && $final['status'] === 'selesai') {
            $juara = (int)$final['pemenang_id'];
            $status = $belum === 0 ? 'selesai' : 'gugur';
        } else {
            $juara = null;
            $status = 'gugur';
        }
    }
    if ($status !== $t['status'] || $juara !== ($t['juara_id'] !== null ? (int)$t['juara_id'] : null)) {
        q('UPDATE turnamen SET status = ?, juara_id = ? WHERE id = ?', [$status, $juara, $id]);
    }
}

/* ---------- Susun jadwal otomatis ---------- */
function susun_jadwal_otomatis(int $id, string $fase, string $mulai, int $slotMenit, int $arena): array
{
    $t = ambil_turnamen($id);
    if (!$t || $t['status'] === 'draft') {
        return hasil(false, 'Mulai turnamen dulu sebelum menyusun jadwal.');
    }
    $d = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $mulai);
    if (!$d) {
        return hasil(false, 'Waktu mulai tidak valid.');
    }
    $laga = ambil_semua("SELECT id, ronde, urutan FROM pertandingan WHERE turnamen_id = ? AND fase = ? AND status <> 'selesai' AND bye = 0", [$id, $fase]);
    if (!$laga) {
        return hasil(false, 'Tidak ada laga yang perlu dijadwalkan pada fase ini.');
    }
    $susunan = susun_waktu($laga, $d, $slotMenit, $arena);
    $db = db();
    try {
        $db->beginTransaction();
        foreach ($susunan as $idLaga => $s) {
            q('UPDATE pertandingan SET waktu = ?, lokasi = ?, versi = versi + 1 WHERE id = ?', [$s['waktu'], $s['lokasi'], $idLaga]);
        }
        $db->commit();
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        return hasil(false, 'Gagal menyusun jadwal: ' . $e->getMessage());
    }
    catat('jadwal_otomatis', "Turnamen #$id fase $fase: " . count($susunan) . " laga, $arena arena, slot $slotMenit menit");
    return hasil(true, count($susunan) . ' laga dijadwalkan otomatis.');
}

/** Peringatan yang perlu ditindaklanjuti panitia pada satu turnamen. */
function peringatan_turnamen(array $t): array
{
    $p = [];
    if ($t['status'] === 'grup') {
        $belum = (int)ambil_nilai("SELECT COUNT(*) FROM pertandingan WHERE turnamen_id = ? AND fase = 'grup' AND status <> 'selesai'", [$t['id']]);
        if ($belum === 0) {
            $semua = klasemen_turnamen($t);
            if ($t['format'] === 'liga') {
                $b = $semua[0]['baris'] ?? [];
                if (seri_di_batas($b, 1)) {
                    $p[] = 'Peringkat 1 dan 2 seri total. Isi nilai undian pada salah satu tim agar juara bisa ditentukan.';
                }
            } else {
                foreach ($semua as $g) {
                    if (seri_di_batas($g['baris'], (int)$t['lolos_per_grup'])) {
                        $p[] = 'Grup ' . $g['grup']['nama'] . ': ada seri total di batas lolos. Isi nilai undian pada salah satu tim.';
                    }
                }
            }
        }
    }
    return $p;
}
