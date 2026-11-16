# Website Inkra (HTML + PHP native)

## Menjalankan
- **Laragon/XAMPP**: salin folder `inkra` ke `htdocs`/`www`, buka `http://localhost/inkra/`.
- **PHP built-in server** (PHP 8.1+): `php -S localhost:8000` di dalam folder ini.

## Admin
- Tombol **Admin** ada di footer. Login di `/admin/login.php`.
- Username `admin`, password `admin123`. **Ganti sebelum situs dipasang online** (`ADMIN_USER` dan `ADMIN_PASS` di `config.php`).
- Dashboard: ubah carousel beranda, gambar Kaos/Jaket/Totebag, dan nomor telepon admin.
- Nomor telepon dipakai untuk footer, tombol Kontak Kami, dan tujuan pesan WhatsApp dari halaman Minta Penawaran.
- Pengaturan tersimpan di `data/pengaturan.json`, gambar unggahan di `uploads/`. Kedua folder harus bisa ditulis oleh PHP.

## Struktur
- `index.php`, `media-sablon.php`, `cara-pesan.php`, `tanya-jawab.php`, `minta-penawaran.php` — halaman
- `admin/` — login, dashboard, logout
- `includes/` — header, footer, data (produk/langkah/FAQ), helper admin, dan potongan seksi
- `kirim.php` — memproses form (validasi, CSRF, honeypot), menyimpan ke `data/permintaan.jsonl`, lalu mengarahkan ke WhatsApp
- `config.php` — nama situs, alamat, akun admin, `EMAIL_ADMIN` (opsional)
- `assets/img/` — foto bawaan (placeholder dari desain); bisa diganti lewat dashboard

Folder `data/` dilindungi `.htaccess` (Apache). Di Nginx, blok akses ke folder tersebut dan matikan eksekusi PHP di `uploads/` secara manual.

## Versi statis (tanpa PHP)
Folder `statis/` berisi versi HTML + CSS + JavaScript saja (tanpa admin). Buka `statis/index.html` langsung di browser.
