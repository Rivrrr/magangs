<?php
session_start();

require_once '../config/database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

$db = new Database();
$conn = $db->getConnection();

$page = "laporan_akhir";

$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = ?");
$stmt->execute([$user['id']]);
$mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

$nama_mahasiswa = $mahasiswa['nama'];
$nim = $mahasiswa['NIM'];
$prodi = $mahasiswa['prodi'];
$email = $mahasiswa['email'];

$stmtLaporan = $conn->prepare("SELECT * FROM laporan_akhir WHERE id_mahasiswa = ? LIMIT 1");
$stmtLaporan->execute([$user['id']]);
$laporan_akhir = $stmtLaporan->fetch(PDO::FETCH_ASSOC);

$stmtNilai = $conn->prepare("SELECT * FROM nilai_akhir WHERE id_mahasiswa = ? ORDER BY tanggal_penilaian DESC LIMIT 1");
$stmtNilai->execute([$user['id']]);
$nilai = $stmtNilai->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Akhir - SIMMAG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/mahasiswa_navbar.css">
    <link rel="stylesheet" href="assets/css/mahasiswa.css">
</head>

<body>

    <!-- Top Navigation -->
    <div class="top-navbar">
        <div class="navbar-brand">
            <img src="assets/logo.png" alt="SIMMAG">
        </div>
        <div class="nav-center">
            <a href="magang_aktif.php" class="<?= ($page == 'magang_aktif') ? 'active' : '' ?>">Beranda</a>
            <a href="laporan_harian.php" class="<?= ($page == 'laporan_harian') ? 'active' : '' ?>">Laporan Harian</a>
            <a href="laporan_akhir.php" class="<?= ($page == 'laporan_akhir') ? 'active' : '' ?>">Laporan Akhir</a>
            <a href="riwayat.php" class="<?= ($page == 'riwayat') ? 'active' : '' ?>">Riwayat</a>
        </div>
        <div class="nav-right">
            <div class="profile-section">
                <div class="profile-wrapper">
                    <div class="profile-info" id="profileToggle" style="cursor:pointer;">
                        <div class="profile-avatar"><i class="bi bi-person"></i></div>
                        <div class="profile-text">
                            <div class="name"><?= $nama_mahasiswa ?></div>
                            <div class="role">Mahasiswa</div>
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

    <!-- Main Content -->
    <div class="main-container">
        <div class="row g-4">

            <!-- Left: Upload Form / Status Card -->
            <div class="col-md-8">
                <div class="card p-4 border-0 shadow-sm" style="border-radius: 12px; height: 100%;">
                    <h4 class="mb-1 fw-bold" style="color: #1e293b;">Unggah Laporan Akhir</h4>
                    <p class="text-muted mb-4" style="font-size: 14px;">Silakan unggah dokumen laporan akhir magang Anda yang telah disetujui oleh pembimbing lapangan.</p>

                    <?php if (!$laporan_akhir): ?>

                        <form action="store_laporan_akhir.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id_mahasiswa" value="<?= $user['id'] ?>">
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Judul Laporan</label>
                                <input type="text" name="judul_laporan" class="form-control" style="border-radius: 8px;" placeholder="Masukkan judul laporan akhir" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Dokumen Laporan Akhir (PDF)</label>
                                <div class="border border-dashed p-5 text-center mt-2" style="border-radius: 12px; border-width: 2px; border-color: #cbd5e1; background: #f8fafc; border-style: dashed;">
                                    <div class="mb-3">
                                        <div class="d-inline-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 60px; height: 60px; border-radius: 50%;">
                                            <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 28px;"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="color: #1e293b;">Klik untuk unggah file</h6>
                                    <p class="text-muted small mb-3">Atau seret dan lepas file ke area ini (Maks. 10MB)</p>
                                    <input type="file" name="file_laporan" class="form-control" accept=".pdf" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold w-100" style="border-radius: 8px; background: linear-gradient(135deg, #2563eb, #7c3aed); border: none;">Kirim Laporan Akhir</button>
                        </form>

                    <?php else: ?>

                        <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-success-subtle text-success d-flex justify-content-center align-items-center me-3" style="width: 56px; height: 56px; border-radius: 14px;">
                                            <i class="bi bi-file-earmark-check-fill" style="font-size: 24px;"></i>
                                        </div>
                                        <div>
                                            <h4 class="fw-bold mb-1" style="color: #1e293b;">Laporan berhasil diunggah</h4>
                                            <p class="text-muted mb-0">
                                                <?php if ($laporan_akhir['status_review'] == 'Menunggu'): ?>
                                                    Menunggu review dosen pembimbing
                                                <?php elseif ($laporan_akhir['status_review'] == 'Disetujui'): ?>
                                                    Laporan akhir telah disetujui dosen pembimbing
                                                <?php else: ?>
                                                    Laporan perlu direvisi sesuai catatan dosen
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <?php if ($laporan_akhir['status_review'] == 'Menunggu'): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">Menunggu Review</span>
                                        <?php elseif ($laporan_akhir['status_review'] == 'Disetujui'): ?>
                                            <span class="badge bg-success px-3 py-2 fs-6">Disetujui</span>
                                        <?php elseif ($laporan_akhir['status_review'] == 'Revisi'): ?>
                                            <span class="badge bg-info px-3 py-2 fs-6">Revisi</span>
                                        <?php elseif ($laporan_akhir['status_review'] == 'Ditolak'): ?>
                                            <span class="badge bg-danger px-3 py-2 fs-6">Ditolak</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1">Judul Laporan</div>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($laporan_akhir['judul_laporan']) ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-muted small mb-1">Tanggal Upload</div>
                                        <div class="fw-semibold text-dark"><?= date('d F Y', strtotime($laporan_akhir['tanggal_upload'])) ?></div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <div class="text-muted small mb-2">Dokumen Laporan</div>
                                    <a href="../uploads/<?= $laporan_akhir['file_laporan'] ?>" target="_blank" class="btn btn-outline-primary">
                                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Lihat Dokumen
                                    </a>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Status & Info -->
            <div class="col-md-4">
                <div class="card p-4 border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <h6 class="fw-bold mb-4" style="color: #1e293b;">Status Laporan Akhir</h6>

                    <div class="d-flex align-items-start">
                        <div class="<?php
                                    if (!$laporan_akhir) echo 'bg-warning-subtle text-warning';
                                    elseif ($laporan_akhir['status_review'] == 'Disetujui') echo 'bg-success-subtle text-success';
                                    elseif ($laporan_akhir['status_review'] == 'Ditolak') echo 'bg-danger-subtle text-danger';
                                    elseif ($laporan_akhir['status_review'] == 'Revisi') echo 'bg-info-subtle text-info';
                                    else echo 'bg-warning-subtle text-warning';
                                    ?> d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px; border-radius: 12px;">
                            <?php if (!$laporan_akhir): ?>
                                <i class="bi bi-hourglass-split" style="font-size: 20px;"></i>
                            <?php elseif ($laporan_akhir['status_review'] == 'Disetujui'): ?>
                                <i class="bi bi-check-circle-fill" style="font-size: 20px;"></i>
                            <?php elseif ($laporan_akhir['status_review'] == 'Ditolak'): ?>
                                <i class="bi bi-x-circle-fill" style="font-size: 20px;"></i>
                            <?php elseif ($laporan_akhir['status_review'] == 'Revisi'): ?>
                                <i class="bi bi-pencil-square" style="font-size: 20px;"></i>
                            <?php else: ?>
                                <i class="bi bi-clock-history" style="font-size: 20px;"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (!$laporan_akhir): ?>
                                <h6 class="mb-1 fw-bold" style="color: #1e293b;">Belum Dikumpulkan</h6>
                                <p class="mb-0 text-muted" style="font-size: 12px;">Silakan unggah laporan akhir Anda</p>
                            <?php else: ?>
                                <h6 class="mb-1 fw-bold" style="color: #1e293b;"><?= $laporan_akhir['status_review'] ?></h6>
                                <p class="mb-0 text-muted" style="font-size: 12px;">Upload: <?= date('d F Y', strtotime($laporan_akhir['tanggal_upload'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($laporan_akhir && $laporan_akhir['status_review'] == 'Disetujui' && $nilai): ?>
                        <?php
                        $nilaiAngka = $nilai['nilai'];
                        if ($nilaiAngka < 60) $nilaiClass = 'danger';
                        elseif ($nilaiAngka < 70) $nilaiClass = 'warning';
                        elseif ($nilaiAngka < 85) $nilaiClass = 'primary';
                        else $nilaiClass = 'success';
                        ?>
                        <hr class="my-4">
                        <div class="mt-3">
                            <h6 class="fw-bold mb-2" style="color: #1e293b;">Nilai Akhir</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-<?= $nilaiClass ?>-subtle text-<?= $nilaiClass ?> fw-bold d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; border-radius: 12px; font-size: 20px;"><?= $nilai['nilai'] ?></div>
                                <div class="text-muted" style="font-size: 13px;">Diberikan oleh dosen pembimbing</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($laporan_akhir && !empty($laporan_akhir['catatan_dosen'])): ?>
                        <hr class="my-4">
                        <div>
                            <div class="fw-semibold mb-2" style="color: #1e293b;">Catatan Dosen</div>
                            <div class="text-muted" style="font-size: 13px;"><?= nl2br(htmlspecialchars($laporan_akhir['catatan_dosen'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card p-4 border-0 shadow-sm" style="border-radius: 12px; background: #eff6ff; border: 1px solid #bfdbfe;">
                    <h6 class="fw-bold mb-3" style="color: #1e3a8a;">Informasi Penting</h6>
                    <ul class="text-muted ps-3 mb-0" style="font-size: 13px;">
                        <li class="mb-2">Pastikan laporan sudah ditandatangani oleh pembimbing lapangan.</li>
                        <li class="mb-2">Format file wajib PDF.</li>
                        <li class="mb-0">Ukuran maksimal file adalah 10MB.</li>
                    </ul>
                    <hr class="my-3 border-primary opacity-25">
                    <a href="../uploads/template.pdf" download class="btn btn-outline-primary btn-sm w-100 fw-semibold" style="border-radius: 6px;">
                        <i class="bi bi-download me-2"></i>Unduh Template Laporan
                    </a>
                </div>
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
            <div class="profile-modal-name"><?= $nama_mahasiswa ?></div>
            <div class="profile-modal-role">Mahasiswa</div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-credit-card-2-front"></i></div>
                <div>
                    <div class="profile-detail-label">NIM</div>
                    <div class="profile-detail-value"><?= $nim ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-book"></i></div>
                <div>
                    <div class="profile-detail-label">Program Studi</div>
                    <div class="profile-detail-value"><?= $prodi ?></div>
                </div>
            </div>
            <div class="profile-detail">
                <div class="profile-detail-icon"><i class="bi bi-envelope"></i></div>
                <div>
                    <div class="profile-detail-label">Surel</div>
                    <div class="profile-detail-value"><?= $email ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/mahasiswa.js"></script>
</body>

</html>