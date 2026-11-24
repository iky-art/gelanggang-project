<?php
/* Potongan tampilan yang dipakai berulang (chip tim, laga, klasemen, bagan). */

function lencana_status(string $status): string
{
    $peta = [
        'draft'   => ['Pendaftaran', 'sun'],
        'grup'    => ['Babak grup', 'sky'],
        'gugur'   => ['Babak gugur', 'coral'],
        'selesai' => ['Selesai', 'mint'],
    ];
    $p = $peta[$status] ?? [$status, 'sky'];
    return '<span class="lencana lencana-' . e($p[1]) . '">' . e($p[0]) . '</span>';
}

function chip_tim(?string $nama, ?string $warna = null, ?string $singkatan = null): string
{
    if ($nama === null || $nama === '') {
        return '<span class="chip chip-kosong">Menunggu pemenang</span>';
    }
    $w = warna_valid($warna) ? $warna : '#2F6BFF';
    $s = $singkatan ? '<small>' . e($singkatan) . '</small>' : '';
    return '<span class="chip" style="--w:' . e($w) . '"><i aria-hidden="true"></i><span class="chip-nama">' . e($nama) . '</span>' . $s . '</span>';
}

function nama_babak_grup(?string $namaGrup): string
{
    if ($namaGrup === null || $namaGrup === '') {
        return 'Grup';
    }
    return $namaGrup === 'Liga' ? 'Liga' : 'Grup ' . $namaGrup;
}

/** Satu baris laga. $o: ['turnamen'=>bool, 'aksi'=>html] */
function baris_laga(array $m, array $o = []): string
{
    $meta = [];
    if (!empty($o['turnamen']) && !empty($m['nama_turnamen'])) {
        $meta[] = e($m['nama_turnamen']);
    }
    if ($m['fase'] === 'grup') {
        $meta[] = e(nama_babak_grup($m['nama_grup'] ?? null)) . ' · Ronde ' . (int)$m['ronde'];
    } else {
        $meta[] = e($m['label'] ?: 'Babak gugur');
    }
    $meta[] = e(tanggal_id($m['waktu'] ?? null));
    if (!empty($m['lokasi'])) {
        $meta[] = e($m['lokasi']);
    }

    $selesai = $m['status'] === 'selesai' && !$m['bye'];
    $menangA = $selesai && !empty($m['pemenang_id']) && (int)$m['pemenang_id'] === (int)$m['tim_a_id'];
    $menangB = $selesai && !empty($m['pemenang_id']) && (int)$m['pemenang_id'] === (int)$m['tim_b_id'];

    if ($m['bye']) {
        $tengah = '<span class="laga-vs">Bye</span>';
    } elseif ($selesai) {
        $tengah = '<span class="laga-skor"><b>' . (int)$m['skor_a'] . '</b><i>:</i><b>' . (int)$m['skor_b'] . '</b></span>';
    } else {
        $tengah = '<span class="laga-vs">vs</span>';
    }

    $h = '<div class="laga' . ($selesai ? ' laga-selesai' : '') . '">';
    $h .= '<div class="laga-meta">' . implode(' <span class="titik">•</span> ', $meta) . '</div>';
    $h .= '<div class="laga-isi">';
    $h .= '<div class="laga-sisi' . ($menangA ? ' menang' : '') . '">' . chip_tim($m['nama_a'] ?? null, $m['warna_a'] ?? null) . '</div>';
    $h .= $tengah;
    $h .= '<div class="laga-sisi laga-sisi-b' . ($menangB ? ' menang' : '') . '">' . chip_tim($m['nama_b'] ?? null, $m['warna_b'] ?? null) . '</div>';
    $h .= '</div>';
    if (!empty($o['aksi'])) {
        $h .= '<div class="laga-aksi">' . $o['aksi'] . '</div>';
    }
    return $h . '</div>';
}

