<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$id_laporan_akhir = $_POST['id_laporan_akhir'];
$id_mahasiswa = $_POST['id_mahasiswa'];
$id_dosen = $_SESSION['user']['id'];
$nilai = $_POST['nilai'];
$catatan = $_POST['catatan_dosen'];
$action = $_POST['action'];

$db = new Database();
$conn = $db->getConnection();

if ($action == 'approve') {
    $stmt = $conn->prepare("
        UPDATE laporan_akhir
        SET status_review = 'Disetujui',
            catatan_dosen = ?
        WHERE id_laporan_akhir = ?
    ");
    
    $stmt->execute([$catatan, $id_laporan_akhir]);

    $stmtCheck = $conn->prepare("SELECT * FROM nilai_akhir WHERE id_mahasiswa = ?");
    $stmtCheck->execute([$id_mahasiswa]);
    $existingNilai = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existingNilai) {
        $stmtNilai = $conn->prepare("
            UPDATE nilai_akhir
            SET nilai = ?,
                tanggal_penilaian = NOW()
            WHERE id_mahasiswa = ?
        ");
        $stmtNilai->execute([$nilai, $id_mahasiswa]);
    } else {
        $stmtNilai = $conn->prepare("
            INSERT INTO nilai_akhir (id_mahasiswa, id_dosen, nilai, tanggal_penilaian)
            VALUES (?, ?, ?, NOW())
        ");
        $stmtNilai->execute([$id_mahasiswa, $id_dosen, $nilai]);
    }

    $_SESSION['success'] = "Laporan berhasil disetujui.";
} else {
    $stmt = $conn->prepare("
        UPDATE laporan_akhir
        SET status_review = 'Ditolak',
            catatan_dosen = ?
        WHERE id_laporan_akhir = ?
    ");
    
    $stmt->execute([$catatan, $id_laporan_akhir]);

    $_SESSION['error'] = "Revisi laporan berhasil dikirim.";
}

header("Location: review_laporan.php");
exit;