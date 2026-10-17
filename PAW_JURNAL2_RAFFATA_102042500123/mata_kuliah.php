<?php
require 'functions.php';
require 'connect.php';

$aksi = input($_GET, 'aksi');
if (!in_array($aksi, ['tambah', 'detail', 'edit', 'hapus'], true)) {
    $aksi = 'daftar';
}

$mk = ['kode' => '', 'nama' => '', 'dosen' => '', 'semester' => ''];
$errors = [];
$gagal = false;
$id = 0;

if ($aksi === 'detail' || $aksi === 'edit' || $aksi === 'hapus') {
    $id = (int) input($_GET, 'id');
    $mk = cari_mata_kuliah($conn, $id);
    if (!$mk) {
        pindah_halaman('mata_kuliah.php', 'Mata kuliah tidak ditemukan.');
    }
}

if ($aksi === 'daftar') {
    $search = input($_GET, 'search');

    $sql = 'SELECT * FROM mata_kuliah
            WHERE kode LIKE ? OR nama LIKE ? OR dosen LIKE ?
            ORDER BY kode';
    $kata = '%' . $search . '%';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$kata, $kata, $kata]);
    $daftar_mk = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (($aksi === 'tambah' || $aksi === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $mk = [
        'kode'     => strtoupper(input($_POST, 'kode')),
        'nama'     => input($_POST, 'nama'),
        'dosen'    => input($_POST, 'dosen'),
        'semester' => input($_POST, 'semester'),
    ];
    $errors = validasi_mata_kuliah($conn, $mk, $id);

    if (count($errors) === 0 && $aksi === 'tambah') {
        $stmt = $conn->prepare('INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)');
        $stmt->execute([$mk['kode'], $mk['nama'], $mk['dosen'], $mk['semester']]);

        pindah_halaman('mata_kuliah.php', 'Mata kuliah berhasil ditambahkan.');
    }

    if (count($errors) === 0 && $aksi === 'edit') {
        $stmt = $conn->prepare('UPDATE mata_kuliah SET kode = ?, nama = ?, dosen = ?, semester = ? WHERE id = ?');
        $stmt->execute([$mk['kode'], $mk['nama'], $mk['dosen'], $mk['semester'], $id]);

        pindah_halaman('mata_kuliah.php?aksi=detail&id=' . $id, 'Mata kuliah berhasil diperbarui.');
    }
}

if ($aksi === 'hapus') {
    $stmt_materi = $conn->prepare('SELECT COUNT(*) AS jumlah FROM materi WHERE mata_kuliah_id = ?');
    $stmt_materi->execute([$id]);
    $jumlah_materi = (int) $stmt_materi->get_result()->fetch_assoc()['jumlah'];

    $stmt_tugas = $conn->prepare('SELECT COUNT(*) AS jumlah FROM tugas WHERE mata_kuliah_id = ?');
    $stmt_tugas->execute([$id]);
    $jumlah_tugas  = (int) $stmt_tugas->get_result()->fetch_assoc()['jumlah'];

    $masih_dipakai = $jumlah_materi > 0 || $jumlah_tugas > 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$masih_dipakai) {
        try {
            $stmt = $conn->prepare('DELETE FROM mata_kuliah WHERE id = ?');
            $stmt->execute([$id]);

            pindah_halaman('mata_kuliah.php', 'Mata kuliah berhasil dihapus.');
        } catch (mysqli_sql_exception $e) {
            $gagal = true;
        }
    }
}

$judul = 'Mata kuliah';
require 'header.php';
?>

<?php if ($aksi === 'daftar'): ?>
    <div class="page-head">
        <div>
            <h1>Mata kuliah</h1>
            <p class="muted">Menampilkan <?= count($daftar_mk) ?> data.</p>
        </div>
        <a class="btn" href="mata_kuliah.php?aksi=tambah">+ Tambah</a>
    </div>

    <form class="toolbar" method="get" action="mata_kuliah.php">
        <input type="text" name="search" placeholder="Cari kode, nama, atau dosen" value="<?= h($search) ?>">
        <button class="btn" type="submit">Cari</button>
        <a class="btn btn-ghost" href="mata_kuliah.php">Reset</a>
    </form>

    <?php if (count($daftar_mk) === 0): ?>
        <p class="empty">Data tidak ditemukan.</p>
    <?php else: ?>
        <div class="card list">
            <?php foreach ($daftar_mk as $baris): ?>
                <div class="list-item">
                    <div>
                        <p class="kicker"><span class="code"><?= h($baris['kode']) ?></span> | Semester <?= (int) $baris['semester'] ?></p>
                        <h2><?= h($baris['nama']) ?></h2>
                        <p class="muted"><?= h($baris['dosen']) ?></p>
                    </div>
                    <div class="actions">
                        <a class="btn btn-sm" href="mata_kuliah.php?aksi=detail&id=<?= (int) $baris['id'] ?>">Detail</a>
                        <a class="btn btn-sm btn-ghost" href="mata_kuliah.php?aksi=edit&id=<?= (int) $baris['id'] ?>">Edit</a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

