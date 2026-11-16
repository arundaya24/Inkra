<?php
require_once __DIR__ . '/../data.php';
$home = $home ?? false;
$h = $home ? 'h2' : 'h1';
?>
<section class="section" id="media-sablon">
    <div class="wrap">
        <?php if ($home): ?><p class="eyebrow">01 / Produk sablon</p><?php endif; ?>
        <div class="section-head">
            <<?= $h ?> class="judul">Pilih media sablon Anda.</<?= $h ?>>
            <?php if ($home): ?><p class="section-note">Tiga pilihan media yang paling sering dipesan.</p><?php endif; ?>
        </div>
        <div class="kartu-grid">
            <?php foreach ($produk as $p): ?>
                <a class="kartu" href="minta-penawaran.php?produk=<?= rawurlencode($p['nama']) ?>">
                    <img src="<?= e($p['foto']) ?>" width="254" height="207" alt="<?= e($p['nama']) ?> dengan desain sablon" loading="lazy">
                    <span class="kartu-judul"><span class="kartu-nama"><?= e($p['nama']) ?></span><?= ikon_panah() ?></span>
                    <span class="kartu-ket"><?= e($p['ket']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
