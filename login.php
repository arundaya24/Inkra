<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$next = tujuan_aman((string) ($_GET['next'] ?? $_POST['next'] ?? ''), '');

if (admin_masuk()) {
    header('Location: admin/index.php');
    exit;
}
if (user_masuk()) {
    header('Location: ' . ($next !== '' ? $next : 'index.php'));
    exit;
}

$galat = '';
$id    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = bersih_teks((string) ($_POST['identitas'] ?? ''), 150);
    $terkunci = ($_SESSION['gagal_login_sampai'] ?? 0) > time();

    if ($terkunci) {
        $galat = 'Terlalu banyak percobaan. Tunggu satu menit lalu coba lagi.';
    } elseif (!csrf_valid()) {
        $galat = 'Sesi formulir kedaluwarsa. Muat ulang halaman.';
    } else {
        $pass = (string) ($_POST['password'] ?? '');

        // Admin: username + password dari config.php. Semua perbandingan dijalankan agar waktunya seragam.
        $adminOk = hash_equals(ADMIN_USER, $id) & hash_equals(ADMIN_PASS, $pass);

        // User: username (atau email) + password. Hash tiruan dipakai bila email tidak ada, supaya waktu respons tetap mirip.
        $akunUser = $id !== '' ? user_cari_masuk(users_baca(), $id) : null;
        $hashUji  = $akunUser['password_hash'] ?? '$2y$10$euFGu0oUd5XFVmvbj0KRVO.I4Zh8r4C935/GFIePurGvw95Jj.mjm';
        $userOk   = password_verify($pass, $hashUji) && $akunUser !== null;

        if ($adminOk) {
            session_regenerate_id(true);
            unset($_SESSION['user_id'], $_SESSION['gagal_login'], $_SESSION['gagal_login_sampai']);
            $_SESSION['admin'] = true;
            header('Location: admin/index.php');
            exit;
        }
        if ($userOk) {
            session_regenerate_id(true);
            unset($_SESSION['admin'], $_SESSION['gagal_login'], $_SESSION['gagal_login_sampai']);
            $_SESSION['user_id'] = $akunUser['id'];
            if (password_needs_rehash($akunUser['password_hash'], PASSWORD_DEFAULT)) {
                try { user_ganti_password($akunUser['id'], $pass); } catch (RuntimeException $ex) { /* abaikan */ }
            }
            header('Location: ' . ($next !== '' ? $next : 'index.php'));
            exit;
        }

        usleep(400000);
        $_SESSION['gagal_login'] = ($_SESSION['gagal_login'] ?? 0) + 1;
        if ($_SESSION['gagal_login'] >= 5) {
            $_SESSION['gagal_login_sampai'] = time() + 60;
            $_SESSION['gagal_login'] = 0;
        }
        $galat = 'Username atau password salah.';
    }
}

$halaman = 'login';
$judul   = 'Login — Inkra';
include __DIR__ . '/includes/header.php';
?>
<section class="panel-gelap akun-halaman">
    <div class="wrap akun-kotak">
        <p class="eyebrow eyebrow-lime">Akun</p>
        <h1 class="judul">Masuk ke Inkra.</h1>
        <p class="form-ket">Login dengan username Anda agar data pesanan terisi otomatis. Admin juga masuk lewat sini.</p>

        <form class="form" method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <?php if ($galat !== ''): ?>
                <p class="galat galat-umum" role="alert"><?= e($galat) ?></p>
            <?php endif; ?>
            <div class="field">
                <label for="identitas">Username (atau email)</label>
                <input id="identitas" name="identitas" type="text" maxlength="150" autocomplete="username" required autofocus value="<?= e($id) ?>">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="btn btn-lime btn-blok" type="submit"><span>Masuk</span><?= ikon_panah() ?></button>
            <p class="form-catatan">Belum punya akun? <a class="tautan-garis" href="daftar.php<?= $next !== '' ? '?next=' . e(rawurlencode($next)) : '' ?>">Daftar</a></p>
        </form>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
