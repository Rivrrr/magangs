<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$page = "monitoring_mahasiswa";

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
$status = $_GET['status'] ?? '';

$count_query = "
    SELECT COUNT(DISTINCT m.id_mahasiswa) as total
    FROM mahasiswa m
    LEFT JOIN pendaftaran_magang pm ON m.id_mahasiswa = pm.id_mahasiswa
    WHERE 1=1
";
$count_params = [];

if (!empty($search)) {
    $count_query .= " AND (m.nama LIKE ? OR m.NIM LIKE ?) ";
    $count_params[] = "%$search%";
    $count_params[] = "%$search%";
}

if (!empty($status)) {
    if ($status == 'Belum Magang') {
        $count_query .= " AND pm.id_mahasiswa IS NULL ";
    } else {
        $count_query .= " AND pm.status_pendaftaran = ? ";
        $count_params[] = $status;
    }
}

include '../config/paging.php';

$query = "
    SELECT
        m.id_mahasiswa,
        m.nama,
        m.NIM,
        pm.tempat_magang,
        pm.status_pendaftaran,
        pm.tanggal_mulai,
        pm.tanggal_selesai,
        d.nama AS nama_dosen,
        MAX(lh.minggu_ke) AS minggu_terakhir
    FROM mahasiswa m
    LEFT JOIN pendaftaran_magang pm ON m.id_mahasiswa = pm.id_mahasiswa
    LEFT JOIN dosen d ON pm.id_dosen = d.id_dosen
    LEFT JOIN laporan_harian lh ON m.id_mahasiswa = lh.id_mahasiswa
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND (m.nama LIKE ? OR m.NIM LIKE ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status)) {
    if ($status == 'Belum Magang') {
        $query .= " AND pm.id_mahasiswa IS NULL ";
    } else {
        $query .= " AND pm.status_pendaftaran = ? ";
        $params[] = $status;
    }
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
    <title>Pemantauan Mahasiswa - SIMMAG</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/kaprodi.css">
    <link rel="stylesheet" href="assets/css/monitoring.css">
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
                <h4 class="mb-0 fw-bold page-title">Data Monitoring Mahasiswa Magang</h4>
                
                <form method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="status" class="form-select form-select-sm" style="width: 180px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="Aktif" <?= $status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="Selesai" <?= $status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="Menunggu" <?= $status == 'Menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="Belum Magang" <?= $status == 'Belum Magang' ? 'selected' : '' ?>>Belum Magang</option>
                    </select>

                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama atau NIM..." value="<?= htmlspecialchars($search) ?>" style="width: 240px;">
                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                </form>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="py-3 px-4">Nama Mahasiswa</th>
                            <th class="py-3">Tempat Magang</th>
                            <th class="py-3">Dosen Pembimbing</th>
                            <th class="py-3 col-progress">Progress Magang</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 px-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_mahasiswa as $mhs): ?>
                            <?php
                            $minggu = $mhs['minggu_terakhir'] ?? 0;
                            $progress = 0;

                            if (!empty($mhs['tanggal_mulai']) && !empty($mhs['tanggal_selesai'])) {
                                $mulai = strtotime($mhs['tanggal_mulai']);
                                $selesai = strtotime($mhs['tanggal_selesai']);
                                $today = time();
                                $durasi = $selesai - $mulai;
                                $berjalan = $today - $mulai;

                                if ($durasi > 0) {
                                    $progress = min(max(($berjalan / $durasi) * 100, 0), 100);
                                }
                            }
                            ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark"><?= $mhs['nama'] ?></div>
                                    <div class="text-muted sub-text"><?= $mhs['NIM'] ?></div>
                                </td>
                                <td class="py-3 text-muted">
                                    <?= $mhs['tempat_magang'] ?? '-' ?>
                                </td>
                                <td class="py-3">
                                    <div class="text-dark dosen-name">
                                        <?= $mhs['nama_dosen'] ?? '-' ?>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1 progress-info">
                                        <span class="progress-percent"><?= round($progress) ?>%</span>
                                        <span class="progress-week">Minggu <?= $minggu ?></span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" style="width:<?= $progress ?>%;"></div>
                                    </div>
                                </td>
                                <td class="py-3 text-center">
                                    <?php
                                    $statusClass = 'bg-secondary-subtle text-secondary';
                                    if ($mhs['status_pendaftaran'] == 'Aktif') {
                                        $statusClass = 'bg-success-subtle text-success';
                                    } elseif ($mhs['status_pendaftaran'] == 'Menunggu') {
                                        $statusClass = 'bg-warning-subtle text-warning';
                                    } elseif ($mhs['status_pendaftaran'] == 'Selesai') {
                                        $statusClass = 'bg-primary-subtle text-primary';
                                    }
                                    ?>
                                    <span class="badge <?= $statusClass ?> border px-3 py-1 rounded-pill">
                                        <?= $mhs['status_pendaftaran'] ?? 'Belum Magang' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <button class="btn btn-sm btn-light text-purple btn-detail"
                                        data-nama="<?= $mhs['nama'] ?>"
                                        data-nim="<?= $mhs['NIM'] ?>"
                                        data-tempat="<?= $mhs['tempat_magang'] ?? '-' ?>"
                                        data-dosen="<?= $mhs['nama_dosen'] ?? '-' ?>"
                                        data-status="<?= $mhs['status_pendaftaran'] ?? 'Belum Magang' ?>"
                                        data-progress="<?= round($progress) ?>"
                                        data-minggu="<?= $minggu ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
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
                <a href="../index.php" class="modal-logout-link"><button class="btn-logout">Ya, Keluar</button></a>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="profileModal">
        <div class="profile-modal-box">
            <div class="profile-modal-header">
                <h5>Info Profil</h5>
                <button class="profile-modal-close" id="btnTutupProfil">&times;</button>
            </div>
            <div class="profile-modal-avatar"><i class="bi bi-person"></i></div>
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

    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Detail Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <small class="text-muted">Nama</small>
                        <div class="fw-semibold" id="detailNama"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">NIM</small>
                        <div class="fw-semibold" id="detailNim"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Tempat Magang</small>
                        <div class="fw-semibold" id="detailTempat"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Dosen Pembimbing</small>
                        <div class="fw-semibold" id="detailDosen"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Status</small>
                        <div class="fw-semibold" id="detailStatus"></div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Progress</small>
                        <div class="fw-semibold">
                            <span id="detailProgress"></span>% - Minggu <span id="detailMinggu"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/kaprodi.js"></script>

    <script>
        const detailButtons = document.querySelectorAll('.btn-detail');
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

        detailButtons.forEach((btn) => {
            btn.addEventListener('click', function () {
                document.getElementById('detailNama').innerText = this.dataset.nama;
                document.getElementById('detailNim').innerText = this.dataset.nim;
                document.getElementById('detailTempat').innerText = this.dataset.tempat;
                document.getElementById('detailDosen').innerText = this.dataset.dosen;
                document.getElementById('detailStatus').innerText = this.dataset.status;
                document.getElementById('detailProgress').innerText = this.dataset.progress;
                document.getElementById('detailMinggu').innerText = this.dataset.minggu;
                
                detailModal.show();
            });
        });
    </script>
</body>
</html>