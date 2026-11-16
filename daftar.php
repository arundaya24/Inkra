<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$next = tujuan_aman((string) ($_GET['next'] ?? $_POST['next'] ?? ''), '');

if (admin_masuk()) {
    header('Location: admin/index.php');
    exit;
}
if (user_masuk()) {
    header('Location: profil.php');
    exit;
}

$errors = [];
$old    = ['nama' => '', 'email' => '', 'telepon' => '', 'alamat' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$data, $errors] = validasi_profil($_POST);
    $old = $data;
    $old['telepon'] = bersih_teks((string) ($_POST['telepon'] ?? ''), 25);
    $pass  = (string) ($_POST['password'] ?? '');
    $pass2 = (string) ($_POST['password2'] ?? '');

    if (!csrf_valid()) {
        $errors['_umum'] = 'Sesi formulir kedaluwarsa. Muat ulang halaman.';
    } elseif (($_POST['situs'] ?? '') !== '') {
        header('Location: index.php');       // honeypot
        exit;
    } elseif (isset($_SESSION['terakhir_daftar']) && time() - $_SESSION['terakhir_daftar'] < 5) {
        $errors['_umum'] = 'Terlalu cepat. Tunggu sebentar lalu coba lagi.';
    }

    if (!isset($errors['_umum'])) {
        if (($g = galat_password($pass)) !== null) {
            $errors['password'] = $g;
        } elseif (!hash_equals($pass, $pass2)) {
            $errors['password2'] = 'Konfirmasi password tidak sama.';
        }
    }

    if (!$errors) {
        try {
            $baru = user_buat($data, $pass);
            session_regenerate_id(true);
            $_SESSION['user_id'] = $baru['id'];
            $_SESSION['terakhir_daftar'] = time();
            header('Location: ' . ($next !== '' ? $next : 'profil.php'));
            exit;
        } catch (RuntimeException $ex) {
            $errors[str_contains($ex->getMessage(), 'Email') ? 'email' : '_umum'] = $ex->getMessage();
        }
    }
}

$val  = fn(string $k): string => e($old[$k] ?? '');
$err  = fn(string $k): string => isset($errors[$k]) ? '<p class="galat" id="err-' . $k . '">' . e($errors[$k]) . '</p>' : '';
$aria = fn(string $k): string => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : '';

$halaman = 'login';
$judul   = 'Daftar — Inkra';
include __DIR__ . '/includes/header.php';
?>
<section class="panel-gelap akun-halaman">
    <div class="wrap akun-kotak">
        <p class="eyebrow eyebrow-lime">Akun</p>
        <h1 class="judul">Buat akun.</h1>
        <p class="form-ket">Simpan data Anda sekali, pesan berikutnya tinggal klik.</p>

        <form class="form" method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="next" value="<?= e($next) ?>">
            <div class="jebakan" aria-hidden="true">
                <label>Jangan diisi <input type="text" name="situs" tabindex="-1" autocomplete="off"></label>
            </div>
            <?php if (isset($errors['_umum'])): ?>
                <p class="galat galat-umum" role="alert"><?= e($errors['_umum']) ?></p>
            <?php endif; ?>
            <div class="baris">
                <div class="field">
                    <label for="nama">Nama</label>
                    <input id="nama" name="nama" type="text" maxlength="100" autocomplete="name" required value="<?= $val('nama') ?>"<?= $aria('nama') ?>>
                    <?= $err('nama') ?>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" maxlength="150" autocomplete="email" required value="<?= $val('email') ?>"<?= $aria('email') ?>>
                    <?= $err('email') ?>
                </div>
            </div>
            <div class="field">
                <label for="telepon">Nomor telepon (opsional)</label>
                <input id="telepon" name="telepon" type="tel" inputmode="tel" maxlength="25" placeholder="0852-1793-0056" autocomplete="tel" value="<?= $val('telepon') ?>"<?= $aria('telepon') ?>>
                <?= $err('telepon') ?>
            </div>
            <div class="baris">
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required<?= $aria('password') ?>>
                    <?= $err('password') ?>
                </div>
                <div class="field">
                    <label for="password2">Ulangi password</label>
                    <input id="password2" name="password2" type="password" minlength="8" autocomplete="new-password" required<?= $aria('password2') ?>>
                    <?= $err('password2') ?>
                </div>
            </div>
            <button class="btn btn-lime btn-blok" type="submit"><span>Daftar</span><?= ikon_panah() ?></button>
            <p class="form-catatan">Sudah punya akun? <a class="tautan-garis" href="login.php<?= $next !== '' ? '?next=' . e(rawurlencode($next)) : '' ?>">Login</a></p>
        </form>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
