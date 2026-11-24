<?php
/**
 * Uji logika inti (tanpa database): klasemen, jadwal, bagan, validasi.
 *   Lewat browser : localhost/gelanggang/tests/uji-logika.php
 *   Lewat terminal: php tests/uji-logika.php
 * Nilai yang diharapkan dihitung terlebih dahulu dari prototipe terpisah dan dicek manual.
 */
$root = dirname(__DIR__);
require $root . '/app/helpers.php';
require $root . '/app/logic/validasi.php';
require $root . '/app/logic/klasemen.php';
require $root . '/app/logic/jadwal.php';
require $root . '/app/logic/bagan.php';

$hasil = [];
function uji(string $nama, bool $lulus, string $detail = ''): void
{
    global $hasil;
    $hasil[] = [$nama, $lulus, $detail];
}
function sama($a, $b): bool { return $a === $b; }
function ids(array $baris): array { return array_map(function ($r) { return (int)$r['id']; }, $baris); }

$aturan = ['poin_menang' => 3, 'poin_seri' => 1, 'poin_kalah' => 0];
function laga_k($a, $b, $sa, $sb): array { return ['tim_a_id' => $a, 'tim_b_id' => $b, 'skor_a' => $sa, 'skor_b' => $sb]; }
function tim_k(array $daftar): array { $o = []; foreach ($daftar as $id => $n) { $o[] = ['id' => $id, 'nama' => $n, 'undian' => 0]; } return $o; }

/* ---- Seed bagan ---- */
uji('urutan_seed(2)', sama(urutan_seed(2), [1, 2]));
uji('urutan_seed(8)', sama(urutan_seed(8), [1, 8, 4, 5, 2, 7, 3, 6]));
uji('urutan_seed(16)', sama(urutan_seed(16), [1, 16, 8, 9, 4, 13, 5, 12, 2, 15, 7, 10, 3, 14, 6, 11]));

/* ---- Pasangan liga ---- */
$p4 = buat_pasangan_liga([1, 2, 3, 4], false);
$ringkas = array_map(function ($p) { return [$p['ronde'], $p['a'], $p['b']]; }, $p4);
uji('liga 4 tim: 6 laga, 3 ronde (urutan pasti)', sama($ringkas, [[1, 1, 4], [1, 2, 3], [2, 1, 3], [2, 4, 2], [3, 1, 2], [3, 3, 4]]));
uji('liga 5 tim: 10 laga', count(buat_pasangan_liga([1, 2, 3, 4, 5], false)) === 10);
uji('liga 6 tim pulang-pergi: 30 laga', count(buat_pasangan_liga([1, 2, 3, 4, 5, 6], true)) === 30);
$unik = true;
$lihat = [];
foreach (buat_pasangan_liga([1, 2, 3, 4, 5, 6, 7, 8], false) as $p) {
    $k = min($p['a'], $p['b']) . '-' . max($p['a'], $p['b']);
    if (isset($lihat[$k])) { $unik = false; }
    $lihat[$k] = true;
}
uji('liga 8 tim: setiap pasangan bertemu tepat sekali (28 pasangan)', $unik && count($lihat) === 28);
$tabrak = false;
$perRonde = [];
foreach (buat_pasangan_liga([1, 2, 3, 4, 5, 6, 7], false) as $p) {
    foreach ([$p['a'], $p['b']] as $x) {
        if (isset($perRonde[$p['ronde']][$x])) { $tabrak = true; }
        $perRonde[$p['ronde']][$x] = true;
    }
}
uji('liga 7 tim: tidak ada tim main dua kali dalam satu ronde', !$tabrak);

/* ---- Pembagian grup ---- */
$tim8 = [];
for ($i = 1; $i <= 8; $i++) { $tim8[] = ['id' => $i, 'seed' => $i]; }
$g = bagi_grup($tim8, 2, false);
uji('bagi_grup snake: grup 1 = seed 1,4,5,8', sama(ids($g[0]), [1, 4, 5, 8]));
uji('bagi_grup snake: grup 2 = seed 2,3,6,7', sama(ids($g[1]), [2, 3, 6, 7]));
$g7 = bagi_grup(array_slice($tim8, 0, 7), 2, false);
uji('bagi_grup 7 tim / 2 grup: ukuran 4 dan 3', count($g7[0]) === 4 && count($g7[1]) === 3);