<?php elseif ($aksi === 'tambah' || $aksi === 'edit'): ?>
    <h1><?= $aksi === 'tambah' ? 'Tambah' : 'Edit' ?> mata kuliah</h1>

    <?php if (count($errors) > 0): ?>
        <p class="notice notice-error">Data belum tersimpan. Perbaiki kolom yang ditandai.</p>
    <?php endif ?>

    <form class="card form" method="post" action="">
        <div class="fields">
            <div class="field">
                <label for="kode">Kode mata kuliah</label>
                <input class="kapital" type="text" id="kode" name="kode" maxlength="20" placeholder="PAW-101" value="<?= h($mk['kode']) ?>">
                <?php if (isset($errors['kode'])): ?>
                    <p class="field-error"><?= h($errors['kode']) ?></p>
                <?php endif ?>
            </div>

            <div class="field">
                <label for="semester">Semester</label>
                <input type="number" id="semester" name="semester" min="1" max="14" placeholder="3" value="<?= h($mk['semester']) ?>">
                <?php if (isset($errors['semester'])): ?>
                    <p class="field-error"><?= h($errors['semester']) ?></p>
                <?php endif ?>
            </div>

            <div class="field wide">
                <label for="nama">Nama mata kuliah</label>
                <input type="text" id="nama" name="nama" maxlength="100" placeholder="Pengembangan Aplikasi Website" value="<?= h($mk['nama']) ?>">
                <?php if (isset($errors['nama'])): ?>
                    <p class="field-error"><?= h($errors['nama']) ?></p>
                <?php endif ?>
            </div>

            <div class="field wide">
                <label for="dosen">Dosen pengampu</label>
                <input type="text" id="dosen" name="dosen" maxlength="100" placeholder="Nama dosen" value="<?= h($mk['dosen']) ?>">
                <?php if (isset($errors['dosen'])): ?>
                    <p class="field-error"><?= h($errors['dosen']) ?></p>
                <?php endif ?>
            </div>
        </div>
        <div class="actions">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-ghost" href="<?= $aksi === 'edit' ? 'mata_kuliah.php?aksi=detail&id=' . $id : 'mata_kuliah.php' ?>">Batal</a>
        </div>
    </form>

<?php elseif ($aksi === 'detail'): ?>
    <article class="card detail">
        <p class="kicker"><span class="code"><?= h($mk['kode']) ?></span> | Semester <?= (int) $mk['semester'] ?></p>
        <h1><?= h($mk['nama']) ?></h1>

        <dl class="specs">
            <dt>Kode</dt>
            <dd><?= h($mk['kode']) ?></dd>
            <dt>Dosen pengampu</dt>
            <dd><?= h($mk['dosen']) ?></dd>
            <dt>Semester</dt>
            <dd><?= (int) $mk['semester'] ?></dd>
        </dl>

        <div class="actions">
            <a class="btn" href="mata_kuliah.php?aksi=edit&id=<?= $id ?>">Edit</a>
            <a class="btn btn-ghost btn-danger" href="mata_kuliah.php?aksi=hapus&id=<?= $id ?>">Hapus</a>
            <a class="btn btn-ghost" href="mata_kuliah.php">Kembali</a>
        </div>
    </article>

<?php elseif ($aksi === 'hapus'): ?>
    <?php if ($gagal): ?>
        <p class="notice notice-error">Mata kuliah ini tidak dapat dihapus karena masih memiliki materi atau tugas.</p>
    <?php endif ?>

    <?php if ($masih_dipakai): ?>
        <div class="card detail">
            <h1>Belum bisa dihapus</h1>
            <p class="muted"><b><?= h($mk['nama']) ?></b> masih punya <?= (int) $jumlah_materi ?> materi dan <?= (int) $jumlah_tugas ?> tugas. Hapus dulu materi dan tugasnya.</p>
            <div class="actions">
                <a class="btn" href="mata_kuliah.php?aksi=detail&id=<?= $id ?>">Kembali</a>
            </div>
        </div>
    <?php else: ?>
        <form class="card detail" method="post" action="">
            <h1>Hapus mata kuliah ini?</h1>
            <p class="muted"><b><?= h($mk['nama']) ?></b> dengan kode <b><?= h($mk['kode']) ?></b> akan dihapus dan tidak bisa dikembalikan.</p>
            <div class="actions">
                <button class="btn" type="submit">Ya, hapus</button>
                <a class="btn btn-ghost" href="mata_kuliah.php?aksi=detail&id=<?= $id ?>">Batal</a>
            </div>
        </form>
    <?php endif ?>
<?php endif ?>

<?php require 'footer.php'; ?>
