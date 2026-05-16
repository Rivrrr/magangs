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

$page = "mahasiswa_bimbingan";
$search = $_GET['search'] ?? '';

$stmtDosen = $conn->prepare("
    SELECT *
    FROM dosen
    WHERE id_dosen = ?
");
$stmtDosen->execute([$user['id']]);
$dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);

$nama_dosen = $dosen['nama'];
$nip = $dosen['NIP'];

$count_query = "
    SELECT COUNT(*) as total
    FROM pendaftaran_magang pm
    JOIN mahasiswa m ON pm.id_mahasiswa = m.id_mahasiswa
    WHERE pm.id_dosen = ?
";
$count_params = [$user['id']];

if (!empty($search)) {
    $count_query .= " AND (m.nama LIKE ? OR m.NIM LIKE ?) ";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
}

include '../config/paging.php';

$query = "
    SELECT
        m.*,
        pm.tempat_magang,
        pm.status_pendaftaran,
        pm.tanggal_mulai,
        pm.tanggal_selesai,
        MAX(lh.minggu_ke) AS minggu_terakhir
    FROM pendaftaran_magang pm
    JOIN mahasiswa m ON pm.id_mahasiswa = m.id_mahasiswa
    LEFT JOIN laporan_harian lh ON m.id_mahasiswa = lh.id_mahasiswa
    WHERE pm.id_dosen = ?
";
$params = [$user['id']];

if (!empty($search)) {
    $query .= " AND (m.nama LIKE ? OR m.NIM LIKE ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= "
    GROUP BY m.id_mahasiswa
    ORDER BY m.nama ASC
    LIMIT $limit
    OFFSET $offset
";

$stmtMahasiswa = $conn->prepare($query);
$stmtMahasiswa->execute($params);
$data_mahasiswa = $stmtMahasiswa->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa Bimbingan - SIMMAG</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/dosen.css">
    <link rel="stylesheet" href="assets/css/mahasiswa_bimbingan.css">
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
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h4 class="mb-0 fw-bold" style="color: #1e293b;">Daftar Mahasiswa Bimbingan</h4>
                <form method="GET" class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau NIM..." value="<?= htmlspecialchars($search) ?>">
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: #f8fafc; color: #64748b; font-size: 13px; text-transform: uppercase;">
                        <tr>
                            <th class="py-3 px-4 rounded-start" style="font-weight: 600;">Nama Mahasiswa</th>
                            <th class="py-3" style="font-weight: 600;">NIM</th>
                            <th class="py-3" style="font-weight: 600;">Tempat Magang</th>
                            <th class="py-3" style="font-weight: 600; width: 180px;">Progress Magang</th>
                            <th class="py-3 text-center" style="font-weight: 600;">Status</th>
                            <th class="py-3 px-4 rounded-end text-end" style="font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_mahasiswa as $mhs): ?>
                            <?php
                                $start = strtotime($mhs['tanggal_mulai']);
                                $end = strtotime($mhs['tanggal_selesai']);
                                $today = time();
                                
                                $totalDuration = $end - $start;
                                $currentDuration = $today - $start;

                                $progress = ($totalDuration > 0) 
                                    ? max(0, min(100, round(($currentDuration / $totalDuration) * 100))) 
                                    : 0;

                                $status = ($progress >= 100) ? 'Selesai Magang' : 'Aktif';
                                $badgeClass = ($progress >= 100) ? 'primary' : 'success';
                                $inisial = strtoupper(substr($mhs['nama'], 0, 1));
                            ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 12px; font-weight: 600;">
                                            <?= $inisial ?>
                                        </div>
                                        <div class="fw-semibold text-dark">
                                            <?= $mhs['nama'] ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-muted"><?= $mhs['NIM'] ?></td>
                                <td class="py-3 text-muted"><?= $mhs['tempat_magang'] ?? '-' ?></td>
                                <td class="py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span style="font-size: 12px; font-weight: 600; color: #2563eb;"><?= $progress ?>%</span>
                                        <span style="font-size: 11px; color: #94a3b8;"><?= $progress >= 100 ? 'Selesai' : 'Magang Aktif' ?></span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" style="width: <?= $progress ?>%;"></div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-<?= $badgeClass ?>-subtle text-<?= $badgeClass ?> border border-<?= $badgeClass ?>-subtle px-3 py-1 rounded-pill">
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="detail_mahasiswa.php?id=<?= $mhs['id_mahasiswa'] ?>" class="btn btn-sm btn-light text-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
        <?php include '../config/pagination.php'; ?>

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