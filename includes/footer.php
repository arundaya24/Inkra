</main>
<footer class="site-footer">
    <div class="wrap footer-grid">
        <div>
            <a class="logo" href="index.php" aria-label="Inkra, ke beranda">Inkra<?= logo_ikon() ?></a>
            <p class="footer-tagline"><?= e(TAGLINE) ?></p>
        </div>
        <div class="footer-info">
            <nav class="footer-links" aria-label="Tautan footer">
                <a href="media-sablon.php">Produk Sablon</a>
                <a href="cara-pesan.php">Cara Pesan</a>
                <a href="minta-penawaran.php">Minta Penawaran</a>
            </nav>
            <p class="footer-alamat"><?= e(ALAMAT) ?></p>
            <p class="footer-alamat"><?= e(nama_kontak()) ?> <?= e(telp_tampil()) ?></p>
        </div>
    </div>
</footer>

<?php if (flash('sukses')): $waUrl = (string) flash('wa_url', ''); ?>
<div class="modal" id="modal-sukses" role="dialog" aria-modal="true" aria-labelledby="modal-judul">
    <div class="modal-kotak">
        <h2 id="modal-judul">Permintaan diterima.</h2>
        <p>Mohon berkenan untuk menunggu beberapa saat, kami akan menghubungi anda segera.</p>
        <?php if ($waUrl !== ''): ?>
            <p class="modal-wa" role="status">Mengarahkan Anda ke WhatsApp…</p>
        <?php endif; ?>
        <div class="modal-aksi">
            <?php if ($waUrl !== ''): ?>
                <a class="btn btn-gelap" href="<?= e($waUrl) ?>" data-wa>Buka WhatsApp</a>
            <?php endif; ?>
            <a class="btn <?= $waUrl !== '' ? 'btn-garis' : 'btn-gelap' ?>" href="<?= e(strtok($_SERVER['REQUEST_URI'] ?? 'index.php', '#')) ?>" data-tutup>Tutup</a>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
