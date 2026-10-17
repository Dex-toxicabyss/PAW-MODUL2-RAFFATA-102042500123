<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$pilihan_status = ['Draf', 'Aktif', 'Selesai'];

function h($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function input($sumber, $nama)
{
    if (isset($sumber[$nama]) && is_string($sumber[$nama])) {
        return trim($sumber[$nama]);
    }
    return '';
}

function pindah_halaman($tujuan, $pesan = '')
{
    if ($pesan !== '') {
        $_SESSION['pesan'] = $pesan;
    }
    header('Location: ' . $tujuan);
    exit;
}

function cari_mata_kuliah($conn, $id)
{
    $stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->get_result()->fetch_assoc();
}

function pilihan_mata_kuliah($conn)
{
    $stmt = $conn->prepare('SELECT id, kode, nama FROM mata_kuliah ORDER BY kode');
    $stmt->execute();
    $hasil = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $pilihan = [];
    foreach ($hasil as $baris) {
        $pilihan[(int) $baris['id']] = $baris['kode'] . ' - ' . $baris['nama'];
    }
    return $pilihan;
}

function cari_materi($conn, $id)
{
    $sql = 'SELECT m.*, mk.kode AS kode_mk, mk.nama AS nama_mk
            FROM materi m
            JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id
            WHERE m.id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->get_result()->fetch_assoc();
}

function cari_tugas($conn, $id)
{
    $sql = 'SELECT t.*, mk.kode AS kode_mk, mk.nama AS nama_mk
            FROM tugas t
            JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id
            WHERE t.id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->get_result()->fetch_assoc();
}

function kode_mk_sudah_dipakai($conn, $kode, $id_sendiri = 0)
{
    $stmt = $conn->prepare('SELECT id FROM mata_kuliah WHERE kode = ? AND id <> ?');
    $stmt->execute([$kode, $id_sendiri]);
    return $stmt->get_result()->num_rows > 0;
}

function validasi_mata_kuliah($conn, $data, $id_sendiri = 0)
{
    $errors = [];

    if (!preg_match('/^[A-Z0-9-]{3,20}$/', $data['kode'])) {
        $errors['kode'] = 'Kode 3-20 karakter, hanya huruf, angka, dan tanda -.';
    } elseif (kode_mk_sudah_dipakai($conn, $data['kode'], $id_sendiri)) {
        $errors['kode'] = 'Kode mata kuliah sudah digunakan.';
    }

    if (mb_strlen($data['nama']) < 2 || mb_strlen($data['nama']) > 100) {
        $errors['nama'] = 'Isi 2-100 karakter.';
    }

    if (mb_strlen($data['dosen']) < 2 || mb_strlen($data['dosen']) > 100) {
        $errors['dosen'] = 'Isi 2-100 karakter.';
    }

    if (!preg_match('/^\d+$/', (string) $data['semester']) || (int) $data['semester'] < 1 || (int) $data['semester'] > 14) {
        $errors['semester'] = 'Semester harus angka 1 sampai 14.';
    }

    return $errors;
}

function validasi_materi($data, $pilihan_mk)
{
    $errors = [];
    $id_mk = (int) $data['mata_kuliah_id'];

    if ($data['mata_kuliah_id'] === '' || !isset($pilihan_mk[$id_mk])) {
        $errors['mata_kuliah_id'] = 'Pilih mata kuliah yang tersedia.';
    }

    if (mb_strlen($data['judul']) < 2 || mb_strlen($data['judul']) > 150) {
        $errors['judul'] = 'Isi 2-150 karakter.';
    }

    if (mb_strlen($data['isi']) < 5) {
        $errors['isi'] = 'Isi materi minimal 5 karakter.';
    }

    if (
        mb_strlen($data['tautan']) > 255 ||
        !preg_match('/^https?:\/\/.+/i', $data['tautan']) ||
        filter_var($data['tautan'], FILTER_VALIDATE_URL) === false
    ) {
        $errors['tautan'] = 'Tautan harus diawali http:// atau https:// yang valid.';
    }

    return $errors;
}

function validasi_tugas($data, $pilihan_mk)
{
    global $pilihan_status;
    $errors = [];
    $id_mk = (int) $data['mata_kuliah_id'];

    if ($data['mata_kuliah_id'] === '' || !isset($pilihan_mk[$id_mk])) {
        $errors['mata_kuliah_id'] = 'Pilih mata kuliah yang tersedia.';
    }

    if (mb_strlen($data['judul']) < 2 || mb_strlen($data['judul']) > 150) {
        $errors['judul'] = 'Isi 2-150 karakter.';
    }

    if (mb_strlen($data['instruksi']) < 5) {
        $errors['instruksi'] = 'Instruksi tugas minimal 5 karakter.';
    }

    $tgl = DateTime::createFromFormat('Y-m-d', $data['tenggat']);
    if (!$tgl || $tgl->format('Y-m-d') !== $data['tenggat']) {
        $errors['tenggat'] = 'Isi tanggal tenggat yang valid.';
    }

    if (!in_array($data['status'], $pilihan_status, true)) {
        $errors['status'] = 'Pilih status yang tersedia.';
    }

    return $errors;
}
