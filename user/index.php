<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

// Only admin can access
if ($_SESSION['role'] != 'admin') {
    header('Location: /XAMPPPPP/index.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Don't delete self
    if ($id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
        header('Location: /XAMPPPPP/user/index.php?msg=deleted');
        exit;
    }
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
?>

<div class="content-wrapper">
    <div class="container">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><i class="bi bi-people me-2"></i>Manajemen User</h2>
                <p>Kelola akun pengguna sistem</p>
            </div>
            <a href="/XAMPPPPP/user/tambah.php" class="btn btn-primary-custom btn-sm-custom">
                <i class="bi bi-person-plus me-1"></i>Tambah User
            </a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-custom mb-4">
                <i class="bi bi-check-circle me-2"></i>
                <?php
                    switch($_GET['msg']) {
                        case 'added': echo 'User berhasil ditambahkan.'; break;
                        case 'deleted': echo 'User berhasil dihapus.'; break;
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="table-custom">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($row['username']) ?></strong></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td>
                            <?php if ($row['role'] == 'admin'): ?>
                                <span class="badge-admin">Admin</span>
                            <?php else: ?>
                                <span class="badge-staff">Staff</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                            <a href="/XAMPPPPP/user/index.php?delete=<?= $row['id'] ?>" 
                               class="btn btn-danger-custom btn-sm-custom"
                               onclick="return confirm('Yakin ingin menghapus user ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php else: ?>
                                <span class="text-muted">(Anda)</span>
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
