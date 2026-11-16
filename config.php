<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const NAMA_SITUS   = 'Inkra';
const TAGLINE      = 'Sablon custom untuk ide hebat Anda berikutnya.';
const ALAMAT       = 'Komplek Cileungsi Indah, Cileungsi, Kec. Cileungsi, Bogor, Jawa Barat, Indonesia';
const KONTAK_NAMA  = 'Anggra';
const KONTAK_TELP  = '+62 852-1793-0056';
const WA_NOMOR     = '6285217930056';   // format internasional tanpa +
const EMAIL_ADMIN  = '';                // isi untuk menerima notifikasi email (opsional)

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

/** Ambil lalu hapus data flash dari session. */
function flash(string $kunci, $default = null)
{
    $nilai = $_SESSION[$kunci] ?? $default;
    unset($_SESSION[$kunci]);
    return $nilai;
}

function wa_link(string $pesan = 'Halo Inkra, saya ingin bertanya soal sablon.'): string
{
    return 'https://wa.me/' . WA_NOMOR . '?text=' . rawurlencode($pesan);
}

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
