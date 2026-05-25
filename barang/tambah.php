<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = mysqli_real_escape_string($conn, trim($_POST['nama_barang'] ?? ''));
    $stok = (int)($_POST['stok'] ?? 0);
    $foto = '';

    if (empty($nama_barang)) {
        $error = 'Nama barang wajib diisi.';
    } else {
        // Handle file upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['foto']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = 'Format file harus JPG, JPEG, atau PNG.';
            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 2MB.';
            } else {
                $foto = 'barang_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $upload_path = __DIR__ . '/../upload/' . $foto;

                if (!is_dir(__DIR__ . '/../upload')) {
                    mkdir(__DIR__ . '/../upload', 0755, true);
                }

                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                    $error = 'Gagal mengupload file.';
                    $foto = '';
                }
            }
        }

        if (empty($error)) {
            $stmt = mysqli_prepare($conn, "INSERT INTO barang (nama_barang, stok, foto) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sis", $nama_barang, $stok, $foto);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: ' . BASE_URL . '/barang/index.php?msg=added');
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
            <h2><i class="bi bi-plus-circle me-2"></i>Tambah Barang</h2>
            <p>Tambah data barang baru ke inventaris</p>
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

                        <form method="POST" enctype="multipart/form-data" class="form-custom">
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang</label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" 
                                       placeholder="Masukkan nama barang" value="<?= htmlspecialchars($nama_barang ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok Awal</label>
                                <input type="number" class="form-control" id="stok" name="stok" 
                                       placeholder="0" value="<?= $stok ?? 0 ?>" min="0" required>
                            </div>
                            <div class="mb-4">
                                <label for="foto" class="form-label">Foto Barang (opsional)</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-check-lg me-1"></i>Simpan
                                </button>
                                <a href="<?= BASE_URL ?>/barang/index.php" class="btn btn-outline-custom">
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
