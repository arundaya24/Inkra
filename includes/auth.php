<?php
declare(strict_types=1);

/*
 * Akun pengguna (guest / user) untuk Inkra.
 * Data disimpan di data/users.json (folder data/ sudah diproteksi .htaccess).
 * Password disimpan sebagai hash (password_hash), tidak pernah plain text.
 */

const FILE_USERS = __DIR__ . '/../data/users.json';

/* ---------- Penyimpanan ---------- */

function users_baca(): array
{
    $mentah = @file_get_contents(FILE_USERS);
    $data   = $mentah ? json_decode($mentah, true) : null;
    return is_array($data) ? $data : [];
}

/**
 * Baca-ubah-tulis dengan kunci file supaya dua pendaftaran bersamaan tidak saling menimpa.
 * $fn menerima daftar user (by reference) dan boleh mengembalikan nilai apa pun.
 */
function users_transaksi(callable $fn)
{
    $h = @fopen(FILE_USERS, 'c+');
    if ($h === false) {
        throw new RuntimeException('Data akun tidak bisa dibuka. Periksa izin tulis folder data.');
    }
    try {
        flock($h, LOCK_EX);
        $mentah = stream_get_contents($h);
        $users  = $mentah !== '' && $mentah !== false ? json_decode($mentah, true) : [];
        if (!is_array($users)) {
            $users = [];
        }

        $hasil = $fn($users);

        $json = json_encode(array_values($users), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('Data akun tidak bisa disimpan.');
        }
        rewind($h);
        ftruncate($h, 0);
        fwrite($h, $json);
        fflush($h);
        return $hasil;
    } finally {
        flock($h, LOCK_UN);
        fclose($h);
    }
}

function user_cari_email(array $users, string $email): ?array
{
    $email = mb_strtolower($email);
    foreach ($users as $u) {
        if (mb_strtolower((string) ($u['email'] ?? '')) === $email) {
            return $u;
        }
    }
    return null;
}

/* ---------- Sesi ---------- */

/** User yang sedang login, atau null (berarti guest). */
function pengguna(bool $segarkan = false): ?array
{
    static $cache = false;
    if ($cache !== false && !$segarkan) {
        return $cache;
    }
    $id = $_SESSION['user_id'] ?? null;
    $cache = null;
    if (is_string($id) && $id !== '') {
        foreach (users_baca() as $u) {
            if (($u['id'] ?? '') === $id) {
                $cache = $u;
                break;
            }
        }
        if ($cache === null) {
            unset($_SESSION['user_id']);   // akun sudah tidak ada
        }
    }
    return $cache;
}

function user_masuk(): bool
{
    return pengguna() !== null;
}

