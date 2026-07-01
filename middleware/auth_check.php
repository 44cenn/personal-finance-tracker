<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?error=Silakan login terlebih dahulu");
    exit;
}

if (!isset($_SESSION['role'])) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php?error=Session tidak valid, silakan login ulang");
    exit;
}