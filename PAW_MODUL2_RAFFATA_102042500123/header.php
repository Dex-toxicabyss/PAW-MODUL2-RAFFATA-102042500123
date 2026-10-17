<?php
$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($judul) ?> | ShowRoom EAD</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
    <div class="wrap">
        <a class="brand" href="index.php"><span class="brand-mark"></span>ShowRoom EAD</a>
        <nav>
            <a class="nav-link" href="index.php">Daftar mobil</a>
            <a class="btn btn-sm" href="create.php">+ Tambah</a>
        </nav>
    </div>
</header>
<main class="wrap">
<?php if ($pesan !== ''): ?>
    <p class="notice" role="status"><?= h($pesan) ?></p>
<?php endif ?>
