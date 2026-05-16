<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$id = $_POST['id_mahasiswa'];
$nama = $_POST['nama'];
$tempat = $_POST['tempat_magang'];
$kota = $_POST['kota'];
$status = $_POST['status_pendaftaran'];
$id_dosen = !empty($_POST['id_dosen']) ? $_POST['id_dosen'] : null;

$stmtMahasiswa = $conn->prepare("
    UPDATE mahasiswa
    SET nama = ?
    WHERE id_mahasiswa = ?
");
$stmtMahasiswa->execute([$nama, $id]);

$stmtCheck = $conn->prepare("SELECT * FROM pendaftaran_magang WHERE id_mahasiswa = ?");
$stmtCheck->execute([$id]);
$existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    $stmtMagang = $conn->prepare("
        UPDATE pendaftaran_magang
        SET tempat_magang = ?,
            status_pendaftaran = ?,
            id_dosen = ?
        WHERE id_mahasiswa = ?
    ");
    $stmtMagang->execute([$tempat, $status, $id_dosen, $id]);
} else {
    $stmtInsert = $conn->prepare("
        INSERT INTO pendaftaran_magang (id_mahasiswa, id_dosen, tempat_magang, status_pendaftaran)
        VALUES (?, ?, ?, ?)
    ");
    $stmtInsert->execute([$id, $id_dosen, $tempat, $status]);
}

$stmtPengajuan = $conn->prepare("
    UPDATE pengajuan
    SET kota = ?
    WHERE id_mahasiswa = ?
");
$stmtPengajuan->execute([$kota, $id]);

header("Location: mahasiswa.php");
exit;