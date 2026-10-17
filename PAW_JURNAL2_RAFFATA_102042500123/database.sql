CREATE DATABASE IF NOT EXISTS paw_jurnal2_lms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE paw_jurnal2_lms;

CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    semester TINYINT UNSIGNED NOT NULL
);

CREATE TABLE IF NOT EXISTS materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tautan VARCHAR(255) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah (id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    instruksi TEXT NOT NULL,
    tenggat DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah (id) ON DELETE RESTRICT
);

INSERT IGNORE INTO mata_kuliah (id, kode, nama, dosen, semester) VALUES
(1, 'PAW-101', 'Pengembangan Aplikasi Website', 'Dosen PAW', 3),
(2, 'ALP-101', 'Algoritma dan Pemrograman', 'Dosen ALP', 1);

INSERT IGNORE INTO materi (id, mata_kuliah_id, judul, isi, tautan) VALUES
(1, 1, 'Operasi CRUD PHP dan MySQL', 'Pelajari penambahan, pembacaan, perubahan, dan penghapusan data melalui PHP.', 'https://www.php.net/manual/en/book.mysqli.php'),
(2, 2, 'Percabangan dan Perulangan', 'Gunakan kondisi dan perulangan untuk mengolah data dalam program.', 'https://www.php.net/manual/en/language.control-structures.php');

INSERT IGNORE INTO tugas (id, mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
(1, 1, 'Membangun LMS sederhana', 'Bangun pengelolaan mata kuliah, materi, dan tugas dengan operasi CRUD yang dapat diuji.', '2026-11-01', 'Aktif'),
(2, 2, 'Latihan percabangan', 'Buat program pemilihan kondisi dan dokumentasikan contoh masukannya.', '2026-11-03', 'Draf');
