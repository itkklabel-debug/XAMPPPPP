<?php
require_once __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../template/header.php';
include __DIR__ . '/../template/navbar.php';

$error = '';
$barang_list = mysqli_query($conn, "SELECT id, nama_barang, stok FROM barang ORDER BY nama_barang ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah = (int)($_POST['jumlah'] ?? 0);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? '');
    $keterangan = mysqli_real_escape_string($conn, trim($_POST['keterangan'] ?? ''));
    $id_user = $_SESSION['user_id'];

    if (!$id_barang || $jumlah <= 0 || empty($tanggal)) {
        $error = 'Semua field wajib diisi dengan benar.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO pengajuan (id_barang, id_user, jumlah, tanggal, keterangan) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiiss", $id_barang, $id_user, $jumlah, $tanggal, $keterangan);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: /XAMPPPPP/pengajuan/index.php?msg=added');
            exit;
        } else {
            $error = 'Gagal menyimpan pengajuan.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="content-wrapper">
    <div class="container">
        <div class="page-header">
            <h2><i class="bi bi-clipboard-plus me-2"></i>Ajukan Barang</h2>
            <p>Buat pengajuan permintaan barang baru</p>
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
                                <label for="id_barang" class="form-label">Pilih Barang</label>
                                <select class="form-select" id="id_barang" name="id_barang" required>
                                    <option value="">-- Pilih Barang --</option>
                                    <?php while ($b = mysqli_fetch_assoc($barang_list)): ?>
                                        <option value="<?= $b['id'] ?>" <?= (isset($id_barang) && $id_barang == $b['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Yang Diajukan</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                       value="<?= $jumlah ?? '' ?>" min="1" placeholder="0" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                       value="<?= $tanggal ?? date('Y-m-d') ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="keterangan" class="form-label">Keterangan / Alasan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                          placeholder="Jelaskan alasan pengajuan..."><?= htmlspecialchars($keterangan ?? '') ?></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-send me-1"></i>Kirim Pengajuan
                                </button>
                                <a href="/XAMPPPPP/pengajuan/index.php" class="btn btn-outline-custom">
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
