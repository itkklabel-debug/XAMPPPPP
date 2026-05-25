<?php
// API JSON Endpoint - Data Barang
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once __DIR__ . '/../config/koneksi.php';

$result = mysqli_query($conn, "SELECT id, nama_barang, stok, foto, created_at, updated_at FROM barang ORDER BY id ASC");

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'id' => (int)$row['id'],
        'nama_barang' => $row['nama_barang'],
        'stok' => (int)$row['stok'],
        'foto' => $row['foto'] ? '/XAMPPPPP/upload/' . $row['foto'] : null,
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at']
    ];
}

echo json_encode([
    'status' => 'success',
    'total' => count($data),
    'data' => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
