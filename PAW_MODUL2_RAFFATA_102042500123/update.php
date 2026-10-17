<?php
require 'functions.php';
require 'connect.php';

$id    = (int) input($_GET, 'id');
$mobil = cari_mobil($conn, $id);
if (!$mobil) {
    pindah_halaman('index.php', 'Data mobil tidak ditemukan.');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobil  = data_form();
    $errors = validasi_mobil($conn, $mobil, $id);

    if (count($errors) === 0) {
        $stmt = $conn->prepare('UPDATE showroom_mobil SET kode_mobil = ?, nama_mobil = ?, brand_mobil = ?, warna_mobil = ?, tipe_mobil = ?, harga_mobil = ? WHERE id = ?');
        $berhasil = $stmt->execute([
            $mobil['kode_mobil'],
            $mobil['nama_mobil'],
            $mobil['brand_mobil'],
            $mobil['warna_mobil'],
            $mobil['tipe_mobil'],
            $mobil['harga_mobil'],
            $id,
        ]);

        if ($berhasil) {
            pindah_halaman('detail.php?id=' . $id, 'Data mobil berhasil diperbarui.');
        }
        $errors['umum'] = 'Perubahan data mobil gagal disimpan.';
    }
}

$judul = 'Edit mobil';
require 'header.php';
?>
<h1>Edit mobil</h1>

<?php if (count($errors) > 0): ?>
    <p class="notice notice-error"><?= h($errors['umum'] ?? 'Data belum tersimpan. Perbaiki kolom yang ditandai.') ?></p>
<?php endif ?>

<form class="card form" method="post" action="update.php?id=<?= $id ?>">
    <?php require 'form_mobil.php'; ?>
    <div class="actions">
        <button class="btn" type="submit">Simpan perubahan</button>
        <a class="btn btn-ghost" href="detail.php?id=<?= $id ?>">Batal</a>
    </div>
</form>
<?php require 'footer.php'; ?>
