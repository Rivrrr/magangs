<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$page = "beranda";

$stmtMahasiswa = $conn->query("SELECT COUNT(*) FROM mahasiswa");
$total_mahasiswa = $stmtMahasiswa->fetchColumn();

$stmtDosen = $conn->query("SELECT COUNT(*) FROM dosen");
$total_dosen = $stmtDosen->fetchColumn();

$stmtPerusahaan = $conn->query("SELECT COUNT(DISTINCT tempat_magang) FROM pendaftaran_magang");
$total_perusahaan = $stmtPerusahaan->fetchColumn();

$stmtAktif = $conn->query("SELECT COUNT(*) FROM pendaftaran_magang WHERE status_pendaftaran = 'Aktif'");
$total_magang = $stmtAktif->fetchColumn();

$stmtSelesai = $conn->query("SELECT COUNT(*) FROM pendaftaran_magang WHERE status_pendaftaran = 'Selesai'");
$status_selesai = $stmtSelesai->fetchColumn();

$stmtBerlangsung = $conn->query("SELECT COUNT(*) FROM pendaftaran_magang WHERE status_pendaftaran = 'Aktif'");
$status_berlangsung = $stmtBerlangsung->fetchColumn();

$stmtMenunggu = $conn->query("SELECT COUNT(*) FROM pengajuan WHERE status = 'Menunggu'");
$status_menunggu = $stmtMenunggu->fetchColumn();

$total_status = $status_selesai + $status_berlangsung + $status_menunggu;

$deg_selesai = ($status_selesai / max($total_status, 1)) * 360;
$deg_berlangsung = ($status_berlangsung / max($total_status, 1)) * 360;
$deg_menunggu = ($status_menunggu / max($total_status, 1)) * 360;

