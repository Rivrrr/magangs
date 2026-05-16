-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 12:58 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `no_hp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `email`, `password`, `no_hp`) VALUES
(1, 'admin', 'admin@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '085704231123'),
(2, 'Demo Admin', 'demo_admin@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081100000000');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id_dosen` int(11) NOT NULL,
  `NIP` varchar(20) NOT NULL,
  `NIDN` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'Aktif',
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id_dosen`, `NIP`, `NIDN`, `nama`, `email`, `password`, `status`, `no_hp`) VALUES
(3, '198902142011012001', '220011000', 'Dr. Budi Santoso', '220011000@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '085292887766'),
(9, '198902142011012002', '220011001', 'Dr. Siti Rahmawati', '220011001@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Cuti', '081122334455'),
(10, '199003212015041003', '220011002', 'Ahmad Fauzi, S.Kom., M.Kom.', '220011002@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '085299887766'),
(11, '199205112017032004', '220011003', 'Rina Kurniasih, M.Kom.', '220011003@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '087811223344'),
(12, '198812082014021005', '220011004', 'Muhammad Rizky, S.T., M.T.', '220011004@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '082155443322'),
(13, '198812082014021006', '220011005', 'Agung Hafsah, M.Kom.', '220011005@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Cuti', '086123871203'),
(15, '198711102012122008', '220011007', 'Dr. Irwan Setiawan', '220011007@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '085344556677'),
(16, '199304152020011009', '220011008', 'Hendra Pratama, M.Cs.', '220011008@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '082211223344'),
(17, '198508302010102010', '220011009', 'Maya Sartika, S.Kom., M.I.T.', '220011009@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Cuti', '087799887766'),
(18, '199012122015041011', '220011010', 'Andi Budiman, M.T.', '220011010@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '081388776655'),
(19, '198902012014022012', '220011011', 'Citra Lestari, S.Pd., M.Pd.', '220011011@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '085211002233'),
(20, '199407252022031013', '220011012', 'Fajar Nugraha, M.Eng.', '220011012@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '081944556611'),
(21, '199405672022031013', '220011013', 'Eko Prasetyo, S.T.', '220011013@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '081944241611'),
(25, '197508152002121001', '0015087501', 'Prof. Dr. Ir. H. Ahmad Subarjo, M.Kom.', '0015087501@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Cuti', '081233445566'),
(26, '', '0722049201', 'Rizky Amelia, S.Tr.Kom., M.T.', '0722049201@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '085711223344'),
(27, '199001010000999', '220011999', 'Demo Dosen', 'demo_dosen@polije.ac.id', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', 'Aktif', '081199900000');

-- --------------------------------------------------------

--
-- Table structure for table `kps`
--

CREATE TABLE `kps` (
  `id_kps` int(11) NOT NULL,
  `id_dosen` int(11) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `program_studi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kps`
--

INSERT INTO `kps` (`id_kps`, `id_dosen`, `jabatan`, `program_studi`) VALUES
(1, 10, 'Ketua Program Studi', 'Teknik Informatika');

-- --------------------------------------------------------

--
-- Table structure for table `laporan_akhir`
--

CREATE TABLE `laporan_akhir` (
  `id_laporan_akhir` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `file_laporan` varchar(255) NOT NULL,
  `tanggal_upload` date NOT NULL,
  `status_review` varchar(20) NOT NULL DEFAULT 'pending',
  `judul_laporan` varchar(255) DEFAULT NULL,
  `catatan_dosen` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_akhir`
--

