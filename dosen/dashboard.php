<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];

$db = new Database();
$conn = $db->getConnection();

$page = "dashboard";

$stmtDosen = $conn->prepare("SELECT * FROM dosen WHERE id_dosen = ?");
$stmtDosen->execute([$user['id']]);
$dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);

$stmtMahasiswa = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM pendaftaran_magang 
    WHERE id_dosen = ? AND status_pendaftaran = 'Aktif'
");
$stmtMahasiswa->execute([$user['id']]);
$total_mahasiswa = $stmtMahasiswa->fetch(PDO::FETCH_ASSOC)['total'];

$stmtPending = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM laporan_harian lh 
    JOIN pendaftaran_magang pm ON lh.id_mahasiswa = pm.id_mahasiswa 
    WHERE pm.id_dosen = ? AND lh.status = 'Menunggu'
");
$stmtPending->execute([$user['id']]);
$total_pending = $stmtPending->fetch(PDO::FETCH_ASSOC)['total'];

$stmtLaporanAkhir = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM laporan_akhir la 
    JOIN pendaftaran_magang pm ON la.id_mahasiswa = pm.id_mahasiswa 
    WHERE pm.id_dosen = ? AND la.status_review = 'Menunggu'
");
$stmtLaporanAkhir->execute([$user['id']]);
$total_laporan_akhir = $stmtLaporanAkhir->fetch(PDO::FETCH_ASSOC)['total'];

$stmtAktivitas = $conn->prepare("
    SELECT m.nama, m.NIM, lh.kegiatan, lh.minggu_ke, lh.tanggal_submit, lh.status 
    FROM laporan_harian lh 
    JOIN mahasiswa m ON lh.id_mahasiswa = m.id_mahasiswa 
    JOIN pendaftaran_magang pm ON lh.id_mahasiswa = pm.id_mahasiswa 
    WHERE pm.id_dosen = ? 
    ORDER BY lh.tanggal_submit DESC 
    LIMIT 5
");
$stmtAktivitas->execute([$user['id']]);
$data_aktivitas = $stmtAktivitas->fetchAll(PDO::FETCH_ASSOC);

$stmtApproved = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM laporan_harian lh 
    JOIN pendaftaran_magang pm ON lh.id_mahasiswa = pm.id_mahasiswa 
    WHERE pm.id_dosen = ? AND lh.status = 'Disetujui'
");
$stmtApproved->execute([$user['id']]);
$total_disetujui = $stmtApproved->fetch(PDO::FETCH_ASSOC)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Dosen - SIMMAG</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/dosen.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

    <div class="top-navbar">
        <div class="navbar-brand">
            <img src="../mahasiswa/assets/logo.png" alt="SIMMAG" style="height:35px;" onerror="this.src='https://ui-avatars.com/api/?name=S+M&background=2563eb&color=fff&rounded=true&font-size=0.5'">
        </div>
        
        <div class="nav-center">
            <a href="dashboard.php" class="<?= ($page == 'dashboard') ? 'active' : '' ?>">Beranda</a>
            <a href="mahasiswa_bimbingan.php" class="<?= ($page == 'mahasiswa_bimbingan') ? 'active' : '' ?>">Mahasiswa Bimbingan</a>
            <a href="review_logbook.php" class="<?= ($page == 'review_logbook') ? 'active' : '' ?>">Review Logbook</a>
            <a href="review_laporan.php" class="<?= ($page == 'review_laporan') ? 'active' : '' ?>">Review Laporan</a>
        </div>
        
        <div class="nav-right">
            <div class="profile-section">
                <div class="profile-wrapper">
                    <div class="profile-info" id="profileToggle" style="cursor:pointer;">
                        <div class="profile-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="profile-text">
                            <div class="name"><?= $dosen['nama'] ?></div>
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
        <div class="row g-4">
            
            <div class="col-lg-8 col-xl-9">
                
                <div class="welcome-banner">
                    <h2>Selamat Datang, <?= $dosen['nama'] ?>!</h2>
                    <p>Ada <?= $total_pending ?> logbook baru dan <?= $total_laporan_akhir ?> laporan akhir yang menunggu persetujuan Anda hari ini.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title text-blue">Total Mahasiswa</div>
                        <div class="stat-subtitle">Sedang Magang</div>
                        <div class="stat-number"><?= $total_mahasiswa ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-orange">Menunggu Review</div>
                        <div class="stat-subtitle">Logbook Harian</div>
                        <div class="stat-number"><?= $total_pending ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-red">Perlu Dinilai</div>
                        <div class="stat-subtitle">Laporan Akhir</div>
                        <div class="stat-number"><?= $total_laporan_akhir ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-green">Logbook Disetujui</div>
                        <div class="stat-subtitle">Total Review Selesai</div>
                        <div class="stat-number"><?= $total_disetujui ?></div>
                    </div>
                </div>

                <div class="recent-card">
                    <div class="card-header-custom">
                        <span>Aktivitas Mahasiswa Bimbingan</span>
                        <a href="review_logbook.php" class="btn btn-sm btn-light text-primary fw-semibold" style="font-size:12px;">Lihat Semua</a>
                    </div>
                    
                    <?php foreach ($data_aktivitas as $item): ?>
                        <div class="activity-item">
                            <div class="activity-icon <?= ($item['status'] == 'Disetujui') ? 'green' : 'orange' ?>">
                                <?php if ($item['status'] == 'Disetujui'): ?>
                                    <i class="bi bi-check-circle"></i>
                                <?php else: ?>
                                    <i class="bi bi-journal-text"></i>
                                <?php endif; ?>
                            </div>

                            <div class="activity-content w-100">
                                <h6><?= $item['nama'] ?> (<?= $item['NIM'] ?>)</h6>
                                <p><?= htmlspecialchars($item['kegiatan']) ?> untuk minggu ke-<?= $item['minggu_ke'] ?>.</p>
                                <div class="activity-time">
                                    <?= date('d M Y H:i', strtotime($item['tanggal_submit'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4 col-xl-3">
                <div class="recent-card">
                    <div class="card-header-custom mb-3">
                        <span>Tindakan Cepat</span>
                    </div>
                    
                    <div class="d-grid gap-3">
                        <a href="review_logbook.php" class="btn btn-warning text-dark fw-semibold py-2 d-flex align-items-center justify-content-center" style="border-radius: 8px; background: #fef08a; border: 1px solid #fde047;">
                            <i class="bi bi-journal-check me-2"></i> Review Logbook
                        </a>
                        <a href="review_laporan.php" class="btn btn-primary fw-semibold py-2 d-flex align-items-center justify-content-center" style="border-radius: 8px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;">
                            <i class="bi bi-file-earmark-text me-2"></i> Nilai Laporan Akhir
                        </a>
                        <a href="mahasiswa_bimbingan.php" class="btn btn-light text-dark fw-semibold py-2 d-flex align-items-center justify-content-center" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                            <i class="bi bi-people me-2"></i> Daftar Mahasiswa
                        </a>
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
            <div class="profile-modal-name"><?= $dosen['nama'] ?></div>
            <div class="profile-modal-role">Dosen Pembimbing</div>

            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
                <div>
                    <div class="profile-detail-label">Surel</div>
                    <div class="profile-detail-value"><?= $dosen['email'] ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-telephone"></i></div>
                <div>
                    <div class="profile-detail-label">No. Telepon</div>
                    <div class="profile-detail-value"><?= $dosen['no_hp'] ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-person-badge"></i></div>
                <div>
                    <div class="profile-detail-label">NIP</div>
                    <div class="profile-detail-value"><?= $dosen['NIP'] ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/dosen.js"></script>
</body>
</html>