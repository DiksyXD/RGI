<?php
require 'koneksi.php';

function hitungUmur($tanggalLahir)
{
    if (empty($tanggalLahir)) {
        return '-';
    }

    try {
        $lahir = new DateTime($tanggalLahir);
        $sekarang = new DateTime();

        return $lahir->diff($sekarang)->y . ' tahun';
    } catch (Exception $e) {
        return '-';
    }
}

$cari = trim($_GET['cari'] ?? '');
$kamar = trim($_GET['kamar'] ?? '');

if ($cari !== '') {
    $stmt = $pdo->prepare('SELECT * FROM santri WHERE nama LIKE :cari ORDER BY id DESC');
    $stmt->execute([':cari' => "%$cari%"]);
} elseif ($kamar !== '') {
    $stmt = $pdo->prepare('SELECT * FROM santri WHERE kamar = :kamar ORDER BY id DESC');
    $stmt->execute([':kamar' => $kamar]);
} else {
    $stmt = $pdo->query('SELECT * FROM santri ORDER BY id DESC');
}
$daftar = $stmt->fetchAll();
include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Santri</h4>
    <a href="tambah.php" class="btn btn-success">+ Tambah Santri</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-sm-8 col-md-6">
        <input type="text" name="cari" class="form-control"
            placeholder="Cari nama santri..." value="<?= e($cari) ?>">
    </div>
    <div class="col-sm-3">
        <select name="kamar" class="form-control">
            <option value="">-- Semua Kamar --</option>
            <option value="A1" <?= ($kamar == 'A1') ? 'selected' : ''; ?>>A1</option>
            <option value="A2" <?= ($kamar == 'A2') ? 'selected' : ''; ?>>A2</option>
            <option value="A3" <?= ($kamar == 'A3') ? 'selected' : ''; ?>>A3</option>
            <option value="B1" <?= ($kamar == 'B1') ? 'selected' : ''; ?>>B1</option>
            <option value="B2" <?= ($kamar == 'B2') ? 'selected' : ''; ?>>B2</option>
            <option value="B3" <?= ($kamar == 'B3') ? 'selected' : ''; ?>>B3</option>
            <option value="3C" <?= ($kamar == '3C') ? 'selected' : ''; ?>>3C</option>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-success">Cari</button>
        <a href="index.php" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>L/P</th>
                    <th>Tanggal Lahir</th>
                    <th>Asal Daerah</th>
                    <th>Kamar</th>
                    <th>No. HP Wali</th>
                    <th>Angkatan</th>
                    <th>Umur</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($daftar) === 0): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">Belum ada data santri.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($daftar as $no => $s): ?>
                    <tr>
                        <td><?= $no + 1 ?></td>
                        <td><?= e($s['nama']) ?></td>
                        <td>
                            <?php
                            if ($s['jenis_kelamin'] === 'L') {
                                echo 'Laki-laki';
                            } elseif ($s['jenis_kelamin'] === 'P') {
                                echo 'Perempuan';
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?= e($s['tanggal_lahir']) ?></td>
                        <td><?= e($s['asal_daerah']) ?></td>
                        <td><?= e($s['kamar']) ?></td>
                        <td><?= e($s['no_hp_wali']) ?></td>
                        <td><?= e($s['angkatan']) ?></td>
                        <td><?= e(hitungUmur($s['tanggal_lahir'])) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" class="btn btn-sm btn-danger"
                                data-bs-toggle="modal" data-bs-target="#modalHapus"
                                data-id="<?= $s['id'] ?>" data-nama="<?= e($s['nama']) ?>">Hapus</button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="hapus.php" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus data <strong id="namaHapus"></strong>?
                <input type="hidden" name="id" id="idHapus">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modalHapus').addEventListener('show.bs.modal', function(event) {
        var tombol = event.relatedTarget;
        document.getElementById('idHapus').value = tombol.getAttribute('data-id');
        document.getElementById('namaHapus').textContent = tombol.getAttribute('data-nama');
    });
</script>

<?php include 'footer.php'; ?>