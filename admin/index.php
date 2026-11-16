<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/admin.php';
wajib_admin();

/* ---------- Proses aksi (POST lalu redirect) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            throw new RuntimeException('Ukuran unggahan melebihi batas server (post_max_size). Pakai gambar yang lebih kecil.');
        }
        if (!csrf_valid()) {
            throw new RuntimeException('Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
        }

        $set  = pengaturan(true);
        $aksi = (string) ($_POST['aksi'] ?? '');

        switch ($aksi) {
            case 'carousel_tambah':
                if (count($set['carousel']) >= 8) {
                    throw new RuntimeException('Carousel maksimal 8 gambar. Hapus satu gambar dulu.');
                }
                $baru = simpan_unggahan($_FILES['gambar'] ?? []);
                $set['carousel'][] = $baru;
                if (!simpan_pengaturan($set)) {
                    hapus_unggahan($baru);
                    throw new RuntimeException('Pengaturan tidak bisa disimpan. Periksa izin tulis folder data.');
                }
                $_SESSION['pesan'] = ['ok', 'Gambar ditambahkan ke carousel.'];
                break;

            case 'carousel_hapus':
                $idx = filter_var($_POST['indeks'] ?? null, FILTER_VALIDATE_INT);
                if ($idx === false || !isset($set['carousel'][$idx])) {
                    throw new RuntimeException('Gambar carousel tidak ditemukan.');
                }
                if (count($set['carousel']) <= 1) {
                    throw new RuntimeException('Carousel minimal punya satu gambar.');
                }
                $lama = $set['carousel'][$idx];
                array_splice($set['carousel'], $idx, 1);
                if (!simpan_pengaturan($set)) {
                    throw new RuntimeException('Pengaturan tidak bisa disimpan. Periksa izin tulis folder data.');
                }
                hapus_unggahan($lama);
                $_SESSION['pesan'] = ['ok', 'Gambar dihapus dari carousel.'];
                break;

            case 'media_ganti':
                $nama = (string) ($_POST['media'] ?? '');
                if (!isset($set['media'][$nama])) {
                    throw new RuntimeException('Media sablon tidak dikenal.');
                }
                $baru = simpan_unggahan($_FILES['gambar'] ?? []);
                $lama = $set['media'][$nama];
                $set['media'][$nama] = $baru;
                if (!simpan_pengaturan($set)) {
                    hapus_unggahan($baru);
                    throw new RuntimeException('Pengaturan tidak bisa disimpan. Periksa izin tulis folder data.');
                }
                hapus_unggahan($lama);
                $_SESSION['pesan'] = ['ok', 'Gambar ' . $nama . ' diperbarui.'];
                break;

            case 'telepon':
                $nomor = normalisasi_telepon((string) ($_POST['telepon'] ?? ''));
                if ($nomor === null) {
                    throw new RuntimeException('Nomor telepon tidak valid. Contoh: 0852-1793-0056.');
                }
                $set['telepon'] = $nomor;
                if (!simpan_pengaturan($set)) {
                    throw new RuntimeException('Pengaturan tidak bisa disimpan. Periksa izin tulis folder data.');
                }
                $_SESSION['pesan'] = ['ok', 'Nomor telepon diperbarui.'];
                break;

            default:
                throw new RuntimeException('Aksi tidak dikenal.');
        }
    } catch (RuntimeException $ex) {
        $_SESSION['pesan'] = ['galat', $ex->getMessage()];
    }
    header('Location: index.php');
    exit;
}

/* ---------- Tampilan ---------- */
$set   = pengaturan(true);
$pesan = flash('pesan');
$csrf  = csrf_token();
$tipe  = 'accept="image/jpeg,image/png,image/webp"';

admin_head('Dashboard admin');
?>
<header class="admin-bar">
    <div class="wrap admin-bar-isi">
        <a class="logo" href="index.php">Inkra<?= logo_ikon() ?></a>
        <div class="admin-bar-aksi">
            <a class="tautan-garis" href="../index.php" target="_blank" rel="noopener">Lihat situs</a>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <button class="btn btn-garis" type="submit">Keluar</button>
            </form>
        </div>
    </div>
</header>

<main class="wrap admin-isi">
    <h1 class="judul">Dashboard admin.</h1>

    <?php if ($pesan): ?>
        <p class="pesan <?= $pesan[0] === 'ok' ? 'pesan-ok' : 'pesan-galat' ?>" role="status"><?= e($pesan[1]) ?></p>
    <?php endif; ?>

    <section class="panel" aria-labelledby="h-carousel">
        <h2 id="h-carousel">Carousel beranda</h2>
        <p class="panel-ket">Gambar yang bergantian di bagian atas beranda. Maksimal 8 gambar, minimal 1. Format JPG, PNG, atau WebP, maksimal 5 MB.</p>

        <ul class="galeri">
            <?php foreach ($set['carousel'] as $i => $path): ?>
                <li class="galeri-item">
                    <img src="../<?= e($path) ?>" alt="Gambar carousel <?= $i + 1 ?>">
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                        <input type="hidden" name="aksi" value="carousel_hapus">
                        <input type="hidden" name="indeks" value="<?= $i ?>">
                        <button class="btn btn-garis btn-kecil" type="submit"
                            <?= count($set['carousel']) <= 1 ? 'disabled' : '' ?>
                            onclick="return confirm('Hapus gambar ini dari carousel?')">Hapus gambar <?= $i + 1 ?></button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <form class="form-unggah" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <input type="hidden" name="aksi" value="carousel_tambah">
            <div class="field">
                <label for="gambar-carousel">Tambah gambar</label>
                <input id="gambar-carousel" name="gambar" type="file" <?= $tipe ?> required>
            </div>
            <button class="btn btn-gelap" type="submit">Unggah</button>
        </form>
    </section>

    <section class="panel" aria-labelledby="h-media">
        <h2 id="h-media">Gambar pilih media sablon</h2>
        <p class="panel-ket">Foto pada kartu Kaos, Jaket, dan Totebag. Rasio paling pas sekitar 5:4.</p>

        <div class="media-grid">
            <?php foreach ($set['media'] as $nama => $path): ?>
                <div class="media-item">
                    <img src="../<?= e($path) ?>" alt="Gambar <?= e($nama) ?> saat ini">
                    <h3><?= e($nama) ?></h3>
                    <form class="form-unggah form-unggah-rapat" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                        <input type="hidden" name="aksi" value="media_ganti">
                        <input type="hidden" name="media" value="<?= e($nama) ?>">
                        <div class="field">
                            <label for="media-<?= e($nama) ?>">Ganti gambar <?= e($nama) ?></label>
                            <input id="media-<?= e($nama) ?>" name="gambar" type="file" <?= $tipe ?> required>
                        </div>
                        <button class="btn btn-gelap" type="submit">Ganti</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel" aria-labelledby="h-telepon">
        <h2 id="h-telepon">Nomor telepon admin</h2>
        <p class="panel-ket">Dipakai untuk tombol Kontak Kami, tampilan nomor di footer, dan tujuan pesan WhatsApp dari halaman Minta Penawaran.</p>

        <form class="form-unggah" method="post">
            <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
            <input type="hidden" name="aksi" value="telepon">
            <div class="field">
                <label for="telepon">Nomor WhatsApp</label>
                <input id="telepon" name="telepon" type="tel" inputmode="tel" maxlength="20"
                       value="<?= e(telp_tampil()) ?>" placeholder="0852-1793-0056" required>
            </div>
            <button class="btn btn-gelap" type="submit">Simpan nomor</button>
        </form>
    </section>
</main>
</body>
</html>
