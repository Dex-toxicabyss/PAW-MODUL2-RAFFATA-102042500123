<?php
require 'functions.php';
require 'connect.php';

$aksi = input($_GET, 'aksi');
if (!in_array($aksi, ['tambah', 'detail', 'edit', 'hapus'], true)) {
    $aksi = 'daftar';
}

$pilihan_mk = pilihan_mata_kuliah($conn);
$materi = ['mata_kuliah_id' => '', 'judul' => '', 'isi' => '', 'tautan' => ''];
$errors = [];
$gagal = false;
$id = 0;

if ($aksi === 'detail' || $aksi === 'edit' || $aksi === 'hapus') {
    $id = (int) input($_GET, 'id');
    $materi = cari_materi($conn, $id);
    if (!$materi) {
        pindah_halaman('materi.php', 'Materi tidak ditemukan.');
    }
}

if ($aksi === 'daftar') {
    $search = input($_GET, 'search');
    $id_mk  = (int) input($_GET, 'mata_kuliah_id');

    $sql = 'SELECT m.*, mk.kode AS kode_mk, mk.nama AS nama_mk
            FROM materi m
            JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id
            WHERE 1=1';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND (m.judul LIKE ? OR mk.nama LIKE ?)';
        $kata = '%' . $search . '%';
        $params[] = $kata;
        $params[] = $kata;
    }

    if ($id_mk !== 0) {
        $sql .= ' AND m.mata_kuliah_id = ?';
        $params[] = $id_mk;
    }

    $sql .= ' ORDER BY mk.kode, m.judul';
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $daftar_materi = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (($aksi === 'tambah' || $aksi === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $materi = [
        'mata_kuliah_id' => input($_POST, 'mata_kuliah_id'),
        'judul'          => input($_POST, 'judul'),
        'isi'            => input($_POST, 'isi'),
        'tautan'         => input($_POST, 'tautan'),
    ];
    $errors = validasi_materi($materi, $pilihan_mk);

    if (count($errors) === 0 && $aksi === 'tambah') {
        $stmt = $conn->prepare('INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES (?, ?, ?, ?)');
        $berhasil = $stmt->execute([
            $materi['mata_kuliah_id'],
            $materi['judul'],
            $materi['isi'],
            $materi['tautan'],
        ]);

        if ($berhasil) {
            pindah_halaman('materi.php', 'Materi berhasil ditambahkan.');
        }
        $errors['umum'] = 'Data materi gagal disimpan.';
    }

    if (count($errors) === 0 && $aksi === 'edit') {
        $stmt = $conn->prepare('UPDATE materi SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ? WHERE id = ?');
        $berhasil = $stmt->execute([
            $materi['mata_kuliah_id'],
            $materi['judul'],
            $materi['isi'],
            $materi['tautan'],
            $id,
        ]);

        if ($berhasil) {
            pindah_halaman('materi.php?aksi=detail&id=' . $id, 'Materi berhasil diperbarui.');
        }
        $errors['umum'] = 'Perubahan data materi gagal disimpan.';
    }
}

if ($aksi === 'hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM materi WHERE id = ?');
    $berhasil = $stmt->execute([$id]);

    if ($berhasil) {
        pindah_halaman('materi.php', 'Materi berhasil dihapus.');
    }
    $gagal = true;
}

$judul = 'Materi';
require 'header.php';
?>

<?php if ($aksi === 'daftar'): ?>
    <div class="page-head">
        <div>
            <h1>Materi</h1>
            <p class="muted">Menampilkan <?= count($daftar_materi) ?> data.</p>
        </div>
        <a class="btn" href="materi.php?aksi=tambah">+ Tambah</a>
    </div>

    <form class="toolbar" method="get" action="materi.php">
        <input type="text" name="search" placeholder="Cari judul atau mata kuliah" value="<?= h($search) ?>">
        <select name="mata_kuliah_id">
            <option value="">Semua mata kuliah</option>
            <?php foreach ($pilihan_mk as $id_pilihan => $nama): ?>
                <option value="<?= (int) $id_pilihan ?>" <?= $id_pilihan == $id_mk ? 'selected' : '' ?>><?= h($nama) ?></option>
            <?php endforeach ?>
        </select>
        <button class="btn" type="submit">Cari</button>
        <a class="btn btn-ghost" href="materi.php">Reset</a>
    </form>

    <?php if (count($daftar_materi) === 0): ?>
        <p class="empty">Data tidak ditemukan.</p>
    <?php else: ?>
        <div class="card list">
            <?php foreach ($daftar_materi as $baris): ?>
                <div class="list-item">
                    <div>
                        <p class="kicker"><span class="code"><?= h($baris['kode_mk']) ?></span> | <?= h($baris['nama_mk']) ?></p>
                        <h2><?= h($baris['judul']) ?></h2>
                    </div>
                    <div class="actions">
                        <a class="btn btn-sm" href="materi.php?aksi=detail&id=<?= (int) $baris['id'] ?>">Detail</a>
                        <a class="btn btn-sm btn-ghost" href="materi.php?aksi=edit&id=<?= (int) $baris['id'] ?>">Edit</a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