/* ---- Klasemen ---- */
$k1 = hitung_klasemen(tim_k([1 => 'Alfa', 2 => 'Bravo', 3 => 'Charlie', 4 => 'Delta']),
    [laga_k(1, 2, 2, 0), laga_k(3, 4, 1, 1), laga_k(1, 3, 0, 0), laga_k(2, 4, 3, 1), laga_k(1, 4, 1, 2), laga_k(2, 3, 1, 0)], $aturan);
uji('klasemen dasar: urutan Bravo, Alfa, Delta, Charlie', sama(ids($k1), [2, 1, 4, 3]));
uji('klasemen dasar: poin 6,4,4,2', sama(array_column($k1, 'poin'), [6, 4, 4, 2]));
uji('klasemen dasar: tidak ada yang terikat', !in_array(true, array_column($k1, 'terikat'), true));

$k2 = hitung_klasemen(tim_k([1 => 'Aster', 2 => 'Bima', 3 => 'Citra', 4 => 'Dewa']),
    [laga_k(1, 2, 1, 2), laga_k(1, 3, 0, 0), laga_k(1, 4, 2, 1), laga_k(2, 3, 0, 1), laga_k(2, 4, 0, 1), laga_k(3, 4, 1, 0)], $aturan);
uji('head-to-head: Dewa di atas Bima (poin & selisih sama, Dewa menang langsung)', sama(ids($k2), [3, 1, 4, 2]));
uji('head-to-head: Dewa & Bima sama-sama 3 poin, selisih -1', $k2[2]['poin'] === 3 && $k2[3]['poin'] === 3 && $k2[2]['selisih'] === -1 && $k2[3]['selisih'] === -1);

$k3 = hitung_klasemen(tim_k([1 => 'Elang', 2 => 'Harimau', 3 => 'Gajah']),
    [laga_k(1, 2, 1, 0), laga_k(2, 3, 1, 0), laga_k(3, 1, 1, 0)], $aturan);
uji('lingkaran 3 tim sama persis: semua ditandai terikat', !in_array(false, array_column($k3, 'terikat'), true));
uji('lingkaran 3 tim: urutan jatuh ke nama (Elang, Gajah, Harimau)', sama(ids($k3), [1, 3, 2]));

$k4 = hitung_klasemen(tim_k([1 => 'X', 2 => 'Y']), [laga_k(1, 2, 1, 1)], $aturan);
uji('seri total dua tim: terikat', $k4[0]['terikat'] && $k4[1]['terikat'] && seri_di_batas($k4, 1));
$undi = tim_k([1 => 'X', 2 => 'Y']);
$undi[1]['undian'] = 1;
$k5 = hitung_klasemen($undi, [laga_k(1, 2, 1, 1)], $aturan);
uji('nilai undian memutus seri total (X dengan undian 1 unggul)', sama(ids($k5), [1, 2]) && !seri_di_batas($k5, 1));
uji('seri_di_batas pada klasemen tanpa seri = false', !seri_di_batas($k1, 2));

/* ---- Bagan gugur ---- */
$s = function (int $n): array { $o = []; for ($i = 1; $i <= $n; $i++) { $o[] = ['id' => $i, 'grup' => null]; } return $o; };
$b4 = buat_bagan($s(4), false);
$r1 = array_values(array_filter($b4['laga'], function ($l) { return $l['ronde'] === 1; }));
uji('bagan 4 tim: 3 laga total', count($b4['laga']) === 3);
uji('bagan 4 tim: ronde 1 = 1v4 dan 2v3', sama([[$r1[0]['a'], $r1[0]['b']], [$r1[1]['a'], $r1[1]['b']]], [[1, 4], [2, 3]]));
uji('bagan 4 tim: pemenang ronde 1 masuk final slot a dan b', $r1[0]['menang_ke'] === 'R2-1' && $r1[0]['menang_slot'] === 'a' && $r1[1]['menang_slot'] === 'b');

