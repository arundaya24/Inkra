<?php
require_once __DIR__ . '/../data.php';
$home = $home ?? false;
$daftar = $home ? array_slice($faq, 0, 3) : $faq;
?>
<section class="section" id="tanya-jawab">
    <div class="wrap faq-grid">
        <div>
            <p class="eyebrow"><?= $home ? '04 / Tanya jawab' : 'Hal yang perlu diketahui' ?></p>
            <<?= $home ? 'h2' : 'h1' ?> class="judul">Sebelum menyablon.</<?= $home ? 'h2' : 'h1' ?>>
        </div>
        <div class="faq-daftar">
            <?php foreach ($daftar as [$tanya, $jawab]): ?>
                <div class="faq-item">
                    <h3><?= e($tanya) ?></h3>
                    <p><?= $jawab /* konten statis dari data.php */ ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
