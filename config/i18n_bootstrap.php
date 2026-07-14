<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../user/i18n.php';

$current_language = $_SESSION['language'] ?? 'id';

load_language($current_language);
