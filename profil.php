<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

if (admin_masuk()) {
    header('Location: admin/index.php');
    exit;
}
wajib_user();

$akun   = pengguna();
$errors = [];
$errPw  = [];
$old    = profil_untuk_form();
$pesan  = flash('pesan_profil');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = (string) ($_POST['aksi'] ?? '');

    if (!csrf_valid()) {
        $errors['_umum'] = 'Sesi formulir kedaluwarsa. Muat ulang halaman.';
    } elseif ($aksi === 'profil') {
        [$data, $errors] = validasi_profil($_POST);
        $old = $data;
        $old['telepon'] = bersih_teks((string) ($_POST['telepon'] ?? ''), 25);
        if (!$errors) {
            try {
                user_perbarui($akun['id'], $data);
                $_SESSION['pesan_profil'] = ['ok', 'Profil diperbarui.'];
                header('Location: profil.php');
                exit;
            } catch (DataSudahDipakai $ex) {
                $errors = $ex->galat;
            } catch (RuntimeException $ex) {
                $errors['_umum'] = $ex->getMessage();
            }
        }
    } elseif ($aksi === 'password') {
        $lama  = (string) ($_POST['password_lama'] ?? '');
        $baru  = (string) ($_POST['password_baru'] ?? '');
        $baru2 = (string) ($_POST['password_baru2'] ?? '');
        if (!password_verify($lama, (string) $akun['password_hash'])) {
            $errPw['password_lama'] = 'Password lama salah.';
        } elseif (($g = galat_password($baru)) !== null) {
            $errPw['password_baru'] = $g;
        } elseif (!hash_equals($baru, $baru2)) {
            $errPw['password_baru2'] = 'Konfirmasi password tidak sama.';
        } else {
            try {
                user_ganti_password($akun['id'], $baru);
                session_regenerate_id(true);
                $_SESSION['pesan_profil'] = ['ok', 'Password diganti.'];
                header('Location: profil.php');
                exit;
            } catch (RuntimeException $ex) {
                $errPw['_umum'] = $ex->getMessage();
            }
        }
    }
}

$val   = fn(string $k): string => e($old[$k] ?? '');
$err   = fn(array $a, string $k): string => isset($a[$k]) ? '<p class="galat" id="err-' . $k . '">' . e($a[$k]) . '</p>' : '';
$aria  = fn(array $a, string $k): string => isset($a[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : '';

$halaman = 'profil';
$judul   = 'Profil — Inkra';
include __DIR__ . '/includes/header.php';
?>
<section class="panel-gelap akun-halaman">
    <div class="wrap akun-kotak akun-lebar">
        <p class="eyebrow eyebrow-lime">Akun</p>
        <h1 class="judul">Profil Anda.</h1>
        <p class="form-ket">Data ini dipakai untuk mengisi formulir pesanan secara otomatis. Admin hanya dapat melihat nama, email, dan nomor telepon Anda.</p>

        <?php if ($pesan): ?>
            <p class="akun-ok" role="status"><?= e($pesan[1]) ?></p>
        <?php endif; ?>

        <form class="form" method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="aksi" value="profil">
            <?php if (isset($errors['_umum'])): ?>
                <p class="galat galat-umum" role="alert"><?= e($errors['_umum']) ?></p>
            <?php endif; ?>
            <div class="baris">
                <div class="field">
                    <label for="nama">Nama</label>
                    <input id="nama" name="nama" type="text" maxlength="100" autocomplete="name" required value="<?= $val('nama') ?>"<?= $aria($errors, 'nama') ?>>
                    <?= $err($errors, 'nama') ?>
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" maxlength="150" autocomplete="email" required value="<?= $val('email') ?>"<?= $aria($errors, 'email') ?>>
                    <?= $err($errors, 'email') ?>
                </div>
            </div>
            <div class="field">
                <label for="telepon">Nomor telepon</label>
                <input id="telepon" name="telepon" type="tel" inputmode="tel" maxlength="25" placeholder="0852-1793-0056" autocomplete="tel" value="<?= $val('telepon') ?>"<?= $aria($errors, 'telepon') ?>>
                <?= $err($errors, 'telepon') ?>
            </div>
            <div class="field">
                <label for="alamat">Alamat pengiriman</label>
                <textarea id="alamat" name="alamat" rows="3" maxlength="500" autocomplete="street-address" placeholder="Alamat lengkap"><?= $val('alamat') ?></textarea>
            </div>
            <button class="btn btn-lime btn-blok" type="submit"><span>Simpan profil</span><?= ikon_panah() ?></button>
        </form>

        <h2 class="akun-sub">Ganti password</h2>
        <form class="form" method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="aksi" value="password">
            <?php if (isset($errPw['_umum'])): ?>
                <p class="galat galat-umum" role="alert"><?= e($errPw['_umum']) ?></p>
            <?php endif; ?>
            <div class="field">
                <label for="password_lama">Password lama</label>
                <input id="password_lama" name="password_lama" type="password" autocomplete="current-password" required<?= $aria($errPw, 'password_lama') ?>>
                <?= $err($errPw, 'password_lama') ?>
            </div>
            <div class="baris">
                <div class="field">
                    <label for="password_baru">Password baru</label>
                    <input id="password_baru" name="password_baru" type="password" minlength="8" autocomplete="new-password" required<?= $aria($errPw, 'password_baru') ?>>
                    <?= $err($errPw, 'password_baru') ?>
                </div>
                <div class="field">
                    <label for="password_baru2">Ulangi password baru</label>
                    <input id="password_baru2" name="password_baru2" type="password" minlength="8" autocomplete="new-password" required<?= $aria($errPw, 'password_baru2') ?>>
                    <?= $err($errPw, 'password_baru2') ?>
                </div>
            </div>
            <button class="btn btn-lime btn-blok" type="submit"><span>Ganti password</span><?= ikon_panah() ?></button>
        </form>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
