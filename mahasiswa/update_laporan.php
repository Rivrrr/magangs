<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db   = new Database();
$conn = $db->getConnection();

$id_laporan     = $_POST['id_laporan_harian'];
$tanggal_submit = $_POST['tanggal_submit'];
$jam_masuk      = $_POST['jam_masuk'];
$jam_keluar     = $_POST['jam_keluar'];
$kegiatan       = $_POST['kegiatan'];

$stmtOld = $conn->prepare("SELECT * FROM laporan_harian WHERE id_laporan_harian = ?");
$stmtOld->execute([$id_laporan]);
$oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

$file_pendukung = $oldData['file_pendukung'];

if (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] == 0) {
    $upload_dir = "../uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir);
    }

    $filename = time() . "_" . basename($_FILES['file_pendukung']['name']);
    move_uploaded_file($_FILES['file_pendukung']['tmp_name'], $upload_dir . $filename);

    $file_pendukung = $filename;
}

$stmt = $conn->prepare("
    UPDATE laporan_harian
    SET tanggal_submit  = ?,
        jam_masuk       = ?,
        jam_keluar      = ?,
        kegiatan        = ?,
        file_pendukung  = ?
    WHERE id_laporan_harian = ?
");

$stmt->execute([
    $tanggal_submit,
    $jam_masuk,
    $jam_keluar,
    $kegiatan,
    $file_pendukung,
    $id_laporan
]);

header("Location: laporan_harian.php");
exit;