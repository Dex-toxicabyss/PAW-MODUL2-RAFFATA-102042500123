<?php
require 'functions.php';
require 'connect.php';

$aksi = input($_GET, 'aksi');
if (!in_array($aksi, ['tambah', 'detail', 'edit', 'hapus'], true)) {
    $aksi = 'daftar';
}

$pilihan_mk = pilihan_mata_kuliah($conn);
$tugas = ['mata_kuliah_id' => '', 'judul' => '', 'instruksi' => '', 'tenggat' => '', 'status' => 'Draf'];
$errors = [];
$gagal = false;
$id = 0;

if ($aksi === 'detail' || $aksi === 'edit' || $aksi === 'hapus') {
    $id = (int) input($_GET, 'id');
    $tugas = cari_tugas($conn, $id);
    if (!$tugas) {
        pindah_halaman('tugas.php', 'Tugas tidak ditemukan.');
    }
}

if ($aksi === 'daftar') {
    $search = input($_GET, 'search');
    $id_mk  = (int) input($_GET, 'mata_kuliah_id');
    $status = input($_GET, 'status');

    $sql = 'SELECT t.*, mk.kode AS kode_mk, mk.nama AS nama_mk
            FROM tugas t
            JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id
            WHERE 1=1';
    $params = [];

    if ($search !== '') {
        $sql .= ' AND (t.judul LIKE ? OR mk.nama LIKE ?)';
        $kata = '%' . $search . '%';
        $params[] = $kata;
        $params[] = $kata;
    }

    if ($id_mk !== 0) {
        $sql .= ' AND t.mata_kuliah_id = ?';
        $params[] = $id_mk;
    }

    if ($status !== '') {
        $sql .= ' AND t.status = ?';
        $params[] = $status;
    }

    $sql .= ' ORDER BY t.tenggat, t.judul';
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $daftar_tugas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (($aksi === 'tambah' || $aksi === 'edit') && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tugas = [
        'mata_kuliah_id' => input($_POST, 'mata_kuliah_id'),
        'judul'          => input($_POST, 'judul'),
        'instruksi'      => input($_POST, 'instruksi'),
        'tenggat'        => input($_POST, 'tenggat'),
        'status'         => input($_POST, 'status'),
    ];
    $errors = validasi_tugas($tugas, $pilihan_mk);

    if (count($errors) === 0 && $aksi === 'tambah') {
        $stmt = $conn->prepare('INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)');
        $berhasil = $stmt->execute([
            $tugas['mata_kuliah_id'],
            $tugas['judul'],
            $tugas['instruksi'],
            $tugas['tenggat'],
            $tugas['status'],
        ]);

        if ($berhasil) {
            pindah_halaman('tugas.php', 'Tugas berhasil ditambahkan.');
        }
        $errors['umum'] = 'Data tugas gagal disimpan.';
    }

    if (count($errors) === 0 && $aksi === 'edit') {
        $stmt = $conn->prepare('UPDATE tugas SET mata_kuliah_id = ?, judul = ?, instruksi = ?, tenggat = ?, status = ? WHERE id = ?');
        $berhasil = $stmt->execute([
            $tugas['mata_kuliah_id'],
            $tugas['judul'],
            $tugas['instruksi'],
            $tugas['tenggat'],
            $tugas['status'],
            $id,
        ]);

        if ($berhasil) {
            pindah_halaman('tugas.php?aksi=detail&id=' . $id, 'Tugas berhasil diperbarui.');
        }
        $errors['umum'] = 'Perubahan data tugas gagal disimpan.';
    }
}

if ($aksi === 'hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare('DELETE FROM tugas WHERE id = ?');
    $berhasil = $stmt->execute([$id]);

    if ($berhasil) {
        pindah_halaman('tugas.php', 'Tugas berhasil dihapus.');
    }
    $gagal = true;
}

$judul = 'Tugas';
require 'header.php';
?>

<?php if ($aksi === 'daftar'): ?>
    <div class="page-head">
        <div>
            <h1>Tugas</h1>
            <p class="muted">Menampilkan <?= count($daftar_tugas) ?> data.</p>
        </div>
        <a class="btn" href="tugas.php?aksi=tambah">+ Tambah</a>
    </div>

    <form class="toolbar" method="get" action="tugas.php">
        <input type="text" name="search" placeholder="Cari judul atau mata kuliah" value="<?= h($search) ?>">
        <select name="mata_kuliah_id">
            <option value="">Semua mata kuliah</option>
            <?php foreach ($pilihan_mk as $id_pilihan => $nama): ?>
                <option value="<?= (int) $id_pilihan ?>" <?= $id_pilihan == $id_mk ? 'selected' : '' ?>><?= h($nama) ?></option>
            <?php endforeach ?>
        </select>
        <select name="status">
            <option value="">Semua status</option>
            <?php foreach ($pilihan_status as $pilihan): ?>
                <option value="<?= h($pilihan) ?>" <?= $pilihan === $status ? 'selected' : '' ?>><?= h($pilihan) ?></option>
            <?php endforeach ?>
        </select>
        <button class="btn" type="submit">Cari</button>
        <a class="btn btn-ghost" href="tugas.php">Reset</a>
    </form>

    <?php if (count($daftar_tugas) === 0): ?>
        <p class="empty">Data tidak ditemukan.</p>
    <?php else: ?>
        <div class="card list">
            <?php foreach ($daftar_tugas as $baris): ?>
                <div class="list-item">
                    <div>
                        <p class="kicker"><span class="code"><?= h($baris['kode_mk']) ?></span> | <?= h($baris['nama_mk']) ?></p>
                        <h2><?= h($baris['judul']) ?></h2>
                        <p class="muted">Tenggat <?= date('d-m-Y', strtotime($baris['tenggat'])) ?> <span class="badge"><?= h($baris['status']) ?></span></p>
                    </div>
                    <div class="actions">
                        <a class="btn btn-sm" href="tugas.php?aksi=detail&id=<?= (int) $baris['id'] ?>">Detail</a>
                        <a class="btn btn-sm btn-ghost" href="tugas.php?aksi=edit&id=<?= (int) $baris['id'] ?>">Edit</a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

<?php elseif ($aksi === 'tambah' || $aksi === 'edit'): ?>
    <h1><?= $aksi === 'tambah' ? 'Tambah' : 'Edit' ?> tugas</h1>

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
                        <option value="<?= (int) $id_pilihan ?>" <?= $id_pilihan == $tugas['mata_kuliah_id'] ? 'selected' : '' ?>><?= h($nama) ?></option>
                    <?php endforeach ?>
                </select>
                <?php if (isset($errors['mata_kuliah_id'])): ?>
                    <p class="field-error"><?= h($errors['mata_kuliah_id']) ?></p>
                <?php endif ?>
            </div>

            <div class="field wide">
                <label for="judul">Judul tugas</label>
                <input type="text" id="judul" name="judul" maxlength="150" placeholder="Membangun LMS sederhana" value="<?= h($tugas['judul']) ?>">
                <?php if (isset($errors['judul'])): ?>
                    <p class="field-error"><?= h($errors['judul']) ?></p>
                <?php endif ?>
            </div>

            <div class="field wide">
                <label for="instruksi">Instruksi tugas</label>
                <textarea id="instruksi" name="instruksi" rows="6"><?= h($tugas['instruksi']) ?></textarea>
                <?php if (isset($errors['instruksi'])): ?>
                    <p class="field-error"><?= h($errors['instruksi']) ?></p>
                <?php endif ?>
            </div>

            <div class="field">
                <label for="tenggat">Tenggat</label>
                <input type="date" id="tenggat" name="tenggat" value="<?= h($tugas['tenggat']) ?>">
                <?php if (isset($errors['tenggat'])): ?>
                    <p class="field-error"><?= h($errors['tenggat']) ?></p>
                <?php endif ?>
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php foreach ($pilihan_status as $pilihan): ?>
                        <option value="<?= h($pilihan) ?>" <?= $pilihan === $tugas['status'] ? 'selected' : '' ?>><?= h($pilihan) ?></option>
                    <?php endforeach ?>
                </select>
                <?php if (isset($errors['status'])): ?>
                    <p class="field-error"><?= h($errors['status']) ?></p>
                <?php endif ?>
            </div>
        </div>
        <div class="actions">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-ghost" href="<?= $aksi === 'edit' ? 'tugas.php?aksi=detail&id=' . $id : 'tugas.php' ?>">Batal</a>
        </div>
    </form>

<?php elseif ($aksi === 'detail'): ?>
    <article class="card detail">
        <p class="kicker"><span class="code"><?= h($tugas['kode_mk']) ?></span> | <?= h($tugas['nama_mk']) ?></p>
        <h1><?= h($tugas['judul']) ?></h1>

        <dl class="specs">
            <dt>Tenggat</dt>
            <dd><?= date('d-m-Y', strtotime($tugas['tenggat'])) ?></dd>
            <dt>Status</dt>
            <dd><span class="badge"><?= h($tugas['status']) ?></span></dd>
        </dl>

        <h2 class="label">Instruksi</h2>
        <p><?= nl2br(h($tugas['instruksi'])) ?></p>

        <div class="actions">
            <a class="btn" href="tugas.php?aksi=edit&id=<?= $id ?>">Edit</a>
            <a class="btn btn-ghost btn-danger" href="tugas.php?aksi=hapus&id=<?= $id ?>">Hapus</a>
            <a class="btn btn-ghost" href="tugas.php">Kembali</a>
        </div>
    </article>

<?php elseif ($aksi === 'hapus'): ?>
    <?php if ($gagal): ?>
        <p class="notice notice-error">Data tugas gagal dihapus.</p>
    <?php endif ?>

    <form class="card detail" method="post" action="">
        <h1>Hapus tugas ini?</h1>
        <p class="muted"><b><?= h($tugas['judul']) ?></b> akan dihapus dan tidak bisa dikembalikan.</p>
        <div class="actions">
            <button class="btn" type="submit">Ya, hapus</button>
            <a class="btn btn-ghost" href="tugas.php?aksi=detail&id=<?= $id ?>">Batal</a>
        </div>
    </form>
<?php endif ?>

<?php require 'footer.php'; ?>
