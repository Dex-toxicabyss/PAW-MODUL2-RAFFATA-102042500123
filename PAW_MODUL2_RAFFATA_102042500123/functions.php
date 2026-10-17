<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$pilihan_tipe = ['MPV', 'SUV', 'Sedan', 'Hatchback'];

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

function rupiah($angka)
{
    return 'Rp' . number_format((float) $angka, 0, ',', '.');
}

function pindah_halaman($tujuan, $pesan = '')
{
    if ($pesan !== '') {
        $_SESSION['pesan'] = $pesan;
    }
    header('Location: ' . $tujuan);
    exit;
}

function data_form()
{
    return [
        'kode_mobil'  => strtoupper(input($_POST, 'kode_mobil')),
        'nama_mobil'  => input($_POST, 'nama_mobil'),
        'brand_mobil' => input($_POST, 'brand_mobil'),
        'warna_mobil' => input($_POST, 'warna_mobil'),
        'tipe_mobil'  => input($_POST, 'tipe_mobil'),
        'harga_mobil' => input($_POST, 'harga_mobil'),
    ];
}

function cari_mobil($conn, $id)
{
    $stmt = $conn->prepare('SELECT * FROM showroom_mobil WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->get_result()->fetch_assoc();
}

function kode_sudah_dipakai($conn, $kode, $id_sendiri = 0)
{
    $stmt = $conn->prepare('SELECT id FROM showroom_mobil WHERE kode_mobil = ? AND id <> ?');
    $stmt->execute([$kode, $id_sendiri]);
    return $stmt->get_result()->num_rows > 0;
}

function validasi_mobil($conn, $data, $id_sendiri = 0)
{
    global $pilihan_tipe;
    $errors = [];

    if (!preg_match('/^[A-Z0-9-]{3,20}$/', $data['kode_mobil'])) {
        $errors['kode_mobil'] = 'Kode 3-20 karakter, hanya huruf, angka, dan tanda -.';
    } elseif (kode_sudah_dipakai($conn, $data['kode_mobil'], $id_sendiri)) {
        $errors['kode_mobil'] = 'Kode mobil sudah digunakan.';
    }

    $batas = ['nama_mobil' => 100, 'brand_mobil' => 50, 'warna_mobil' => 30];
    foreach ($batas as $kolom => $maks) {
        $panjang = mb_strlen($data[$kolom]);
        if ($panjang < 2 || $panjang > $maks) {
            $errors[$kolom] = 'Isi 2-' . $maks . ' karakter.';
        }
    }

    if (!in_array($data['tipe_mobil'], $pilihan_tipe, true)) {
        $errors['tipe_mobil'] = 'Pilih tipe yang tersedia.';
    }

    if (!preg_match('/^\d{1,11}(\.\d{1,2})?$/', $data['harga_mobil']) || (float) $data['harga_mobil'] <= 0) {
        $errors['harga_mobil'] = 'Harga harus angka lebih dari 0.';
    }

    return $errors;
}
