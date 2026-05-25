<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

// Handle delete
if (isset($_GET['delete']) && $_SESSION['role'] == 'admin') {
    $id = (int)$_GET['delete'];
    // Get foto to delete file
    $res = mysqli_query($conn, "SELECT foto FROM barang WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['foto'] && file_exists(__DIR__ . '/../upload/' . $row['foto'])) {
        unlink(__DIR__ . '/../upload/' . $row['foto']);
    }
    mysqli_query($conn, "DELETE FROM barang WHERE id = $id");
    header('Location: /XAMPPPPP/barang/index.php?msg=deleted');
    exit;
}

$barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
?>

<div class="content-wrapper">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><i class="bi bi-archive me-2"></i>Manajemen Barang</h2>
                <p>Kelola data barang inventaris</p>
            </div>
            <div class="d-flex gap-2">
                <a href="/XAMPPPPP/barang/export.php" class="btn btn-outline-custom btn-sm-custom">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                </a>
                <a href="/XAMPPPPP/barang/tambah.php" class="btn btn-primary-custom btn-sm-custom">
                    <i class="bi bi-plus-lg me-1"></i>Tambah Barang
                </a>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-custom mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php
                    switch($_GET['msg']) {
                        case 'added': echo 'Barang berhasil ditambahkan.'; break;
                        case 'updated': echo 'Barang berhasil diupdate.'; break;
                        case 'deleted': echo 'Barang berhasil dihapus.'; break;
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="table-custom">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($barang)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <?php if ($row['foto']): ?>
                                <img src="/XAMPPPPP/upload/<?= htmlspecialchars($row['foto']) ?>" class="img-thumbnail-custom" alt="foto">
                            <?php else: ?>
                                <span class="text-muted"><i class="bi bi-image" style="font-size: 1.5rem;"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($row['nama_barang']) ?></strong></td>
                        <td>
                            <span class="badge-approved"><?= $row['stok'] ?> unit</span>
                        </td>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="/XAMPPPPP/barang/edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-custom btn-sm-custom me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="/XAMPPPPP/barang/index.php?delete=<?= $row['id'] ?>" 
                               class="btn btn-danger-custom btn-sm-custom"
                               onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>
