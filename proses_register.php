<?php
require_once 'config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: register.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$nim =
    trim($_POST['nim']);

$nama =
    trim($_POST['nama']);

$prodi =
    trim($_POST['prodi']);

$no_hp =
    trim($_POST['no_hp']);

$email =
    trim($_POST['email']);

$password =
    trim($_POST['password']);

$konfirmasi =
    trim($_POST['konfirmasi_password']);

if (
    empty($nim) ||
    empty($nama) ||
    empty($prodi) ||
    empty($no_hp) ||
    empty($email) ||
    empty($password) ||
    empty($konfirmasi)
) {

    $_SESSION['error'] =
        "Semua field wajib diisi!";

    header("Location: register.php");
    exit;
}

if ($password !== $konfirmasi) {

    $_SESSION['error'] =
        "Konfirmasi password tidak cocok!";

    header("Location: register.php");
    exit;
}

$stmtNim = $conn->prepare("
    SELECT *
    FROM mahasiswa
    WHERE NIM = ?
");

$stmtNim->execute([$nim]);

if ($stmtNim->fetch()) {

    $_SESSION['error'] =
        "NIM sudah digunakan!";

    header("Location: register.php");
    exit;
}

$stmtEmail = $conn->prepare("
    SELECT *
    FROM mahasiswa
    WHERE email = ?
");

$stmtEmail->execute([$email]);

if ($stmtEmail->fetch()) {

    $_SESSION['error'] =
        "Email sudah digunakan!";

    header("Location: register.php");
    exit;
}

$hashedPassword =
    password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO mahasiswa
    (
        NIM,
        nama,
        email,
        prodi,
        no_hp,
        password
    )
    VALUES (?, ?, ?, ?, ?, ?)
");

$success = $stmt->execute([
    $nim,
    $nama,
    $email,
    $prodi,
    $no_hp,
    $hashedPassword
]);

if ($success) {

    $_SESSION['success'] =
        "Registrasi berhasil! Silakan login.";

    header("Location: login.php");
    exit;

} else {

    $_SESSION['error'] =
        "Registrasi gagal!";

    header("Location: register.php");
    exit;
}
?>