$b6 = buat_bagan($s(6), false);
$bye = array_values(array_filter($b6['laga'], function ($l) { return $l['bye']; }));
uji('bagan 6 tim: ukuran 8, 7 laga, 2 bye', $b6['ukuran'] === 8 && count($b6['laga']) === 7 && count($bye) === 2);
uji('bagan 6 tim: seed 1 dan 2 mendapat bye', sama([$bye[0]['a'], $bye[1]['a']], [1, 2]));

$b3 = buat_bagan($s(3), true);
uji('bagan 3 tim + juara 3: tidak dibuat (ada bye di semifinal)', $b3['juara_tiga'] === false);
$b4j = buat_bagan($s(4), true);
$j3 = array_values(array_filter($b4j['laga'], function ($l) { return $l['kunci'] === 'J3'; }));
uji('bagan 4 tim + juara 3: laga J3 dibuat', $b4j['juara_tiga'] === true && count($j3) === 1);
$semi = array_values(array_filter($b4j['laga'], function ($l) { return $l['ronde'] === 1; }));
uji('juara 3: pecundang semifinal masuk slot a dan b', $semi[0]['kalah_ke'] === 'J3' && $semi[0]['kalah_slot'] === 'a' && $semi[1]['kalah_slot'] === 'b');
$galat = false;
try { buat_bagan($s(1), false); } catch (InvalidArgumentException $e) { $galat = true; }
uji('bagan 1 tim ditolak', $galat);

/* ---- Seed dari grup: 4 grup, 2 lolos ---- */
$mk = function (int $id, string $nama, int $poin, int $sel, int $gm): array { return ['id' => $id, 'nama' => $nama, 'poin' => $poin, 'selisih' => $sel, 'gm' => $gm]; };
$hg = [
    ['grup' => 'A', 'baris' => [$mk(1, 'A1', 9, 5, 6), $mk(2, 'A2', 4, 0, 3)]],
    ['grup' => 'B', 'baris' => [$mk(3, 'B1', 9, 3, 5), $mk(4, 'B2', 4, 0, 3)]],
    ['grup' => 'C', 'baris' => [$mk(5, 'C1', 7, 2, 4), $mk(6, 'C2', 3, 0, 2)]],
    ['grup' => 'D', 'baris' => [$mk(7, 'D1', 6, 1, 3), $mk(8, 'D2', 3, 0, 2)]],
];
$seed = peserta_gugur_dari_grup($hg, 2);
uji('seed dari grup: juara dulu (1,3,5,7) lalu runner-up (2,4,6,8)', sama(array_column($seed, 'id'), [1, 3, 5, 7, 2, 4, 6, 8]));
$bg = buat_bagan($seed, false);
$r1 = array_values(array_filter($bg['laga'], function ($l) { return $l['ronde'] === 1; }));
$pairs = array_map(function ($l) { return [$l['a'], $l['b']]; }, $r1);
uji('4 grup x 2 lolos: ronde 1 = [1v8, 7v2, 3v6, 5v4]', sama($pairs, [[1, 8], [7, 2], [3, 6], [5, 4]]));
$peta = []; foreach ($seed as $x) { $peta[$x['id']] = $x['grup']; }
$samaGrup = false;
foreach ($pairs as $p) { if ($peta[$p[0]] === $peta[$p[1]]) { $samaGrup = true; } }
uji('4 grup x 2 lolos: tidak ada pertemuan satu grup di ronde 1', !$samaGrup);

/* ---- Penyusunan waktu ---- */
$laga6 = [];
foreach ([[1, 1], [2, 1], [3, 1], [4, 2], [5, 2], [6, 2]] as $x) { $laga6[] = ['id' => $x[0], 'ronde' => $x[1], 'urutan' => $x[0]]; }
$w = susun_waktu($laga6, new DateTimeImmutable('2026-12-01 08:00:00'), 45, 2);
$ring = []; foreach ($w as $id => $v) { $ring[$id] = substr($v['waktu'], 11, 5) . ' ' . $v['lokasi']; }
uji('susun_waktu: ronde baru mulai di slot baru', sama($ring, [1 => '08:00 Arena 1', 2 => '08:00 Arena 2', 3 => '08:45 Arena 1', 4 => '09:30 Arena 1', 5 => '09:30 Arena 2', 6 => '10:15 Arena 1']));