<?php elseif ($aksi === 'tambah' || $aksi === 'edit'): ?>
    <h1><?= $aksi === 'tambah' ? 'Tambah' : 'Edit' ?> materi</h1>

    <?php if (count($errors) > 0): ?>
        <p class="notice notice-error"><?= h($errors['umum'] ?? 'Data belum tersimpan. Perbaiki kolom yang ditandai.') ?></p>
    <?php endif ?>

    <form class="card form" method="post" action="">
        <div class="fields">
            <div class="field wide">
                <label for="mata_kuliah_id">Mata kuliah</label>
                <select id="mata_kuliah_id" name="mata_kuliah_id">
                    <option value="">Pilih mata kuliah</option>
                    <?php foreach ($pilihan_mk as $id_pilihan => $nama): ?>
                        <option value="<?= (int) $id_pilihan ?>" <?= $id_pilihan == $materi['mata_kuliah_id'] ? 'selected' : '' ?>><?= h($nama) ?></option>
                    <?php endforeach ?>
                </select>
                <?php if (isset($errors['mata_kuliah_id'])): ?>
                    <p class="field-error"><?= h($errors['mata_kuliah_id']) ?></p>
                <?php endif ?>
            </div>

            <div class="field wide">
                <label for="judul">Judul materi</label>
                <input type="text" id="judul" name="judul" maxlength="150" placeholder="Operasi CRUD PHP dan MySQL" value="<?= h($materi['judul']) ?>">
                <?php if (isset($errors['judul'])): ?>
                    <p class="field-error"><?= h($errors['judul']) ?></p>
                <?php endif ?>
            </div>

            <div class="field wide">
                <label for="isi">Isi materi</label>
                <textarea id="isi" name="isi" rows="6"><?= h($materi['isi']) ?></textarea>
                <?php if (isset($errors['isi'])): ?>
                    <p class="field-error"><?= h($errors['isi']) ?></p>
                <?php endif ?>
            </div>

            <div class="field wide">
                <label for="tautan">Tautan referensi</label>
                <input type="text" id="tautan" name="tautan" maxlength="255" placeholder="https://" value="<?= h($materi['tautan']) ?>">
                <?php if (isset($errors['tautan'])): ?>
                    <p class="field-error"><?= h($errors['tautan']) ?></p>
                <?php endif ?>
            </div>
        </div>
        <div class="actions">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-ghost" href="<?= $aksi === 'edit' ? 'materi.php?aksi=detail&id=' . $id : 'materi.php' ?>">Batal</a>
        </div>
    </form>

<?php elseif ($aksi === 'detail'): ?>
    <article class="card detail">
        <p class="kicker"><span class="code"><?= h($materi['kode_mk']) ?></span> | <?= h($materi['nama_mk']) ?></p>
        <h1><?= h($materi['judul']) ?></h1>

        <h2 class="label">Isi materi</h2>
        <p><?= nl2br(h($materi['isi'])) ?></p>

        <h2 class="label">Tautan referensi</h2>
        <p><a class="link" href="<?= h($materi['tautan']) ?>" target="_blank" rel="noopener"><?= h($materi['tautan']) ?></a></p>

        <div class="actions">
            <a class="btn" href="materi.php?aksi=edit&id=<?= $id ?>">Edit</a>
            <a class="btn btn-ghost btn-danger" href="materi.php?aksi=hapus&id=<?= $id ?>">Hapus</a>
            <a class="btn btn-ghost" href="materi.php">Kembali</a>
        </div>
    </article>

<?php elseif ($aksi === 'hapus'): ?>
    <?php if ($gagal): ?>
        <p class="notice notice-error">Data materi gagal dihapus.</p>
    <?php endif ?>

    <form class="card detail" method="post" action="">
        <h1>Hapus materi ini?</h1>
        <p class="muted"><b><?= h($materi['judul']) ?></b> akan dihapus dan tidak bisa dikembalikan.</p>
        <div class="actions">
            <button class="btn" type="submit">Ya, hapus</button>
            <a class="btn btn-ghost" href="materi.php?aksi=detail&id=<?= $id ?>">Batal</a>
        </div>
    </form>
<?php endif ?>

<?php require 'footer.php'; ?>
