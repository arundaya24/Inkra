<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    keluar_sesi();
}
header('Location: index.php');
exit;
