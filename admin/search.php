<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$page = "search";
$keyword = $_GET['q'] ?? '';

$hasil_mahasiswa = [];
$hasil_dosen = [];
$hasil_pengajuan = [];

if ($keyword !== '') {
    $search_param = "%$keyword%";

    $stmtMhs = $conn->prepare("SELECT * FROM mahasiswa WHERE nama LIKE ? OR NIM LIKE ? LIMIT 6");
    $stmtMhs->execute([$search_param, $search_param]);
    $hasil_mahasiswa = $stmtMhs->fetchAll(PDO::FETCH_ASSOC);

    $stmtDsn = $conn->prepare("SELECT * FROM dosen WHERE nama LIKE ? OR NIDN LIKE ? LIMIT 6");
    $stmtDsn->execute([$search_param, $search_param]);
    $hasil_dosen = $stmtDsn->fetchAll(PDO::FETCH_ASSOC);

    $stmtPengajuan = $conn->prepare("SELECT * FROM pengajuan WHERE kota LIKE ? LIMIT 6");
    $stmtPengajuan->execute([$search_param]);
    $hasil_pengajuan = $stmtPengajuan->fetchAll(PDO::FETCH_ASSOC);
}

$total_hasil = count($hasil_mahasiswa) + count($hasil_dosen) + count($hasil_pengajuan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pencarian - Sistem Magang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    
    <style>
        .search-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s ease;
            height: 100%;
        }
        .search-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }
        .search-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
        }
        .avatar-search {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .avatar-mhs { background: #eef2ff; color: #2563eb; }
        .avatar-dsn { background: #f0fdf4; color: #16a34a; }
        .avatar-pgj { background: #fffbeb; color: #d97706; }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px dashed #cbd5e1;
        }
        .empty-state i {
            font-size: 48px;
            color: #94a3b8;
            margin-bottom: 16px;
        }
    </style>
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

        <div class="p-4 w-100" style="background-color: #f8fafc; min-height: 100vh;">
            
            <div class="header-top mb-4 bg-white p-3 rounded-4 shadow-sm" style="border: 1px solid #e2e8f0;">
                <form action="search.php" method="GET" class="search-box m-0" style="flex:1; max-width:500px;">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Cari mahasiswa, dosen, atau pengajuan..." value="<?= htmlspecialchars($keyword) ?>" required style="border:none; width:100%; outline:none; background:transparent;">
                    <button type="submit" style="display:none;"></button>
                </form>
                
                <div class="profile-section">
                    <div class="profile-wrapper">
                        <div class="profile-info" style="cursor:pointer;">
                            <div class="profile-avatar"><i class="bi bi-person"></i></div>
                            <div class="profile-text">
                                <div class="name">Admin Sistem</div>
                                <div class="email">admin@magang.ac.id</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h4 style="color:#2d6cdf; font-weight:700;">Hasil Pencarian</h4>
                <p class="text-muted mb-0" style="font-size:14px;">
                    Menemukan <span class="badge bg-primary rounded-pill"><?= $total_hasil ?></span> hasil untuk kata kunci <strong>"<?= htmlspecialchars($keyword) ?>"</strong>
                </p>
            </div>

            <?php if($keyword === ''): ?>
                <div class="empty-state">
                    <i class="bi bi-keyboard"></i>
                    <h5 class="text-dark fw-bold">Ketik sesuatu untuk mulai mencari</h5>
                    <p class="text-muted" style="font-size:14px;">Gunakan kotak pencarian di atas untuk menemukan data Mahasiswa, Dosen, atau Pengajuan Magang.</p>
                </div>
            <?php elseif($total_hasil == 0): ?>
                <div class="empty-state">
                    <i class="bi bi-search"></i>
                    <h5 class="text-dark fw-bold">Tidak ada hasil ditemukan</h5>
                    <p class="text-muted" style="font-size:14px;">Maaf, kami tidak menemukan data yang cocok dengan <strong>"<?= htmlspecialchars($keyword) ?>"</strong>. Coba kata kunci lain.</p>
                </div>
            <?php else: ?>

                <div class="row g-4">
                    
                    <?php if(!empty($hasil_mahasiswa)): ?>
                    <div class="col-12">
                        <div class="search-section-title d-flex align-items-center">
                            <i class="bi bi-people-fill text-primary me-2"></i> Mahasiswa
                        </div>
                        <div class="row g-3">
                            <?php foreach($hasil_mahasiswa as $mhs): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="search-card d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-search avatar-mhs me-3">
                                            <?= substr($mhs['nama'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 15px;"><?= $mhs['nama'] ?></div>
                                            <div style="font-size: 12px; color: #64748b;">NIM: <?= $mhs['NIM'] ?></div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <a href="mahasiswa.php?search=<?= $mhs['NIM'] ?>" class="btn btn-sm text-primary fw-medium" style="background:#eef2ff; width:100%; border-radius:8px;">Lihat di Data Mahasiswa</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($hasil_dosen)): ?>
                    <div class="col-12 mt-5">
                        <div class="search-section-title d-flex align-items-center">
                            <i class="bi bi-person-badge-fill text-success me-2"></i> Dosen Pembimbing
                        </div>
                        <div class="row g-3">
                            <?php foreach($hasil_dosen as $dsn): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="search-card d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-search avatar-dsn me-3">
                                            <?= substr($dsn['nama'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 15px;"><?= $dsn['nama'] ?></div>
                                            <div style="font-size: 12px; color: #64748b;">NIDN: <?= $dsn['NIDN'] ?></div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <a href="dosen.php?search=<?= $dsn['NIDN'] ?>" class="btn btn-sm text-success fw-medium" style="background:#f0fdf4; width:100%; border-radius:8px;">Lihat di Data Dosen</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($hasil_pengajuan)): ?>
                    <div class="col-12 mt-5">
                        <div class="search-section-title d-flex align-items-center">
                            <i class="bi bi-send-fill text-warning me-2"></i> Pengajuan Magang
                        </div>
                        <div class="row g-3">
                            <?php foreach($hasil_pengajuan as $pgj): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="search-card d-flex flex-column">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-search avatar-pgj me-3">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: #1e293b; font-size: 15px;">Pengajuan di <?= $pgj['kota'] ?></div>
                                            <div style="font-size: 12px; color: #64748b;"><i class="bi bi-geo-alt-fill"></i> Data Pengajuan Kota</div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <a href="pengajuan.php?search=<?= urlencode($pgj['kota']) ?>" class="btn btn-sm text-warning fw-medium" style="background:#fffbeb; width:100%; border-radius:8px;">Lihat di Pengajuan</a>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>