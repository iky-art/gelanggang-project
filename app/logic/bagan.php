<?php
/**
 * Bagan gugur (single elimination): fungsi murni.
 * Mendukung jumlah tim bukan pangkat dua (bye), pemenang otomatis maju,
 * dan opsi perebutan juara 3.
 */

/** Urutan seed standar untuk bagan berukuran $p (pangkat dua): 8 -> [1,8,4,5,2,7,3,6]. */
function urutan_seed(int $p): array
{
    $l = [1];
    while (count($l) < $p) {
        $m = count($l);
        $baru = [];
        foreach ($l as $s) {
            $baru[] = $s;
            $baru[] = 2 * $m + 1 - $s;
        }
        $l = $baru;
    }
    return $l;
}

function label_babak(int $jumlahLaga): string
{
    if ($jumlahLaga === 1) return 'Final';
    if ($jumlahLaga === 2) return 'Semifinal';
    if ($jumlahLaga === 4) return 'Perempat Final';
    return ($jumlahLaga * 2) . ' Besar';
}

/**
 * Usahakan dua tim satu grup tidak bertemu di ronde pertama dengan menukar lawan.
 * Upaya terbaik (heuristik), bukan jaminan matematis untuk semua kombinasi.
 */
function hindari_bentrok_grup(array $pasangan): array
{
    $sama = function ($a, $b) {
        return $a !== null && $b !== null
            && isset($a['grup'], $b['grup'])
            && (string)$a['grup'] === (string)$b['grup'];
    };
    $n = count($pasangan);
    for ($i = 0; $i < $n; $i++) {
        if (!$sama($pasangan[$i][0], $pasangan[$i][1])) {
            continue;
        }
        for ($j = 0; $j < $n; $j++) {
            if ($j === $i) {
                continue;
            }
            $bI = $pasangan[$i][1];
            $aJ = $pasangan[$j][0];
            $bJ = $pasangan[$j][1];
            if ($aJ === null || $bJ === null) {
                continue;
            }
            if (!$sama($pasangan[$i][0], $bJ) && !$sama($aJ, $bI)) {
                $pasangan[$i][1] = $bJ;
                $pasangan[$j][1] = $bI;
                break;
            }
        }
    }
    return $pasangan;
}

/**
 * @param array $seed list tim berurutan seed (indeks 0 = seed 1): [['id'=>int,'grup'=>mixed], ...]
 * @return array ['ukuran'=>P,'putaran'=>R,'laga'=>[...], 'juara_tiga'=>bool]
 */
function buat_bagan(array $seed, bool $juaraTiga = false): array
{
    $n = count($seed);
    if ($n < 2) {
        throw new InvalidArgumentException('Bagan gugur butuh minimal 2 tim.');
    }
    $p = 2;
    while ($p < $n) {
        $p *= 2;
    }
    $putaran = (int)round(log($p, 2));
    $urut = urutan_seed($p);

    $pasangan = [];
    for ($i = 0; $i < $p / 2; $i++) {
        $pasangan[] = [$seed[$urut[2 * $i] - 1] ?? null, $seed[$urut[2 * $i + 1] - 1] ?? null];
    }
    $pasangan = hindari_bentrok_grup($pasangan);

    $laga = [];
    foreach ($pasangan as $i => $pr) {
        $a = $pr[0];
        $b = $pr[1];
        $laga[] = [
            'kunci' => 'R1-' . ($i + 1), 'ronde' => 1, 'urutan' => $i + 1, 'label' => label_babak($p / 2),
            'a' => $a ? (int)$a['id'] : null, 'b' => $b ? (int)$b['id'] : null, 'bye' => ($a === null || $b === null),
            'menang_ke' => null, 'menang_slot' => null, 'kalah_ke' => null, 'kalah_slot' => null,
        ];
    }
    for ($r = 2; $r <= $putaran; $r++) {
        $jumlah = (int)($p / pow(2, $r));
        for ($i = 1; $i <= $jumlah; $i++) {
            $laga[] = [
                'kunci' => 'R' . $r . '-' . $i, 'ronde' => $r, 'urutan' => $i, 'label' => label_babak($jumlah),
                'a' => null, 'b' => null, 'bye' => false,
                'menang_ke' => null, 'menang_slot' => null, 'kalah_ke' => null, 'kalah_slot' => null,
            ];
        }
    }

    $indeks = [];
    foreach ($laga as $k => $l) {
        $indeks[$l['kunci']] = $k;
    }
    foreach ($laga as $k => $l) {
        if ($l['ronde'] < $putaran) {
            $laga[$k]['menang_ke'] = 'R' . ($l['ronde'] + 1) . '-' . (int)ceil($l['urutan'] / 2);
            $laga[$k]['menang_slot'] = ($l['urutan'] % 2 === 1) ? 'a' : 'b';
        }
    }

    /* Perebutan juara 3 hanya dibuat bila kedua semifinal pasti punya pecundang (tanpa bye). */
    $adaJuaraTiga = false;
    if ($juaraTiga && $putaran >= 2) {
        $adaBye = false;
        if ($putaran - 1 === 1) {
            foreach ($laga as $l) {
                if ($l['ronde'] === 1 && $l['bye']) {
                    $adaBye = true;
                }
            }
        }
        if (!$adaBye) {
            $adaJuaraTiga = true;
            foreach ($laga as $k => $l) {
                if ($l['ronde'] === $putaran - 1) {
                    $laga[$k]['kalah_ke'] = 'J3';
                    $laga[$k]['kalah_slot'] = ($l['urutan'] === 1) ? 'a' : 'b';
                }
            }
            $laga[] = [
                'kunci' => 'J3', 'ronde' => $putaran, 'urutan' => 2, 'label' => 'Perebutan Juara 3',
                'a' => null, 'b' => null, 'bye' => false,
                'menang_ke' => null, 'menang_slot' => null, 'kalah_ke' => null, 'kalah_slot' => null,
            ];
        }
    }
    return ['ukuran' => $p, 'putaran' => $putaran, 'laga' => $laga, 'juara_tiga' => $adaJuaraTiga];
}

/**
 * Seed babak gugur dari hasil grup.
 * Juara grup diurutkan menurut statistik; peringkat berikutnya mengikuti urutan grup juara
 * (1A,1B,... lalu 2A,2B,...) sehingga ronde pertama mempertemukan tim dari grup berbeda.
 * @param array $hasilGrup [['grup'=>'A','baris'=>[baris klasemen...]], ...]
 */
function peserta_gugur_dari_grup(array $hasilGrup, int $lolos): array
{
    $juara = [];
    foreach ($hasilGrup as $g) {
        if (isset($g['baris'][0])) {
            $juara[] = ['grup' => $g['grup'], 'b' => $g['baris'][0]];
        }
    }
    usort($juara, function ($x, $y) {
        $c = [$y['b']['poin'], $y['b']['selisih'], $y['b']['gm']] <=> [$x['b']['poin'], $x['b']['selisih'], $x['b']['gm']];
        return $c !== 0 ? $c : strcmp($x['b']['nama'], $y['b']['nama']);
    });
    $urutanGrup = array_column($juara, 'grup');
    $peta = [];
    foreach ($hasilGrup as $g) {
        $peta[$g['grup']] = $g['baris'];
    }
    $seed = [];
    for ($r = 0; $r < $lolos; $r++) {
        foreach ($urutanGrup as $grup) {
            if (isset($peta[$grup][$r])) {
                $seed[] = ['id' => (int)$peta[$grup][$r]['id'], 'grup' => $grup];
            }
        }
    }
    return $seed;
}