/** Tabel klasemen satu grup. $lolos = jumlah baris teratas yang diberi tanda lolos. */
function tabel_klasemen(array $baris, int $lolos = 0): string
{
    if (!$baris) {
        return '<p class="teks-redup">Belum ada tim di grup ini.</p>';
    }
    $h = '<div class="table-responsive"><table class="table tabel-klasemen align-middle">';
    $h .= '<thead><tr><th scope="col">#</th><th scope="col">Tim</th><th scope="col" title="Main">M</th><th scope="col" title="Menang">Mn</th><th scope="col" title="Seri">S</th><th scope="col" title="Kalah">K</th><th scope="col" title="Gol/skor memasukkan">GM</th><th scope="col" title="Gol/skor kemasukan">GK</th><th scope="col" title="Selisih">SG</th><th scope="col" class="poin">Poin</th></tr></thead><tbody>';
    foreach ($baris as $i => $r) {
        $kelas = ($lolos > 0 && $i < $lolos) ? ' class="lolos"' : '';
        $tanda = !empty($r['terikat']) ? ' <span class="tanda-seri" title="Seri total dengan tim lain: butuh nilai undian">=</span>' : '';
        $sg = ($r['selisih'] > 0 ? '+' : '') . $r['selisih'];
        $h .= '<tr' . $kelas . '><td class="no">' . (int)$r['peringkat'] . '</td><td>' . chip_tim($r['nama'], $r['warna'] ?? null) . $tanda . '</td>';
        $h .= '<td>' . $r['main'] . '</td><td>' . $r['menang'] . '</td><td>' . $r['seri'] . '</td><td>' . $r['kalah'] . '</td><td>' . $r['gm'] . '</td><td>' . $r['gk'] . '</td><td>' . e($sg) . '</td><td class="poin"><b>' . $r['poin'] . '</b></td></tr>';
    }
    return $h . '</tbody></table></div>';
}

/** Bagan gugur: satu kolom per ronde. $laga = hasil laga_turnamen(id,'gugur'). */
function bagan_html(array $laga): string
{
    if (!$laga) {
        return '<p class="teks-redup">Bagan belum dibuat.</p>';
    }
    $kolom = [];
    $juaraTiga = null;
    foreach ($laga as $m) {
        if ($m['label'] === 'Perebutan Juara 3') {
            $juaraTiga = $m;
            continue;
        }
        $kolom[(int)$m['ronde']][] = $m;
    }
    ksort($kolom);
    $kartu = function (array $m): string {
        $selesai = $m['status'] === 'selesai';
        $baris = function ($nama, $warna, $skor, $menang) use ($m, $selesai) {
            $isi = $nama === null ? '<span class="teks-redup">Menunggu</span>' : '<span class="bt-nama"><i style="background:' . e(warna_valid($warna) ? $warna : '#2F6BFF') . '"></i>' . e($nama) . '</span>';
            $s = ($selesai && !$m['bye'] && $nama !== null) ? '<b>' . (int)$skor . '</b>' : '';
            return '<div class="bagan-tim' . ($menang ? ' menang' : '') . '">' . $isi . $s . '</div>';
        };
        $mA = $selesai && !empty($m['pemenang_id']) && (int)$m['pemenang_id'] === (int)$m['tim_a_id'];
        $mB = $selesai && !empty($m['pemenang_id']) && (int)$m['pemenang_id'] === (int)$m['tim_b_id'];
        $h = '<div class="bagan-laga">';
        $h .= $baris($m['nama_a'], $m['warna_a'], $m['skor_a'], $mA);
        $h .= $baris($m['nama_b'], $m['warna_b'], $m['skor_b'], $mB);
        if ($m['bye']) {
            $h .= '<div class="bagan-info">Bye</div>';
        } elseif (!empty($m['waktu'])) {
            $h .= '<div class="bagan-info">' . e(tanggal_id($m['waktu'])) . '</div>';
        }
        return $h . '</div>';
    };
    $h = '<div class="bagan-gulir"><div class="bagan">';
    foreach ($kolom as $ronde => $daftar) {
        $h .= '<div class="bagan-kolom"><h3>' . e($daftar[0]['label']) . '</h3><div class="bagan-isi">';
        foreach ($daftar as $m) {
            $h .= $kartu($m);
        }
        $h .= '</div></div>';
    }
    $h .= '</div></div>';
    if ($juaraTiga) {
        $h .= '<div class="juara-tiga"><h3>Perebutan Juara 3</h3>' . $kartu($juaraTiga) . '</div>';
    }
    return $h;
}

function kartu_kosong(string $judul, string $teks, string $aksiHtml = ''): string
{
    return '<div class="kosong"><h3>' . e($judul) . '</h3><p>' . e($teks) . '</p>' . $aksiHtml . '</div>';
}

function label_format(string $f): string
{
    return ['liga_gugur' => 'Grup + gugur', 'liga' => 'Liga', 'gugur' => 'Gugur'][$f] ?? $f;
}
