<?php
require_once __DIR__ . '/../config.php';
$halaman = $halaman ?? 'beranda';
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
        </nav>
    </div>
</header>
<main>
