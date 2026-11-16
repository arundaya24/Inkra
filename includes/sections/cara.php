<?php
require_once __DIR__ . '/../data.php';
$home = $home ?? false;
$h = $home ? 'h2' : 'h1';
$daftar = $home ? array_slice($langkah, 0, 3) : $langkah;
?>
<section class="section" id="cara-pesan">
    <div class="wrap">
        <?php if ($home): ?><p class="eyebrow">02 / Cara pesan</p><?php endif; ?>
        <<?= $h ?> class="judul">Dari ide jadi sablon.</<?= $h ?>>
        <ol class="langkah">
            <?php foreach ($daftar as $i => [$judul, $ket]): ?>
                <li class="langkah-item">
                    <span class="langkah-no"><?= sprintf('%02d', $i + 1) ?></span>
                    <div>
                        <h3><?= e($judul) ?></h3>
                        <p><?= e($ket) ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        <?php if (!$home): ?>
            <img class="banner-foto" src="assets/img/banner.jpg" width="801" height="161" alt="Totebag, jaket, dan kaos hasil sablon" loading="lazy">
        <?php endif; ?>
    </div>
</section>
