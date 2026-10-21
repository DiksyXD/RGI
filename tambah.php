<?php
require 'koneksi.php';

$errors = [];
$santri = [
  'nama'          => '',
  'jenis_kelamin' => '',
  'tanggal_lahir' => '',
  'asal_daerah'   => '',
  'kamar'         => '',
  'no_hp_wali'    => '',
  'angkatan'      => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($santri as $kolom => $nilai) {
    $santri[$kolom] = trim($_POST[$kolom] ?? '');
  }

  if ($santri['nama'] === '') {
    $errors[] = 'Nama wajib diisi.';
  }
  if (!in_array($santri['jenis_kelamin'], ['L', 'P'])) {
    $errors[] = 'Jenis kelamin wajib dipilih.';
  }

  if (count($errors) === 0) {
    $sql = 'INSERT INTO santri
                  (nama, jenis_kelamin, tanggal_lahir, asal_daerah, angkatan, kamar, no_hp_wali)
                VALUES
                  (:nama, :jk, :tgl, :asal, :angkatan, :kamar, :hp)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':nama'  => $santri['nama'],
      ':jk'    => $santri['jenis_kelamin'],
      ':tgl'   => $santri['tanggal_lahir'] ?: null,
      ':asal'  => $santri['asal_daerah'],
      ':angkatan' => $santri['angkatan'],
      ':kamar' => $santri['kamar'],
      ':hp'    => $santri['no_hp_wali'],
    ]);

    $_SESSION['pesan'] = 'santri ' . $santri['nama'] . ' berhasil ditambahkan.';
    header('Location: index.php');
    exit;
  }
}

include 'header.php';
?>

<h4 class="mb-3">Tambah Santri</h4>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $pesan): ?>
        <li><?= e($pesan) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php $labelTombol = 'Simpan'; include 'form.php'; ?>

<?php include 'footer.php'; ?>