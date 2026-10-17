# PAW-MODUL2-RAFFATA-102042500123

Repositori praktikum **Pengembangan Aplikasi Website (PAW) — Modul 2: Operasi CRUD dengan PHP & MySQL** di Program Studi S1 Sistem Informasi, Fakultas Rekayasa Industri, Telkom University Jakarta.

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-DB1A1A.svg)](LICENSE)

## Tentang Repositori Ini

Repositori ini memuat dua aplikasi web PHP native yang terhubung ke database MySQL/MariaDB menggunakan ekstensi `mysqli` dan *prepared statement*:
1. **Modul 2 (`ShowRoom EAD`)** — Aplikasi pencatatan inventaris mobil showroom dengan operasi CRUD, pencarian kata kunci, filter tipe kendaraan, dan validasi sisi server.
2. **Jurnal 2 (`LMS Sederhana`)** — Aplikasi *Learning Management System* (LMS) sederhana untuk mengelola **Mata Kuliah**, **Materi**, dan **Tugas** dengan relasi satu-ke-banyak (`1:N`), kueri `JOIN`, dan aturan `ON DELETE RESTRICT`.

## Identitas Praktikan

| Atribut | Keterangan |
| :--- | :--- |
| **Nama Lengkap** | Raffata Izacky Yuargya Aletama |
| **NIM** | `102042500123` |
| **Kelas** | `S1-SI-KJ-25-02` |
| **Kode Asisten** | `MARK` |
| **Dosen Pembimbing** | Dewi Marini Umi Atmaja, S.Kom., M.Kom. |
| **Program Studi** | S1 Sistem Informasi — Telkom University Jakarta |

## Struktur Folder Repositori

```text
PAW-MODUL2-RAFFATA-102042500123/
├── PAW_JURNAL2_RAFFATA_102042500123/
│   ├── database.sql           # Skema 3 tabel berelasi (ON DELETE RESTRICT) & data awal paw_jurnal2_lms
│   ├── connect.php            # Koneksi MySQLi ke database paw_jurnal2_lms
│   ├── functions.php          # Fungsi bantu h(), input(), pindah_halaman(), cari_*, & validasi_*
│   ├── header.php             # Kerangka navigasi atas & pembacaan pesan notifikasi session
│   ├── footer.php             # Penutup halaman HTML
│   ├── styles.css             # Gaya tampilan antarmuka LMS Sederhana
│   ├── index.php              # Beranda ringkasan statistik jumlah mata kuliah, materi, dan tugas
│   ├── mata_kuliah.php        # Pengelolaan CRUD mata kuliah & proteksi relasi sebelum hapus
│   ├── materi.php             # Pengelolaan CRUD materi, pencarian, & filter JOIN mata kuliah
│   └── tugas.php              # Pengelolaan CRUD tugas, pencarian, filter mata kuliah & status
├── PAW_MODUL2_RAFFATA_102042500123/
│   ├── db_modul2.sql          # Skema tabel showroom_mobil & 3 data mobil awal
│   ├── connect.php            # Koneksi objek mysqli ke database paw_modul2_showroom
│   ├── functions.php          # Fungsi bantu h(), input(), rupiah(), cari_mobil(), & validasi_mobil()
│   ├── header.php             # Kerangka navigasi atas & pembacaan pesan notifikasi session
│   ├── footer.php             # Penutup halaman HTML
│   ├── form_mobil.php         # Komponen input form mobil bersama untuk Create & Update
│   ├── index.php              # Daftar mobil, pencarian nama/brand, & filter tipe kendaraan
│   ├── create.php             # Penambahan mobil baru dengan prepared statement
│   ├── detail.php             # Halaman rincian lengkap satu unit mobil berdasarkan ID
│   ├── update.php             # Pembaruan data mobil berdasarkan ID
│   ├── delete.php             # Konfirmasi & penghapusan mobil lewat metode POST
│   └── styles.css             # Gaya tampilan antarmuka ShowRoom EAD
├── LICENSE                    # Lisensi MIT
└── README.md                  # Dokumentasi repositori
```

## Cara Menjalankan Project (Quick Start)

### Prasyarat
- **PHP 8.1** atau lebih baru dengan ekstensi `mysqli` aktif (Laragon, XAMPP, atau MAMP).
- **MySQL 8.0+** atau **MariaDB 10.4+** yang berjalan pada host `127.0.0.1` port `3306`.

### 1. Menjalankan Aplikasi Modul 2 (`ShowRoom EAD`)

1. Impor berkas SQL ke MySQL:
   ```bash
   mysql -u root < "PAW_MODUL2_RAFFATA_102042500123/db_modul2.sql"
   ```
2. Masuk ke folder Modul 2 dan nyalakan server bawaan PHP:
   ```bash
   cd "PAW_MODUL2_RAFFATA_102042500123"
   php -S 127.0.0.1:8000
   ```
3. Buka `http://127.0.0.1:8000` di web browser.

### 2. Menjalankan Aplikasi Jurnal 2 (`LMS Sederhana`)

1. Impor berkas `database.sql` ke MySQL:
   ```bash
   mysql -u root < "PAW_JURNAL2_RAFFATA_102042500123/database.sql"
   ```
2. Masuk ke folder Jurnal 2 dan nyalakan server bawaan PHP:
   ```bash
   cd "PAW_JURNAL2_RAFFATA_102042500123"
   php -S 127.0.0.1:8080
   ```
3. Buka `http://127.0.0.1:8080` di web browser.

## Contributor

Repositori ini dikerjakan secara mandiri oleh satu kontributor:

- **Raffata Izacky Yuargya Aletama** ([@Dex-toxicabyss](https://github.com/Dex-toxicabyss)) — *Author & Sole Contributor*

## License

Project ini dilisensikan di bawah **MIT License** — lihat berkas [LICENSE](LICENSE) untuk rincian lengkap.
