<?php

function checkRole($required_role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $required_role) {
        // Redirect atau tampilkan pesan error jika role tidak sesuai
        header("Location: ../auth/login.php?error=Akses ditolak. Anda tidak memiliki izin.");
        exit;
    }
}