<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

$asal   = ($_POST['asal'] ?? '') === 'index' ? 'index' : 'minta-penawaran';
$tujuan = $asal === 'index' ? 'index.php#penawaran' : 'minta-penawaran.php';

function kembali(string $tujuan)
{
    header('Location: ' . $tujuan);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    kembali($tujuan);
}

// Bersihkan input: potong spasi, buang karakter kontrol, batasi panjang.
function bersih(string $kunci, int $maks): string
{
    $nilai = trim((string) ($_POST[$kunci] ?? ''));
    $nilai = preg_replace('/[^\P{C}\n\r\t]/u', '', $nilai) ?? '';
    return mb_substr($nilai, 0, $maks);
}

$data = [
    'nama'    => bersih('nama', 100),
    'email'   => bersih('email', 150),
    'produk'  => bersih('produk', 150),
    'jumlah'  => bersih('jumlah', 50),
    'target'  => bersih('target', 100),
    'rencana' => bersih('rencana', 2000),
    'alamat'  => bersih('alamat', 500),
];

$errors = [];

// Token CSRF
if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''))) {
    $errors['_umum'] = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu kirim lagi.';
}

// Honeypot: bot biasanya mengisi kolom tersembunyi ini. Pura-pura sukses.
if (($_POST['situs'] ?? '') !== '') {
    $_SESSION['sukses'] = true;
    kembali($tujuan);
}

// Batasi kecepatan kirim (5 detik per sesi)
if (!$errors && isset($_SESSION['terakhir_kirim']) && time() - $_SESSION['terakhir_kirim'] < 5) {
    $errors['_umum'] = 'Terlalu cepat. Tunggu sebentar lalu kirim lagi.';
}

if (!$errors) {
    if ($data['nama'] === '')   { $errors['nama']   = 'Nama wajib diisi.'; }
    if ($data['email'] === '')  { $errors['email']  = 'Email wajib diisi.'; }
    elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Format email belum benar.'; }
    if ($data['produk'] === '') { $errors['produk'] = 'Sebutkan produk yang ingin disablon.'; }
    if ($data['jumlah'] === '') { $errors['jumlah'] = 'Isi perkiraan jumlah.'; }
}

if ($errors) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = $data;
    kembali($tujuan);
}

// Simpan satu baris JSON per permintaan ke data/permintaan.jsonl
$baris = json_encode(
    ['waktu' => date('c'), 'ip' => $_SERVER['REMOTE_ADDR'] ?? ''] + $data,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
$tersimpan = @file_put_contents(__DIR__ . '/data/permintaan.jsonl', $baris . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($tersimpan === false) {
    $_SESSION['errors'] = ['_umum' => 'Permintaan belum bisa disimpan. Coba lagi atau hubungi kami lewat WhatsApp.'];
    $_SESSION['old']    = $data;
    kembali($tujuan);
}

// Notifikasi email (opsional, aktif jika EMAIL_ADMIN diisi di config.php)
if (EMAIL_ADMIN !== '') {
    $isi = '';
    foreach ($data as $k => $v) {
        $isi .= ucfirst($k) . ': ' . $v . "\n";
    }
    $subjek = '=?UTF-8?B?' . base64_encode('Permintaan penawaran baru dari ' . $data['nama']) . '?=';
    @mail(EMAIL_ADMIN, $subjek, $isi, [
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Reply-To'     => str_replace(["\r", "\n"], '', $data['email']),
    ]);
}

$_SESSION['terakhir_kirim'] = time();
$_SESSION['sukses'] = true;
unset($_SESSION['old']);
kembali($tujuan);
