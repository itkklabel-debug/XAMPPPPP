<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

$data = mysqli_query($conn, "SELECT bm.*, b.nama_barang FROM barang_masuk bm 
                             JOIN barang b ON bm.id_barang = b.id 
                             ORDER BY bm.id DESC");
?>

<div class="content-wrapper">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><i class="bi bi-box-arrow-in-down me-2"></i>Barang Masuk</h2>
                <p>Catatan barang masuk ke inventaris</p>
            </div>
            <a href="<?= BASE_URL ?>/barang_masuk/tambah.php" class="btn btn-primary-custom btn-sm-custom">
                <i class="bi bi-plus-lg me-1"></i>Input Barang Masuk
            </a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
            <div class="alert alert-success alert-custom mb-4">
                <i class="bi bi-check-circle me-2"></i>Barang masuk berhasil dicatat. Stok telah diperbarui.
            </div>
        <?php endif; ?>

        <div class="table-custom">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_barang']) ?></strong></td>
                        <td><span class="badge-approved">+<?= $row['jumlah'] ?></span></td>
                        <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>
