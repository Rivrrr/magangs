<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$id_mahasiswa = $_POST['id_mahasiswa'];
$judul_laporan = $_POST['judul_laporan'];

$file_laporan = null;

if (
    isset($_FILES['file_laporan']) &&
    $_FILES['file_laporan']['error'] == 0
) {

    $upload_dir = "../uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir);
    }

    $filename =
        time() . "_" .
        basename($_FILES['file_laporan']['name']);

    move_uploaded_file(
        $_FILES['file_laporan']['tmp_name'],
        $upload_dir . $filename
    );

    $file_laporan = $filename;
}

$stmt = $conn->prepare("
    INSERT INTO laporan_akhir
    (
        id_mahasiswa,
        judul_laporan,
        file_laporan,
        status_review,
        tanggal_upload
    )
    VALUES
    (
        ?, ?, ?, 'Menunggu', NOW()
    )
");

$stmt->execute([
    $id_mahasiswa,
    $judul_laporan,
    $file_laporan
]);

header("Location: laporan_akhir.php");
exit;