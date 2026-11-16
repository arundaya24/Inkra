<section class="hero">
    <div class="wrap hero-grid">
        <div class="hero-teks">
            <p class="eyebrow">Sablon custom</p>
            <h1>Ide Anda.<br>Siap dipakai.</h1>
            <p class="hero-ket">Sablon custom untuk kaos, jaket, totebag, dan lainnya. Wujudkan desain Anda jadi produk yang siap dipakai, dibawa, dan jadi bagian dari kehidupan.</p>
            <div class="hero-aksi">
                <a class="btn btn-gelap" href="minta-penawaran.php">Mulai Proyek Sablon Anda <?= ikon_panah() ?></a>
                <a class="tautan-garis" href="media-sablon.php">Lihat Media Sablon</a>
            </div>
        </div>
        <?php $slide = pengaturan()['carousel']; $jumlah = count($slide); ?>
        <div class="carousel" data-carousel aria-roledescription="carousel" aria-label="Contoh hasil sablon">
            <?php foreach ($slide as $i => $path): ?>
                <img class="carousel-slide<?= $i === 0 ? ' is-aktif' : '' ?>" src="<?= e($path) ?>" width="612" height="504"
                     alt="<?= $path === 'assets/img/hero.jpg' ? 'Kaos hitam, jaket ungu, dan totebag dengan desain sablon' : 'Contoh hasil sablon ' . ($i + 1) . ' dari ' . $jumlah ?>"
                     <?= $i > 0 ? 'loading="lazy"' : '' ?>>
            <?php endforeach; ?>
            <?php if ($jumlah > 1): ?>
                <button class="carousel-btn carousel-prev" type="button" aria-label="Gambar sebelumnya">
                    <svg viewBox="0 0 12 12" width="12" height="12" aria-hidden="true"><path d="M7.5 2L3.5 6l4 4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
                </button>
                <button class="carousel-btn carousel-next" type="button" aria-label="Gambar berikutnya">
                    <svg viewBox="0 0 12 12" width="12" height="12" aria-hidden="true"><path d="M4.5 2l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
                </button>
                <div class="carousel-dots">
                    <?php foreach ($slide as $i => $path): ?>
                        <button class="carousel-dot" type="button" aria-label="Gambar <?= $i + 1 ?>"<?= $i === 0 ? ' aria-current="true"' : '' ?>></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
