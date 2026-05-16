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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nidn    = $_POST['nidn'];
    $nip     = $_POST['nip'];
    $nama    = $_POST['nama'];
    $email   = $_POST['email'];
    $telepon = $_POST['telepon'];
    $status  = $_POST['status'];

    $password = password_hash('123456', PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO dosen (NIDN, NIP, nama, email, no_hp, password, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $nidn,
        $nip,
        $nama,
        $email,
        $telepon,
        $password,
        $status
    ]);

    header("Location: dosen.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen - Sistem Magang</title>

    <!-- CSS External -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/tambahDosen.css">
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

        <!-- MAIN CONTENT -->
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
                <h4 style="color:#2d6cdf;">Tambah Dosen Pembimbing</h4>
                <p class="text-muted mb-0" style="font-size:14px;">Isi formulir di bawah ini untuk mendaftarkan dosen pembimbing magang.</p>
            </div>

            <!-- FORM CARD -->
            <div class="card form-card shadow-sm border-0 p-4">
                <form action="" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIDN</label>
                            <input type="text" class="form-control custom-input" name="nidn" placeholder="Masukkan NIDN Dosen" required>
                            <div class="form-text" style="font-size: 11px;">Nomor Induk Dosen Nasional (Wajib)</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP</label>
                            <input type="text" class="form-control custom-input" name="nip" placeholder="Masukkan NIP Dosen">
                            <div class="form-text" style="font-size: 11px;">Kosongkan jika dosen non-PNS</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap (Beserta Gelar)</label>
                            <input type="text" class="form-control custom-input" name="nama" placeholder="Contoh: Dr. Budi Santoso, M.Kom." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email Aktif</label>
                            <input type="email" class="form-control custom-input" name="email" placeholder="contoh: dosen@magang.ac.id" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Telepon / WhatsApp</label>
                            <input type="text" class="form-control custom-input" name="telepon" placeholder="Contoh: 081234567890">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Dosen</label>
                            <select class="form-select custom-input" name="status" required>
                                <option value="Aktif">Aktif Mengajar</option>
                                <option value="Cuti">Sedang Cuti</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #f1f5f9;">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="dosen.php" class="btn btn-cancel-form btn-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-save-form btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <div class="modal-icon text-danger">
                <i class="bi bi-box-arrow-right"></i>
            </div>
            <h5>Keluar dari Akun?</h5>
            <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            <div class="modal-actions">
                <button class="btn-cancel" id="btnBatal">Batal</button>
                <a href="login.php" style="flex:1;text-decoration:none;">
                    <button class="btn-logout w-100">Ya, Keluar</button>
                </a>
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

            <div class="p-3">
                <div class="profile-detail d-flex align-items-center mb-2">
                    <div class="profile-detail-icon me-3"><i class="bi bi-envelope"></i></div>
                    <div>
                        <div class="profile-detail-label small text-muted">Surel</div>
                        <div class="profile-detail-value fw-bold">admin@magang.ac.id</div>
                    </div>
                </div>
                <div class="profile-detail d-flex align-items-center mb-2">
                    <div class="profile-detail-icon me-3"><i class="bi bi-telephone"></i></div>
                    <div>
                        <div class="profile-detail-label small text-muted">No. Telepon</div>
                        <div class="profile-detail-value fw-bold">+62 812-3456-7890</div>
                    </div>
                </div>
                <div class="profile-detail d-flex align-items-center mb-2">
                    <div class="profile-detail-icon me-3"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <div class="profile-detail-label small text-muted">Peran</div>
                        <div class="profile-detail-value fw-bold">Admin Utama</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>

</body>

</html>