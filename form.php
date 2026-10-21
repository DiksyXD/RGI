<form method="post" class="card card-body shadow-sm">
  <div class="mb-3">
    <label class="form-label" for="nama">Nama Lengkap</label>
    <input type="text" id="nama" name="nama" class="form-control" maxlength="100" value="<?= e($santri['nama']) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
      <option value="">-- Pilih --</option>
      <option value="L" <?= $santri['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
      <option value="P" <?= $santri['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
    <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" value="<?= e($santri['tanggal_lahir']) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label" for="asal_daerah">Asal Daerah</label>
    <input type="text" id="asal_daerah" name="asal_daerah" class="form-control" value="<?= e($santri['asal_daerah']) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label" for="angkatan">Angkatan</label>
    <input type="text" id="angkatan" name="angkatan" class="form-control" value="<?= e($santri['angkatan']) ?>">
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label" for="kamar">Kamar</label>
      <input type="text" id="kamar" name="kamar" class="form-control" value="<?= e($santri['kamar']) ?>">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label" for="no_hp_wali">No. HP Wali</label>
      <input type="text" id="no_hp_wali" name="no_hp_wali" class="form-control" value="<?= e($santri['no_hp_wali']) ?>">
    </div>
  </div>

  <div>
    <button type="submit" class="btn btn-success"><?= e($labelTombol ?? 'Simpan') ?></button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
  </div>
</form>
