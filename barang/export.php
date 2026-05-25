<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_barang_" . date('Y-m-d') . ".xls");

$barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY id ASC");
?>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Stok</th>
            <th>Tanggal Dibuat</th>
            <th>Tanggal Update</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($barang)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
            <td><?= $row['stok'] ?></td>
            <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
            <td><?= date('d-m-Y H:i', strtotime($row['updated_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
