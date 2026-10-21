<?php
require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');
$kamar = trim($_GET['kamar'] ?? '');
$sql = 'SELECT id, nama, jenis_kelamin, tanggal_lahir, asal_daerah, kamar, no_hp_wali, angkatan FROM santri';
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
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="daftar-santri-' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
$output = fopen('php://output', 'w');
fwrite($output, "\xEF\xBB\xBF");
fputcsv($output, ['ID', 'Nama', 'Jenis Kelamin', 'Tanggal Lahir', 'Asal Daerah', 'Kamar', 'No. HP Wali', 'Angkatan']);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['jenis_kelamin'] = $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : ($row['jenis_kelamin'] === 'P' ? 'Perempuan' : '-');
    // Cegah nilai teks dari database diperlakukan sebagai formula spreadsheet.
    foreach ($row as &$value) {
        if (is_string($value) && preg_match('/^[=+@\-]/', $value)) {
            $value = "'" . $value;
        }
    }
    unset($value);
    fputcsv($output, $row);
}
fclose($output);
exit;
