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

// ============================================
// BASE URL - Auto detect folder name
// ============================================
// Deteksi nama folder project secara otomatis
$script_name = $_SERVER['SCRIPT_NAME']; // e.g. /XAMPPPPP/index.php or /XAMPPPPP/barang/index.php
$doc_root = $_SERVER['DOCUMENT_ROOT'];
$file_path = str_replace('\\', '/', __DIR__); // config folder path

// Get the project root (one level up from /config)
$project_root = dirname($file_path);
// Get relative path from document root
$relative_path = str_replace(str_replace('\\', '/', $doc_root), '', $project_root);

// Define BASE_URL constant
if (!defined('BASE_URL')) {
    define('BASE_URL', $relative_path);
}

