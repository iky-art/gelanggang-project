<?php
/**
 * Klasemen liga: fungsi murni (tanpa database) supaya mudah diuji.
 *
 * Urutan peringkat:
 *   1. Poin
 *   2. Selisih gol (skor)
 *   3. Head-to-head antar tim yang masih seri (poin, lalu selisih) pada mini-liga
 *   4. Total gol memasukkan
 *   5. Nilai undian (diisi panitia bila perlu)
 *   6. Nama (hanya agar urutan tetap stabil; ditandai "terikat" bila 1-5 sama)
 */

/* Statistik dasar tiap tim dari daftar laga selesai. */
function statistik_dasar(array $tim, array $laga, array $aturan): array
{
    $s = [];
    foreach ($tim as $t) {
        $id = (int)$t['id'];
        $s[$id] = [
            'id' => $id, 'nama' => (string)$t['nama'], 'undian' => (int)($t['undian'] ?? 0),
            'main' => 0, 'menang' => 0, 'seri' => 0, 'kalah' => 0,
            'gm' => 0, 'gk' => 0, 'selisih' => 0, 'poin' => 0,
        ];
    }
    foreach ($laga as $l) {
        $a = (int)$l['tim_a_id'];
        $b = (int)$l['tim_b_id'];
        if (!isset($s[$a]) || !isset($s[$b])) {
            continue;
        }
        $sa = (int)$l['skor_a'];
        $sb = (int)$l['skor_b'];
        $s[$a]['main']++; $s[$b]['main']++;
        $s[$a]['gm'] += $sa; $s[$a]['gk'] += $sb;
        $s[$b]['gm'] += $sb; $s[$b]['gk'] += $sa;
        if ($sa > $sb) {
            $s[$a]['menang']++; $s[$b]['kalah']++;
            $s[$a]['poin'] += (int)$aturan['poin_menang'];
            $s[$b]['poin'] += (int)$aturan['poin_kalah'];
        } elseif ($sa < $sb) {
            $s[$b]['menang']++; $s[$a]['kalah']++;
            $s[$b]['poin'] += (int)$aturan['poin_menang'];
            $s[$a]['poin'] += (int)$aturan['poin_kalah'];
        } else {
            $s[$a]['seri']++; $s[$b]['seri']++;
            $s[$a]['poin'] += (int)$aturan['poin_seri'];
            $s[$b]['poin'] += (int)$aturan['poin_seri'];
        }
    }
    foreach ($s as $id => $r) {
        $s[$id]['selisih'] = $r['gm'] - $r['gk'];
    }
    return $s;
}

/* Urutkan sekelompok tim yang poin dan selisihnya sama. */
function urutkan_blok(array $blok, array $laga, array $aturan): array
{
    $ids = [];
    $timMini = [];
    foreach ($blok as $r) {
        $ids[(int)$r['id']] = true;
        $timMini[] = ['id' => $r['id'], 'nama' => $r['nama'], 'undian' => $r['undian']];
    }
    $lagaMini = [];
    foreach ($laga as $l) {
        if (isset($ids[(int)$l['tim_a_id']]) && isset($ids[(int)$l['tim_b_id']])) {
            $lagaMini[] = $l;
        }
    }
    $mini = statistik_dasar($timMini, $lagaMini, $aturan);
    foreach ($blok as $k => $r) {
        $blok[$k]['h2h_poin']    = $mini[(int)$r['id']]['poin'];
        $blok[$k]['h2h_selisih'] = $mini[(int)$r['id']]['selisih'];
    }
    usort($blok, function ($x, $y) {
        foreach (['h2h_poin', 'h2h_selisih', 'gm', 'undian'] as $f) {
            if ($x[$f] !== $y[$f]) {
                return $y[$f] <=> $x[$f];
            }
        }
        return strcmp($x['nama'], $y['nama']);
    });
    return $blok;
}

/**
 * @param array $tim    [['id','nama','undian'], ...]
 * @param array $laga   laga SELESAI saja: [['tim_a_id','tim_b_id','skor_a','skor_b'], ...]
 * @param array $aturan ['poin_menang','poin_seri','poin_kalah']
 * @return array baris terurut + 'peringkat', 'kunci', 'terikat'
 */
function hitung_klasemen(array $tim, array $laga, array $aturan): array
{
    $daftar = array_values(statistik_dasar($tim, $laga, $aturan));
    usort($daftar, function ($x, $y) {
        if ($x['poin'] !== $y['poin']) {
            return $y['poin'] <=> $x['poin'];
        }
        if ($x['selisih'] !== $y['selisih']) {
            return $y['selisih'] <=> $x['selisih'];
        }
        return strcmp($x['nama'], $y['nama']);
    });

    $hasil = [];
    $n = count($daftar);
    $i = 0;
    while ($i < $n) {
        $j = $i;
        while ($j < $n && $daftar[$j]['poin'] === $daftar[$i]['poin'] && $daftar[$j]['selisih'] === $daftar[$i]['selisih']) {
            $j++;
        }
        $blok = array_slice($daftar, $i, $j - $i);
        if (count($blok) > 1) {
            $blok = urutkan_blok($blok, $laga, $aturan);
        } else {
            $blok[0]['h2h_poin'] = 0;
            $blok[0]['h2h_selisih'] = 0;
        }
        foreach ($blok as $r) {
            $hasil[] = $r;
        }
        $i = $j;
    }

    foreach ($hasil as $k => $r) {
        $hasil[$k]['peringkat'] = $k + 1;
        $hasil[$k]['kunci'] = implode('|', [$r['poin'], $r['selisih'], $r['h2h_poin'], $r['h2h_selisih'], $r['gm'], $r['undian']]);
    }
    $hitung = array_count_values(array_column($hasil, 'kunci'));
    foreach ($hasil as $k => $r) {
        $hasil[$k]['terikat'] = $hitung[$r['kunci']] > 1;
    }
    return $hasil;
}

/* Apakah peringkat ke-$batas dan ke-($batas+1) seri total (butuh undian)? */
function seri_di_batas(array $baris, int $batas): bool
{
    return $batas >= 1 && isset($baris[$batas - 1], $baris[$batas]) && $baris[$batas - 1]['kunci'] === $baris[$batas]['kunci'];
}
