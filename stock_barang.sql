-- ============================================
-- DATABASE: stock_barang
-- Aplikasi Stock Barang - PHP Native + MySQL
-- ============================================

CREATE DATABASE IF NOT EXISTS stock_barang;
USE stock_barang;

-- ============================================
-- TABEL: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL: barang
-- ============================================
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL: barang_masuk
-- ============================================
CREATE TABLE IF NOT EXISTS barang_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NOT NULL,
    jumlah INT NOT NULL,
    tanggal DATE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_barang) REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABEL: barang_keluar
-- ============================================
CREATE TABLE IF NOT EXISTS barang_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NOT NULL,
    jumlah INT NOT NULL,
    tanggal DATE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_barang) REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABEL: pengajuan
-- ============================================
CREATE TABLE IF NOT EXISTS pengajuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NOT NULL,
    id_user INT NOT NULL,
    jumlah INT NOT NULL,
    keterangan TEXT,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    tanggal DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_barang) REFERENCES barang(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- DATA AWAL: Admin Default
-- Password: admin123
-- ============================================
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'),
('staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff User', 'staff');

-- ============================================
-- DATA AWAL: Barang Contoh
-- ============================================
INSERT INTO barang (nama_barang, stok) VALUES
('Kertas A4 70gr', 100),
('Tinta Printer HP', 50),
('Pulpen Pilot', 200),
('Map Plastik', 150),
('Stapler Kenko', 30);
