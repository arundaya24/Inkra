# Website Inkra (HTML + PHP native)

## Menjalankan
- **Laragon/XAMPP**: salin folder `inkra` ke `htdocs`/`www`, buka `http://localhost/inkra/`.
- **PHP built-in server** (PHP 8.1+): `php -S localhost:8000` di dalam folder ini.

## Struktur
- `index.php`, `media-sablon.php`, `cara-pesan.php`, `tanya-jawab.php`, `minta-penawaran.php` — halaman
- `includes/` — header, footer, data (produk/langkah/FAQ), dan potongan seksi
- `kirim.php` — memproses form (validasi, CSRF, honeypot) lalu menyimpan ke `data/permintaan.jsonl`
- `config.php` — nama situs, alamat, nomor WA, `EMAIL_ADMIN` (opsional, untuk notifikasi email)
- `assets/img/` — foto placeholder dipotong dari desain; **ganti dengan foto asli** (nama file sama)

Folder `data/` dilindungi `.htaccess` (Apache). Di Nginx, blok akses ke folder tersebut secara manual.

## Versi statis (tanpa PHP)
Folder `statis/` berisi versi HTML + CSS + JavaScript saja. Buka `statis/index.html` langsung di browser.
Folder ini berdiri sendiri (punya `assets/` sendiri), jadi tidak mengganggu versi PHP.
