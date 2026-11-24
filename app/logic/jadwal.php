<?php
/* Penjadwalan: pasangan liga, pembagian grup, penyusunan waktu, dan deteksi bentrok. Fungsi murni. */

/**
 * Metode lingkaran (circle method): setiap tim bertemu semua tim lain tepat sekali per putaran.
 * Jumlah tim ganjil -> satu tim istirahat per ronde.
 * @return array [['ronde'=>1,'a'=>id,'b'=>id], ...]
 */
function buat_pasangan_liga(array $ids, bool $pulangPergi = false): array
{
    $pool = array_values($ids);
    if (count($pool) % 2 === 1) {
        $pool[] = null;
    }
    $m = count($pool);
    $putaran = $m - 1;
    $hasil = [];
    for ($r = 0; $r < $putaran; $r++) {
        for ($i = 0; $i < $m / 2; $i++) {
            $a = $pool[$i];
            $b = $pool[$m - 1 - $i];
            if ($a === null || $b === null) {
                continue;
            }
            $hasil[] = ['ronde' => $r + 1, 'a' => $a, 'b' => $b];
        }
        $terakhir = array_pop($pool);
        array_splice($pool, 1, 0, [$terakhir]);
    }
    if ($pulangPergi) {
        $dasar = $hasil;
        foreach ($dasar as $p) {
            $hasil[] = ['ronde' => $p['ronde'] + $putaran, 'a' => $p['b'], 'b' => $p['a']];
        }
    }
    return $hasil;
}

/**
 * Bagi tim ke grup dengan pola ular (snake) supaya kekuatan merata.
 * $acak=false -> urut berdasarkan seed (seed kecil = kuat; tanpa seed ditaruh di akhir).
 * @return array daftar grup, tiap grup berisi baris tim
 */
function bagi_grup(array $tim, int $jumlahGrup, bool $acak = false): array
{
    $jumlahGrup = max(1, $jumlahGrup);
    if ($acak) {
        shuffle($tim);
    } else {
        usort($tim, function ($a, $b) {
            $sa = isset($a['seed']) && $a['seed'] !== null ? (int)$a['seed'] : PHP_INT_MAX;
            $sb = isset($b['seed']) && $b['seed'] !== null ? (int)$b['seed'] : PHP_INT_MAX;
            return $sa <=> $sb ?: ((int)$a['id'] <=> (int)$b['id']);
        });
    }
    $grup = array_fill(0, $jumlahGrup, []);
    foreach (array_values($tim) as $i => $t) {
        $baris = intdiv($i, $jumlahGrup);
        $kolom = $i % $jumlahGrup;
        if ($baris % 2 === 1) {
            $kolom = $jumlahGrup - 1 - $kolom;
        }
        $grup[$kolom][] = $t;
    }
    return $grup;
}

/**
 * Susun waktu & arena. Laga diurutkan per ronde; setiap ronde baru dimulai di slot baru
 * sehingga satu tim tidak pernah main dua kali pada slot waktu yang sama.
 * @return array [id_laga => ['waktu'=>'Y-m-d H:i:s','lokasi'=>'Arena N']]
 */
function susun_waktu(array $laga, DateTimeImmutable $mulai, int $slotMenit, int $arena): array
{
    $arena = max(1, $arena);
    $slotMenit = max(1, $slotMenit);
    usort($laga, function ($a, $b) {
        return [(int)$a['ronde'], (int)$a['urutan'], (int)$a['id']] <=> [(int)$b['ronde'], (int)$b['urutan'], (int)$b['id']];
    });
    $hasil = [];
    $idx = 0;
    $rondeLalu = null;
    foreach ($laga as $l) {
        if ($rondeLalu !== null && (int)$l['ronde'] !== $rondeLalu && $idx % $arena !== 0) {
            $idx += $arena - ($idx % $arena);
        }
        $rondeLalu = (int)$l['ronde'];
        $w = $mulai->modify('+' . (intdiv($idx, $arena) * $slotMenit) . ' minutes');
        $hasil[(int)$l['id']] = ['waktu' => $w->format('Y-m-d H:i:s'), 'lokasi' => 'Arena ' . (($idx % $arena) + 1)];
        $idx++;
    }
    return $hasil;
}

/**
 * Deteksi bentrok: tim yang sama main dua kali, atau arena yang sama dipakai dua laga,
 * pada rentang waktu yang saling tumpang tindih (selisih < durasi laga).
 * @return array daftar masalah: ['jenis'=>'tim'|'lokasi','laga_id'=>int,'tim_id'=>int(opsional)]
 */
function jadwal_bentrok(array $lainnya, array $baru, int $durasiMenit): array
{
    $masalah = [];
    if (empty($baru['waktu'])) {
        return $masalah;
    }
    $t0 = strtotime($baru['waktu']);
    $durasi = max(1, $durasiMenit) * 60;
    foreach ($lainnya as $l) {
        if (empty($l['waktu']) || (int)$l['id'] === (int)($baru['id'] ?? 0)) {
            continue;
        }
        if (abs(strtotime($l['waktu']) - $t0) >= $durasi) {
            continue;
        }
        foreach (['tim_a_id', 'tim_b_id'] as $k1) {
            foreach (['tim_a_id', 'tim_b_id'] as $k2) {
                if (!empty($baru[$k1]) && !empty($l[$k2]) && (int)$baru[$k1] === (int)$l[$k2]) {
                    $masalah[] = ['jenis' => 'tim', 'tim_id' => (int)$baru[$k1], 'laga_id' => (int)$l['id']];
                }
            }
        }
        if (!empty($baru['lokasi']) && !empty($l['lokasi']) && huruf_kecil(trim($baru['lokasi'])) === huruf_kecil(trim($l['lokasi']))) {
            $masalah[] = ['jenis' => 'lokasi', 'laga_id' => (int)$l['id']];
        }
    }
    return $masalah;
}
