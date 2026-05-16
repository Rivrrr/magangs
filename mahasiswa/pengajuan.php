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

$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id_mahasiswa = ?");
$stmt->execute([$user['id']]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$page = "pengajuan";
$nama_mahasiswa = $data['nama'];
$nim = $data['NIM'];
$prodi = $data['prodi'];
$no_hp = $data['no_hp'] ?? '-';
$email = $data['email'];

$stmtMitra = $conn->query("SELECT * FROM mitra");
$data_mitra = $stmtMitra->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Proposal - Sistem Magang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/mahasiswa.css">
    <link rel="stylesheet" href="assets/css/pengajuan.css">
</head>
<body>

<div class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <div>
            <div class="logo-container">
                <img src="assets/logo.png" class="logo-sidebar" alt="Logo">
            </div>
            <a href="beranda.php" class="<?= ($page == 'beranda') ? 'active' : '' ?>"><i class="bi bi-grid me-2"></i>Beranda</a>
            <a href="pengajuan.php" class="<?= ($page == 'pengajuan') ? 'active' : '' ?>"><i class="bi bi-file-earmark-text me-2"></i>Pengajuan Proposal</a>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 w-100" style="max-height: 100vh; overflow-y: auto;">

        <!-- Header -->
        <div class="header-top">
            <h4 class="page-title">Formulir Pengajuan Magang</h4>
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
                        <a href="../logout.php" class="text-danger" id="btnKeluar"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="formPengajuan" method="POST" action="pengajuan_store.php" enctype="multipart/form-data">
            <div class="form-card">

                <!-- Data Mahasiswa -->
                <div class="form-section-title"><i class="bi bi-person-badge"></i> Data Mahasiswa</div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                        <input type="text" class="form-control" value="<?= $nim ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" value="<?= $nama_mahasiswa ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Program Studi</label>
                        <input type="text" class="form-control" value="<?= $prodi ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nomor HP</label>
                        <input type="text" class="form-control" value="<?= $no_hp ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="<?= $email ?>" readonly>
                    </div>
                </div>

                <!-- Data Perusahaan -->
                <div class="form-section-title"><i class="bi bi-building"></i> Data Perusahaan Tujuan</div>
                <div class="mb-4">
                    <label class="form-label d-block">Pilih Jenis Perusahaan <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_perusahaan" id="mitraTersedia" value="tersedia" checked>
                        <label class="form-check-label" for="mitraTersedia">Mitra Tersedia</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jenis_perusahaan" id="perusahaanBaru" value="baru">
                        <label class="form-check-label" for="perusahaanBaru">Perusahaan Baru</label>
                    </div>
                </div>

                <div class="row mb-3" id="opsiMitraTersedia">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Pilih Mitra Perusahaan <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectMitra" name="id_mitra" required>
                            <option value="" disabled selected>-- Pilih Mitra Perusahaan --</option>
                            <?php foreach ($data_mitra as $mitra): ?>
                            <option value="<?= $mitra['id_mitra'] ?>" data-alamat="<?= $mitra['alamat'] ?>" data-provinsi="<?= $mitra['provinsi'] ?>" data-kota="<?= $mitra['kota'] ?>" data-kecamatan="<?= $mitra['kecamatan'] ?>" data-kodepos="<?= $mitra['kode_pos'] ?>"><?= $mitra['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-4" id="formPerusahaanDetails">
                    <div class="col-md-12 mb-3" id="fieldNamaPerusahaan" style="display:none;">
                        <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputNamaPerusahaan" name="nama_perusahaan" placeholder="Contoh: PT Bangun Persada">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Alamat Lengkap Perusahaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="inputAlamat" name="alamat" rows="2" placeholder="Nama Jalan, Gedung, Nomor" required></textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputProvinsi" name="provinsi" placeholder="Provinsi" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Kota / Kabupaten <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputKota" name="kota" placeholder="Kota/Kabupaten" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputKecamatan" name="kecamatan" placeholder="Kecamatan" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputKodePos" name="kode_pos" placeholder="Kode Pos" required>
                    </div>
                </div>

                <!-- Detail Magang -->
                <div class="form-section-title"><i class="bi bi-briefcase"></i> Detail Magang & Proposal</div>
                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Judul Proposal <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="judul_proposal" placeholder="Masukkan judul proposal magang" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bidang Magang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="bidang" placeholder="Contoh: Software Development" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai Magang <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_mulai" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai Magang <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_selesai" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="3" placeholder="Informasi tambahan yang perlu diketahui admin atau dosen pembimbing..."></textarea>
                    </div>
                </div>

                <!-- Upload Berkas -->
                <div class="form-section-title"><i class="bi bi-file-earmark-arrow-up"></i> Upload Berkas Proposal</div>
                <div class="mb-4">
                    <div class="file-upload-wrapper">
                        <input type="file" id="berkasProposal" name="file_proposal" accept=".pdf" required>
                        <div class="file-upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="file-upload-text" id="fileName">Pilih file PDF atau seret ke sini</div>
                        <div class="file-upload-hint">Format yang diizinkan: PDF. Maksimal ukuran: 5MB</div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="d-flex justify-content-end border-top pt-4 mt-2">
                    <button type="submit" class="btn-submit"><i class="bi bi-send me-2"></i>Kirim Pengajuan</button>
                </div>

            </div>
        </form>

    </div>
</div>

<!-- Success Modal -->
<div class="modal-overlay" id="successModal">
    <div class="modal-box">
        <div class="modal-icon success-icon"><i class="bi bi-check-lg"></i></div>
        <h5>Berhasil Terkirim!</h5>
        <p>Proposal magang Anda telah berhasil dikirim. Silakan tunggu proses review oleh Admin dan penentuan Dosen Pembimbing.</p>
        <div class="modal-actions">
            <a href="beranda.php" style="flex:1;text-decoration:none;"><button class="btn-success-modal" style="width:100%;">Kembali ke Beranda</button></a>
        </div>
    </div>
</div>

<!-- Logout Modal -->
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

<?php if (isset($_GET['success'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById('successModal');
    if (modal) modal.classList.add('show');
    window.history.replaceState({}, document.title, "pengajuan.php");
});
</script>
<?php endif; ?>

<script src="assets/js/mahasiswa.js"></script>
<script src="assets/js/pengajuan.js"></script>
</body>
</html>