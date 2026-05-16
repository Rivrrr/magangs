<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'];
$action = $_GET['action'];
$id_dosen = $_GET['id_dosen'] ?? null;
$status = ($action === 'approve') ? 'Disetujui' : 'Ditolak';

$stmtPengajuan = $conn->prepare("SELECT * FROM pengajuan WHERE id_pengajuan = ?");
$stmtPengajuan->execute([$id]);
$pengajuan = $stmtPengajuan->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("UPDATE pengajuan SET status = ? WHERE id_pengajuan = ?");
$stmt->execute([$status, $id]);

if ($action === 'approve') {
    $stmtCheck = $conn->prepare("SELECT * FROM pendaftaran_magang WHERE id_mahasiswa = ?");
    $stmtCheck->execute([$pengajuan['id_mahasiswa']]);
    $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmtUpdate = $conn->prepare("
            UPDATE pendaftaran_magang
            SET id_dosen = ?,
                status_pendaftaran = ?,
                tempat_magang = ?,
                id_pengajuan = ?,
                tanggal_mulai = ?,
                tanggal_selesai = ?
            WHERE id_mahasiswa = ?
        ");
        $stmtUpdate->execute([
            $id_dosen,
            'Aktif',
            $pengajuan['nama_perusahaan'],
            $pengajuan['id_pengajuan'],
            $pengajuan['tanggal_mulai'],
            $pengajuan['tanggal_selesai'],
            $pengajuan['id_mahasiswa']
        ]);
    } else {
        $stmtInsert = $conn->prepare("
            INSERT INTO pendaftaran_magang (id_mahasiswa, id_dosen, status_pendaftaran, tempat_magang, id_pengajuan, tanggal_mulai, tanggal_selesai)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtInsert->execute([
            $pengajuan['id_mahasiswa'],
            $id_dosen,
            'Aktif',
            $pengajuan['nama_perusahaan'],
            $pengajuan['id_pengajuan'],
            $pengajuan['tanggal_mulai'],
            $pengajuan['tanggal_selesai']
        ]);
    }
}

header("Location: pengajuan.php");
exit;