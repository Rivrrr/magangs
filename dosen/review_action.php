<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_POST['id_laporan_harian'];
$catatan = $_POST['catatan_dosen'];
$action = $_POST['action'];

$db = new Database();
$conn = $db->getConnection();

if ($action == 'approve') {
    $stmt = $conn->prepare("
        UPDATE laporan_harian
        SET status = 'Disetujui',
            catatan_dosen = ?
        WHERE id_laporan_harian = ?
    ");
    
    $stmt->execute([$catatan, $id]);

    $_SESSION['success'] = "Logbook berhasil disetujui.";
} else {
    $stmt = $conn->prepare("
        UPDATE laporan_harian
        SET status = 'Ditolak',
            catatan_dosen = ?
        WHERE id_laporan_harian = ?
    ");
    
    $stmt->execute([$catatan, $id]);

    $_SESSION['error'] = "Revisi logbook berhasil dikirim.";
}

header("Location: review_logbook.php");
exit;