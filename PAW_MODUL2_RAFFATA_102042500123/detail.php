<?php
require 'functions.php';
require 'connect.php';

$id    = (int) input($_GET, 'id');
$mobil = cari_mobil($conn, $id);
if (!$mobil) {
    pindah_halaman('index.php', 'Data mobil tidak ditemukan.');
}

$judul = 'Detail mobil';
require 'header.php';
?>
<article class="card detail">
    <div class="car-top">
        <span class="badge"><?= h($mobil['tipe_mobil']) ?></span>
        <span class="code"><?= h($mobil['kode_mobil']) ?></span>
    </div>
    <h1><?= h($mobil['nama_mobil']) ?></h1>
    <p class="price price-lg"><?= rupiah($mobil['harga_mobil']) ?></p>

    <dl class="specs">
        <dt>Kode mobil</dt>
        <dd><?= h($mobil['kode_mobil']) ?></dd>
        <dt>Brand</dt>
        <dd><?= h($mobil['brand_mobil']) ?></dd>
        <dt>Warna</dt>
        <dd><?= h($mobil['warna_mobil']) ?></dd>
        <dt>Tipe</dt>
        <dd><?= h($mobil['tipe_mobil']) ?></dd>
        <dt>Ditambahkan</dt>
        <dd><?= date('d-m-Y H:i', strtotime($mobil['created_at'])) ?></dd>
    </dl>

    <div class="actions">
        <a class="btn" href="update.php?id=<?= $id ?>">Edit mobil</a>
        <a class="btn btn-ghost btn-danger" href="delete.php?id=<?= $id ?>">Hapus mobil</a>
        <a class="btn btn-ghost" href="index.php">Kembali</a>
    </div>
</article>
<?php require 'footer.php'; ?>
