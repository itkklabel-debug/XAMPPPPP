<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

// Only admin
if ($_SESSION['role'] != 'admin') {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['admin', 'staff']) ? $_POST['role'] : 'staff';

    if (empty($username) || empty($nama_lengkap) || empty($password)) {
        $error = 'Semua field wajib diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        // Check duplicate username
        $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Username sudah digunakan.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed, $nama_lengkap, $role);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: ' . BASE_URL . '/user/index.php?msg=added');
                exit;
            } else {
                $error = 'Gagal menyimpan data.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="content-wrapper">
    <div class="container">
        <div class="page-header">
            <h2><i class="bi bi-person-plus me-2"></i>Tambah User</h2>
            <p>Buat akun pengguna baru</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card-custom">
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-custom mb-4">
                                <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="form-custom">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= htmlspecialchars($username ?? '') ?>" placeholder="Username unik" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                       value="<?= htmlspecialchars($nama_lengkap ?? '') ?>" placeholder="Nama lengkap pengguna" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="mb-4">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="staff" <?= (isset($role) && $role == 'staff') ? 'selected' : '' ?>>Staff</option>
                                    <option value="admin" <?= (isset($role) && $role == 'admin') ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-check-lg me-1"></i>Simpan
                                </button>
                                <a href="<?= BASE_URL ?>/user/index.php" class="btn btn-outline-custom">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../template/footer.php'; ?>
