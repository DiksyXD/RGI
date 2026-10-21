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
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 10;
$offset = ($halaman - 1) * $perHalaman;

$where = [];
$params = [];
if ($cari !== '') {
    $where[] = 'nama LIKE :cari';
    $params[':cari'] = "%$cari%";
}
if ($kamar !== '') {
    $where[] = 'kamar = :kamar';
    $params[':kamar'] = $kamar;
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$stmtJumlah = $pdo->prepare('SELECT COUNT(*) FROM santri' . $whereSql);
$stmtJumlah->execute($params);
$totalSantri = (int) $stmtJumlah->fetchColumn();
$totalHalaman = max(1, (int) ceil($totalSantri / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

$sql = 'SELECT * FROM santri' . $whereSql . ' ORDER BY id DESC LIMIT :limit OFFSET :offset';
$stmt = $pdo->prepare($sql);
foreach ($params as $nama => $nilai) {
    $stmt->bindValue($nama, $nilai, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftar = $stmt->fetchAll();
include 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Santri</h4>
    <div class="d-flex gap-2"><a href="ekspor.php?<?= e(http_build_query(['cari' => $cari, 'kamar' => $kamar])) ?>" class="btn btn-outline-success">Unduh CSV</a><a href="tambah.php" class="btn btn-success">+ Tambah Santri</a></div>
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
                        <td colspan="10" class="text-center text-muted">Belum ada data santri.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($daftar as $no => $s): ?>
                    <tr>
                        <td><?= $offset + $no + 1 ?></td>
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

<?php if ($totalSantri > 0): ?>
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
    <small class="text-muted">Menampilkan <?= $offset + 1 ?>–<?= min($offset + count($daftar), $totalSantri) ?> dari <?= $totalSantri ?> santri</small>
    <nav aria-label="Navigasi halaman daftar santri">
      <ul class="pagination pagination-sm mb-0">
        <li class="page-item <?= $halaman <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= e(http_build_query(['cari' => $cari, 'kamar' => $kamar, 'halaman' => max(1, $halaman - 1)])) ?>" aria-label="Sebelumnya">Sebelumnya</a>
        </li>
        <?php for ($i = 1; $i <= $totalHalaman; $i++): ?>
          <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
            <a class="page-link" href="?<?= e(http_build_query(['cari' => $cari, 'kamar' => $kamar, 'halaman' => $i])) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $halaman >= $totalHalaman ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= e(http_build_query(['cari' => $cari, 'kamar' => $kamar, 'halaman' => min($totalHalaman, $halaman + 1)])) ?>" aria-label="Berikutnya">Berikutnya</a>
        </li>
      </ul>
    </nav>
  </div>
<?php endif; ?>

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