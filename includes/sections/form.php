<?php
$errors = flash('errors', []);
$old    = flash('old', []);
$asal   = $asal ?? 'minta-penawaran';          // 'index' atau 'minta-penawaran'
$h      = $asal === 'index' ? 'h2' : 'h1';
$profil = user_masuk() ? profil_untuk_form() : [];   // data profil user yang login (kosong untuk guest)
// Urutan: isian sebelumnya (jika validasi gagal) > profil user > parameter ?produk=
$val    = fn(string $k): string => e($old[$k] ?? ($profil[$k] ?? ($k === 'produk' ? (string) ($_GET['produk'] ?? '') : '')));
$err    = fn(string $k): string => isset($errors[$k]) ? '<p class="galat" id="err-' . $k . '">' . e($errors[$k]) . '</p>' : '';
$aria   = fn(string $k): string => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : '';
?>
<section class="panel-gelap" id="penawaran">
    <div class="wrap form-grid">
        <div class="form-kiri">
            <div>
                <p class="eyebrow eyebrow-lime">03 / Yuk, wujudkan ide Anda</p>
                <<?= $h ?> class="judul judul-besar">Punya ide?<br>Mulai di sini.</<?= $h ?>>
                <p class="form-ket">Ceritakan rencana sablon Anda.<br>Hasil yang pas dimulai dari obrolan.</p>
            </div>
            <p class="eyebrow eyebrow-lime form-tag">Kaos / Jaket / Totebag / Lainnya</p>
        </div>

        <form class="form" action="kirim.php" method="post" novalidate>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="asal" value="<?= e($asal) ?>">
            <div class="jebakan" aria-hidden="true">
                <label>Jangan diisi <input type="text" name="situs" tabindex="-1" autocomplete="off"></label>
            </div>

            <?php if (isset($errors['_umum'])): ?>
                <p class="galat galat-umum" role="alert"><?= e($errors['_umum']) ?></p>
            <?php endif; ?>

            <div class="field">
                <label for="nama">Nama Anda</label>
                <input id="nama" name="nama" type="text" maxlength="100" placeholder="Nama lengkap" autocomplete="name" required value="<?= $val('nama') ?>"<?= $aria('nama') ?>>
                <?= $err('nama') ?>
            </div>

            <div class="field">
                <label for="telepon">Nomor WhatsApp (opsional)</label>
                <input id="telepon" name="telepon" type="tel" inputmode="tel" maxlength="25" placeholder="0852-1793-0056" autocomplete="tel" value="<?= $val('telepon') ?>"<?= $aria('telepon') ?>>
                <?= $err('telepon') ?>
            </div>

            <div class="field">
                <label for="produk">Apa yang ingin Anda sablon?</label>
                <input id="produk" name="produk" type="text" maxlength="150" placeholder="Kaos, jaket, totebag, atau produk lainnya" required value="<?= $val('produk') ?>"<?= $aria('produk') ?>>
                <?= $err('produk') ?>
            </div>

            <div class="baris">
                <div class="field">
                    <label for="jumlah">Perkiraan jumlah</label>
                    <input id="jumlah" name="jumlah" type="text" maxlength="50" placeholder="Berapa banyak?" required value="<?= $val('jumlah') ?>"<?= $aria('jumlah') ?>>
                    <?= $err('jumlah') ?>
                </div>
                <div class="field">
                    <label for="target">Target selesai (opsional)</label>
                    <input id="target" name="target" type="text" maxlength="100" placeholder="Kapan Anda membutuhkannya?" value="<?= $val('target') ?>">
                </div>
            </div>

            <div class="field">
                <label for="rencana">Ceritakan rencana Anda</label>
                <textarea id="rencana" name="rencana" rows="4" maxlength="2000" placeholder="Desain, warna, posisi sablon, dan detail lainnya..."><?= $val('rencana') ?></textarea>
            </div>

            <div class="field">
                <label for="alamat">Alamat Anda (opsional)</label>
                <textarea id="alamat" name="alamat" rows="3" maxlength="500" placeholder="Alamat lengkap untuk pengiriman"><?= $val('alamat') ?></textarea>
            </div>

            <button class="btn btn-lime btn-blok" type="submit"><span>Minta Penawaran</span><?= ikon_panah() ?></button>
            <p class="form-catatan">Detail desain bisa dibahas saat konsultasi penawaran.</p>
            <?php if (user_masuk()): ?>
                <p class="form-catatan">Nama, nomor WhatsApp, dan alamat terisi dari <a class="tautan-garis" href="profil.php">profil Anda</a>. Boleh diubah untuk pesanan ini.</p>
            <?php elseif (!admin_masuk()): ?>
                <p class="form-catatan">Anda masuk sebagai guest. <a class="tautan-garis" href="login.php?next=<?= e(rawurlencode($asal === 'index' ? 'index.php' : 'minta-penawaran.php')) ?>">Login</a> agar data terisi otomatis.</p>
            <?php endif; ?>
        </form>
    </div>
</section>
