<?php
declare(strict_types=1);

/** Simpan gambar unggahan ke folder uploads/ dan kembalikan path relatifnya. */
function simpan_unggahan(array $berkas): string
{
    if (!isset($berkas['error']) || is_array($berkas['error'])) {
        throw new RuntimeException('Berkas tidak valid.');
    }
    switch ($berkas['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('Pilih gambar terlebih dahulu.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Ukuran gambar melebihi batas server.');
        default:
            throw new RuntimeException('Gambar gagal diunggah. Coba lagi.');
    }

    if ($berkas['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Ukuran gambar maksimal 5 MB.');
    }
    if (!is_uploaded_file($berkas['tmp_name'])) {
        throw new RuntimeException('Berkas tidak valid.');
    }

    $info = @getimagesize($berkas['tmp_name']);
    $ekstensi = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ][$info['mime'] ?? ''] ?? null;

    if ($info === false || $ekstensi === null) {
        throw new RuntimeException('Format gambar harus JPG, PNG, atau WebP.');
    }
    if ($info[0] > 8000 || $info[1] > 8000) {
        throw new RuntimeException('Dimensi gambar maksimal 8000 piksel.');
    }

    $folder = __DIR__ . '/../uploads';
    if (!is_dir($folder) && !@mkdir($folder, 0755, true)) {
        throw new RuntimeException('Folder uploads tidak bisa dibuat.');
    }

    $nama = bin2hex(random_bytes(16)) . '.' . $ekstensi;
    if (!@move_uploaded_file($berkas['tmp_name'], $folder . '/' . $nama)) {
        throw new RuntimeException('Gambar tidak bisa disimpan. Periksa izin tulis folder uploads.');
    }
    return 'uploads/' . $nama;
}

/** Hapus file unggahan lama. Gambar bawaan di assets/img tidak pernah dihapus. */
function hapus_unggahan(string $path): void
{
    if (preg_match('~^uploads/[a-f0-9]{32}\.(jpg|png|webp)$~', $path)) {
        @unlink(__DIR__ . '/../' . $path);
    }
}

function admin_head(string $judul): void
{
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($judul) ?> — Inkra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin">
<?php
}
