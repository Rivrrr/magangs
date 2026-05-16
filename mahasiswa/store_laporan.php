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
$id_pendaftaran = $_POST['id_pendaftaran'];
$tanggal_submit = $_POST['tanggal_submit'];
$jam_masuk = $_POST['jam_masuk'];
$jam_keluar = $_POST['jam_keluar'];
$kegiatan = $_POST['kegiatan'];

$stmtPendaftaran = $conn->prepare("
    SELECT tanggal_mulai
    FROM pendaftaran_magang
    WHERE id_pendaftaran = ?
");
$stmtPendaftaran->execute([$id_pendaftaran]);
$pendaftaran = $stmtPendaftaran->fetch(PDO::FETCH_ASSOC);

$tanggal_mulai = strtotime($pendaftaran['tanggal_mulai']);
$tanggal_logbook = strtotime($tanggal_submit);
$selisih_hari = floor(($tanggal_logbook - $tanggal_mulai) / (60 * 60 * 24));
$minggu_ke = floor($selisih_hari / 7) + 1;

$file_pendukung = null;

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
    INSERT INTO laporan_harian (
        id_mahasiswa,
        id_pendaftaran,
        tanggal_submit,
        minggu_ke,
        kegiatan,
        jam_masuk,
        jam_keluar,
        file_pendukung,
        status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu')
");

$stmt->execute([
    $id_mahasiswa,
    $id_pendaftaran,
    $tanggal_submit,
    $minggu_ke,
    $kegiatan,
    $jam_masuk,
    $jam_keluar,
    $file_pendukung
]);

header("Location: laporan_harian.php");
exit;