/* ---- Bentrok jadwal ---- */
$ada = [['id' => 1, 'waktu' => '2026-12-01 08:00:00', 'tim_a_id' => 1, 'tim_b_id' => 2, 'lokasi' => 'Arena 1']];
$m1 = jadwal_bentrok($ada, ['id' => 2, 'waktu' => '2026-12-01 08:30:00', 'tim_a_id' => 2, 'tim_b_id' => 3, 'lokasi' => 'arena 1'], 45);
uji('bentrok: tim sama + arena sama (huruf besar/kecil diabaikan) = 2 masalah', count($m1) === 2);
$m2 = jadwal_bentrok($ada, ['id' => 2, 'waktu' => '2026-12-01 08:45:00', 'tim_a_id' => 2, 'tim_b_id' => 3, 'lokasi' => 'Arena 1'], 45);
uji('bentrok: tepat setelah durasi selesai = aman', count($m2) === 0);
$m3 = jadwal_bentrok($ada, ['id' => 1, 'waktu' => '2026-12-01 08:10:00', 'tim_a_id' => 1, 'tim_b_id' => 2, 'lokasi' => 'Arena 1'], 45);
uji('bentrok: laga yang sama tidak dianggap bentrok dengan dirinya', count($m3) === 0);

/* ---- Validasi ---- */
uji('skor_valid("12")', skor_valid('12') === 12);
uji('skor_valid ditolak: 100, -1, 1.5, abc, kosong', skor_valid('100') === null && skor_valid('-1') === null && skor_valid('1.5') === null && skor_valid('abc') === null && skor_valid('') === null);
uji('waktu_valid format datetime-local', waktu_valid('2026-12-01T08:00') === '2026-12-01 08:00:00');
uji('waktu_valid menolak tanggal mustahil', waktu_valid('2026-13-01T08:00') === null && waktu_valid('2026-02-30T08:00') === null);
uji('warna_valid', warna_valid('#2F6BFF') && !warna_valid('2F6BFF') && !warna_valid('#2F6BF') && !warna_valid('red'));
uji('cek_kata_sandi', cek_kata_sandi_uji('abc12345') === null && cek_kata_sandi_uji('abcdefgh') !== null && cek_kata_sandi_uji('1234567') !== null);

/* cek_kata_sandi ada di auth.php (butuh DB), jadi diuji lewat salinan aturan yang sama */
function cek_kata_sandi_uji(string $s): ?string
{
    if (panjang($s) < 8) { return 'pendek'; }
    if (!preg_match('/[A-Za-z]/', $s) || !preg_match('/\d/', $s)) { return 'lemah'; }
    return null;
}

/* ---- Laporan ---- */
$gagal = 0;
foreach ($hasil as $h) { if (!$h[1]) { $gagal++; } }
$cli = PHP_SAPI === 'cli';
if ($cli) {
    foreach ($hasil as $h) { echo ($h[1] ? '[LULUS] ' : '[GAGAL] ') . $h[0] . "\n"; }
    echo "\n" . (count($hasil) - $gagal) . ' dari ' . count($hasil) . " uji lulus\n";
    exit($gagal ? 1 : 0);
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Uji logika · Gelanggang</title>
<style>body{font-family:system-ui,"Segoe UI",sans-serif;background:#F3F6FC;color:#1B2540;margin:0;padding:1rem}main{max-width:760px;margin:0 auto}h1{margin:.5rem 0}li{padding:.35rem .6rem;margin:.25rem 0;border:2px solid #1B2540;border-radius:8px;background:#fff;list-style:none}.ok{background:#C6F1E0}.no{background:#FFD3CB}ul{padding:0}.ringkas{font-weight:800;font-size:1.2rem}</style></head><body><main>
<h1>Uji logika inti</h1>
<p class="ringkas"><?= count($hasil) - $gagal ?> dari <?= count($hasil) ?> uji lulus<?= $gagal ? " — $gagal GAGAL" : '' ?></p>
<ul><?php foreach ($hasil as $h): ?><li class="<?= $h[1] ? 'ok' : 'no' ?>"><?= $h[1] ? '✔' : '✘' ?> <?= htmlspecialchars($h[0], ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
</main></body></html>
