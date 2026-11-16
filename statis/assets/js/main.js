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

    /* ---------- Modal "Permintaan diterima" ---------- */
    var modal = document.getElementById('modal-sukses');
    var elemenSebelumnya = null;

    function bukaModal() {
        if (!modal) { return; }
        elemenSebelumnya = document.activeElement;
        modal.hidden = false;
        modal.querySelector('[data-tutup]').focus();
    }

    function tutupModal() {
        if (!modal || modal.hidden) { return; }
        modal.hidden = true;
        if (elemenSebelumnya && elemenSebelumnya.focus) { elemenSebelumnya.focus(); }
    }

    if (modal) {
        modal.querySelector('[data-tutup]').addEventListener('click', tutupModal);
        modal.addEventListener('click', function (e) { if (e.target === modal) { tutupModal(); } });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { tutupModal(); } });
    }

    /* ---------- Form permintaan penawaran ---------- */
    var form = document.getElementById('form-penawaran');
    if (!form) { return; }

    // Isi otomatis kolom produk dari ?produk=Kaos
    var produkDariUrl = new URLSearchParams(window.location.search).get('produk');
    if (produkDariUrl && form.elements.produk) {
        form.elements.produk.value = produkDariUrl.slice(0, 150);
    }

    var POLA_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var wajib = ['nama', 'email', 'produk', 'jumlah'];
    var pesanKosong = {
        nama: 'Nama wajib diisi.',
        email: 'Email wajib diisi.',
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
        } else if (nama === 'email' && !POLA_EMAIL.test(nilai)) {
            pesan = 'Format email belum benar.';
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
        form.reset();
        bukaModal();
    });
})();
