<?php

function checkRole($role)
{
    if (!isset($_SESSION['role'])) {
        header("Location: ../auth/login.php?error=Silakan login terlebih dahulu");
        exit;
    }

    if ($_SESSION['role'] !== $role) {
        header("Location: ../auth/login.php?error=Akses ditolak");
        exit;
    }
}