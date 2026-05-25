<?php
// ============================================
// KONFIGURASI DATABASE
// ============================================

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'stock_barang';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
