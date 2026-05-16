<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$page = "pemantauan_dosen";

$stmtKps = $conn->prepare("
    SELECT k.*, d.nama, d.NIP, d.email, d.no_hp
    FROM kps k
    JOIN dosen d ON k.id_dosen = d.id_dosen
    WHERE k.id_dosen = ?
");
$stmtKps->execute([$_SESSION['user']['id']]);
$kps = $stmtKps->fetch(PDO::FETCH_ASSOC);

$nama_kaprodi = $kps['nama'];
$nip = $kps['NIP'];

$search = $_GET['search'] ?? '';

$count_query = "
    SELECT COUNT(*) as total
    FROM dosen d
    WHERE 1=1
";
$count_params = [];

if (!empty($search)) {
    $count_query .= " AND (d.nama LIKE ? OR d.NIP LIKE ?) ";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
}

include '../config/paging.php';

$query = "
    SELECT
        d.id_dosen,
        d.nama,
        d.NIP,
        (SELECT COUNT(DISTINCT p2.id_mahasiswa) FROM pendaftaran_magang p2 WHERE p2.id_dosen = d.id_dosen) AS total_bimbingan,
        COUNT(DISTINCT CASE WHEN lh.status = 'Menunggu' THEN lh.id_laporan_harian END) AS total_logbook,
        COUNT(DISTINCT CASE WHEN la.status_review = 'Menunggu' THEN la.id_laporan_akhir END) AS total_laporan
    FROM dosen d
    LEFT JOIN pendaftaran_magang pm ON d.id_dosen = pm.id_dosen
    LEFT JOIN laporan_harian lh ON pm.id_mahasiswa = lh.id_mahasiswa
    LEFT JOIN laporan_akhir la ON pm.id_mahasiswa = la.id_mahasiswa
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND (d.nama LIKE ? OR d.NIP LIKE ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= "
    GROUP BY d.id_dosen
    ORDER BY d.nama ASC
    LIMIT $limit
    OFFSET $offset
";

$stmtDosen = $conn->prepare($query);
$stmtDosen->execute($params);
$data_dosen = $stmtDosen->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kinerja Dosen - SIMMAG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/kaprodi.css">
    <link rel="stylesheet" href="assets/css/pemantauan.css">
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
                        <div class="profile-avatar"><i class="bi bi-person"></i></div>
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
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h4 class="mb-0 fw-bold page-title">Pemantauan Kinerja Dosen Pembimbing</h4>
                <form method="GET" class="filter-section">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama dosen atau NIP..." value="<?= htmlspecialchars($search) ?>" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="py-3 px-4">Nama Dosen</th>
                            <th class="py-3 text-center">Mahasiswa Bimbingan</th>
                            <th class="py-3 text-center">Logbook Belum Dinilai</th>
                            <th class="py-3 text-center">Laporan Belum Dinilai</th>
                            <th class="py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_dosen as $dosen): ?>
                            <?php
                            $status = 'Sangat Baik';
                            $statusClass = 'bg-success-subtle text-success border-success-subtle';

                            if ($dosen['total_logbook'] > 0 || $dosen['total_laporan'] > 0) {
                                $status = 'Perlu Perhatian';
                                $statusClass = 'bg-warning-subtle text-warning border-warning-subtle';
                            }
                            ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark"><?= $dosen['nama'] ?></div>
                                    <div class="text-muted sub-text">NIP. <?= $dosen['NIP'] ?></div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="fw-bold bimbingan-count"><?= $dosen['total_bimbingan'] ?></span>
                                </td>
                                <td class="py-3 text-center">
                                    <?php if ($dosen['total_logbook'] > 0): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill badge-lg">
                                            <?= $dosen['total_logbook'] ?> Logbook
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-3 py-1 rounded-pill">
                                            0 Logbook
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-center">
                                    <?php if ($dosen['total_laporan'] > 0): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill badge-lg">
                                            <?= $dosen['total_laporan'] ?> Laporan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-3 py-1 rounded-pill">
                                            0 Laporan
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="badge <?= $statusClass ?> border px-3 py-1 rounded-pill">
                                        <?= $status ?>
                                    </span>
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
            <div class="modal-icon"><i class="bi bi-box-arrow-right"></i></div>
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