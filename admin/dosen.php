<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$page = "dosen";
$filter_status = $_GET['status'] ?? '';

$count_query = "
    SELECT COUNT(*) as total 
    FROM dosen
";

include '../config/paging.php';

$query = "
    SELECT d.*, COUNT(DISTINCT pm.id_mahasiswa) AS total_bimbingan
    FROM dosen d
    LEFT JOIN pendaftaran_magang pm ON d.id_dosen = pm.id_dosen
    WHERE 1=1
";
$params = [];

if (!empty($filter_status)) {
    $query .= " AND d.status = ? ";
    $params[] = $filter_status;
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

$stmtAdmin = $conn->prepare("
    SELECT * FROM admin 
    WHERE id_admin = ?
");
$stmtAdmin->execute([$_SESSION['user']['id']]);
$admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Dosen - Sistem Magang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/dosen.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar d-flex flex-column">
            <div>
                <div class="logo-container">
                    <img src="assets/logo.png" class="logo-sidebar" alt="Logo">
                </div>
                <a href="beranda.php" class="<?= ($page == 'beranda') ? 'active' : '' ?>">
                    <i class="bi bi-grid me-2"></i> Beranda
                </a>
                <a href="mahasiswa.php" class="<?= ($page == 'mahasiswa') ? 'active' : '' ?>">
                    <i class="bi bi-people me-2"></i> Mahasiswa
                </a>
                <a href="dosen.php" class="<?= ($page == 'dosen') ? 'active' : '' ?>">
                    <i class="bi bi-person-badge me-2"></i> Dosen
                </a>
                <a href="pengajuan.php" class="<?= ($page == 'pengajuan') ? 'active' : '' ?>">
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
                <h4 style="color:#2d6cdf;">Manajemen Dosen Pembimbing</h4>
                <p class="text-muted mb-0" style="font-size:14px;">Kelola data dosen pembimbing magang (Teknik Informatika) di sini.</p>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h5 class="m-0" style="color: #1e293b;">Daftar Dosen</h5>
                    <div class="d-flex gap-3 align-items-center">
                        <form method="GET">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-funnel text-muted me-2"></i>
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="border-radius: 8px; font-size: 13px; color: #475569; width: 160px; border-color: #e2e8f0;">
                                    <option value="">Semua Status</option>
                                    <option value="Aktif" <?= $filter_status == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="Cuti" <?= $filter_status == 'Cuti' ? 'selected' : '' ?>>Cuti</option>
                                </select>
                            </div>
                        </form>
                        <a href="tambahDosen.php" class="btn-add text-decoration-none">
                            <i class="bi bi-plus-lg me-2"></i> Tambah Dosen
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>NIDN</th>
                                <th>NIP</th>
                                <th>Nama Lengkap</th>
                                <th>Mahasiswa Bimbingan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_dosen as $dsn): ?>
                                <tr>
                                    <td><code class="text-primary fw-bold"><?= $dsn['NIDN'] ?></code></td>
                                    <td>
                                        <span class="text-muted">
                                            <?= !empty($dsn['NIP']) ? $dsn['NIP'] : '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="profile-avatar" style="width: 32px; height: 32px; font-size: 14px; margin-right: 12px; background: #eef2ff; color: #2563eb;">
                                                <?= substr($dsn['nama'], 0, 1) ?>
                                            </div>
                                            <span style="font-weight: 600;"><?= $dsn['nama'] ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-bimbingan">
                                            <i class="bi bi-people-fill me-1"></i> <?= $dsn['total_bimbingan'] ?> Mahasiswa
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-status status-<?= strtolower($dsn['status']) ?>">
                                            <?= $dsn['status'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn-action btn-edit"
                                            data-id="<?= $dsn['id_dosen'] ?>"
                                            data-nidn="<?= $dsn['NIDN'] ?>"
                                            data-nama="<?= $dsn['nama'] ?>"
                                            data-status="<?= $dsn['status'] ?>"
                                            title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="#" class="btn-action btn-delete" data-id="<?= $dsn['id_dosen'] ?>" title="Hapus Data">
                                            <i class="bi bi-trash-fill"></i>
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
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div class="modal-icon"><i class="bi bi-box-arrow-right"></i></div>
            <h5>Keluar dari Akun?</h5>
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="modal-actions">
                <button class="btn-cancel" id="btnBatal">Batal</button>
                <a href="login.php" style="flex:1;text-decoration:none;"><button class="btn-logout" style="width:100%;">Ya, Keluar</button></a>
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

    <div class="modal-overlay" id="editModal">
        <div class="modal-box" style="width: 500px; text-align: left; padding: 25px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="m-0" style="color: #1e293b;"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Dosen</h5>
                <button class="profile-modal-close" id="btnTutupEdit">&times;</button>
            </div>

            <form action="edit_dosen.php" method="POST">
                <input type="hidden" name="id_dosen" id="edit-id-dosen">

                <div class="mb-3">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">NIDN</label>
                    <input type="text" id="edit-nidn" class="form-control" style="background:#f8fafc; font-size:14px;" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit-nama" class="form-control" style="font-size:14px;">
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Status</label>
                    <select name="status" id="edit-status" class="form-select" style="font-size:14px;">
                        <option value="Aktif">Aktif</option>
                        <option value="Cuti">Cuti</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-cancel" id="btnBatalEdit" style="font-size:14px; font-weight:600;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="font-size:14px; font-weight:600; background: linear-gradient(135deg, #2563eb, #7c3aed); border:none; padding: 10px 20px; border-radius: 8px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <h5>Hapus Data Dosen?</h5>
            <p>Apakah Anda yakin ingin menghapus data dosen ini? Tindakan ini tidak dapat dibatalkan.</p>

            <form action="hapus_dosen.php" method="POST">
                <input type="hidden" name="id_dosen" id="delete-id-dosen">
                <div class="modal-actions mt-4">
                    <button type="button" class="btn-cancel" id="btnBatalHapus">Batal</button>
                    <button type="submit" class="btn-logout" style="flex:1;">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script src="assets/js/dosen.js"></script>
</body>
</html>