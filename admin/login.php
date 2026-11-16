<?php
declare(strict_types=1);
// Login admin sekarang satu pintu dengan login user: /login.php
// (file ini dipertahankan agar tautan lama dan wajib_admin() tetap bekerja).
require_once __DIR__ . '/../config.php';
header('Location: ' . (admin_masuk() ? 'index.php' : '../login.php'));
exit;
