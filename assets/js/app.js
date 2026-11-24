/* Gelanggang — interaksi kecil. Tanpa pustaka, tanpa skrip inline (selaras dengan CSP). */
(function () {
  'use strict';

  /* 1. Konfirmasi aksi berbahaya: <form data-konfirmasi="Teks pertanyaan"> */
  function tampilKonfirmasi(teks, lanjut) {
    if (!window.HTMLDialogElement) {
      if (window.confirm(teks)) { lanjut(); }
      return;
    }
    var d = document.createElement('dialog');
    d.className = 'dialog-konfirmasi';
    d.setAttribute('aria-label', 'Konfirmasi');
    d.innerHTML = '<form method="dialog"><p></p><div class="aksi-form">' +
      '<button value="batal" class="btn tombol-garis">Batal</button>' +
      '<button value="ok" class="btn tombol-bahaya" autofocus>Ya, lanjutkan</button></div></form>';
    d.querySelector('p').textContent = teks;
    d.addEventListener('close', function () {
      var setuju = d.returnValue === 'ok';
      d.remove();
      if (setuju) { lanjut(); }
    });
    document.body.appendChild(d);
    d.showModal();
  }

  document.addEventListener('submit', function (e) {
    var f = e.target;
    var teks = f && f.getAttribute ? f.getAttribute('data-konfirmasi') : null;
    if (!teks) { return; }
    e.preventDefault();
    tampilKonfirmasi(teks, function () { f.submit(); });
  });

  /* 2. Formulir turnamen: tampilkan kolom sesuai format yang dipilih */
  var pilihFormat = document.getElementById('format');
  if (pilihFormat) {
    var terapkan = function () {
      var v = pilihFormat.value;
      document.querySelectorAll('[data-hanya-format]').forEach(function (el) {
        el.hidden = el.getAttribute('data-hanya-format').split(' ').indexOf(v) === -1;
      });
    };
    pilihFormat.addEventListener('change', terapkan);
    terapkan();
  }
})();
