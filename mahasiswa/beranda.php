<?php
session_start();

require_once '../config/Database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

$db = new Database();
$conn = $db->getConnection();

$stmtPengajuan = $conn->prepare("
    SELECT *
    FROM pengajuan
    WHERE id_mahasiswa = ?
    ORDER BY created_at DESC
    LIMIT 1
");
$stmtPengajuan->execute([$user['id']]);
$pengajuan = $stmtPengajuan->fetch(PDO::FETCH_ASSOC);

if ($pengajuan && $pengajuan['status'] == 'Disetujui') {
    header("Location: magang_aktif.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT *
    FROM mahasiswa
    WHERE id_mahasiswa = ?
");
$stmt->execute([$user['id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$page = "beranda";
$nama_mahasiswa = $data['nama'];
$nim = $data['NIM'];
$prodi = $data['prodi'];
$email = $data['email'];
$status_pengajuan = $pengajuan['status'] ?? 'Belum Ada';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Mahasiswa - Sistem Magang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/mahasiswa.css">
    <link rel="stylesheet" href="assets/css/beranda.css">
</head>
<body>

<div class="d-flex">
    <div class="sidebar d-flex flex-column">
        <div>
            <div class="logo-container">
                <img src="assets/logo.png" class="logo-sidebar" alt="Logo">
            </div>
            <a href="beranda.php" class="<?= ($page == 'beranda') ? 'active' : '' ?>"><i class="bi bi-grid me-2"></i>Beranda</a>
            <a href="pengajuan.php" class="<?= ($page == 'pengajuan') ? 'active' : '' ?>"><i class="bi bi-file-earmark-text me-2"></i>Pengajuan Proposal</a>
        </div>
    </div>

    <div class="p-4 w-100">
        <div class="header-top">
            <div class="welcome-text">👋 Halo, <?= $nama_mahasiswa ?>!</div>
            <div class="profile-section">
                <div class="profile-wrapper">
                    <div class="profile-info" id="profileToggle" style="cursor:pointer;">
                        <div class="profile-avatar"><i class="bi bi-person"></i></div>
                        <div class="profile-text">
                            <div class="name"><?= $nama_mahasiswa ?></div>
                            <div class="role">Mahasiswa</div>
                        </div>
                        <i class="bi bi-chevron-down ms-2 text-muted" style="font-size: 12px;"></i>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">Akun Saya</div>
                        <a href="#" id="btnProfil"><i class="bi bi-person me-2"></i>Profil</a>
                        <div class="dropdown-divider my-1"></div>
                        <a href="../logout.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($status_pengajuan == 'Belum Ada') : ?>
            <div class="empty-state-card">
                <div class="empty-state-content">
                    <div class="empty-state-icon">
                        <i class="bi bi-rocket-takeoff"></i>
                    </div>
                    <h3>Belum Ada Pengajuan Magang</h3>
                    <p>Anda belum mengajukan proposal magang. Segera ajukan proposal Anda untuk memulai proses magang.</p>
                    <a href="pengajuan.php" class="btn-primary-gradient">
                        <i class="bi bi-plus-circle me-2"></i>Ajukan Proposal Magang
                    </a>
                    <div class="steps-container">
                        <div class="step-item active">
                            <div class="step-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="step-title">Ajukan Proposal</div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="step-title">Menunggu Review</div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="step-title">Disetujui</div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <div class="step-title">Mulai Magang</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($status_pengajuan == 'pending') : ?>
            <div class="empty-state-card">
                <div class="empty-state-content">
                    <div class="empty-state-icon text-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h3>Proposal Sedang Direview</h3>
                    <p>Proposal magang Anda telah dikirim and sedang menunggu persetujuan.</p>
                    <button class="btn-primary-gradient" disabled>
                        <i class="bi bi-clock-history me-2"></i>Menunggu Review
                    </button>
                    <div class="steps-container">
                        <div class="step-item active">
                            <div class="step-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="step-title">Ajukan Proposal</div>
                        </div>
                        <div class="step-item active">
                            <div class="step-icon">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="step-title">Menunggu Review</div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="step-title">Disetujui</div>
                        </div>
                        <div class="step-item">
                            <div class="step-icon">
                                <i class="bi bi-briefcase"></i>
                            </div>
                            <div class="step-title">Mulai Magang</div>
                        </div>
                    </div>
                </div>
            </div>
        <?php elseif ($status_pengajuan == 'Ditolak') : ?>
            <div class="empty-state-card">
                <div class="empty-state-content">
                    <div class="empty-state-icon text-danger">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <h3>Proposal Ditolak</h3>
                    <p>Proposal magang Anda ditolak. Silakan ajukan ulang proposal baru.</p>
                    <a href="pengajuan.php" class="btn-primary-gradient">
                        <i class="bi bi-arrow-repeat me-2"></i>Ajukan Ulang
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal-overlay" id="profileModal">
    <div class="profile-modal-box">
        <div class="profile-modal-header">
            <h5>Info Profil</h5>
            <button class="profile-modal-close" id="btnTutupProfil">&times;</button>
        </div>
        <div class="profile-modal-avatar"><i class="bi bi-person"></i></div>
        <div class="profile-modal-name"><?= $nama_mahasiswa ?></div>
        <div class="profile-modal-role">Mahasiswa</div>
        <div class="profile-detail">
            <div class="profile-detail-icon"><i class="bi bi-credit-card-2-front"></i></div>
            <div>
                <div class="profile-detail-label">NIM</div>
                <div class="profile-detail-value"><?= $nim ?></div>
            </div>
        </div>
        <div class="profile-detail">
            <div class="profile-detail-icon"><i class="bi bi-book"></i></div>
            <div>
                <div class="profile-detail-label">Program Studi</div>
                <div class="profile-detail-value"><?= $prodi ?></div>
            </div>
        </div>
        <div class="profile-detail">
            <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
            <div>
                <div class="profile-detail-label">Surel</div>
                <div class="profile-detail-value"><?= $email ?></div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/mahasiswa.js"></script>
</body>
</html>