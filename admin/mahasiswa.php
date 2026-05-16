<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$page = "mahasiswa";
$filter_kota = $_GET['kota'] ?? '';

$count_query = "
    SELECT COUNT(*) as total 
    FROM mahasiswa
";

include '../config/paging.php';

$query = "
    SELECT
        m.*,
        pm.tempat_magang,
        pm.status_pendaftaran,
        pm.id_dosen,
        d.nama AS nama_dosen,
        p.kota
    FROM mahasiswa m
    LEFT JOIN pendaftaran_magang pm ON m.id_mahasiswa = pm.id_mahasiswa
    LEFT JOIN dosen d ON pm.id_dosen = d.id_dosen
    LEFT JOIN pengajuan p ON pm.id_pengajuan = p.id_pengajuan
";

if ($filter_kota) {
    $query .= " WHERE p.kota = :kota ";
}

$query .= "
    ORDER BY m.nama ASC
    LIMIT $limit
    OFFSET $offset
";

$stmtMahasiswa = $conn->prepare($query);

if ($filter_kota) {
    $stmtMahasiswa->bindParam(':kota', $filter_kota);
}

$stmtMahasiswa->execute();
$data_mahasiswa = $stmtMahasiswa->fetchAll(PDO::FETCH_ASSOC);

$stmtDosen = $conn->query("
    SELECT * FROM dosen 
    ORDER BY nama ASC
");
$data_dosen = $stmtDosen->fetchAll(PDO::FETCH_ASSOC);

$stmtKota = $conn->query("
    SELECT DISTINCT kota 
    FROM pengajuan 
    WHERE kota IS NOT NULL AND kota != '' 
    ORDER BY kota ASC
");
$data_kota = $stmtKota->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Data Mahasiswa - Sistem Magang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/mahasiswa.css">
</head>

<body>
    <div class="d-flex">
        <!-- SIDEBAR -->
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

        <!-- CONTENT AREA -->
        <div class="p-4 w-100">
            <!-- HEADER -->
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

            <!-- PAGE TITLE -->
            <div class="mb-4">
                <h4 style="color:#2d6cdf;">Manajemen Mahasiswa</h4>
                <p class="text-muted mb-0" style="font-size:14px;">Kelola data mahasiswa magang di sini.</p>
            </div>

            <!-- DATA TABLE CARD -->
            <div class="table-card">
                <div class="table-header">
                    <h5 class="m-0" style="color: #1e293b;">Daftar Mahasiswa</h5>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-funnel text-muted me-2"></i>
                            <form method="GET">
                                <select name="kota" class="form-select form-select-sm" onchange="this.form.submit()" style="border-radius: 8px; font-size: 13px; color: #475569; width: 140px; border-color: #e2e8f0;">
                                    <option value="">Semua Kota</option>
                                    <?php foreach ($data_kota as $kota): ?>
                                        <option value="<?= $kota['kota'] ?>" <?= ($filter_kota == $kota['kota']) ? 'selected' : '' ?>>
                                            <?= $kota['kota'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama Lengkap</th>
                                <th>Tempat Magang</th>
                                <th>Kota</th>
                                <th>Status Magang</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_mahasiswa as $mhs): ?>
                                <tr>
                                    <td><?= $mhs['NIM'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="profile-avatar" style="width: 32px; height: 32px; font-size: 14px; margin-right: 12px; background: #eef2ff; color: #2563eb;">
                                                <?= substr($mhs['nama'], 0, 1) ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600;"><?= $mhs['nama'] ?></div>
                                                <div style="font-size: 11px; color: #64748b;">Pembimbing: <?= $mhs['nama_dosen'] ?? '-' ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $mhs['tempat_magang'] ?? '-' ?></td>
                                    <td>
                                        <span style="font-size: 13px; color: #64748b;">
                                            <i class="bi bi-geo-alt-fill me-1" style="color: #cbd5e1;"></i><?= $mhs['kota'] ?? '-' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-status status-<?= strtolower($mhs['status_pendaftaran'] ?? 'belum') ?>">
                                            <?= $mhs['status_pendaftaran'] ?? 'Belum Magang' ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="detail_mahasiswa.php?id=<?= $mhs['id_mahasiswa'] ?>" class="btn-action" title="Detail Mahasiswa">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="#" class="btn-action btn-edit"
                                            data-id="<?= $mhs['id_mahasiswa'] ?>"
                                            data-nama="<?= $mhs['nama'] ?>"
                                            data-nim="<?= $mhs['NIM'] ?>"
                                            data-tempat="<?= $mhs['tempat_magang'] ?>"
                                            data-kota="<?= $mhs['kota'] ?>"
                                            data-status="<?= $mhs['status_pendaftaran'] ?>"
                                            data-dosen="<?= $mhs['id_dosen'] ?? '' ?>"
                                            title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="#" class="btn-action btn-delete" data-id="<?= $mhs['id_mahasiswa'] ?>" title="Hapus Data">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php include '../config/pagination.php'; ?>

            </div>
        </div>
    </div>

    <!-- LOGOUT MODAL -->
    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div class="modal-icon">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <h5>Keluar dari Akun?</h5>
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="modal-actions">
                <button class="btn-cancel" id="btnBatal">Batal</button>
                <a href="login.php" style="flex:1;text-decoration:none;"><button class="btn-logout" style="width:100%;">Ya, Keluar</button></a>
            </div>
        </div>
    </div>

    <!-- PROFILE MODAL -->
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

    <!-- EDIT MODAL -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-box" style="width: 500px; text-align: left; padding: 25px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="m-0" style="color: #1e293b;"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Mahasiswa</h5>
                <button class="profile-modal-close" id="btnTutupEdit">&times;</button>
            </div>

            <form action="edit_mahasiswa.php" method="POST">
                <input type="hidden" name="id_mahasiswa" id="edit-id">

                <div class="mb-3">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Nomor Induk Mahasiswa (NIM)</label>
                    <input type="text" id="edit-nim" name="nim" class="form-control" style="background:#f8fafc; font-size:14px;" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Nama Lengkap</label>
                    <input type="text" id="edit-nama" name="nama" class="form-control" style="font-size:14px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Tempat Magang</label>
                    <input type="text" id="edit-tempat" name="tempat_magang" class="form-control" style="font-size:14px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Kota Penempatan</label>
                    <input type="text" id="edit-kota" name="kota" class="form-control" style="font-size:14px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Dosen Pembimbing</label>
                    <select class="form-select" id="edit-dosen" name="id_dosen" style="font-size:14px;">
                        <option value="">Pilih Pembimbing</option>
                        <?php foreach ($data_dosen as $dosen): ?>
                            <option value="<?= $dosen['id_dosen'] ?>"><?= $dosen['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label" style="font-size:13px; font-weight:600; color:#475569;">Status Magang</label>
                    <select class="form-select" id="edit-status" name="status_pendaftaran" style="font-size:14px;">
                        <option value="Aktif">Aktif</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Menunggu">Menunggu</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-cancel" id="btnBatalEdit" style="font-size:14px; font-weight:600;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="font-size:14px; font-weight:600; background: linear-gradient(135deg, #2563eb, #7c3aed); border:none; padding: 10px 20px; border-radius: 8px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h5>Hapus Data?</h5>
            <p>Apakah Anda yakin ingin menghapus data mahasiswa ini? Tindakan ini tidak dapat dibatalkan.</p>

            <form action="hapus_mahasiswa.php" method="POST">
                <input type="hidden" name="id_mahasiswa" id="delete-id">
                <div class="modal-actions mt-4">
                    <button type="button" class="btn-cancel" id="btnBatalHapus">Batal</button>
                    <button type="submit" class="btn-logout" style="flex:1;">Ya, Hapus</button>
                </div>
            </form>

        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script src="assets/js/mahasiswa.js"></script>
</body>

</html>