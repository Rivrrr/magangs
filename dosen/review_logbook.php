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

$page = "review_logbook";

$status_filter = $_GET['status'] ?? 'Semua';

$stmtDosen = $conn->prepare("
    SELECT *
    FROM dosen
    WHERE id_dosen = ?
");

$stmtDosen->execute([$user['id']]);

$dosen = $stmtDosen->fetch(PDO::FETCH_ASSOC);

$nama_dosen = $dosen['nama'];
$nip = $dosen['NIP'];

$query = "
    SELECT
        lh.*,
        m.nama,
        m.NIM,
        pm.tempat_magang
    FROM laporan_harian lh
    JOIN mahasiswa m ON lh.id_mahasiswa = m.id_mahasiswa
    LEFT JOIN pendaftaran_magang pm ON lh.id_mahasiswa = pm.id_mahasiswa
    WHERE pm.id_dosen = ?
";

$params = [$user['id']];

if ($status_filter != 'Semua') {
    $query .= " AND lh.status = ? ";
    $params[] = $status_filter;
}

$query .= " ORDER BY lh.tanggal_submit DESC ";

$stmtLogbook = $conn->prepare($query);
$stmtLogbook->execute($params);

$data_logbook = $stmtLogbook->fetchAll(PDO::FETCH_ASSOC);

$stmtCount = $conn->prepare("
    SELECT
        lh.status,
        COUNT(*) as total
    FROM laporan_harian lh
    JOIN pendaftaran_magang pm ON lh.id_mahasiswa = pm.id_mahasiswa
    WHERE pm.id_dosen = ?
    GROUP BY lh.status
");

$stmtCount->execute([$user['id']]);

$countData = $stmtCount->fetchAll(PDO::FETCH_ASSOC);

$total_menunggu = 0;
$total_disetujui = 0;
$total_ditolak = 0;

foreach ($countData as $row) {
    if ($row['status'] == 'Menunggu') {
        $total_menunggu = $row['total'];
    } elseif ($row['status'] == 'Disetujui') {
        $total_disetujui = $row['total'];
    } elseif ($row['status'] == 'Ditolak') {
        $total_ditolak = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Logbook - SIMMAG</title>
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
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-card">
            <h4 class="mb-3 fw-bold" style="color: #1e293b;">Persetujuan Logbook Harian</h4>

            <div class="filter-pills">
                <?php if ($total_menunggu > 0): ?>
                    <a href="?status=Menunggu" class="filter-pill <?= $status_filter == 'Menunggu' ? 'active' : '' ?>" style="text-decoration: none;">Menunggu Persetujuan (<?= $total_menunggu ?>)</a>
                <?php else: ?>
                    <span class="filter-pill" style="opacity: .5; cursor: not-allowed;">Menunggu Persetujuan (0)</span>
                <?php endif; ?>

                <?php if ($total_disetujui > 0): ?>
                    <a href="?status=Disetujui" class="filter-pill <?= $status_filter == 'Disetujui' ? 'active' : '' ?>" style="text-decoration: none;">Telah Disetujui (<?= $total_disetujui ?>)</a>
                <?php else: ?>
                    <span class="filter-pill" style="opacity: .5; cursor: not-allowed;">Telah Disetujui (0)</span>
                <?php endif; ?>

                <?php if ($total_ditolak > 0): ?>
                    <a href="?status=Ditolak" class="filter-pill <?= $status_filter == 'Ditolak' ? 'active' : '' ?>" style="text-decoration: none;">Perlu Revisi (<?= $total_ditolak ?>)</a>
                <?php else: ?>
                    <span class="filter-pill" style="opacity: .5; cursor: not-allowed;">Perlu Revisi (0)</span>
                <?php endif; ?>

                <a href="?status=Semua" class="filter-pill <?= $status_filter == 'Semua' ? 'active' : '' ?>" style="text-decoration: none;">Semua</a>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle">
                    <thead style="background-color: #f8fafc; color: #64748b; font-size: 13px; text-transform: uppercase;">
                        <tr>
                            <th class="py-3 px-4 rounded-start" style="font-weight: 600;">Mahasiswa</th>
                            <th class="py-3" style="font-weight: 600;">Tanggal / Minggu</th>
                            <th class="py-3" style="font-weight: 600;">Ringkasan Aktivitas</th>
                            <th class="py-3" style="font-weight: 600;">Jam Kerja</th>
                            <th class="py-3 px-4 rounded-end text-end" style="font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_logbook as $log): ?>
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark"><?= $log['nama'] ?></div>
                                    <div class="text-muted" style="font-size: 12px;"><?= $log['NIM'] ?></div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold text-dark" style="font-size: 13px;"><?= date('l, d F Y', strtotime($log['tanggal_submit'])) ?></div>
                                    <span class="badge bg-light text-primary border border-primary-subtle mt-1">Minggu <?= $log['minggu_ke'] ?></span>
                                </td>
                                <td class="py-3">
                                    <div class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($log['kegiatan']) ?></div>
                                    <div class="text-muted" style="font-size: 12px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($log['tempat_magang']) ?></div>
                                </td>
                                <td class="py-3 text-muted"><?= substr($log['jam_masuk'], 0, 5) ?> - <?= substr($log['jam_keluar'], 0, 5) ?></td>
                                <td class="px-4 py-3 text-end">
                                    <button class="btn btn-sm btn-outline-success me-1" title="Setujui" data-bs-toggle="modal" data-bs-target="#modalReview<?= $log['id_laporan_harian'] ?>"><i class="bi bi-check-lg"></i></button>
                                    <button class="btn btn-sm btn-outline-danger me-2" title="Tolak / Revisi" data-bs-toggle="modal" data-bs-target="#modalReview<?= $log['id_laporan_harian'] ?>"><i class="bi bi-x-lg"></i></button>
                                    <button class="btn btn-sm btn-light text-primary" title="Lihat Detail" data-bs-toggle="modal" data-bs-target="#modalReview<?= $log['id_laporan_harian'] ?>"><i class="bi bi-eye"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php foreach ($data_logbook as $log): ?>
        <div class="modal fade" id="modalReview<?= $log['id_laporan_harian'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0" style="border-radius: 16px;">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" style="color: #1e293b;">Detail Logbook Mahasiswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 18px; font-weight: 600;">
                                <?= strtoupper(substr($log['nama'], 0, 1)) ?>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold"><?= $log['nama'] ?></h6>
                                <div class="text-muted" style="font-size: 13px;"><?= $log['NIM'] ?> • <?= $log['tempat_magang'] ?></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Tanggal Aktivitas</label>
                                <p class="text-dark mb-0 fw-medium"><?= date('l, d F Y', strtotime($log['tanggal_submit'])) ?></p>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Jam Magang</label>
                                <p class="text-dark mb-0 fw-medium"><?= substr($log['jam_masuk'], 0, 5) ?> - <?= substr($log['jam_keluar'], 0, 5) ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Minggu Ke</label>
                                <p class="text-dark mb-0 fw-medium"><?= $log['minggu_ke'] ?></p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Deskripsi Aktivitas</label>
                            <div class="p-3 bg-light" style="border-radius: 8px; color: #1e293b; font-size: 14px;">
                                <?= nl2br(htmlspecialchars($log['kegiatan'])) ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Lampiran</label>
                            <div>
                                <a href="../uploads/<?= $log['file_pendukung'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-2"></i><?= $log['file_pendukung'] ?>
                                </a>
                            </div>
                        </div>

                        <form action="review_action.php" method="POST">
                            <input type="hidden" name="id_laporan_harian" value="<?= $log['id_laporan_harian'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Catatan Dosen (Opsional)</label>
                                <textarea name="catatan_dosen" class="form-control" rows="3" style="border-radius: 8px; resize: none;" placeholder="Beri catatan atau feedback untuk mahasiswa ini..."></textarea>
                            </div>
                            <div class="modal-footer border-top-0 pt-0 px-0 pb-0">
                                <button type="submit" name="action" value="reject" class="btn btn-outline-danger fw-semibold" style="border-radius: 8px;">Minta Revisi</button>
                                <button type="submit" name="action" value="approve" class="btn btn-success fw-semibold px-4" style="border-radius: 8px;">Setujui Logbook</button>
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