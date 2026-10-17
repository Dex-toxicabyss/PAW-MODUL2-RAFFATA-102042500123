<?php
require 'functions.php';
require 'connect.php';

$stmt = $conn->prepare('SELECT COUNT(*) AS jumlah FROM mata_kuliah');
$stmt->execute();
$jumlah_mk = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

$stmt = $conn->prepare('SELECT COUNT(*) AS jumlah FROM materi');
$stmt->execute();
$jumlah_materi = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

$stmt = $conn->prepare('SELECT COUNT(*) AS jumlah FROM tugas');
$stmt->execute();
$jumlah_tugas = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

$stmt = $conn->prepare('SELECT COUNT(*) AS jumlah FROM tugas WHERE status = ?');
$stmt->execute(['Aktif']);
$jumlah_aktif = (int) $stmt->get_result()->fetch_assoc()['jumlah'];

$judul = 'Beranda';
require 'header.php';
?>
<p class="eyebrow">Jurnal Modul 2</p>
<h1 class="hero">Kelola mata kuliah, materi, dan tugas di satu tempat.</h1>
<p class="muted">Satu mata kuliah bisa punya banyak materi dan banyak tugas.</p>

<div class="stats">
    <a class="card stat" href="mata_kuliah.php">
        <b><?= (int) $jumlah_mk ?></b>
        <span>Mata kuliah</span>
    </a>
    <a class="card stat" href="materi.php">
        <b><?= (int) $jumlah_materi ?></b>
        <span>Materi</span>
    </a>
    <a class="card stat" href="tugas.php">
        <b><?= (int) $jumlah_tugas ?></b>
        <span>Tugas</span>
    </a>
    <a class="card stat" href="tugas.php?status=Aktif">
        <b><?= (int) $jumlah_aktif ?></b>
        <span>Tugas aktif</span>
    </a>
</div>

<div class="actions">
    <a class="btn" href="mata_kuliah.php">Kelola mata kuliah</a>
    <a class="btn btn-ghost" href="materi.php">Kelola materi</a>
    <a class="btn btn-ghost" href="tugas.php">Kelola tugas</a>
</div>
<?php require 'footer.php'; ?>
