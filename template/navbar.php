<?php
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/XAMPPPPP/index.php">
            <i class="bi bi-box-seam me-2"></i>Stock Barang
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= ($current_page == 'index.php' && $current_dir == 'XAMPPPPP') ? 'active' : '' ?>" href="/XAMPPPPP/index.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_dir == 'barang' ? 'active' : '' ?>" href="/XAMPPPPP/barang/index.php">
                        <i class="bi bi-archive me-1"></i>Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_dir == 'barang_masuk' ? 'active' : '' ?>" href="/XAMPPPPP/barang_masuk/index.php">
                        <i class="bi bi-box-arrow-in-down me-1"></i>Masuk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_dir == 'barang_keluar' ? 'active' : '' ?>" href="/XAMPPPPP/barang_keluar/index.php">
                        <i class="bi bi-box-arrow-up me-1"></i>Keluar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_dir == 'pengajuan' ? 'active' : '' ?>" href="/XAMPPPPP/pengajuan/index.php">
                        <i class="bi bi-clipboard-check me-1"></i>Pengajuan
                    </a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_dir == 'user' ? 'active' : '' ?>" href="/XAMPPPPP/user/index.php">
                        <i class="bi bi-people me-1"></i>User
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted"><?= ucfirst($_SESSION['role'] ?? '') ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/XAMPPPPP/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