$stmtAktivitas = $conn->query("
    SELECT p.*, m.nama 
    FROM pengajuan p 
    JOIN mahasiswa m ON p.id_mahasiswa = m.id_mahasiswa 
    ORDER BY p.created_at DESC 
    LIMIT 4
");
$data_aktivitas = $stmtAktivitas->fetchAll(PDO::FETCH_ASSOC);

$stmtAdmin = $conn->prepare("
    SELECT *
    FROM admin
    WHERE id_admin = ?
");

$stmtAdmin->execute([
    $_SESSION['user']['id']
]);

$admin =
    $stmtAdmin->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Beranda Magang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/beranda.css">
</head>

<body>

    <div class="d-flex">
        <div class="sidebar d-flex flex-column">
            <div>
                <div class="logo-container">
                    <img src="assets/logo.png" class="logo-sidebar">
                </div>
                <a href="beranda.php" class="<?= ($page == 'beranda') ? 'active' : '' ?>">
                    <i class="bi bi-grid me-2"></i> Beranda
                </a>
                <a href="mahasiswa.php">
                    <i class="bi bi-people me-2"></i> Mahasiswa
                </a>
                <a href="dosen.php">
                    <i class="bi bi-person-badge me-2"></i> Dosen
                </a>
                <a href="pengajuan.php">
                    <i class="bi bi-send me-2"></i> Pengajuan
                </a>
            </div>
        </div>

        <div class="p-4 w-100">
            <div class="header-top">
                <form action="search.php" method="GET" class="search-box m-0">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Cari mahasiswa, dosen, atau pengajuan..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" required>

                    <button type="submit" style="display: none;"></button>
                </form>

                <div class="profile-section">
                    <div class="profile-wrapper">
                        <div class="profile-info" id="profileToggle" style="cursor:pointer;">
                            <div class="profile-avatar">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="profile-text">
                                <div class="name">Admin Sistem</div>
                                <div class="email">admin@magang.ac.id</div>
                            </div>
                        </div>
                        <div class="profile-dropdown" id="profileDropdown">
                            <div class="dropdown-header">Akun Saya</div>
                            <a href="#" id="btnProfil"><i class="bi bi-person me-2"></i>Profil</a>
                            <a href="../logout.php" class="text-danger" id="btnKeluar"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 class="fw-bold" style="color:#1e293b;">Gambaran Umum Beranda</h4>
                <p class="text-muted mb-0" style="font-size:14px;">Selamat datang kembali, Admin. Berikut adalah ringkasan sistem hari ini.</p>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm h-100" style="border-radius: 16px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-primary-subtle text-primary" style="width: 48px; height: 48px;">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total Mahasiswa</h6>
                                <h3 class="fw-bold mb-0" style="color: #1e293b;"><?= $total_mahasiswa ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm h-100" style="border-radius: 16px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-info-subtle text-info" style="width: 48px; height: 48px;">
                                <i class="bi bi-person-badge-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total Dosen</h6>
                                <h3 class="fw-bold mb-0" style="color: #1e293b;"><?= $total_dosen ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm h-100" style="border-radius: 16px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-warning-subtle text-warning" style="width: 48px; height: 48px;">
                                <i class="bi bi-building-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Perusahaan Mitra</h6>
                                <h3 class="fw-bold mb-0" style="color: #1e293b;"><?= $total_perusahaan ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 border-0 shadow-sm h-100" style="border-radius: 16px; transition: transform 0.2s;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3 bg-success-subtle text-success" style="width: 48px; height: 48px;">
                                <i class="bi bi-laptop-fill fs-5"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Magang Aktif</h6>
                                <h3 class="fw-bold mb-0" style="color: #1e293b;"><?= $total_magang ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4 align-items-stretch g-3">
                <div class="col-md-4 d-flex">
                    <div class="card p-4 text-center border-0 shadow-sm h-100 w-100" style="border-radius: 16px;">
                        <h5 class="fw-bold text-start mb-4" style="color: #1e293b;">Status Magang</h5>

                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <div class="pie-chart shadow-sm"
                                style="width: 150px; height: 150px; border-radius: 50%; position: relative;
                                    background: conic-gradient(
                                        #198754 0deg <?= $deg_selesai ?>deg,
                                        #0d6efd <?= $deg_selesai ?>deg <?= $deg_selesai + $deg_berlangsung ?>deg,
                                        #ffc107 <?= $deg_selesai + $deg_berlangsung ?>deg 360deg
                                    );">
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90px; height: 90px; background: white; border-radius: 50%;"></div>
                            </div>
                        </div>

                        <div class="text-start mt-auto">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span style="font-size: 14px; color: #475569;"><span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background:#198754;"></span> Selesai</span>
                                    <span class="fw-bold" style="color: #1e293b;"><?= $status_selesai ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span style="font-size: 14px; color: #475569;"><span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background:#0d6efd;"></span> Berlangsung</span>
                                    <span class="fw-bold" style="color: #1e293b;"><?= $status_berlangsung ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-2">
                                    <span style="font-size: 14px; color: #475569;"><span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background:#ffc107;"></span> Menunggu</span>
                                    <span class="fw-bold" style="color: #1e293b;"><?= $status_menunggu ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 d-flex">
                    <div class="card p-4 border-0 shadow-sm h-100 w-100" style="border-radius: 16px;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0" style="color: #1e293b;">Aktivitas Terbaru</h5>
                            <a href="pengajuan.php" class="btn btn-sm btn-light text-primary" style="border-radius: 8px; font-size: 13px; font-weight: 500;">Lihat Semua</a>
                        </div>

                        <div class="mt-2 flex-grow-1">
                            <?php if ($data_aktivitas): ?>
                                <?php foreach ($data_aktivitas as $aktivitas): ?>
                                    <div class="d-flex align-items-start mb-3 pb-3 <?= ($aktivitas !== end($data_aktivitas)) ? 'border-bottom' : '' ?>">
                                        <div class="flex-shrink-0">
                                            <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mt-1" style="width: 40px; height: 40px;">
                                                <i class="bi bi-file-earmark-text-fill fs-6"></i>
                                            </div>
                                        </div>
                                        <div class="ms-3 w-100">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-semibold" style="color: #334155; font-size: 14px;"><?= $aktivitas['nama'] ?></h6>
                                                <small class="text-muted" style="font-size: 11px;">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?= date('d M Y H:i', strtotime($aktivitas['created_at'])) ?>
                                                </small>
                                            </div>
                                            <p class="text-muted mb-0 mt-1" style="font-size: 13px;">Telah mengajukan proposal magang baru.</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox fs-2 text-muted opacity-25"></i>
                                    <p class="text-muted mt-2 mb-0" style="font-size: 14px;">Belum ada aktivitas terbaru.</p>
                                </div>
                            <?php endif; ?>
                        </div>
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
            <div class="profile-modal-name">Admin Sistem</div>
            <div class="profile-modal-role">Admin</div>

            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
                <div>
                    <div class="profile-detail-label">Surel</div>
                    <div class="profile-detail-value"><?= $admin['email'] ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-telephone"></i></div>
                <div>
                    <div class="profile-detail-label">No. Telepon</div>
                    <div class="profile-detail-value"><?= $admin['no_hp'] ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-shield-check"></i></div>
                <div>
                    <div class="profile-detail-label">Peran</div>
                    <div class="profile-detail-value">Admin Utama</div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>

</body>

</html>