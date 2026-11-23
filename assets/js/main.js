(function () {
  var tombol = document.querySelector(".nav-toggle");
  var menu = document.getElementById("menu");
  if (tombol && menu) {
    tombol.addEventListener("click", function () {
      var buka = menu.classList.toggle("terbuka");
      tombol.setAttribute("aria-expanded", buka ? "true" : "false");
    });
  }

  var kurangGerak = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  ).matches;
  document.querySelectorAll("[data-carousel]").forEach(function (el) {
    var slide = el.querySelectorAll(".carousel-slide");
    var titik = el.querySelectorAll(".carousel-dot");
    if (slide.length < 2) {
      return;
    }

    var aktif = 0;
    var timer = null;

    function tampil(n) {
      slide[aktif].classList.remove("is-aktif");
      titik[aktif].removeAttribute("aria-current");
      aktif = (n + slide.length) % slide.length;
      slide[aktif].classList.add("is-aktif");
      titik[aktif].setAttribute("aria-current", "true");
    }
    function mulai() {
      if (kurangGerak || timer) {
        return;
      }
      timer = setInterval(function () {
        tampil(aktif + 1);
      }, 5000);
    }
    function berhenti() {
      clearInterval(timer);
      timer = null;
    }

    el.querySelector(".carousel-prev").addEventListener("click", function () {
      tampil(aktif - 1);
    });
    el.querySelector(".carousel-next").addEventListener("click", function () {
      tampil(aktif + 1);
    });
    titik.forEach(function (t, i) {
      t.addEventListener("click", function () {
        tampil(i);
      });
    });

    el.addEventListener("mouseenter", berhenti);
    el.addEventListener("mouseleave", mulai);
    el.addEventListener("focusin", berhenti);
    el.addEventListener("focusout", mulai);
    mulai();
  });

  // Modal "Permintaan diterima" (lalu otomatis ke WhatsApp)
  var modal = document.getElementById("modal-sukses");
  if (modal) {
    var tutup = modal.querySelector("[data-tutup]");
    var wa = modal.querySelector("[data-wa]");
    var sebelumnya = document.activeElement;
    var timerWa = null;

    var tutupModal = function () {
      clearTimeout(timerWa);
      var info = modal.querySelector(".modal-wa");
      if (info) {
        info.remove();
      }
      modal.hidden = true;
      if (sebelumnya && sebelumnya.focus) {
        sebelumnya.focus();
      }
    };

    tutup.addEventListener("click", function (e) {
      e.preventDefault();
      tutupModal();
    });
    modal.addEventListener("click", function (e) {
      if (e.target === modal) {
        tutupModal();
      }
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        tutupModal();
      }
    });

    if (wa) {
      wa.focus();
      timerWa = setTimeout(function () {
        window.location.href = wa.href;
      }, 2500);
    } else {
      tutup.focus();
    }
  }
})();
