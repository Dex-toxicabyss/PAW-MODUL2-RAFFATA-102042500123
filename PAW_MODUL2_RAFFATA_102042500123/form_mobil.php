<div class="fields">
    <div class="field">
        <label for="kode_mobil">Kode mobil</label>
        <input class="kapital" type="text" id="kode_mobil" name="kode_mobil" maxlength="20" placeholder="MBL-001" value="<?= h($mobil['kode_mobil']) ?>">
        <?php if (isset($errors['kode_mobil'])): ?>
            <p class="field-error"><?= h($errors['kode_mobil']) ?></p>
        <?php endif ?>
    </div>

    <div class="field">
        <label for="nama_mobil">Nama mobil</label>
        <input type="text" id="nama_mobil" name="nama_mobil" maxlength="100" placeholder="Avanza Veloz" value="<?= h($mobil['nama_mobil']) ?>">
        <?php if (isset($errors['nama_mobil'])): ?>
            <p class="field-error"><?= h($errors['nama_mobil']) ?></p>
        <?php endif ?>
    </div>

    <div class="field">
        <label for="brand_mobil">Brand</label>
        <input type="text" id="brand_mobil" name="brand_mobil" maxlength="50" placeholder="Toyota" value="<?= h($mobil['brand_mobil']) ?>">
        <?php if (isset($errors['brand_mobil'])): ?>
            <p class="field-error"><?= h($errors['brand_mobil']) ?></p>
        <?php endif ?>
    </div>

    <div class="field">
        <label for="warna_mobil">Warna</label>
        <input type="text" id="warna_mobil" name="warna_mobil" maxlength="30" placeholder="Putih" value="<?= h($mobil['warna_mobil']) ?>">
        <?php if (isset($errors['warna_mobil'])): ?>
            <p class="field-error"><?= h($errors['warna_mobil']) ?></p>
        <?php endif ?>
    </div>

    <div class="field">
        <label for="tipe_mobil">Tipe</label>
        <select id="tipe_mobil" name="tipe_mobil">
            <option value="">Pilih tipe</option>
            <?php foreach ($pilihan_tipe as $tipe): ?>
                <option value="<?= h($tipe) ?>" <?= $tipe === $mobil['tipe_mobil'] ? 'selected' : '' ?>><?= h($tipe) ?></option>
            <?php endforeach ?>
        </select>
        <?php if (isset($errors['tipe_mobil'])): ?>
            <p class="field-error"><?= h($errors['tipe_mobil']) ?></p>
        <?php endif ?>
    </div>

    <div class="field">
        <label for="harga_mobil">Harga (Rupiah)</label>
        <input type="number" id="harga_mobil" name="harga_mobil" min="1" step="0.01" placeholder="310000000" value="<?= h($mobil['harga_mobil']) ?>">
        <?php if (isset($errors['harga_mobil'])): ?>
            <p class="field-error"><?= h($errors['harga_mobil']) ?></p>
        <?php endif ?>
    </div>
</div>
