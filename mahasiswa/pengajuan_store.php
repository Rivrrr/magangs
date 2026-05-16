<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

$db = new Database();
$conn = $db->getConnection();

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$data = $_POST;

if (!isset($_FILES['file_proposal']) || $_FILES['file_proposal']['error'] !== 0) {
    die("File upload gagal");
}

$file = $_FILES['file_proposal'];
$upload_dir = "../uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir);
}

$filename = time() . "_" . basename($file['name']);

if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
    die("Gagal move file");
}

if (!empty($_POST['id_mitra'])) {

    $stmtMitra = $conn->prepare("SELECT * FROM mitra WHERE id_mitra = ?");
    $stmtMitra->execute([$_POST['id_mitra']]);
    $mitra = $stmtMitra->fetch(PDO::FETCH_ASSOC);

    if (!$mitra) {
        die("Mitra tidak ditemukan");
    }

    $nama_perusahaan = $mitra['nama'];
    $alamat = $mitra['alamat'];
    $provinsi = $mitra['provinsi'];
    $kota = $mitra['kota'];
    $kecamatan = $mitra['kecamatan'];
    $kode_pos = $mitra['kode_pos'];

} else {

    $nama_perusahaan = $_POST['nama_perusahaan'] ?? null;
    $alamat = $_POST['alamat'] ?? null;
    $provinsi = $_POST['provinsi'] ?? null;
    $kota = $_POST['kota'] ?? null;
    $kecamatan = $_POST['kecamatan'] ?? null;
    $kode_pos = $_POST['kode_pos'] ?? null;
}

$stmt = $conn->prepare("
INSERT INTO pengajuan 
(id_mahasiswa, jenis_perusahaan, nama_perusahaan, alamat, provinsi, kota, kecamatan, kode_pos, judul_proposal, bidang, tanggal_mulai, tanggal_selesai, catatan, file_proposal)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

try {
    $stmt->execute([
        $user['id'],
        $data['jenis_perusahaan'] ?? null,
        $nama_perusahaan,
        $alamat,
        $provinsi,
        $kota,
        $kecamatan,
        $kode_pos,
        $data['judul_proposal'] ?? null,
        $data['bidang'] ?? null,
        $data['tanggal_mulai'] ?? null,
        $data['tanggal_selesai'] ?? null,
        $data['catatan'] ?? null,
        $filename
    ]);

    header("Location: pengajuan.php?success=1");
    exit;

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}

exit;