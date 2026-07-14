<?php

// Konfigurasi koneksi database
$host = "localhost"; // Server database
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$database = "personal_finance_tracker"; // Nama database yang kita buat

// Membuat koneksi ke database menggunakan MySQLi
$conn = mysqli_connect($host, $username, $password, $database);

// Memeriksa apakah koneksi berhasil atau gagal
if (!$conn) {
    // Jika gagal, hentikan eksekusi dan tampilkan pesan error
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Opsional tapi direkomendasikan: Set character set ke utf8mb4
mysqli_set_charset($conn, "utf8mb4");

?>