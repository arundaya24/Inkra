<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

const NAMA_SITUS  = 'Inkra';
const TAGLINE     = 'Sablon custom untuk ide hebat Anda berikutnya.';
const ALAMAT      = 'Komplek Cileungsi Indah, Cileungsi, Kec. Cileungsi, Bogor, Jawa Barat, Indonesia';
const KONTAK_NAMA = 'Anggra';
const EMAIL_ADMIN = '';                 // isi untuk menerima notifikasi email (opsional)

// Akun dashboard admin. Ganti password ini sebelum situs dipasang online.
const ADMIN_USER = 'admin';
const ADMIN_PASS = 'admin123';

const FILE_PENGATURAN = __DIR__ . '/data/pengaturan.json';

/* ---------- Umum ---------- */

function e(?string $teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(): bool
{
    return hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''));
}

/** Ambil lalu hapus data flash dari session. */
function flash(string $kunci, $default = null)
{
    $nilai = $_SESSION[$kunci] ?? $default;
    unset($_SESSION[$kunci]);
    return $nilai;
}

/* ---------- Pengaturan yang bisa diubah admin ---------- */

function pengaturan_bawaan(): array
{
    return [
        'telepon'  => '6285217930056',
        'carousel' => ['assets/img/hero.jpg'],
        'media'    => [
            'Kaos'    => 'assets/img/kaos.jpg',
            'Jaket'   => 'assets/img/jaket.jpg',
            'Totebag' => 'assets/img/totebag.jpg',
        ],
    ];
}

function path_gambar_aman(string $path): bool
{
    return (bool) preg_match(
        '~^(assets/img/[a-z0-9._-]+\.(jpg|png|webp)|uploads/[a-f0-9]{32}\.(jpg|png|webp))$~',
        $path
    );
}

function pengaturan(bool $segarkan = false): array
{
    static $cache = null;
    if ($cache !== null && !$segarkan) {
        return $cache;
    }

    $hasil  = pengaturan_bawaan();
    $mentah = @file_get_contents(FILE_PENGATURAN);
    $data   = $mentah ? json_decode($mentah, true) : null;

    if (is_array($data)) {
        if (isset($data['telepon']) && is_string($data['telepon']) && preg_match('/^\d{9,15}$/', $data['telepon'])) {
            $hasil['telepon'] = $data['telepon'];
        }
        if (isset($data['carousel']) && is_array($data['carousel'])) {
            $slide = array_values(array_filter(
                $data['carousel'],
                fn($p) => is_string($p) && path_gambar_aman($p)
            ));
            if ($slide) {
                $hasil['carousel'] = $slide;
            }
        }
        if (isset($data['media']) && is_array($data['media'])) {
            foreach (array_keys($hasil['media']) as $nama) {
                $p = $data['media'][$nama] ?? null;
                if (is_string($p) && path_gambar_aman($p)) {
                    $hasil['media'][$nama] = $p;
                }
            }
        }
    }

    return $cache = $hasil;
}

function simpan_pengaturan(array $data): bool
{
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return $json !== false && @file_put_contents(FILE_PENGATURAN, $json, LOCK_EX) !== false;
}

/* ---------- Nomor telepon / WhatsApp ---------- */

/** Ubah "0852-1793-0056" atau "+62 852 1793 0056" menjadi "6285217930056". Null jika tidak valid. */
function normalisasi_telepon(string $masuk): ?string
{
    $d = preg_replace('/\D+/', '', $masuk) ?? '';
    if ($d === '') {
        return null;
    }
    if (str_starts_with($d, '00')) {
        $d = substr($d, 2);
    }
    if (str_starts_with($d, '0')) {
        $d = '62' . substr($d, 1);
    } elseif (str_starts_with($d, '8')) {
        $d = '62' . $d;
    }
    return preg_match('/^\d{9,15}$/', $d) ? $d : null;
}

function nomor_wa(): string
{
    return pengaturan()['telepon'];
}

function telp_tampil(): string
{
    $d = nomor_wa();
    if (str_starts_with($d, '62')) {
        $s = substr($d, 2);
        if (strlen($s) >= 8) {
            return '+62 ' . substr($s, 0, 3) . '-' . substr($s, 3, 4) . '-' . substr($s, 7);
        }
    }
    return '+' . $d;
}

function wa_link(string $pesan = 'Halo Inkra, saya ingin bertanya soal sablon.'): string
{
    return 'https://wa.me/' . nomor_wa() . '?text=' . rawurlencode($pesan);
}

/* ---------- Admin ---------- */

function admin_masuk(): bool
{
    return !empty($_SESSION['admin']);
}

function wajib_admin(): void
{
    if (!admin_masuk()) {
        header('Location: login.php');
        exit;
    }
}

/* ---------- Ikon ---------- */

function ikon_panah(): string
{
    return '<svg class="ikon" viewBox="0 0 12 12" width="11" height="11" aria-hidden="true">'
         . '<path d="M2.5 9.5l7-7M3.5 2.5h6v6" fill="none" stroke="currentColor" stroke-width="1.3"/></svg>';
}

function logo_ikon(): string
{
    $garis = '';
    for ($i = 0; $i < 18; $i++) {
        $a  = deg2rad($i * 20);
        $r1 = 6.5 + ($i % 2) * 1.2;
        $garis .= sprintf(
            '<line x1="%.2f" y1="%.2f" x2="%.2f" y2="%.2f"/>',
            12 + $r1 * cos($a), 12 + $r1 * sin($a),
            12 + 11 * cos($a),  12 + 11 * sin($a)
        );
    }
    return '<svg class="logo-ikon" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">'
         . '<circle cx="12" cy="12" r="5.2" fill="#8a6aa8"/>'
         . '<g stroke="#232420" stroke-width="1.3" stroke-linecap="round">' . $garis . '</g></svg>';
}