function wajib_user(): void
{
    if (!user_masuk()) {
        header('Location: login.php?next=' . rawurlencode(basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'))));
        exit;
    }
}

/** Hanya izinkan redirect ke halaman internal yang dikenal (cegah open redirect). */
function tujuan_aman(string $next, string $bawaan): string
{
    $boleh = ['index.php', 'profil.php', 'minta-penawaran.php', 'media-sablon.php', 'cara-pesan.php', 'tanya-jawab.php'];
    return in_array($next, $boleh, true) ? $next : $bawaan;
}

function keluar_sesi(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/* ---------- Validasi ---------- */

function bersih_teks(string $nilai, int $maks): string
{
    $nilai = trim($nilai);
    $nilai = preg_replace('/[^\P{C}\n\r\t]/u', '', $nilai) ?? '';
    return mb_substr($nilai, 0, $maks);
}

/** Cek password baru. Mengembalikan pesan galat atau null jika valid. */
function galat_password(string $pass): ?string
{
    if (strlen($pass) < 8) {
        return 'Password minimal 8 karakter.';
    }
    if (strlen($pass) > 72) {
        return 'Password maksimal 72 karakter.';
    }
    return null;
}

/**
 * Validasi data profil. Mengembalikan [data bersih, galat per field].
 * Telepon dan alamat boleh kosong.
 */
function validasi_profil(array $post): array
{
    $d = [
        'nama'    => bersih_teks((string) ($post['nama'] ?? ''), 100),
        'email'   => mb_strtolower(bersih_teks((string) ($post['email'] ?? ''), 150)),
        'telepon' => '',
        'alamat'  => bersih_teks((string) ($post['alamat'] ?? ''), 500),
    ];
    $g = [];

    if ($d['nama'] === '') {
        $g['nama'] = 'Nama wajib diisi.';
    }
    if ($d['email'] === '') {
        $g['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $g['email'] = 'Format email belum benar.';
    }

    $telRaw = bersih_teks((string) ($post['telepon'] ?? ''), 25);
    if ($telRaw !== '') {
        $tel = normalisasi_telepon($telRaw);
        if ($tel === null) {
            $g['telepon'] = 'Nomor telepon tidak valid. Contoh: 0852-1793-0056.';
        } else {
            $d['telepon'] = $tel;
        }
    }
    return [$d, $g];
}

/** Tampilkan nomor "6285217930056" sebagai "+62 852-1793-0056". Kosong jika tidak ada. */
function telp_user_tampil(string $nomor): string
{
    if ($nomor === '') {
        return '';
    }
    if (str_starts_with($nomor, '62')) {
        $s = substr($nomor, 2);
        if (strlen($s) >= 8) {
            return '+62 ' . substr($s, 0, 3) . '-' . substr($s, 3, 4) . '-' . substr($s, 7);
        }
    }
    return '+' . $nomor;
}

/* ---------- Aksi ---------- */

/** Buat akun baru. Melempar RuntimeException bila email sudah terdaftar. */
function user_buat(array $profil, string $password): array
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return users_transaksi(function (array &$users) use ($profil, $hash): array {
        if (user_cari_email($users, $profil['email']) !== null) {
            throw new RuntimeException('Email ini sudah terdaftar. Silakan login.');
        }
        $baru = [
            'id'            => bin2hex(random_bytes(8)),
            'nama'          => $profil['nama'],
            'email'         => $profil['email'],
            'telepon'       => $profil['telepon'],
            'alamat'        => $profil['alamat'],
            'password_hash' => $hash,
            'dibuat'        => date('c'),
        ];
        $users[] = $baru;
        return $baru;
    });
}

/** Perbarui profil user. Melempar RuntimeException bila email dipakai akun lain. */
function user_perbarui(string $id, array $profil): void
{
    users_transaksi(function (array &$users) use ($id, $profil): void {
        $ada = null;
        foreach ($users as $i => $u) {
            if (mb_strtolower((string) $u['email']) === $profil['email'] && $u['id'] !== $id) {
                throw new RuntimeException('Email ini sudah dipakai akun lain.');
            }
            if ($u['id'] === $id) {
                $ada = $i;
            }
        }
        if ($ada === null) {
            throw new RuntimeException('Akun tidak ditemukan.');
        }
        foreach (['nama', 'email', 'telepon', 'alamat'] as $k) {
            $users[$ada][$k] = $profil[$k];
        }
    });
}

function user_ganti_password(string $id, string $passwordBaru): void
{
    $hash = password_hash($passwordBaru, PASSWORD_DEFAULT);
    users_transaksi(function (array &$users) use ($id, $hash): void {
        foreach ($users as $i => $u) {
            if ($u['id'] === $id) {
                $users[$i]['password_hash'] = $hash;
                return;
            }
        }
        throw new RuntimeException('Akun tidak ditemukan.');
    });
}

/** Nilai awal field formulir dari profil user yang sedang login. */
function profil_untuk_form(): array
{
    $u = pengguna();
    return [
        'nama'    => (string) ($u['nama'] ?? ''),
        'email'   => (string) ($u['email'] ?? ''),
        'telepon' => $u ? telp_user_tampil((string) $u['telepon']) : '',
        'alamat'  => (string) ($u['alamat'] ?? ''),
    ];
}
