(function () {
    // Menu mobile
    var tombol = document.querySelector('.nav-toggle');
    var menu = document.getElementById('menu');
    if (tombol && menu) {
        tombol.addEventListener('click', function () {
            var buka = menu.classList.toggle('terbuka');
            tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
        });
    }

    // Modal "Permintaan diterima"
    var modal = document.getElementById('modal-sukses');
    if (modal) {
        var tutup = modal.querySelector('[data-tutup]');
        var sebelumnya = document.activeElement;
        var tutupModal = function () {
            modal.hidden = true;
            if (sebelumnya && sebelumnya.focus) { sebelumnya.focus(); }
        };
        tutup.addEventListener('click', function (e) { e.preventDefault(); tutupModal(); });
        modal.addEventListener('click', function (e) { if (e.target === modal) { tutupModal(); } });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { tutupModal(); } });
        tutup.focus();
    }
})();
