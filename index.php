<?php
require_once __DIR__ . '/config/koneksi.php';
include __DIR__ . '/template/header.php';
include __DIR__ . '/template/navbar.php';

// Get statistics
$total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM barang"))['total'];
$total_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah), 0) as total FROM barang_masuk"))['total'];
$total_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah), 0) as total FROM barang_keluar"))['total'];
$total_pengajuan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengajuan WHERE status = 'pending'"))['total'];

// Get chart data - top 10 items by stock
$chart_query = mysqli_query($conn, "SELECT nama_barang, stok FROM barang ORDER BY stok DESC LIMIT 10");
$chart_labels = [];
$chart_data = [];
while ($row = mysqli_fetch_assoc($chart_query)) {
    $chart_labels[] = $row['nama_barang'];
    $chart_data[] = $row['stok'];
}
?>

<div class="content-wrapper">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
            <p>Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>! Berikut ringkasan stok barang.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-archive"></i></div>
                    <div class="stat-number"><?= $total_barang ?></div>
                    <div class="stat-label">Total Barang</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-box-arrow-in-down"></i></div>
                    <div class="stat-number"><?= $total_masuk ?></div>
                    <div class="stat-label">Barang Masuk</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-box-arrow-up"></i></div>
                    <div class="stat-number"><?= $total_keluar ?></div>
                    <div class="stat-label">Barang Keluar</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
                    <div class="stat-number"><?= $total_pengajuan ?></div>
                    <div class="stat-label">Pengajuan Pending</div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="row">
            <div class="col-lg-8">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="bi bi-bar-chart me-2"></i>Grafik Stok Barang (Top 10)</h5>
                    <canvas id="stockChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="bi bi-pie-chart me-2"></i>Distribusi Stok</h5>
                    <canvas id="pieChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bar Chart
    const ctx = document.getElementById('stockChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Jumlah Stok',
                data: <?= json_encode($chart_data) ?>,
                backgroundColor: 'rgba(0, 117, 74, 0.7)',
                borderColor: '#00754A',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Pie Chart
    const ctx2 = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                data: <?= json_encode($chart_data) ?>,
                backgroundColor: [
                    '#00754A', '#006241', '#1E3932', '#00A862',
                    '#34A853', '#4CAF50', '#66BB6A', '#81C784',
                    '#A5D6A7', '#C8E6C9'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8 } }
            }
        }
    });
});
</script>

<?php include __DIR__ . '/template/footer.php'; ?>
