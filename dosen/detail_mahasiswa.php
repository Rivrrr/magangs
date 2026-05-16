<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$id_mahasiswa = $_GET['id'] ?? 0;

$db = new Database();
$conn = $db->getConnection();

$page = "mahasiswa_bimbingan";

$stmtDosen = $conn->prepare("SELECT * FROM dosen WHERE id_dosen = ?");
$stmtDosen->execute([$user['id']]);
$dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);

$nama_dosen = $dosen['nama'];
$nip = $dosen['NIP'];

$stmtMahasiswa = $conn->prepare("
    SELECT
        m.*,
        pm.tempat_magang,
        pm.tanggal_mulai,
        pm.tanggal_selesai,
        pm.status_pendaftaran
    FROM mahasiswa m
    LEFT JOIN pendaftaran_magang pm ON m.id_mahasiswa = pm.id_mahasiswa
    WHERE pm.id_dosen = ? AND m.id_mahasiswa = ?
    LIMIT 1
");

$stmtMahasiswa->execute([$user['id'], $id_mahasiswa]);
$mahasiswa = $stmtMahasiswa->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa) {
    header("Location: mahasiswa_bimbingan.php");
    exit;
}

$stmtStats = $conn->prepare("
    SELECT
        COUNT(*) AS total_logbook,
        SUM(CASE WHEN status = 'Disetujui' THEN 1 ELSE 0 END) AS total_disetujui,
        SUM(CASE WHEN status = 'Menunggu' THEN 1 ELSE 0 END) AS total_menunggu,
        SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) AS total_ditolak,
        MAX(minggu_ke) AS minggu_terakhir
    FROM laporan_harian
    WHERE id_mahasiswa = ?
");

$stmtStats->execute([$id_mahasiswa]);
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

$progress = 0;

if (!empty($mahasiswa['tanggal_mulai']) && !empty($mahasiswa['tanggal_selesai'])) {
    $start = strtotime($mahasiswa['tanggal_mulai']);
    $end = strtotime($mahasiswa['tanggal_selesai']);
    $today = time();

    $totalDuration = $end - $start;
    $currentDuration = $today - $start;

    $progress = ($totalDuration > 0)
        ? max(0, min(100, round(($currentDuration / $totalDuration) * 100)))
        : 0;
}

