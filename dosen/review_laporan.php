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

$page = "review_laporan";
$status_filter = $_GET['status'] ?? 'Semua';

$stmtDosen = $conn->prepare("SELECT * FROM dosen WHERE id_dosen = ?");
$stmtDosen->execute([$user['id']]);
$dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);

$nama_dosen = $dosen['nama'];
$nip = $dosen['NIP'];

$query = "
    SELECT
        la.*,
        m.nama,
        m.NIM
    FROM laporan_akhir la
    JOIN mahasiswa m ON la.id_mahasiswa = m.id_mahasiswa
    JOIN pendaftaran_magang pm ON la.id_mahasiswa = pm.id_mahasiswa
    WHERE pm.id_dosen = ?
";

$params = [$user['id']];

if ($status_filter != 'Semua') {
    $query .= " AND la.status_review = ? ";
    $params[] = $status_filter;
}

$query .= " ORDER BY la.tanggal_upload DESC ";

$stmtLaporan = $conn->prepare($query);
$stmtLaporan->execute($params);
$data_laporan = $stmtLaporan->fetchAll(PDO::FETCH_ASSOC);

$stmtCount = $conn->prepare("
    SELECT
        la.status_review,
        COUNT(*) as total
    FROM laporan_akhir la
    JOIN pendaftaran_magang pm ON la.id_mahasiswa = pm.id_mahasiswa
    WHERE pm.id_dosen = ?
    GROUP BY la.status_review
");

$stmtCount->execute([$user['id']]);
$countData = $stmtCount->fetchAll(PDO::FETCH_ASSOC);

$total_menunggu = 0;
$total_disetujui = 0;
$total_ditolak = 0;

foreach ($countData as $row) {
    if ($row['status_review'] == 'Menunggu') {
        $total_menunggu = $row['total'];
    } elseif ($row['status_review'] == 'Disetujui') {
        $total_disetujui = $row['total'];
    } elseif ($row['status_review'] == 'Ditolak') {
        $total_ditolak = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Laporan Akhir - SIMMAG</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/dosen.css">
    <link rel="stylesheet" href="assets/css/review.css">
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
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php unset($_SESSION['error']); endif; ?>

        <div class="table-card">
            <h4 class="mb-3 fw-bold" style="color: #1e293b;">Penilaian Laporan Akhir Magang</h4>

            <div class="filter-pills">
                <a href="<?= $total_menunggu > 0 ? '?status=Menunggu' : '#' ?>" class="filter-pill <?= $status_filter == 'Menunggu' ? 'active' : '' ?> <?= $total_menunggu == 0 ? 'disabled opacity-50' : '' ?>">Perlu Penilaian (<?= $total_menunggu ?>)</a>
                <a href="<?= $total_disetujui > 0 ? '?status=Disetujui' : '#' ?>" class="filter-pill <?= $status_filter == 'Disetujui' ? 'active' : '' ?> <?= $total_disetujui == 0 ? 'disabled opacity-50' : '' ?>">Sudah Dinilai (<?= $total_disetujui ?>)</a>
                <a href="<?= $total_ditolak > 0 ? '?status=Ditolak' : '#' ?>" class="filter-pill <?= $status_filter == 'Ditolak' ? 'active' : '' ?> <?= $total_ditolak == 0 ? 'disabled opacity-50' : '' ?>">Revisi Laporan (<?= $total_ditolak ?>)</a>
                <a href="?status=Semua" class="filter-pill <?= $status_filter == 'Semua' ? 'active' : '' ?>">Semua</a>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle">
                    <thead style="background-color: #f8fafc; color: #64748b; font-size: 13px; text-transform: uppercase;">
                        <tr>
                            <th class="py-3 px-4 rounded-start" style="font-weight: 600;">Mahasiswa</th>
                            <th class="py-3" style="font-weight: 600;">Tanggal Submit</th>
                            <th class="py-3" style="font-weight: 600;">Judul Laporan</th>
                            <th class="py-3" style="font-weight: 600;">File</th>
                            <th class="py-3 px-4 rounded-end text-end" style="font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_laporan as $lap): ?>
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="fw-bold text-dark"><?= $lap['nama'] ?></div>
                                    <div class="text-muted" style="font-size: 13px;"><?= $lap['NIM'] ?></div>
                                </td>
                                <td class="py-4">
                                    <div class="text-dark fw-semibold"><?= date('d F Y', strtotime($lap['tanggal_upload'])) ?></div>
                                    <div class="text-muted" style="font-size: 13px;"><?= date('H:i', strtotime($lap['tanggal_upload'])) ?> WIB</div>
                                </td>
                                <td class="py-4"><?= htmlspecialchars($lap['judul_laporan']) ?></td>
                                <td class="py-4">
                                    <a href="../uploads/<?= $lap['file_laporan'] ?>" target="_blank" class="btn btn-sm btn-light border text-primary">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>Unduh
                                    </a>
                                </td>
                                <td class="py-4 px-4 text-end">
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalLaporan<?= $lap['id_laporan_akhir'] ?>">Nilai Laporan</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php foreach ($data_laporan as $lap): ?>
        <div class="modal fade" id="modalLaporan<?= $lap['id_laporan_akhir'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0" style="border-radius: 16px;">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" style="color: #1e293b;">Penilaian Laporan Akhir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 18px; font-weight: 600;">
                                <?= strtoupper(substr($lap['nama'], 0, 1)) ?>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold"><?= $lap['nama'] ?></h6>
                                <div class="text-muted" style="font-size: 13px;"><?= $lap['NIM'] ?></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: #475569;">Judul Laporan</label>
                            <div class="p-3 bg-light" style="border-radius: 8px;"><?= htmlspecialchars($lap['judul_laporan']) ?></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size: 13px; color: #475569;">File Laporan</label>
                            <div>
                                <a href="../uploads/<?= $lap['file_laporan'] ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-pdf me-2"></i><?= $lap['file_laporan'] ?>
                                </a>
                            </div>
                        </div>

                        <form action="review_laporan_action.php" method="POST">
                            <input type="hidden" name="id_laporan_akhir" value="<?= $lap['id_laporan_akhir'] ?>">
                            <input type="hidden" name="id_mahasiswa" value="<?= $lap['id_mahasiswa'] ?>">

                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="font-size: 13px; color: #475569;">Nilai Akhir</label>
                                <input type="number" name="nilai" class="form-control" min="0" max="100" required style="border-radius: 8px;">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="font-size: 13px; color: #475569;">Catatan Dosen</label>
                                <textarea name="catatan_dosen" class="form-control" rows="4" style="border-radius: 8px; resize: none;" placeholder="Berikan evaluasi atau revisi untuk mahasiswa..."></textarea>
                            </div>
                            
                            <div class="modal-footer border-top-0 px-0 pb-0">
                                <button type="submit" name="action" value="reject" class="btn btn-outline-danger fw-semibold">Minta Revisi</button>
                                <button type="submit" name="action" value="approve" class="btn btn-success fw-semibold px-4">Setujui & Nilai</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div class="modal-icon"><i class="bi bi-box-arrow-right"></i></div>
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
            <div class="profile-modal-avatar"><i class="bi bi-person"></i></div>
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