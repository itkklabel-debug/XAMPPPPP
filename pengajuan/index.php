<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

// Handle approve/reject (admin only)
if (isset($_GET['action']) && isset($_GET['id']) && $_SESSION['role'] == 'admin') {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'approve') {
        // Get pengajuan details
        $peng = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengajuan WHERE id = $id AND status = 'pending'"));
        if ($peng) {
            // Check stock availability
            $stok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM barang WHERE id = " . $peng['id_barang']));
            if ($stok && $stok['stok'] >= $peng['jumlah']) {
                // Approve and reduce stock
                mysqli_query($conn, "UPDATE pengajuan SET status = 'approved' WHERE id = $id");
                mysqli_query($conn, "UPDATE barang SET stok = stok - " . $peng['jumlah'] . " WHERE id = " . $peng['id_barang']);
                // Also record as barang keluar
                $stmt = mysqli_prepare($conn, "INSERT INTO barang_keluar (id_barang, jumlah, tanggal, keterangan) VALUES (?, ?, ?, ?)");
                $ket = "Pengajuan disetujui #" . $id;
                $tgl = date('Y-m-d');
                mysqli_stmt_bind_param($stmt, "iiss", $peng['id_barang'], $peng['jumlah'], $tgl, $ket);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                header('Location: ' . BASE_URL . '/pengajuan/index.php?msg=approved');
                exit;
            } else {
                header('Location: ' . BASE_URL . '/pengajuan/index.php?msg=stok_kurang');
                exit;
            }
        }
    } elseif ($action === 'reject') {
        mysqli_query($conn, "UPDATE pengajuan SET status = 'rejected' WHERE id = $id AND status = 'pending'");
        header('Location: ' . BASE_URL . '/pengajuan/index.php?msg=rejected');
        exit;
    }
}

// Query based on role
if ($_SESSION['role'] == 'admin') {
    $data = mysqli_query($conn, "SELECT p.*, b.nama_barang, u.nama_lengkap 
                                 FROM pengajuan p 
                                 JOIN barang b ON p.id_barang = b.id 
                                 JOIN users u ON p.id_user = u.id 
                                 ORDER BY p.id DESC");
} else {
    $user_id = $_SESSION['user_id'];
    $data = mysqli_query($conn, "SELECT p.*, b.nama_barang, u.nama_lengkap 
                                 FROM pengajuan p 
                                 JOIN barang b ON p.id_barang = b.id 
                                 JOIN users u ON p.id_user = u.id 
                                 WHERE p.id_user = $user_id
                                 ORDER BY p.id DESC");
}
?>

<div class="content-wrapper">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><i class="bi bi-clipboard-check me-2"></i>Pengajuan Barang</h2>
                <p><?= $_SESSION['role'] == 'admin' ? 'Kelola pengajuan barang dari staff' : 'Ajukan permintaan barang' ?></p>
            </div>
            <?php if ($_SESSION['role'] == 'staff'): ?>
            <a href="<?= BASE_URL ?>/pengajuan/tambah.php" class="btn btn-primary-custom btn-sm-custom">
                <i class="bi bi-plus-lg me-1"></i>Ajukan Barang
            </a>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert <?= $_GET['msg'] == 'stok_kurang' ? 'alert-danger' : 'alert-success' ?> alert-custom mb-4">
                <i class="bi bi-<?= $_GET['msg'] == 'stok_kurang' ? 'exclamation-circle' : 'check-circle' ?> me-2"></i>
                <?php
                    switch($_GET['msg']) {
                        case 'added': echo 'Pengajuan berhasil dikirim.'; break;
                        case 'approved': echo 'Pengajuan disetujui. Stok telah dikurangi.'; break;
                        case 'rejected': echo 'Pengajuan ditolak.'; break;
                        case 'stok_kurang': echo 'Gagal approve: Stok barang tidak mencukupi!'; break;
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="table-custom">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pengaju</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_barang']) ?></strong></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <span class="badge-pending">Pending</span>
                            <?php elseif ($row['status'] == 'approved'): ?>
                                <span class="badge-approved">Approved</span>
                            <?php else: ?>
                                <span class="badge-rejected">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <a href="<?= BASE_URL ?>/pengajuan/index.php?action=approve&id=<?= $row['id'] ?>" 
                                   class="btn btn-primary-custom btn-sm-custom me-1"
                                   onclick="return confirm('Setujui pengajuan ini?')">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/pengajuan/index.php?action=reject&id=<?= $row['id'] ?>" 
                                   class="btn btn-danger-custom btn-sm-custom"
                                   onclick="return confirm('Tolak pengajuan ini?')">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>
