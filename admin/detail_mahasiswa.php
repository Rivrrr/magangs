<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'];

$stmt = $conn->prepare("
    SELECT
        m.*,
        pm.*,
        d.nama AS nama_dosen
    FROM mahasiswa m
    LEFT JOIN pendaftaran_magang pm ON m.id_mahasiswa = pm.id_mahasiswa
    LEFT JOIN dosen d ON pm.id_dosen = d.id_dosen
    WHERE m.id_mahasiswa = ?
");
$stmt->execute([$id]);
$mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

$stmtLogbook = $conn->prepare("
    SELECT *
    FROM laporan_harian
    WHERE id_mahasiswa = ?
    ORDER BY tanggal_submit DESC
");
$stmtLogbook->execute([$id]);
$data_logbook = $stmtLogbook->fetchAll(PDO::FETCH_ASSOC);

$stmtLaporan = $conn->prepare("
    SELECT *
    FROM laporan_akhir
    WHERE id_mahasiswa = ?
");
$stmtLaporan->execute([$id]);
$laporan_akhir = $stmtLaporan->fetch(PDO::FETCH_ASSOC);

$stmtNilai = $conn->prepare("
    SELECT *
    FROM nilai_akhir
    WHERE id_mahasiswa = ?
");
$stmtNilai->execute([$id]);
$nilai = $stmtNilai->fetch(PDO::FETCH_ASSOC);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - Sistem Magang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/mahasiswa.css">
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
                <a href="mahasiswa.php" class="btn btn-light mb-3" style="border-radius: 10px;">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <h4 style="color:#2d6cdf;">Detail Mahasiswa</h4>
                <p class="text-muted mb-0" style="font-size:14px;">Monitoring aktivitas magang mahasiswa.</p>
            </div>

            <div class="row g-4">

                <div class="col-lg-4">
                    <div class="table-card p-0 overflow-hidden" style="border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 20px;">
                        <div class="p-4 text-center" style="background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%);">
                            <div class="position-relative d-inline-block mb-3">
                                <div class="profile-avatar mx-auto" style="width:90px; height:90px; font-size:32px; background:#2563eb; color:white; display: flex; align-items: center; justify-content: center; border-radius: 24px; box-shadow: 0 8px 16px rgba(37, 99, 235, 0.2);">
                                    <?= substr($mahasiswa['nama'], 0, 1) ?>
                                </div>
                                <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-success border border-2 border-white" style="padding: 6px;">
                                    <span class="visually-hidden">Online</span>
                                </span>
                            </div>
                            <h5 class="fw-bold mb-1" style="color: #1e293b;"><?= $mahasiswa['nama'] ?></h5>
                            <p class="text-muted small mb-0">NIM: <?= $mahasiswa['NIM'] ?></p>
                        </div>

                        <div class="p-4 pt-0">
                            <div class="list-group list-group-flush border-top-0">
                                <div class="list-group-item px-0 border-0 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box me-3" style="width:38px; height:38px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                            <i class="bi bi-book"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Program Studi</small>
                                            <span class="fw-semibold" style="color: #334155;"><?= $mahasiswa['prodi'] ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item px-0 border-0 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box me-3" style="width:38px; height:38px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Email Mahasiswa</small>
                                            <span class="fw-semibold" style="color: #334155;"><?= $mahasiswa['email'] ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item px-0 border-0 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box me-3" style="width:38px; height:38px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Dosen Pembimbing</small>
                                            <span class="fw-semibold" style="color: #334155;"><?= $mahasiswa['nama_dosen'] ?? 'Belum Ditentukan' ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-group-item px-0 border-0 mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box me-3" style="width:38px; height:38px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Tempat Magang</small>
                                            <span class="fw-semibold" style="color: #334155;"><?= $mahasiswa['tempat_magang'] ?? '-' ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-light" style="border-radius: 15px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="small text-muted fw-medium">Status Magang</span>
                                    <span class="badge rounded-pill bg-primary px-3 py-2" style="font-size: 10px; font-weight: 600;">
                                        <?= strtoupper($mahasiswa['status_pendaftaran'] ?? 'BELUM AKTIF') ?>
                                    </span>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="mailto:<?= $mahasiswa['email'] ?>" class="btn btn-outline-primary btn-sm" style="border-radius: 10px; font-weight: 500;">
                                        <i class="bi bi-chat-dots me-1"></i> Hubungi Mahasiswa
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-8">
                    <div class="table-card mb-4 p-4" style="border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 20px;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0" style="color: #1e293b;">Aktivitas Logbook</h5>
                            <span class="badge bg-light text-primary rounded-pill px-3">Total: <?= count($data_logbook) ?> Entri</span>
                        </div>

                        <?php if ($data_logbook): ?>
                            <div class="timeline-container" style="position: relative; padding-left: 20px;">

                                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 2px; background: #eef2ff;"></div>

                                <?php foreach ($data_logbook as $log): ?>
                                    <div class="timeline-item mb-4" style="position: relative;">

                                        <div style="position: absolute; left: -24px; top: 5px; width: 10px; height: 10px; border-radius: 50%; background: #2563eb; border: 2px solid white; box-shadow: 0 0 0 3px #eef2ff;"></div>

                                        <div class="p-3 border rounded-4" style="background: #ffffff; transition: all 0.3s ease; border-color: #f1f5f9 !important;">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold mb-1" style="color: #334155;"><?= $log['kegiatan'] ?></h6>
                                                    <div class="text-muted" style="font-size: 12px;">
                                                        <i class="bi bi-calendar3 me-1"></i> Minggu ke-<?= $log['minggu_ke'] ?> • <?= date('d M Y', strtotime($log['tanggal_submit'])) ?>
                                                    </div>
                                                </div>
                                                <?php
                                                $statusClass = [
                                                    'disetujui' => 'bg-success-subtle text-success',
                                                    'menunggu' => 'bg-warning-subtle text-warning',
                                                    'ditolak' => 'bg-danger-subtle text-danger'
                                                ][strtolower($log['status'])] ?? 'bg-secondary-subtle text-secondary';
                                                ?>
                                                <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2" style="font-size: 11px; text-transform: capitalize;">
                                                    <?= $log['status'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x fs-1 text-muted opacity-25"></i>
                                <p class="text-muted mt-2">Belum ada aktivitas logbook tercatat.</p>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="table-card p-4" style="border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-radius: 20px; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);">
                        <h5 class="fw-bold mb-4" style="color: #1e293b;">Laporan Akhir</h5>

                        <?php if ($laporan_akhir): ?>
                            <div class="d-flex align-items-center p-3 border rounded-4 bg-white shadow-sm">
                                <div class="file-icon me-3" style="width: 50px; height: 50px; background: #eef2ff; color: #2563eb; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0" style="color: #334155;"><?= $laporan_akhir['judul_laporan'] ?></h6>
                                    <small class="text-muted">Status Review:
                                        <span class="fw-medium text-primary text-capitalize"><?= $laporan_akhir['status_review'] ?></span>
                                    </small>
                                </div>
                                <div class="ms-3 text-end">
                                    <a href="../uploads/<?= $laporan_akhir['file_laporan'] ?>" target="_blank" class="btn btn-primary d-flex align-items-center" style="border-radius: 12px; gap: 8px; padding: 8px 16px;">
                                        <i class="bi bi-eye"></i> Lihat Laporan
                                    </a>
                                </div>
                            </div>

                            <?php if ($nilai): ?>
                                <div class="mt-4 p-4 border-0 rounded-4" style="background: #2563eb; color: white;">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="mb-1 opacity-75">Hasil Penilaian Akhir</h6>
                                            <p class="mb-0 small">Berdasarkan hasil evaluasi dosen pembimbing.</p>
                                        </div>
                                        <div class="col-auto">
                                            <div class="fw-bold" style="font-size: 42px; line-height: 1;"><?= $nilai['nilai'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-4 border border-dashed rounded-4">
                                <i class="bi bi-cloud-upload fs-2 text-muted opacity-50"></i>
                                <p class="text-muted small mt-2 mb-0">Belum ada laporan akhir yang diunggah.</p>
                            </div>
                        <?php endif; ?>
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
                <a href="login.php" style="flex:1; text-decoration:none;">
                    <button class="btn-logout" style="width:100%;">Ya, Keluar</button>
                </a>
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

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <h5>Hapus Data?</h5>
            <p>Apakah Anda yakin ingin menghapus data mahasiswa ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="modal-actions">
                <button class="btn-cancel" id="btnBatalHapus">Batal</button>
                <button class="btn-logout" id="btnKonfirmasiHapus" style="flex:1;">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <script src="assets/js/admin.js"></script>
    <script src="assets/js/mahasiswa.js"></script>

</body>

</html>