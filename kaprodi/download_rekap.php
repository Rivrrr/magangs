<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$date = date('Y-m-d');
$filename = "rekap_nilai_magang_{$date}.csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

fputs($output, "\xEF\xBB\xBF");

$delimiter = ';';

fputcsv($output, [
    'NIM',
    'Nama Lengkap',
    'Dosen Pembimbing',
    'Tempat Magang',
    'Nilai Akhir'
], $delimiter);

$stmt = $conn->query("
    SELECT
        m.NIM,
        m.nama,
        d.nama AS dosen,
        pm.tempat_magang,
        na.nilai
    FROM nilai_akhir na
    JOIN mahasiswa m ON na.id_mahasiswa = m.id_mahasiswa
    LEFT JOIN dosen d ON na.id_dosen = d.id_dosen
    LEFT JOIN pendaftaran_magang pm ON m.id_mahasiswa = pm.id_mahasiswa
    ORDER BY m.nama ASC
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    
    $formatted_row = [
        $row['NIM'],
        $row['nama'],
        $row['dosen'] ?? 'Belum ditentukan',
        $row['tempat_magang'] ?? '-',
        $row['nilai'] ?? '0'
    ];

    fputcsv($output, $formatted_row, $delimiter);
}

fclose($output);
exit;
?>