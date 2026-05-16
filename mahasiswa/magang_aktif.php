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

$page = "magang_aktif";

$stmtMhs = $conn->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = ?");
$stmtMhs->execute([$user['id']]);
$mahasiswa = $stmtMhs->fetch(PDO::FETCH_ASSOC);

$nama_mahasiswa = $mahasiswa['nama'];
$nim = $mahasiswa['NIM'];
$prodi = $mahasiswa['prodi'];
$email = $mahasiswa['email'];

$stmtPengajuan = $conn->prepare("SELECT * FROM pengajuan WHERE id_mahasiswa = ? AND status = 'Disetujui' ORDER BY created_at DESC LIMIT 1");
$stmtPengajuan->execute([$user['id']]);
$pengajuan = $stmtPengajuan->fetch(PDO::FETCH_ASSOC);

if (!$pengajuan) {
    header("Location: beranda.php");
    exit;
}

$start = strtotime($pengajuan['tanggal_mulai']);
$end = strtotime($pengajuan['tanggal_selesai']);
$today = time();

$totalDuration = $end - $start;
$currentDuration = $today - $start;
$progress = max(0, min(100, round(($currentDuration / $totalDuration) * 100)));
$week = max(1, floor(($currentDuration / 60 / 60 / 24) / 7) + 1);

$stmtPendaftaran = $conn->prepare("SELECT * FROM pendaftaran_magang WHERE id_mahasiswa = ? ORDER BY id_pendaftaran DESC LIMIT 1");
$stmtPendaftaran->execute([$user['id']]);
$pendaftaran = $stmtPendaftaran->fetch(PDO::FETCH_ASSOC);

if (!$pendaftaran) die("Belum ada magang aktif");

$id_pendaftaran = $pendaftaran['id_pendaftaran'];

$stmtTotal = $conn->prepare("SELECT COUNT(*) FROM laporan_harian WHERE id_pendaftaran = ?");
$stmtTotal->execute([$id_pendaftaran]);
$total_logbook = $stmtTotal->fetchColumn();

$stmtDiterima = $conn->prepare("SELECT COUNT(*) FROM laporan_harian WHERE id_pendaftaran = ? AND status = 'Disetujui'");
$stmtDiterima->execute([$id_pendaftaran]);
$total_diterima = $stmtDiterima->fetchColumn();

$stmtMenunggu = $conn->prepare("SELECT COUNT(*) FROM laporan_harian WHERE id_pendaftaran = ? AND status = 'Menunggu'");
$stmtMenunggu->execute([$id_pendaftaran]);
$total_menunggu = $stmtMenunggu->fetchColumn();

$stmtDitolak = $conn->prepare("SELECT COUNT(*) FROM laporan_harian WHERE id_pendaftaran = ? AND status = 'Ditolak'");
$stmtDitolak->execute([$id_pendaftaran]);
$total_ditolak = $stmtDitolak->fetchColumn();

$stmtNotif = $conn->prepare("SELECT kegiatan, status, minggu_ke, tanggal_submit FROM laporan_harian WHERE id_mahasiswa = ? ORDER BY tanggal_submit DESC LIMIT 3");
$stmtNotif->execute([$user['id']]);
$data_notif = $stmtNotif->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Magang - SIMMAG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/mahasiswa_navbar.css">
    <link rel="stylesheet" href="assets/css/magang_aktif.css">
</head>
<body>

    <!-- Top Navigation -->
    <div class="top-navbar">
        <div class="navbar-brand">
            <img src="assets/logo.png" alt="SIMMAG">
        </div>
        <div class="nav-center">
            <a href="magang_aktif.php" class="<?= ($page == 'magang_aktif') ? 'active' : '' ?>">Beranda</a>
            <a href="laporan_harian.php" class="<?= ($page == 'laporan_harian') ? 'active' : '' ?>">Laporan Harian</a>
            <a href="laporan_akhir.php" class="<?= ($page == 'laporan_akhir') ? 'active' : '' ?>">Laporan Akhir</a>
            <a href="riwayat.php" class="<?= ($page == 'riwayat') ? 'active' : '' ?>">Riwayat</a>
        </div>
        <div class="nav-right">
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
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <div class="row g-4">

            <!-- Left Content -->
            <div class="col-lg-8 col-xl-9">
                <div class="welcome-banner">
                    <h2>Selamat Datang!</h2>
                    <p>Kamu telah menyelesaikan <?= $progress ?>% dari program magangmu!</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title text-blue">Total logbook</div>
                        <div class="stat-subtitle">Total laporan yang dikirim</div>
                        <div class="stat-number"><?= $total_logbook ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-green">Diterima</div>
                        <div class="stat-subtitle">Laporan disetujui dosen</div>
                        <div class="stat-number"><?= $total_diterima ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-orange">Ditunda</div>
                        <div class="stat-subtitle">Menunggu review dosen</div>
                        <div class="stat-number"><?= $total_menunggu ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-red">Ditolak</div>
                        <div class="stat-subtitle">Laporan perlu revisi</div>
                        <div class="stat-number"><?= $total_ditolak ?></div>
                    </div>
                </div>

                <div class="progress-card">
                    <div class="progress-header">Progress Magang</div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="progress-company"><?= $pengajuan['nama_perusahaan'] ?></div>
                        <div class="text-end">
                            <div class="progress-percentage"><?= $progress ?>%</div>
                            <div class="progress-week">Minggu ke <?= $week ?></div>
                        </div>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar-fill" style="width: <?= $progress ?>%;"></div>
                    </div>
                </div>
            </div>

            <!-- Right Content (Notifications) -->
            <div class="col-lg-4 col-xl-3">
                <div class="notifications-container">
                    <div class="notif-main-title">Notifikasi Terbaru</div>

                    <?php foreach ($data_notif as $notif): ?>
                    <div class="notif-card">
                        <div class="notif-header">
                            <div class="notif-icon <?= ($notif['status'] == 'Ditolak') ? 'orange' : '' ?>">
                                <?php if ($notif['status'] == 'Disetujui'): ?>
                                    <i class="bi bi-check2"></i>
                                <?php elseif ($notif['status'] == 'Ditolak'): ?>
                                    <i class="bi bi-x-lg"></i>
                                <?php else: ?>
                                    <i class="bi bi-clock-history"></i>
                                <?php endif; ?>
                            </div>
                            <div class="notif-title">
                                <?php if ($notif['status'] == 'Disetujui'): ?>
                                    Logbook Disetujui
                                <?php elseif ($notif['status'] == 'Ditolak'): ?>
                                    Logbook Ditolak
                                <?php else: ?>
                                    Menunggu Review
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="notif-body"><?= htmlspecialchars($notif['kegiatan']) ?> untuk minggu ke-<?= $notif['minggu_ke'] ?>.</div>
                        <div class="notif-time"><?= date('d M Y H:i', strtotime($notif['tanggal_submit'])) ?></div>
                    </div>
                    <?php endforeach; ?>

                </div>
            </div>

        </div>
    </div>

    <!-- Profile Modal -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/mahasiswa.js"></script>
</body>
</html>