INSERT INTO `laporan_akhir` (`id_laporan_akhir`, `id_mahasiswa`, `file_laporan`, `tanggal_upload`, `status_review`, `judul_laporan`, `catatan_dosen`) VALUES
(2, 3, '1778564792_(BKPM) TIF120808 - WORKSHOP SISTEM INFORMASI - 20260503_200551_16afbfe3_(BKPM) TIF120808 - Workshop Sistem Informasi [MINGGU 12].pdf', '2026-05-12', 'Disetujui', 'My Thesis', 'revisi'),
(3, 7, '1778589903_(BKPM) TIF120808 - WORKSHOP SISTEM INFORMASI - 20260503_200551_16afbfe3_(BKPM) TIF120808 - Workshop Sistem Informasi [MINGGU 12].pdf', '2026-05-12', 'Menunggu', 'Laporan Saya', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `laporan_harian`
--

CREATE TABLE `laporan_harian` (
  `id_laporan_harian` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `kegiatan` text NOT NULL,
  `file_pendukung` varchar(255) DEFAULT NULL,
  `tanggal_submit` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'menunggu',
  `catatan_dosen` text DEFAULT NULL,
  `id_pendaftaran` int(11) DEFAULT NULL,
  `minggu_ke` int(11) DEFAULT NULL,
  `jam_masuk` time DEFAULT NULL,
  `jam_keluar` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan_harian`
--

INSERT INTO `laporan_harian` (`id_laporan_harian`, `id_mahasiswa`, `kegiatan`, `file_pendukung`, `tanggal_submit`, `status`, `catatan_dosen`, `id_pendaftaran`, `minggu_ke`, `jam_masuk`, `jam_keluar`) VALUES
(2, 3, 'Membuat UI Dashboard & Integrasi API', '1778530759_logo.png', '2026-05-07 00:00:00', 'Disetujui', 'oke bagus', 2, 2, '11:00:00', '17:00:00'),
(3, 3, 'duduk', '1778561283_logoLogin.png', '2026-05-28 00:00:00', 'Disetujui', '', 2, 3, '11:47:00', '16:47:00'),
(4, 3, 'Diam', '1778573081_logo-jti-new.png', '2026-05-29 00:00:00', 'Ditolak', '', 2, 3, '15:05:00', '19:07:00'),
(5, 7, 'Coding', '1778589942_danilo-alvesd-bmaWArQQY-M-unsplash.jpg', '2026-05-13 00:00:00', 'Disetujui', 'good', 4, 3, '12:33:00', '22:11:00'),
(8, 22, 'Menulis', '1778926804_rekap_nilai_magang_2026-05-15.csv', '2026-05-16 00:00:00', 'Disetujui', 'nice', 13, 1, '11:23:00', '17:02:00'),
(9, 22, 'idk', '1778927002_rekap_nilai_magang_2026-05-15.csv', '2026-06-16 00:00:00', 'Menunggu', NULL, 13, 5, '11:03:00', '16:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id_mahasiswa` int(11) NOT NULL,
  `NIM` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `prodi` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id_mahasiswa`, `NIM`, `nama`, `email`, `prodi`, `password`, `no_hp`) VALUES
(3, 'E41252871', 'Joni Dermawan', 'e41252871@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567891'),
(4, 'E41253412', 'Bagus Marta', 'e41253412@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567892'),
(7, 'E41254783', 'Saifur', 'e41254783@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567893'),
(8, 'E41255129', 'Hanni', 'e41255129@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567894'),
(9, 'E41256340', 'Firda Maulina', 'e41256340@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567895'),
(10, 'E41252322', 'Rudi Hermawan', 'e41252322@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234417891'),
(13, 'E41252971', 'Maulensia Apricilla', 'e41252971@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567854'),
(14, 'E41252110', 'Ahmad Zulkarnain', 'e41252110@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567855'),
(15, 'E41253512', 'Siti Sarah Nurhaliza', 'e41253512@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567856'),
(16, 'E41254890', 'Dimas Bagus Prasetyo', 'e41254890@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567857'),
(17, 'E41255201', 'Amanda Putri Kirana', 'e41255201@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567858'),
(18, 'E41256112', 'Rizky Ramadhan', 'e41256112@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567859'),
(19, 'E41252445', 'Farah Annisa', 'e41252445@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234417860'),
(20, 'E41252999', 'Aditya Pratama', 'e41252999@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567861'),
(21, 'E41253102', 'Dewi Fortuna', 'e41253102@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567862'),
(22, 'E41255340', 'Fajar Nugraha', 'e41255340@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567863'),
(23, 'E41256711', 'Rina Kurniasih', 'e41256711@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567864'),
(24, 'E41252115', 'Budi Santoso', 'e41252115@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567865'),
(25, 'E41253220', 'Siti Aminah', 'e41253220@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567866'),
(26, 'E41254335', 'Eko Prasetyo', 'e41254335@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567867'),
(27, 'E41255440', 'Dewi Lestari', 'e41255440@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567868'),
(28, 'E41256555', 'Hendra Wijaya', 'e41256555@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567869'),
(29, 'E41252670', 'Mega Utami', 'e41252670@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234417870'),
(30, 'E41253785', 'Ferry Setiawan', 'e41253785@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567871'),
(31, 'E41254895', 'Citra Resmi', 'e41254895@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567872'),
(32, 'E41255910', 'Andika Pratama', 'e41255910@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567873'),
(33, 'E41256125', 'Nadia Saputri', 'e41256125@polije.ac.id', 'MIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081234567874'),
(34, 'E41259999', 'Demo Mahasiswa', 'e41259999@polije.ac.id', 'TIF', '$2y$10$tDIbg42cFvLmp9SjJXwbx.ogAAXGkjnDbL916N8F0/JeNyljnxpV6', '081199900001');

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id_mitra` varchar(10) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mitra`
--

INSERT INTO `mitra` (`id_mitra`, `nama`, `alamat`, `provinsi`, `kota`, `kecamatan`, `kode_pos`) VALUES
('MTR-001', 'PT Telkom Indonesia', 'Jl. Japati No.1', 'Jawa Barat', 'Bandung', 'Regol', '40251'),
('MTR-002', 'PT Bank Mandiri', 'Jl. Gatot Subroto', 'DKI Jakarta', 'Jakarta Selatan', 'Setiabudi', '12190'),
('MTR-003', 'PT Pertamina Persero', 'Jl. Medan Merdeka Timur No.1', 'DKI Jakarta', 'Jakarta Pusat', 'Gambir', '10110'),
('MTR-004', 'PT Indofood CBP', 'Sudirman Plaza, Indofood Tower', 'DKI Jakarta', 'Jakarta Selatan', 'Setiabudi', '12910'),
('MTR-005', 'PT Unilever Indonesia', 'Jl. BSD Boulevard Barat', 'Banten', 'Tangerang', 'Pagedangan', '15339'),
('MTR-006', 'PT Astra International', 'Jl. Gaya Motor Raya No.8', 'DKI Jakarta', 'Jakarta Utara', 'Tanjung Priok', '14330'),
('MTR-007', 'PT GoTo Gojek Tokopedia', 'Pasaraya Blok M, Gedung B', 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', '12160'),
('MTR-008', 'PT Bukalapak.com', 'Metropolitan Tower Lt. 22', 'DKI Jakarta', 'Jakarta Selatan', 'Cilandak', '12430'),
('MTR-009', 'PT Traveloka Indonesia', 'Wisma 77 Tower 2', 'DKI Jakarta', 'Jakarta Barat', 'Palmerah', '11410'),
('MTR-010', 'PT Kereta Api Indonesia', 'Jl. Perintis Kemerdekaan No.1', 'Jawa Barat', 'Bandung', 'Sumur Bandung', '40117');

-- --------------------------------------------------------

--
-- Table structure for table `nilai_akhir`
--

CREATE TABLE `nilai_akhir` (
  `id_nilaiAkhir` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `id_dosen` int(11) NOT NULL,
  `nilai` int(11) NOT NULL CHECK (`nilai` >= 0 and `nilai` <= 100),
  `tanggal_penilaian` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai_akhir`
--

INSERT INTO `nilai_akhir` (`id_nilaiAkhir`, `id_mahasiswa`, `id_dosen`, `nilai`, `tanggal_penilaian`) VALUES
(1, 3, 3, 20, '2026-05-13');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran_magang`
--

CREATE TABLE `pendaftaran_magang` (
  `id_pendaftaran` int(11) NOT NULL,
  `id_mahasiswa` int(11) NOT NULL,
  `id_koordinator` int(11) DEFAULT NULL,
  `id_dosen` int(11) DEFAULT NULL,
  `status_pendaftaran` varchar(20) NOT NULL DEFAULT 'pending',
  `tempat_magang` varchar(200) NOT NULL,
  `id_pengajuan` int(11) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran_magang`
--

INSERT INTO `pendaftaran_magang` (`id_pendaftaran`, `id_mahasiswa`, `id_koordinator`, `id_dosen`, `status_pendaftaran`, `tempat_magang`, `id_pengajuan`, `tanggal_mulai`, `tanggal_selesai`) VALUES
(2, 3, 1, 11, 'Aktif', 'PAMA', 6, '2026-05-02', '2026-09-02'),
(4, 7, NULL, 3, 'Aktif', 'PT Bank Mandiri', 7, '2026-05-12', '2026-08-12'),
(9, 8, NULL, 15, 'Aktif', 'PT Telkom Indonesia', 9, '2026-05-12', '2026-08-12'),
(10, 4, NULL, 18, 'Menunggu', 'Mitra Sehat', NULL, NULL, NULL),
(11, 13, NULL, 26, 'Aktif', 'PT Telkom Indonesia', 10, '2026-05-15', '2026-08-15'),
(12, 9, NULL, 3, 'Aktif', 'PT Traveloka Indonesia', 13, '2026-05-16', '2026-08-16'),
(13, 22, NULL, 13, 'Aktif', 'PT GoTo Gojek Tokopedia', 14, '2026-05-16', '2026-08-16');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id_pengajuan` int(11) NOT NULL,
  `id_mahasiswa` int(11) DEFAULT NULL,
  `jenis_perusahaan` varchar(50) DEFAULT NULL,
  `nama_perusahaan` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `provinsi` varchar(100) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kode_pos` varchar(20) DEFAULT NULL,
  `judul_proposal` varchar(255) DEFAULT NULL,
  `bidang` varchar(100) DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `file_proposal` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengajuan`
--

INSERT INTO `pengajuan` (`id_pengajuan`, `id_mahasiswa`, `jenis_perusahaan`, `nama_perusahaan`, `alamat`, `provinsi`, `kota`, `kecamatan`, `kode_pos`, `judul_proposal`, `bidang`, `tanggal_mulai`, `tanggal_selesai`, `catatan`, `file_proposal`, `status`, `created_at`) VALUES
(5, 3, 'tersedia', 'PT Bank Mandiri', 'Jl. Gatot Subroto', 'DKI Jakarta', 'Bengkulu', 'Setiabudi', '12190', 'Magang guys2', 'gatau2', '2026-05-08', '2026-05-29', '22', '1778056465_Role, Hak Akses dan CRUD - Role, Hak Akses dan CRUD_82c5cf6 (1).pdf', 'menunggu', '2026-05-06 08:34:25'),
(6, 3, 'baru', 'PAMA', 'Jalan Kalimantan', 'Kalimantan', 'Bengkulu', 'bengkulu', '555555', 'joni mau magang', 'asd', '2026-05-02', '2026-09-02', 'qwe', '1778056524_Role, Hak Akses dan CRUD - Role, Hak Akses dan CRUD_82c5cf6 (1).pdf', 'Disetujui', '2026-05-06 08:35:24'),
(7, 7, 'tersedia', 'PT Bank Mandiri Persero', 'Jl. Gatot Subroto', 'DKI Jakarta', 'Jakarta Selatan', 'Setiabudi', '12190', 'Magang Mandiri', 'Turu', '2026-05-12', '2026-08-12', '', '1778588528_20260421_081047_9e3e5631_MINGGU 10 - WORKSHOP SISTEM INFORMASI-BKPM.pdf', 'Disetujui', '2026-05-12 12:22:08'),
(9, 8, 'tersedia', 'PT Telkom Indonesia', 'Jl. Japati No.1', 'Jawa Barat', 'Bogor', 'Regol', '40251', 'Hanni', 'Hanni', '2026-05-12', '2026-06-09', 'Hanni', '1778592744_(BKPM) TIF120808 - WORKSHOP SISTEM INFORMASI - 20260503_200551_16afbfe3_(BKPM) TIF120808 - Workshop Sistem Informasi [MINGGU 12].pdf', 'Disetujui', '2026-05-12 13:32:24'),
(10, 13, 'tersedia', 'PT Telkom Indonesia', 'Jl. Japati No.1', 'Jawa Barat', 'Jakarta', 'Regol', '40251', 'analisis mahasiswa ti kebanyakan mempunyai side job sebagai femboy', 'Frmboy ', '2026-05-15', '2026-08-15', '', '1778853078_(BKPM) TIF120808 - WORKSHOP SISTEM INFORMASI - 20260503_200551_16afbfe3_(BKPM) TIF120808 - Workshop Sistem Informasi [MINGGU 12].pdf', 'Disetujui', '2026-05-15 13:51:18'),
(13, 9, 'tersedia', 'PT Traveloka Indonesia', 'Wisma 77 Tower 2', 'DKI Jakarta', 'Jakarta Barat', 'Palmerah', '11410', 'DEV', 'DEV', '2026-05-16', '2026-08-16', 'DEV', '1778923178_Role, Hak Akses dan CRUD - Role, Hak Akses dan CRUD_82c5cf6 (1).pdf', 'Disetujui', '2026-05-16 09:19:38'),
(14, 22, 'tersedia', 'PT GoTo Gojek Tokopedia', 'Pasaraya Blok M, Gedung B', 'DKI Jakarta', 'Jakarta Selatan', 'Kebayoran Baru', '12160', 'Magang Gojek', 'Dev', '2026-05-16', '2026-08-16', 'dev', '1778926336_Role, Hak Akses dan CRUD - Role, Hak Akses dan CRUD_82c5cf6 (1).pdf', 'Disetujui', '2026-05-16 10:12:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id_dosen`),
  ADD UNIQUE KEY `NIP` (`NIP`),
  ADD UNIQUE KEY `NIDN` (`NIDN`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `kps`
--
ALTER TABLE `kps`
  ADD PRIMARY KEY (`id_kps`),
  ADD UNIQUE KEY `id_dosen` (`id_dosen`),
  ADD UNIQUE KEY `id_dosen_2` (`id_dosen`);

--
-- Indexes for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD PRIMARY KEY (`id_laporan_akhir`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD PRIMARY KEY (`id_laporan_harian`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id_mahasiswa`),
  ADD UNIQUE KEY `NIM` (`NIM`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- Indexes for table `mitra`
--
ALTER TABLE `mitra`
  ADD PRIMARY KEY (`id_mitra`);

--
-- Indexes for table `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  ADD PRIMARY KEY (`id_nilaiAkhir`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `id_dosen` (`id_dosen`);

--
-- Indexes for table `pendaftaran_magang`
--
ALTER TABLE `pendaftaran_magang`
  ADD PRIMARY KEY (`id_pendaftaran`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`),
  ADD KEY `id_koordinator` (`id_koordinator`);

--
-- Indexes for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_mahasiswa` (`id_mahasiswa`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id_dosen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `kps`
--
ALTER TABLE `kps`
  MODIFY `id_kps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  MODIFY `id_laporan_akhir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  MODIFY `id_laporan_harian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id_mahasiswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  MODIFY `id_nilaiAkhir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pendaftaran_magang`
--
ALTER TABLE `pendaftaran_magang`
  MODIFY `id_pendaftaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kps`
--
ALTER TABLE `kps`
  ADD CONSTRAINT `fk_kps_dosen` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kps_ibfk_1` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `laporan_akhir`
--
ALTER TABLE `laporan_akhir`
  ADD CONSTRAINT `laporan_akhir_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON UPDATE CASCADE;

--
-- Constraints for table `laporan_harian`
--
ALTER TABLE `laporan_harian`
  ADD CONSTRAINT `laporan_harian_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON UPDATE CASCADE;

--
-- Constraints for table `nilai_akhir`
--
ALTER TABLE `nilai_akhir`
  ADD CONSTRAINT `nilai_akhir_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON UPDATE CASCADE,
  ADD CONSTRAINT `nilai_akhir_ibfk_2` FOREIGN KEY (`id_dosen`) REFERENCES `dosen` (`id_dosen`) ON UPDATE CASCADE;

--
-- Constraints for table `pendaftaran_magang`
--
ALTER TABLE `pendaftaran_magang`
  ADD CONSTRAINT `pendaftaran_magang_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`) ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `pengajuan_ibfk_1` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id_mahasiswa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
