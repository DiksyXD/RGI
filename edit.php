<?php
require 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM santri WHERE id = ?');
$stmt->execute([$id]);
$santri = $stmt->fetch();

if (!$santri) {
  $_SESSION['pesan'] = 'Data santri tidak ditemukan.';
  $_SESSION['tipe']  = 'danger';
  header('Location: index.php');
  exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $kolomForm = ['nama', 'jenis_kelamin', 'tanggal_lahir', 'asal_daerah', 'kamar', 'no_hp_wali'];
  foreach ($kolomForm as $kolom) {
    $santri[$kolom] = trim($_POST[$kolom] ?? '');
  }

  if ($santri['nama'] === '') {
    $errors[] = 'Nama wajib diisi.';
  }
  if (!in_array($santri['jenis_kelamin'], ['L', 'P'])) {
    $errors[] = 'Jenis kelamin wajib dipilih.';
  }

  if (mb_strlen($santri['nama']) > 100) {
    $errors[] = 'Nama maksimal 100 karakter.';
  }

  if ($santri['no_hp_wali'] !== '' && !preg_match('/^[0-9+]{8,15}$/', $santri['no_hp_wali'])) {
    $errors[] = 'No. HP wali hanya boleh angka (8-15 digit).';
  }

  if ($santri['tanggal_lahir'] !== '') {
    $tgl = DateTime::createFromFormat('Y-m-d', $santri['tanggal_lahir']);
    if (!$tgl || $tgl->format('Y-m-d') !== $santri['tanggal_lahir']) {
      $errors[] = 'Format tanggal lahir tidak valid.';
    }
  }

  if (count($errors) === 0) {
    $sql = 'UPDATE santri SET
                  nama          = :nama,
                  jenis_kelamin = :jk,
                  tanggal_lahir = :tgl,
                  asal_daerah   = :asal,
                  kamar         = :kamar,
                  no_hp_wali    = :hp,
                  angkatan      = :angkatan
                WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':nama'  => $santri['nama'],
      ':angkatan' => $santri['angkatan'],
      ':jk'    => $santri['jenis_kelamin'],
      ':tgl'   => $santri['tanggal_lahir'] ?: null,
      ':asal'  => $santri['asal_daerah'],
      ':kamar' => $santri['kamar'],
      ':hp'    => $santri['no_hp_wali'],
      ':id'    => $id,
    ]);

    $_SESSION['pesan'] = 'Data santri berhasil diperbarui.';
    header('Location: index.php');
    exit;
  }
}

include 'header.php';
?>

<h4 class="mb-3">Edit Santri</h4>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $pesan): ?>
        <li><?= e($pesan) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" class="card card-body shadow-sm">
  <div class="mb-3">
    <label class="form-label">Nama Lengkap</label>
    <input type="text" name="nama" class="form-control" value="<?= e($santri['nama']) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Jenis Kelamin</label>
    <select name="jenis_kelamin" class="form-select" required>
      <option value="">-- Pilih --</option>
      <option value="L" <?= $santri['jenis_kelamin'] === 'L' ? 'selected' : '' ?>>Laki-laki</option>
      <option value="P" <?= $santri['jenis_kelamin'] === 'P' ? 'selected' : '' ?>>Perempuan</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" class="form-control" value="<?= e($santri['tanggal_lahir']) ?>">
  </div>

  <div class="mb-3">
    <label class="form-label">Asal Daerah</label>
    <input type="text" name="asal_daerah" class="form-control" value="<?= e($santri['asal_daerah']) ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Angkatan</label>
    <input type="text" name="angkatan" class="form-control" value="<?= e($santri['angkatan']) ?>">
  </div>

  <div class="row">
    <div class="col-md-6 mb-3">
      <label class="form-label">Kamar</label>
      <input type="text" name="kamar" class="form-control" value="<?= e($santri['kamar']) ?>">
    </div>
    <div class="col-md-6 mb-3">
      <label class="form-label">No. HP Wali</label>
      <input type="text" name="no_hp_wali" class="form-control" value="<?= e($santri['no_hp_wali']) ?>">
    </div>

  </div>

  <div>
    <button type="submit" class="btn btn-success">Simpan Perubahan</button>
    <a href="index.php" class="btn btn-secondary">Batal</a>
  </div>
</form>

<?php include 'footer.php'; ?>