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

$page = "riwayat";

$stmtMhs = $conn->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = ?");
$stmtMhs->execute([$user['id']]);
$mahasiswa = $stmtMhs->fetch(PDO::FETCH_ASSOC);

$nama_mahasiswa = $mahasiswa['nama'];
$nim = $mahasiswa['NIM'];
$prodi = $mahasiswa['prodi'];
$email = $mahasiswa['email'];

$stmtRiwayat = $conn->prepare("
    SELECT id_laporan_harian, kegiatan, status, tanggal_submit, minggu_ke
    FROM laporan_harian
    WHERE id_mahasiswa = ?
    ORDER BY tanggal_submit DESC
");
$stmtRiwayat->execute([$user['id']]);
$data_riwayat = $stmtRiwayat->fetchAll(PDO::FETCH_ASSOC);

$stmtPengajuan = $conn->prepare("SELECT * FROM pengajuan WHERE id_mahasiswa = ? AND status = 'Disetujui' ORDER BY id_pengajuan DESC LIMIT 1");
$stmtPengajuan->execute([$user['id']]);
$pengajuan = $stmtPengajuan->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Magang - SIMMAG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/mahasiswa_navbar.css">
    <link rel="stylesheet" href="assets/css/riwayat.css">
    <link rel="stylesheet" href="assets/css/mahasiswa.css">
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
                        <a href="../logout.php" class="text-danger" id="btnKeluar"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="mx-auto" style="max-width: 800px;">

            <div class="d-flex align-items-center mb-4">
                <div class="bg-white rounded-circle shadow-sm me-3 text-primary d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold" style="color: #1e293b;">Riwayat Aktivitas</h4>
                    <p class="text-muted mb-0" style="font-size: 14px;">Pantau seluruh aktivitas magang Anda di sini</p>
                </div>
            </div>

            <?php if ($pengajuan): ?>
            <div class="timeline-container">

                <div class="timeline-item">
                    <div class="timeline-icon success"><i class="bi bi-file-earmark-check"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-date"><i class="bi bi-calendar3"></i> <?= date('d F Y, H:i', strtotime($pengajuan['created_at'])) ?></div>
                        <h6 class="timeline-title text-success">Pengajuan Proposal Disetujui</h6>
                        <p class="timeline-desc">Pengajuan proposal magang Anda di <?= $pengajuan['nama_perusahaan'] ?> telah disetujui.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon primary"><i class="bi bi-play-circle"></i></div>
                    <div class="timeline-content">
                        <div class="timeline-date"><i class="bi bi-calendar3"></i> <?= date('d F Y', strtotime($pengajuan['tanggal_mulai'])) ?></div>
                        <h6 class="timeline-title">Mulai Program Magang</h6>
                        <p class="timeline-desc">Hari pertama pelaksanaan program magang di <?= $pengajuan['nama_perusahaan'] ?>.</p>
                    </div>
                </div>

                <?php foreach ($data_riwayat as $item): ?>
                <div class="timeline-item">
                    <div class="timeline-icon <?php if ($item['status'] == 'Disetujui') echo 'success'; elseif ($item['status'] == 'Ditolak') echo 'danger'; else echo 'warning'; ?>">
                        <?php if ($item['status'] == 'Disetujui'): ?>
                            <i class="bi bi-check-circle"></i>
                        <?php elseif ($item['status'] == 'Ditolak'): ?>
                            <i class="bi bi-x-circle"></i>
                        <?php else: ?>
                            <i class="bi bi-clock"></i>
                        <?php endif; ?>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-date"><i class="bi bi-calendar3"></i> <?= date('d F Y, H:i', strtotime($item['tanggal_submit'])) ?></div>
                        <h6 class="timeline-title <?php if ($item['status'] == 'Disetujui') echo 'text-success'; elseif ($item['status'] == 'Ditolak') echo 'text-danger'; ?>"><?= htmlspecialchars($item['kegiatan']) ?></h6>
                        <p class="timeline-desc">Status laporan: <strong><?= $item['status'] ?></strong> untuk minggu ke-<?= $item['minggu_ke'] ?>.</p>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
            <?php endif; ?>

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