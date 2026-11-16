<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin.php';

if (admin_masuk()) {
    header('Location: index.php');
    exit;
}

$galat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $terkunci = ($_SESSION['gagal_login_sampai'] ?? 0) > time();

    if ($terkunci) {
        $galat = 'Terlalu banyak percobaan. Tunggu satu menit lalu coba lagi.';
    } elseif (!csrf_valid()) {
        $galat = 'Sesi formulir kedaluwarsa. Muat ulang halaman.';
    } else {
        $user = (string) ($_POST['username'] ?? '');
        $pass = (string) ($_POST['password'] ?? '');

        // Dua perbandingan dijalankan semuanya agar waktu responsnya seragam.
        $userOk = hash_equals(ADMIN_USER, $user);
        $passOk = hash_equals(ADMIN_PASS, $pass);

        if ($userOk && $passOk) {
            session_regenerate_id(true);
            $_SESSION['admin'] = true;
            unset($_SESSION['gagal_login'], $_SESSION['gagal_login_sampai']);
            header('Location: index.php');
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

admin_head('Masuk admin');
?>
<main class="login">
    <div class="login-kotak">
        <a class="logo" href="../index.php" aria-label="Inkra, ke beranda">Inkra<?= logo_ikon() ?></a>
        <h1 class="judul">Masuk admin.</h1>

        <form class="form-admin" method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <?php if ($galat !== ''): ?>
                <p class="pesan pesan-galat" role="alert"><?= e($galat) ?></p>
            <?php endif; ?>
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>
            <button class="btn btn-gelap btn-blok" type="submit"><span>Masuk</span><?= ikon_panah() ?></button>
        </form>
        <p class="login-balik"><a class="tautan-garis" href="../index.php">Kembali ke situs</a></p>
    </div>
</main>
</body>
</html>
