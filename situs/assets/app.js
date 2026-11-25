/* Gelanggang · situs resmi. JavaScript murni, tanpa pustaka.
   Fungsi hitung di bawah adalah penulisan ulang algoritma app/logic/bagan.php dan jadwal.php. */
(function () {
  'use strict';

  /* ---------- Logika (diuji terpisah di Node) ---------- */

  /** Urutan seed standar untuk bagan berukuran p (pangkat dua): 8 -> [1,8,4,5,2,7,3,6]. */
  function urutanSeed(p) {
    var l = [1];
    while (l.length < p) {
      var m = l.length, baru = [];
      for (var i = 0; i < m; i++) { baru.push(l[i], 2 * m + 1 - l[i]); }
      l = baru;
    }
    return l;
  }

  function labelBabak(jumlahLaga) {
    if (jumlahLaga === 1) { return 'Final'; }
    if (jumlahLaga === 2) { return 'Semifinal'; }
    if (jumlahLaga === 4) { return 'Perempat Final'; }
    return (jumlahLaga * 2) + ' Besar';
  }

  /** Ringkasan bagan gugur untuk n tim. */
  function hitungBagan(n) {
    var p = 2;
    while (p < n) { p *= 2; }
    var urut = urutanSeed(p), r1 = [], bye = 0;
    for (var i = 0; i < p / 2; i++) {
      var a = urut[2 * i], b = urut[2 * i + 1];
      var ada = function (s) { return s <= n; };
      if (ada(a) && ada(b)) { r1.push({a: a, b: b, bye: false}); }
      else { r1.push({a: ada(a) ? a : b, b: null, bye: true}); bye++; }
    }
    var putaran = Math.round(Math.log(p) / Math.log(2)), ronde = [];
    for (var r = 1; r <= putaran; r++) {
      var jml = p / Math.pow(2, r);
      ronde.push({nama: labelBabak(jml), laga: jml});
    }
    return {ukuran: p, bye: bye, putaran: putaran, r1: r1, ronde: ronde, dimainkan: n - 1};
  }

  /** Metode lingkaran. Mengembalikan daftar ronde: [[ [a,b], ... ], ...] dan tim yang istirahat. */
  function hitungLiga(n) {
    var pool = [], i;
    for (i = 1; i <= n; i++) { pool.push(i); }
    if (n % 2 === 1) { pool.push(null); }
    var m = pool.length, rondeList = [];
    for (var r = 0; r < m - 1; r++) {
      var pasang = [], istirahat = null;
      for (i = 0; i < m / 2; i++) {
        var a = pool[i], b = pool[m - 1 - i];
        if (a === null || b === null) { istirahat = a === null ? b : a; continue; }
        pasang.push([a, b]);
      }
      rondeList.push({pasang: pasang, istirahat: istirahat});
      pool.splice(1, 0, pool.pop());
    }
    return rondeList;
  }

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = {urutanSeed: urutanSeed, hitungBagan: hitungBagan, hitungLiga: hitungLiga, labelBabak: labelBabak};
    return;
  }

  /* ---------- Antarmuka ---------- */

  function el(tag, teks, kelas) {
    var e = document.createElement(tag);
    if (teks !== undefined) { e.textContent = teks; }
    if (kelas) { e.className = kelas; }
    return e;
  }
  function bulat(nilai, min, maks, bawaan) {
    var n = parseInt(nilai, 10);
    if (isNaN(n)) { return bawaan; }
    return Math.max(min, Math.min(maks, n));
  }

  /* Kalkulator bagan */
  var inBagan = document.getElementById('in-bagan'), outBagan = document.getElementById('out-bagan');
  function gambarBagan() {
    var n = bulat(inBagan.value, 2, 32, 6), h = hitungBagan(n);
    outBagan.textContent = '';
    var ringkas = el('p', '', 'ringkas');
    ringkas.innerHTML = '<b></b> tim → bagan <b></b>, <b></b> bye, <b></b> ronde, <b></b> laga dimainkan';
    var b = ringkas.querySelectorAll('b');
    b[0].textContent = n; b[1].textContent = h.ukuran; b[2].textContent = h.bye; b[3].textContent = h.putaran; b[4].textContent = h.dimainkan;
    outBagan.appendChild(ringkas);
    var judul = el('h4', 'Ronde 1', ''); judul.style.cssText = 'margin:0 0 .4rem;font-size:.95rem;letter-spacing:.04em;text-transform:uppercase';
    outBagan.appendChild(judul);
    var ul = el('ul', undefined, 'chip-daftar');
    h.r1.forEach(function (l) {
      ul.appendChild(el('li', l.bye ? 'Seed ' + l.a + ' · bye' : 'Seed ' + l.a + ' vs Seed ' + l.b, l.bye ? 'bye' : ''));
    });
    outBagan.appendChild(ul);
    var sisa = h.ronde.slice(1);
    if (sisa.length) {
      var p = el('p', 'Lanjut: ' + sisa.map(function (r) { return r.nama + ' (' + r.laga + ' laga)'; }).join(' → '), 'catatan');
      outBagan.appendChild(p);
    }
  }

  /* Kalkulator liga */
  var inLiga = document.getElementById('in-liga'), inPP = document.getElementById('in-pp'), outLiga = document.getElementById('out-liga');
  function gambarLiga() {
    var n = bulat(inLiga.value, 2, 12, 5), pp = inPP.checked, daftar = hitungLiga(n);
    var total = n * (n - 1) / 2 * (pp ? 2 : 1), jumlahRonde = daftar.length * (pp ? 2 : 1);
    outLiga.textContent = '';
    var ringkas = el('p', '', 'ringkas');
    ringkas.innerHTML = '<b></b> tim → <b></b> ronde, <b></b> laga' + (pp ? ' (pulang-pergi)' : '');
    var b = ringkas.querySelectorAll('b');
    b[0].textContent = n; b[1].textContent = jumlahRonde; b[2].textContent = total;
    outLiga.appendChild(ringkas);
    var gulir = el('div', undefined, 'ronde-gulir');
    daftar.forEach(function (r, i) {
      var blok = el('div', undefined, 'ronde');
      blok.appendChild(el('h4', 'Ronde ' + (i + 1) + (r.istirahat ? ' · Tim ' + r.istirahat + ' istirahat' : '')));
      var ul = el('ul', undefined, 'chip-daftar');
      r.pasang.forEach(function (p) { ul.appendChild(el('li', 'Tim ' + p[0] + ' vs Tim ' + p[1])); });
      blok.appendChild(ul);
      gulir.appendChild(blok);
    });
    outLiga.appendChild(gulir);
    if (pp) { outLiga.appendChild(el('p', 'Putaran kedua mengulang semua pasangan dengan posisi tim ditukar.', 'catatan')); }
  }

  if (inBagan) { inBagan.addEventListener('input', gambarBagan); gambarBagan(); }
  if (inLiga) { inLiga.addEventListener('input', gambarLiga); inPP.addEventListener('change', gambarLiga); gambarLiga(); }

  /* Tombol salin kode */
  document.querySelectorAll('[data-salin]').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
      var sumber = document.getElementById(tombol.getAttribute('data-salin'));
      if (!sumber) { return; }
      var teks = sumber.textContent, asli = tombol.textContent;
      var selesai = function (ok) {
        tombol.textContent = ok ? 'Tersalin' : 'Salin manual';
        setTimeout(function () { tombol.textContent = asli; }, 1800);
      };
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(teks).then(function () { selesai(true); }, function () { selesai(false); });
      } else {
        var r = document.createRange(); r.selectNodeContents(sumber);
        var s = window.getSelection(); s.removeAllRanges(); s.addRange(r);
        selesai(false);
      }
    });
  });

  /* Video: tampilkan pesan bila berkas belum ada */
  var video = document.getElementById('video-demo'), galat = document.getElementById('video-galat');
  if (video && galat) {
    var sumber = video.querySelector('source');
    var tampil = function () { video.hidden = true; galat.hidden = false; };
    if (sumber) { sumber.addEventListener('error', tampil); }
    video.addEventListener('error', tampil);
  }

  /* Hitung naik pada papan skor hero (hanya bila gerak diizinkan; nilai akhir sudah ada di HTML) */
  var boleh = !(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
  if (boleh) {
    document.querySelectorAll('[data-hitung]').forEach(function (e) {
      var akhir = parseInt(e.getAttribute('data-hitung'), 10), mulai = null;
      if (isNaN(akhir) || akhir === 0) { return; }
      e.textContent = '0';
      var langkah = function (t) {
        if (mulai === null) { mulai = t; }
        var p = Math.min(1, (t - mulai) / 900);
        e.textContent = String(Math.round(akhir * p));
        if (p < 1) { requestAnimationFrame(langkah); }
      };
      requestAnimationFrame(langkah);
    });
  }

  /* Tandai menu aktif sesuai bagian yang terlihat */
  var tautan = document.querySelectorAll('.nav a[href^="#"]');
  if ('IntersectionObserver' in window && tautan.length) {
    var peta = {};
    tautan.forEach(function (a) { peta[a.getAttribute('href').slice(1)] = a; });
    var io = new IntersectionObserver(function (daftar) {
      daftar.forEach(function (d) {
        if (d.isIntersecting && peta[d.target.id]) {
          tautan.forEach(function (a) { a.removeAttribute('aria-current'); });
          peta[d.target.id].setAttribute('aria-current', 'true');
        }
      });
    }, {rootMargin: '-45% 0px -50% 0px'});
    Object.keys(peta).forEach(function (id) { var s = document.getElementById(id); if (s) { io.observe(s); } });
  }
})();
