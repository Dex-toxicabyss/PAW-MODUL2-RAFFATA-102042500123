CREATE DATABASE IF NOT EXISTS paw_modul2_showroom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE paw_modul2_showroom;

CREATE TABLE IF NOT EXISTS showroom_mobil (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_mobil VARCHAR(20) NOT NULL UNIQUE,
    nama_mobil VARCHAR(100) NOT NULL,
    brand_mobil VARCHAR(50) NOT NULL,
    warna_mobil VARCHAR(30) NOT NULL,
    tipe_mobil VARCHAR(20) NOT NULL,
    harga_mobil DECIMAL(13,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO showroom_mobil (kode_mobil, nama_mobil, brand_mobil, warna_mobil, tipe_mobil, harga_mobil) VALUES
('MBL-001', 'Avanza Veloz', 'Toyota', 'Putih', 'MPV', 310000000),
('MBL-002', 'Honda City', 'Honda', 'Abu-abu', 'Sedan', 370000000),
('MBL-003', 'Suzuki Baleno', 'Suzuki', 'Biru', 'Hatchback', 285000000);
