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

$page = "laporan_harian";

$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = ?");
$stmt->execute([$user['id']]);
$mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

$nama_mahasiswa = $mahasiswa['nama'];
$nim = $mahasiswa['NIM'];
$prodi = $mahasiswa['prodi'];
$email = $mahasiswa['email'];

$stmtPendaftaran = $conn->prepare("SELECT * FROM pendaftaran_magang WHERE id_mahasiswa = ? ORDER BY id_pendaftaran DESC LIMIT 1");
$stmtPendaftaran->execute([$user['id']]);
$pendaftaran = $stmtPendaftaran->fetch(PDO::FETCH_ASSOC);

if (!$pendaftaran) die("Belum ada magang aktif");

$id_pendaftaran = $pendaftaran['id_pendaftaran'];

$stmtLogbook = $conn->prepare("SELECT * FROM laporan_harian WHERE id_pendaftaran = ? ORDER BY tanggal_submit DESC");
$stmtLogbook->execute([$id_pendaftaran]);
$data_logbook = $stmtLogbook->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - SIMMAG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/mahasiswa_navbar.css">
    <link rel="stylesheet" href="assets/css/mahasiswa.css">
    <link rel="stylesheet" href="assets/css/beranda.css">
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
        <div class="card p-4 border-0 shadow-sm" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold" style="color: #1e293b;">Daftar Logbook Harian</h4>
                <button class="btn btn-primary px-4 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(135deg, #2563eb, #7c3aed); border: none;" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Laporan
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead style="background-color: #f8fafc; color: #64748b; font-size: 13px; text-transform: uppercase;">
                        <tr>
                            <th class="py-3 px-4 rounded-start" style="font-weight: 600;">Tanggal</th>
                            <th class="py-3" style="font-weight: 600;">Minggu Ke</th>
                            <th class="py-3" style="font-weight: 600;">Aktivitas</th>
                            <th class="py-3" style="font-weight: 600;">Jam Kerja</th>
                            <th class="py-3 text-center" style="font-weight: 600;">Status</th>
                            <th class="py-3 px-4 rounded-end text-end" style="font-weight: 600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_logbook as $log): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-semibold text-dark"><?= date('l, d F Y', strtotime($log['tanggal_submit'])) ?></div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-primary border border-primary-subtle">Minggu <?= $log['minggu_ke'] ?></span>
                            </td>
                            <td class="py-3 text-muted"><?= $log['kegiatan'] ?></td>
                            <td class="py-3 text-muted"><?= substr($log['jam_masuk'], 0, 5) ?> - <?= substr($log['jam_keluar'], 0, 5) ?></td>
                            <td class="py-3 text-center">
                                <?php if ($log['status'] == 'Disetujui'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                                <?php elseif ($log['status'] == 'Ditolak'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill"><i class="bi bi-clock me-1"></i>Menunggu</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button class="btn btn-sm btn-light text-primary me-2" data-bs-toggle="modal" data-bs-target="#modalView<?= $log['id_laporan_harian'] ?>" title="Detail"><i class="bi bi-eye"></i></button>
                                <?php if ($log['status'] == 'Menunggu'): ?>
                                    <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $log['id_laporan_harian'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light text-muted" disabled title="Tidak bisa diedit (<?= $log['status'] ?>)"><i class="bi bi-pencil"></i></button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Laporan -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Tambah Logbook Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="store_laporan.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_pendaftaran" value="<?= $id_pendaftaran ?>">
                        <input type="hidden" name="id_mahasiswa" value="<?= $user['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Tanggal Aktivitas</label>
                            <input type="date" name="tanggal_submit" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Jam Mulai</label>
                                <input type="time" name="jam_masuk" class="form-control" style="border-radius: 8px;" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Jam Selesai</label>
                                <input type="time" name="jam_keluar" class="form-control" style="border-radius: 8px;" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Deskripsi Aktivitas</label>
                            <textarea name="kegiatan" class="form-control" rows="4" style="border-radius: 8px; resize: none;" placeholder="Ceritakan apa yang Anda kerjakan hari ini..." required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Lampiran / Bukti Kerja (Opsional)</label>
                            <input class="form-control" type="file" name="file_pendukung" style="border-radius: 8px;">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 8px; background: linear-gradient(135deg, #2563eb, #7c3aed); border: none;">Simpan Laporan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail/View Laporan -->
    <?php foreach ($data_logbook as $log): ?>
    <div class="modal fade" id="modalView<?= $log['id_laporan_harian'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Detail Logbook Harian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Tanggal Aktivitas</label>
                        <p class="text-dark mb-0 fw-medium"><?= date('l, d F Y', strtotime($log['tanggal_submit'])) ?></p>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Jam Mulai</label>
                            <p class="text-dark mb-0 fw-medium"><?= substr($log['jam_masuk'], 0, 5) ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Jam Selesai</label>
                            <p class="text-dark mb-0 fw-medium"><?= substr($log['jam_keluar'], 0, 5) ?></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Deskripsi Aktivitas</label>
                        <div class="p-3 bg-light" style="border-radius: 8px; color: #1e293b; font-size: 14px;"><?= nl2br(htmlspecialchars($log['kegiatan'])) ?></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Lampiran</label>
                        <div>
                            <a href="../uploads/<?= $log['file_pendukung'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf me-2"></i><?= $log['file_pendukung'] ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light w-100 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Modal Edit Laporan -->
    <?php foreach ($data_logbook as $log): ?>
    <div class="modal fade" id="modalEdit<?= $log['id_laporan_harian'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Edit Logbook Harian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="update_laporan.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_laporan_harian" value="<?= $log['id_laporan_harian'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Tanggal Aktivitas</label>
                            <input type="date" name="tanggal_submit" class="form-control" style="border-radius: 8px;" value="<?= date('Y-m-d', strtotime($log['tanggal_submit'])) ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Jam Mulai</label>
                                <input type="time" name="jam_masuk" class="form-control" style="border-radius: 8px;" value="<?= substr($log['jam_masuk'], 0, 5) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Jam Selesai</label>
                                <input type="time" name="jam_keluar" class="form-control" style="border-radius: 8px;" value="<?= substr($log['jam_keluar'], 0, 5) ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Deskripsi Aktivitas</label>
                            <textarea name="kegiatan" class="form-control" rows="4" style="border-radius: 8px; resize: none;"><?= $log['kegiatan'] ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #475569; font-size: 13px;">Lampiran / Bukti Kerja (Opsional)</label>
                            <input type="file" name="file_pendukung" class="form-control" style="border-radius: 8px;">
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah file lampiran sebelumnya.</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 8px; background: linear-gradient(135deg, #2563eb, #7c3aed); border: none;">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

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