<?php
require_once __DIR__ . '/../config.php';
$halaman = $halaman ?? 'beranda';
$akun    = pengguna();          // null = guest
$judul   = $judul ?? 'Inkra — Sablon custom untuk ide hebat Anda';
$menu = [
    'beranda'      => ['Beranda',      'index.php'],
    'media-sablon' => ['Media Sablon', 'media-sablon.php'],
    'cara-pesan'   => ['Cara Pesan',   'cara-pesan.php'],
    'tanya-jawab'  => ['Tanya Jawab',  'tanya-jawab.php'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul) ?></title>
    <meta name="description" content="Sablon custom untuk kaos, jaket, totebag, dan lainnya. Ceritakan ide Anda, kami wujudkan jadi produk siap pakai.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="wrap bar">
        <a class="logo" href="index.php" aria-label="Inkra, ke beranda">Inkra<?= logo_ikon() ?></a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu">Menu</button>
        <nav class="menu" id="menu" aria-label="Navigasi utama">
            <?php foreach ($menu as $kunci => [$label, $href]): ?>
                <a href="<?= e($href) ?>"<?= $halaman === $kunci ? ' aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php endforeach; ?>
            <a class="btn btn-gelap" href="minta-penawaran.php">Minta Penawaran <?= ikon_panah() ?></a>
            <div class="akun-nav">
                <?php if (admin_masuk()): ?>
                    <span class="akun-label">Admin</span>
                    <a class="btn btn-tepi" href="admin/index.php">Dashboard</a>
                <?php elseif ($akun): ?>
                    <a class="akun-nama" href="profil.php"<?= $halaman === 'profil' ? ' aria-current="page"' : '' ?>><?= e(mb_strimwidth($akun['nama'], 0, 18, '…')) ?></a>
                    <form class="akun-keluar" method="post" action="logout.php">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <button class="btn btn-tepi" type="submit">Keluar</button>
                    </form>
                <?php else: ?>
                    <span class="akun-label">Guest</span>
                    <a class="btn btn-tepi" href="login.php<?= $halaman === 'login' ? '' : '?next=' . rawurlencode(basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'))) ?>">Login</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<main>
