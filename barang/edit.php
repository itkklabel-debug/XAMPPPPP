<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: ' . BASE_URL . '/barang/index.php');
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM barang WHERE id = $id");
$barang = mysqli_fetch_assoc($result);
if (!$barang) {
    header('Location: ' . BASE_URL . '/barang/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = mysqli_real_escape_string($conn, trim($_POST['nama_barang'] ?? ''));
    $stok = (int)($_POST['stok'] ?? 0);

    if (empty($nama_barang)) {
        $error = 'Nama barang wajib diisi.';
    } else {
        $foto = $barang['foto']; // Keep existing foto

        // Handle new file upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['foto']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $error = 'Format file harus JPG, JPEG, atau PNG.';
            } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 2MB.';
            } else {
                $new_foto = 'barang_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $upload_path = __DIR__ . '/../upload/' . $new_foto;

                if (!is_dir(__DIR__ . '/../upload')) {
                    mkdir(__DIR__ . '/../upload', 0755, true);
                }

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                    // Delete old foto
                    if ($foto && file_exists(__DIR__ . '/../upload/' . $foto)) {
                        unlink(__DIR__ . '/../upload/' . $foto);
                    }
                    $foto = $new_foto;
                } else {
                    $error = 'Gagal mengupload file.';
                }
            }
        }

        if (empty($error)) {
            $stmt = mysqli_prepare($conn, "UPDATE barang SET nama_barang = ?, stok = ?, foto = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sisi", $nama_barang, $stok, $foto, $id);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: ' . BASE_URL . '/barang/index.php?msg=updated');
                exit;
            } else {
                $error = 'Gagal mengupdate data.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<div class="content-wrapper">
    <div class="container">
        <div class="page-header">
            <h2><i class="bi bi-pencil-square me-2"></i>Edit Barang</h2>
            <p>Edit data barang: <?= htmlspecialchars($barang['nama_barang']) ?></p>
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
                                       value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" 
                                       value="<?= $barang['stok'] ?>" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Barang</label>
                                <?php if ($barang['foto']): ?>
                                    <div class="mb-2">
                                        <img src="<?= BASE_URL ?>/upload/<?= htmlspecialchars($barang['foto']) ?>" 
                                             class="img-thumbnail-custom" style="max-width: 120px; height: auto;" alt="foto">
                                        <small class="d-block text-muted mt-1">Foto saat ini</small>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.jpeg,.png">
                                <small class="text-muted">Kosongkan jika tidak ingin mengganti foto. Format: JPG, JPEG, PNG. Maks: 2MB</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-check-lg me-1"></i>Update
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