$stmtTimeline = $conn->prepare("
    SELECT *
    FROM laporan_harian
    WHERE id_mahasiswa = ?
    ORDER BY tanggal_submit DESC
    LIMIT 5
");

$stmtTimeline->execute([$id_mahasiswa]);
$timeline = $stmtTimeline->fetchAll(PDO::FETCH_ASSOC);

$stmtLaporan = $conn->prepare("
    SELECT *
    FROM laporan_akhir
    WHERE id_mahasiswa = ?
    LIMIT 1
");

$stmtLaporan->execute([$id_mahasiswa]);
$laporan = $stmtLaporan->fetch(PDO::FETCH_ASSOC);

$stmtNilai = $conn->prepare("
    SELECT *
    FROM nilai_akhir
    WHERE id_mahasiswa = ?
    ORDER BY tanggal_penilaian DESC
    LIMIT 1
");

$stmtNilai->execute([$id_mahasiswa]);
$nilai = $stmtNilai->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mahasiswa - SIMMAG</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dosen.css">
</head>
<body>

    <div class="top-navbar">
        <div class="navbar-brand">
            <img src="../mahasiswa/assets/logo.png" alt="SIMMAG" style="height:35px;">
        </div>
        <div class="nav-center">
            <a href="dashboard.php">Beranda</a>
            <a href="mahasiswa_bimbingan.php" class="active">Mahasiswa Bimbingan</a>
            <a href="review_logbook.php">Review Logbook</a>
            <a href="review_laporan.php">Review Laporan</a>
        </div>
        <div class="nav-right">
            <div class="profile-section">
                <div class="profile-wrapper">
                    <div class="profile-info" id="profileToggle" style="cursor:pointer;">
                        <div class="profile-avatar"><i class="bi bi-person"></i></div>
                        <div class="profile-text">
                            <div class="name"><?= $nama_dosen ?></div>
                            <div class="role">Dosen Pembimbing</div>
                        </div>
                        <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 12px;"></i>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">Akun Saya</div>
                        <a href="#" id="btnProfil"><i class="bi bi-person me-2"></i>Profil</a>
                        <div class="dropdown-divider my-1"></div>
                        <a href="../logout.php" class="text-danger" id="btnKeluar"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 18px;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 72px; height: 72px; font-size: 28px; font-weight: 700;">
                                <?= strtoupper(substr($mahasiswa['nama'], 0, 1)) ?>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-1" style="color: #1e293b;">
                                    <?= $mahasiswa['nama'] ?>
                                </h3>
                                <div class="text-muted mb-2">
                                    <?= $mahasiswa['NIM'] ?> • <?= $mahasiswa['prodi'] ?>
                                </div>
                                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                    <?= $mahasiswa['tempat_magang'] ?? '-' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mt-4 mt-lg-0">
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="fw-semibold">Progress Magang</span>
                            <span class="text-primary fw-bold"><?= $progress ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 20px;">
                            <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
                        </div>
                        <div class="mt-3">
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                <?= $progress >= 100 ? 'Selesai Magang' : 'Magang Aktif' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="text-muted mb-2">Total Logbook</div>
                        <h2 class="fw-bold text-primary mb-0"><?= $stats['total_logbook'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="text-muted mb-2">Disetujui</div>
                        <h2 class="fw-bold text-success mb-0"><?= $stats['total_disetujui'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="text-muted mb-2">Menunggu</div>
                        <h2 class="fw-bold text-warning mb-0"><?= $stats['total_menunggu'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="text-muted mb-2">Ditolak</div>
                        <h2 class="fw-bold text-danger mb-0"><?= $stats['total_ditolak'] ?? 0 ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1e293b;">Aktivitas Terbaru</h5>
                        <?php foreach ($timeline as $log): ?>
                        <div class="d-flex mb-4">
                            <div class="me-3">
                                <div class="bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 12px;">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold text-dark">Minggu ke-<?= $log['minggu_ke'] ?></div>
                                        <div class="text-muted" style="font-size: 13px;">
                                            <?= htmlspecialchars($log['kegiatan']) ?>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-dark border"><?= $log['status'] ?></span>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 12px;">
                                    <?= date('d F Y H:i', strtotime($log['tanggal_submit'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" style="color: #1e293b;">Laporan Akhir</h5>
                        
                        <?php if ($laporan): ?>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Status Review</div>
                            <span class="badge bg-success px-3 py-2"><?= $laporan['status_review'] ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Tanggal Upload</div>
                            <div class="fw-semibold">
                                <?= date('d F Y', strtotime($laporan['tanggal_upload'])) ?>
                            </div>
                        </div>
                        
                        <?php if ($nilai): ?>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Nilai Akhir</div>
                            <h3 class="fw-bold text-primary mb-0"><?= $nilai['nilai'] ?></h3>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($laporan['catatan_dosen'])): ?>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Catatan Dosen</div>
                            <div style="font-size: 14px;">
                                <?= nl2br(htmlspecialchars($laporan['catatan_dosen'])) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <a href="../uploads/<?= $laporan['file_laporan'] ?>" target="_blank" class="btn btn-outline-primary w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i> Lihat Laporan
                        </a>
                        
                        <?php else: ?>
                        <div class="text-center text-muted py-4">
                            Belum ada laporan akhir
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div class="modal-icon">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <h5>Keluar dari Akun?</h5>
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="modal-actions">
                <button class="btn-cancel" id="btnBatal">Batal</button>
                <a href="../logout.php" style="flex:1;text-decoration:none;"><button class="btn-logout" style="width:100%;">Ya, Keluar</button></a>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="profileModal">
        <div class="profile-modal-box">
            <div class="profile-modal-header">
                <h5>Info Profil</h5>
                <button class="profile-modal-close" id="btnTutupProfil">&times;</button>
            </div>
            <div class="profile-modal-avatar">
                <i class="bi bi-person"></i>
            </div>
            <div class="profile-modal-name"><?= $nama_dosen ?? "Dr. Ir. Suyanto, M.T." ?></div>
            <div class="profile-modal-role">Dosen Pembimbing</div>

            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
                <div>
                    <div class="profile-detail-label">Surel</div>
                    <div class="profile-detail-value">suyanto@univ.ac.id</div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-telephone"></i></div>
                <div>
                    <div class="profile-detail-label">No. Telepon</div>
                    <div class="profile-detail-value">+62 812-3456-7890</div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-building"></i></div>
                <div>
                    <div class="profile-detail-label">NIP</div>
                    <div class="profile-detail-value"><?= $nip ?? "197508232005011002" ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dosen.js"></script>

</body>
</html>