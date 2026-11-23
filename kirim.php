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

// Susun teks pesan WhatsApp dari isi formulir.
function pesan_wa(array $d): string
{
    $baris = [
        'Halo Inkra, saya ingin minta penawaran sablon.',
        '',
        'Nama: ' . $d['nama'],
        'Produk: ' . $d['produk'],
        'Jumlah: ' . $d['jumlah'],
    ];
    if ($d['telepon'] !== '') { $baris[] = 'Nomor WhatsApp: ' . $d['telepon']; }
    if ($d['target'] !== '')  { $baris[] = 'Target selesai: ' . $d['target']; }
    if ($d['rencana'] !== '') { $baris[] = 'Rencana: ' . mb_strimwidth($d['rencana'], 0, 1000, '…'); }
    if ($d['alamat'] !== '')  { $baris[] = 'Alamat: ' . mb_strimwidth($d['alamat'], 0, 500, '…'); }
    return implode("\n", $baris);
}

$data = [
    'nama'    => bersih('nama', 100),
    'telepon' => bersih('telepon', 25),
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
    if ($data['telepon'] !== '' && normalisasi_telepon($data['telepon']) === null) { $errors['telepon'] = 'Nomor WhatsApp tidak valid. Contoh: 0852-1793-0056.'; }
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
    ['waktu' => date('c'), 'ip' => $_SERVER['REMOTE_ADDR'] ?? '', 'user_id' => $_SESSION['user_id'] ?? null] + $data,
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
$tersimpan = @file_put_contents(__DIR__ . '/data/permintaan.jsonl', $baris . PHP_EOL, FILE_APPEND | LOCK_EX);

if ($tersimpan === false) {
    $_SESSION['errors'] = ['_umum' => 'Permintaan belum bisa disimpan. Coba lagi atau hubungi kami lewat WhatsApp.'];
    $_SESSION['old']    = $data;
    kembali($tujuan);
}

$_SESSION['terakhir_kirim'] = time();
$_SESSION['sukses'] = true;
$_SESSION['wa_url'] = wa_link(pesan_wa($data));
unset($_SESSION['old']);
kembali($tujuan);
