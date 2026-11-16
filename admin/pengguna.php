<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin.php';
wajib_admin();

$cari = mb_strtolower(bersih_teks((string) ($_GET['q'] ?? ''), 100));

// Hanya field yang boleh dilihat admin yang diambil: nama, email, telepon (+ tanggal daftar).
// Alamat dan hash password sengaja tidak pernah dikirim ke halaman ini.
$daftar = array_map(
    fn(array $u): array => [
        'nama'    => (string) ($u['nama'] ?? ''),
        'email'   => (string) ($u['email'] ?? ''),
        'telepon' => (string) ($u['telepon'] ?? ''),
        'dibuat'  => (string) ($u['dibuat'] ?? ''),
    ],
    users_baca()
);
usort($daftar, fn($a, $b) => strcmp($b['dibuat'], $a['dibuat']));   // terbaru di atas

if ($cari !== '') {
    $daftar = array_values(array_filter(
        $daftar,
        fn($u) => str_contains(mb_strtolower($u['nama']), $cari) || str_contains(mb_strtolower($u['email']), $cari)
    ));
}

$csrf = csrf_token();
admin_head('Akun pengguna');
?>
<header class="admin-bar">
    <div class="wrap admin-bar-isi">
        <a class="logo" href="index.php">Inkra<?= logo_ikon() ?></a>
        <div class="admin-bar-aksi">
            <a class="tautan-garis" href="index.php">Dashboard</a>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <button class="btn btn-garis" type="submit">Keluar</button>
            </form>
        </div>
    </div>
</header>

<main class="wrap admin-isi">
    <h1 class="judul">Akun pengguna.</h1>

    <section class="panel" aria-labelledby="h-user">
        <h2 id="h-user">Semua akun</h2>
        <p class="panel-ket">Menampilkan nama, email, dan nomor telepon yang diisi pengguna di profilnya.</p>

        <form class="cari-user" method="get">
            <div class="field">
                <label for="q">Cari nama atau email</label>
                <input id="q" name="q" type="search" maxlength="100" value="<?= e($cari) ?>">
            </div>
            <button class="btn btn-gelap" type="submit">Cari</button>
        </form>

        <div class="tabel-bungkus">
            <table class="tabel">
                <thead>
                    <tr><th scope="col">Nama</th><th scope="col">Email</th><th scope="col">Telepon</th><th scope="col">Terdaftar</th></tr>
                </thead>
                <tbody>
                <?php if (!$daftar): ?>
                    <tr><td colspan="4" class="kosong">Belum ada akun.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftar as $u): ?>
                    <tr>
                        <td><?= e($u['nama']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= $u['telepon'] !== '' ? e(telp_user_tampil($u['telepon'])) : '<span class="kosong">—</span>' ?></td>
                        <td><?= $u['dibuat'] !== '' ? e(date('d M Y', (int) strtotime($u['dibuat']))) : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="jumlah-user"><?= count($daftar) ?> akun</p>
    </section>
</main>
</body>
</html>
