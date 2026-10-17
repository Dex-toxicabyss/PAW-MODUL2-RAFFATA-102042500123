<?php
$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($judul) ?> | LMS Sederhana</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
    <div class="wrap">
        <a class="brand" href="index.php"><span class="brand-mark"></span>LMS Sederhana</a>
        <nav>
            <a class="nav-link" href="index.php">Beranda</a>
            <a class="nav-link" href="mata_kuliah.php">Mata kuliah</a>
            <a class="nav-link" href="materi.php">Materi</a>
            <a class="nav-link" href="tugas.php">Tugas</a>
        </nav>
    </div>
</header>
<main class="wrap">
<?php if ($pesan !== ''): ?>
    <p class="notice" role="status"><?= h($pesan) ?></p>
<?php endif ?>
