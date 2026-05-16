<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$page = "dashboard";

$stmtKps = $conn->prepare("
    SELECT k.*, d.nama, d.NIP, d.email, d.no_hp
    FROM kps k
    JOIN dosen d ON k.id_dosen = d.id_dosen
    WHERE k.id_dosen = ?
");
$stmtKps->execute([$_SESSION['user']['id']]);
$kps = $stmtKps->fetch(PDO::FETCH_ASSOC);

if (!$kps) {
    die("Data KPS tidak ditemukan");
}

$nama_kaprodi = $kps['nama'];
$nip = $kps['NIP'];

$stmtTotalMahasiswa = $conn->query("
    SELECT COUNT(*) as total
    FROM pendaftaran_magang
    WHERE status_pendaftaran = 'Aktif'
");
$totalMahasiswa = $stmtTotalMahasiswa->fetch(PDO::FETCH_ASSOC)['total'];

$stmtTotalDosen = $conn->query("
    SELECT COUNT(*) as total
    FROM dosen
    WHERE status = 'Aktif'
");
$totalDosen = $stmtTotalDosen->fetch(PDO::FETCH_ASSOC)['total'];

$stmtPerusahaan = $conn->query("
    SELECT COUNT(*) as total
    FROM mitra
");
$totalPerusahaan = $stmtPerusahaan->fetch(PDO::FETCH_ASSOC)['total'];

$stmtKelulusan = $conn->query("
    SELECT ROUND(AVG(nilai), 0) as rata_nilai
    FROM nilai_akhir
");
$kelulusan = $stmtKelulusan->fetch(PDO::FETCH_ASSOC)['rata_nilai'];

if (!$kelulusan) {
    $kelulusan = 0;
}

$stmtSelesai = $conn->query("
    SELECT COUNT(*) as total
    FROM pendaftaran_magang
    WHERE status_pendaftaran = 'Selesai'
");
$totalSelesai = $stmtSelesai->fetch(PDO::FETCH_ASSOC)['total'];

$stmtAktif = $conn->query("
    SELECT COUNT(*) as total
    FROM pendaftaran_magang
    WHERE status_pendaftaran = 'Aktif'
");
$totalAktif = $stmtAktif->fetch(PDO::FETCH_ASSOC)['total'];

$stmtMenunggu = $conn->query("
    SELECT COUNT(*) as total
    FROM pengajuan
    WHERE status = 'Pending'
");
$totalMenunggu = $stmtMenunggu->fetch(PDO::FETCH_ASSOC)['total'];

$stmtAktivitas = $conn->query("
    SELECT m.nama, p.nama_perusahaan, p.status, p.created_at
    FROM pengajuan p
    JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa
    ORDER BY p.created_at DESC
    LIMIT 3
");
$aktivitas = $stmtAktivitas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Kaprodi - SIMMAG</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/kaprodi.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

    <div class="top-navbar">
        <div class="navbar-brand">
            <img src="../mahasiswa/assets/logo.png" alt="SIMMAG">
        </div>
        
        <div class="nav-center">
            <a href="dashboard.php" class="<?= ($page == 'dashboard') ? 'active' : '' ?>">Beranda</a>
            <a href="monitoring_mahasiswa.php" class="<?= ($page == 'monitoring_mahasiswa') ? 'active' : '' ?>">Pemantauan Mahasiswa</a>
            <a href="pemantauan_dosen.php" class="<?= ($page == 'pemantauan_dosen') ? 'active' : '' ?>">Kinerja Dosen</a>
        </div>
        
        <div class="nav-right">
            <div class="profile-section">
                <div class="profile-wrapper">
                    <div class="profile-info" id="profileToggle">
                        <div class="profile-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="profile-text">
                            <div class="name"><?= $nama_kaprodi ?></div>
                            <div class="role">Ketua Program Studi</div>
                        </div>
                        <i class="bi bi-chevron-down ms-2 text-muted chevron-icon"></i>
                    </div>

                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">Akun Saya</div>
                        <a href="javascript:void(0)" id="btnProfil"><i class="bi bi-person me-2"></i>Profil</a>
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
                    <h2>Selamat Datang, <?= $nama_kaprodi ?>!</h2>
                    <p>Program Studi Teknik Informatika memiliki <?= $totalMahasiswa ?> mahasiswa aktif dalam program magang saat ini.</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-title text-purple">Total Mahasiswa</div>
                        <div class="stat-subtitle">Sedang Magang</div>
                        <div class="stat-number"><?= $totalMahasiswa ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-blue">Dosen Pembimbing</div>
                        <div class="stat-subtitle">Terlibat Program</div>
                        <div class="stat-number"><?= $totalDosen ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-orange">Perusahaan Mitra</div>
                        <div class="stat-subtitle">Kerjasama Aktif</div>
                        <div class="stat-number"><?= $totalPerusahaan ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-title text-green">Tingkat Kelulusan</div>
                        <div class="stat-subtitle">Rata-rata Nilai A</div>
                        <div class="stat-number"><?= $kelulusan ?>%</div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="recent-card">
                            <div class="card-header-custom">
                                <span>Status Magang Mahasiswa</span>
                            </div>
                            <div class="chart-container">
                                <div class="pie-chart"></div>
                                <div class="legend">
                                    <div class="legend-item">
                                        <div class="legend-color legend-green"></div>
                                        Selesai Magang (<?= $totalSelesai ?>)
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color legend-blue"></div>
                                        Sedang Berlangsung (<?= $totalAktif ?>)
                                    </div>
                                    <div class="legend-item">
                                        <div class="legend-color legend-yellow"></div>
                                        Menunggu Plotting (<?= $totalMenunggu ?>)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="recent-card">
                            <div class="card-header-custom">
                                <span>Aktivitas Program Studi</span>
                                <a href="monitoring_mahasiswa.php" class="btn btn-sm btn-light text-primary fw-semibold view-all-link">Lihat Semua</a>
                            </div>
                            
                            <?php foreach ($aktivitas as $item): ?>
                            <div class="activity-item">
                                <div class="activity-icon <?= $item['status'] == 'Disetujui' ? 'green' : ($item['status'] == 'Ditolak' ? 'orange' : 'purple') ?>">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="activity-content w-100">
                                    <h6>Pengajuan Magang</h6>
                                    <p><?= $item['nama'] ?> mengajukan magang ke <?= $item['nama_perusahaan'] ?></p>
                                    <div class="activity-time">
                                        <?= date('d M Y H:i', strtotime($item['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-xl-3">
                <div class="recent-card">
                    <div class="card-header-custom mb-3">
                        <span>Akses Cepat</span>
                    </div>
                    
                    <div class="d-grid gap-3">
                        <a href="monitoring_mahasiswa.php" class="btn fw-semibold py-3 d-flex align-items-center justify-content-center flex-column gap-2 quick-link quick-link-purple">
                            <i class="bi bi-mortarboard quick-link-icon"></i>
                            Data Mahasiswa
                        </a>
                        <a href="pemantauan_dosen.php" class="btn fw-semibold py-3 d-flex align-items-center justify-content-center flex-column gap-2 quick-link quick-link-blue">
                            <i class="bi bi-person-video3 quick-link-icon"></i>
                            Kinerja Dosen
                        </a>
                        <a href="download_rekap.php" class="btn btn-light text-dark fw-semibold py-3 d-flex align-items-center justify-content-center flex-column gap-2 quick-link quick-link-neutral">
                            <i class="bi bi-file-earmark-spreadsheet text-success quick-link-icon"></i>
                            Unduh Rekap Nilai
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
                <a href="../logout.php" class="modal-logout-link"><button class="btn-logout">Ya, Keluar</button></a>
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
            <div class="profile-modal-name"><?= $nama_kaprodi ?></div>
            <div class="profile-modal-role">Ketua Program Studi</div>

            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
                <div>
                    <div class="profile-detail-label">Surel</div>
                    <div class="profile-detail-value"><?= $kps['email'] ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-telephone"></i></div>
                <div>
                    <div class="profile-detail-label">No. Telepon</div>
                    <div class="profile-detail-value"><?= $kps['no_hp'] ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-building"></i></div>
                <div>
                    <div class="profile-detail-label">NIP</div>
                    <div class="profile-detail-value"><?= $nip ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/kaprodi.js"></script>
</body>
</html>