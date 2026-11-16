# Website Inkra (HTML + PHP native)

## Menjalankan
- **Laragon/XAMPP**: salin folder `inkra` ke `htdocs`/`www`, buka `http://localhost/inkra/`.
- **PHP built-in server** (PHP 8.1+): `php -S localhost:8000` di dalam folder ini.

## Admin
- Admin login lewat tombol **Login** di navbar (`/login.php`), atau tombol **Admin** di footer.
- Username `admin`, password `admin123`. **Ganti sebelum situs dipasang online** (`ADMIN_USER` dan `ADMIN_PASS` di `config.php`).
- Dashboard: ubah carousel beranda, gambar Kaos/Jaket/Totebag, dan nomor telepon admin.
- Nomor telepon dipakai untuk footer, tombol Kontak Kami, dan tujuan pesan WhatsApp dari halaman Minta Penawaran.
- Pengaturan tersimpan di `data/pengaturan.json`, gambar unggahan di `uploads/`. Kedua folder harus bisa ditulis oleh PHP.

## Akun pengguna (guest, user, admin)

- Pengunjung yang belum login dihitung sebagai **Guest** (label di navbar kanan atas) dan tetap bisa mengirim permintaan penawaran.
- Tombol **Login** ada di navbar kanan atas. Halaman `login.php` dipakai bersama oleh user (email + password) dan admin (username + password dari `config.php`). Admin langsung diarahkan ke dashboard.
- `daftar.php`: pengunjung membuat akun (nama, email, telepon opsional, password minimal 8 karakter).
- `profil.php`: user mengubah nama, email, telepon, alamat, dan password.
- Saat user login, formulir **Minta Penawaran** terisi otomatis dari profil (nama, email, telepon, alamat) dan masih bisa diubah per pesanan.
- Dashboard admin → **Akun pengguna** (`admin/pengguna.php`): admin hanya melihat nama, email, dan telepon. Alamat dan hash password tidak pernah dikirim ke halaman itu.
- Dashboard admin → **Nama kontak**: mengganti nama di footer (sebelumnya "Anggra", kini disimpan di `data/pengaturan.json`; nilai bawaan tetap `KONTAK_NAMA` di `config.php`).
- Data akun disimpan di `data/users.json` (password berupa hash). File ini otomatis dibuat saat ada pendaftaran pertama, jadi folder `data/` harus bisa ditulis PHP. Perlu ekstensi PHP `mbstring`.

## Struktur
- `index.php`, `media-sablon.php`, `cara-pesan.php`, `tanya-jawab.php`, `minta-penawaran.php` — halaman
- `login.php`, `daftar.php`, `profil.php`, `logout.php` — akun user dan admin
- `admin/` — dashboard, akun pengguna, logout (`admin/login.php` hanya mengarahkan ke `login.php`)
- `includes/` — header, footer, data (produk/langkah/FAQ), helper admin, `auth.php` (akun user), dan potongan seksi
- `kirim.php` — memproses form (validasi, CSRF, honeypot), menyimpan ke `data/permintaan.jsonl`, lalu mengarahkan ke WhatsApp
- `config.php` — nama situs, alamat, akun admin, `EMAIL_ADMIN` (opsional)
- `assets/img/` — foto bawaan (placeholder dari desain); bisa diganti lewat dashboard

Folder `data/` dilindungi `.htaccess` (Apache). Di Nginx, blok akses ke folder tersebut dan matikan eksekusi PHP di `uploads/` secara manual.

## Versi statis (tanpa PHP)
Folder `statis/` berisi versi HTML + CSS + JavaScript saja (tanpa admin). Buka `statis/index.html` langsung di browser.
