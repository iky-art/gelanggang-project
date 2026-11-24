<?php
/* Validasi & pembersihan input. Fungsi murni: tidak menyentuh database. */

function bersih($s, int $maks = 255): string
{
    $s = trim((string)$s);
    $s = preg_replace('/\s+/u', ' ', $s) ?? '';
    return panjang($s) > $maks ? potong($s, $maks) : $s;
}

/* Bilangan bulat dalam rentang, atau null bila tidak valid. */
function bulat_dalam($v, int $min, int $maks): ?int
{
    if (is_int($v)) {
        $s = (string)$v;
    } elseif (is_string($v)) {
        $s = trim($v);
    } else {
        return null;
    }
    if (!preg_match('/^-?\d{1,9}$/', $s)) {
        return null;
    }
    $n = (int)$s;
    return ($n < $min || $n > $maks) ? null : $n;
}

/* Skor pertandingan: 0 sampai 99. */
function skor_valid($v): ?int
{
    return bulat_dalam($v, 0, 99);
}

function warna_valid($w): bool
{
    return is_string($w) && preg_match('/^#[0-9a-fA-F]{6}$/', $w) === 1;
}

/* Dari input datetime-local ("2026-12-01T08:00") ke "2026-12-01 08:00:00". */
function waktu_valid($v): ?string
{
    if (!is_string($v) || trim($v) === '') {
        return null;
    }
    $d = DateTime::createFromFormat('Y-m-d\TH:i', trim($v));
    if (!$d || $d->format('Y-m-d\TH:i') !== trim($v)) {
        return null;
    }
    return $d->format('Y-m-d H:i:00');
}

function singkatan_dari(string $nama): string
{
    $s = preg_replace('/[^A-Za-z0-9]/', '', $nama) ?? '';
    return huruf_besar(potong($s, 4));
}
