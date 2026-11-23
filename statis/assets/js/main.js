(function () {
    'use strict';

    /* ---------- Menu mobile ---------- */
    var tombolMenu = document.querySelector('.nav-toggle');
    var menu = document.getElementById('menu');
    if (tombolMenu && menu) {
        tombolMenu.addEventListener('click', function () {
            var buka = menu.classList.toggle('terbuka');
            tombolMenu.setAttribute('aria-expanded', buka ? 'true' : 'false');
        });
    }

    /* ---------- Modal "Permintaan diterima" (lalu otomatis ke WhatsApp) ---------- */
    var NOMOR_WA = '6285217930056';   // ganti dengan nomor penjual (format 62...)
    var modal = document.getElementById('modal-sukses');
    var elemenSebelumnya = null;
    var timerWa = null;

    function bukaModal(urlWa) {
        if (!modal) { return; }
        elemenSebelumnya = document.activeElement;
        var tautan = modal.querySelector('[data-wa]');
        tautan.href = urlWa;
        modal.hidden = false;
        tautan.focus();
        timerWa = setTimeout(function () { window.location.href = urlWa; }, 2500);
    }

    function tutupModal() {
        if (!modal || modal.hidden) { return; }
        clearTimeout(timerWa);
        var info = modal.querySelector('.modal-wa');
        if (info) { info.remove(); }
        modal.hidden = true;
        if (elemenSebelumnya && elemenSebelumnya.focus) { elemenSebelumnya.focus(); }
    }

    if (modal) {
        modal.querySelector('[data-tutup]').addEventListener('click', tutupModal);
        modal.addEventListener('click', function (e) { if (e.target === modal) { tutupModal(); } });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { tutupModal(); } });
    }

    function susunPesan(f) {
        var v = function (n) { return f.elements[n].value.trim(); };
        var baris = [
            'Halo Inkra, saya ingin minta penawaran sablon.',
            '',
            'Nama: ' + v('nama'),
            'Produk: ' + v('produk'),
            'Jumlah: ' + v('jumlah')
        ];
        if (v('target')) { baris.push('Target selesai: ' + v('target')); }
        if (v('rencana')) { baris.push('Rencana: ' + v('rencana').slice(0, 1000)); }
        if (v('alamat')) { baris.push('Alamat: ' + v('alamat').slice(0, 500)); }
        return baris.join('\n');
    }

    /* ---------- Form permintaan penawaran ---------- */
    var form = document.getElementById('form-penawaran');
    if (!form) { return; }

    // Isi otomatis kolom produk dari ?produk=Kaos
    var produkDariUrl = new URLSearchParams(window.location.search).get('produk');
    if (produkDariUrl && form.elements.produk) {
        form.elements.produk.value = produkDariUrl.slice(0, 150);
    }

    var wajib = ['nama', 'produk', 'jumlah'];
    var pesanKosong = {
        nama: 'Nama wajib diisi.',
        produk: 'Sebutkan produk yang ingin disablon.',
        jumlah: 'Isi perkiraan jumlah.'
    };

    function tampilkanGalat(input, pesan) {
        var id = 'err-' + input.name;
        var el = document.getElementById(id);
        if (pesan) {
            if (!el) {
                el = document.createElement('p');
                el.className = 'galat';
                el.id = id;
                input.parentNode.appendChild(el);
            }
            el.textContent = pesan;
            input.setAttribute('aria-invalid', 'true');
            input.setAttribute('aria-describedby', id);
        } else {
            if (el) { el.remove(); }
            input.removeAttribute('aria-invalid');
            input.removeAttribute('aria-describedby');
        }
    }

    function periksa(nama) {
        var input = form.elements[nama];
        var nilai = input.value.trim();
        var pesan = '';
        if (nilai === '') {
            pesan = pesanKosong[nama];
        }
        tampilkanGalat(input, pesan);
        return pesan === '';
    }

    wajib.forEach(function (nama) {
        form.elements[nama].addEventListener('input', function () {
            if (this.hasAttribute('aria-invalid')) { periksa(nama); }
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var inputPertamaSalah = null;
        wajib.forEach(function (nama) {
            if (!periksa(nama) && !inputPertamaSalah) {
                inputPertamaSalah = form.elements[nama];
            }
        });
        if (inputPertamaSalah) {
            inputPertamaSalah.focus();
            return;
        }
        var urlWa = 'https://wa.me/' + NOMOR_WA + '?text=' + encodeURIComponent(susunPesan(form));
        form.reset();
        bukaModal(urlWa);
    });
})();
