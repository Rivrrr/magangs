<?php
session_start();

require_once 'config/database.php';
require_once 'config/userchecker.php';
require_once 'config/google_oauth.php';

if (isset($_SESSION['user'])) {

    $role = $_SESSION['user']['role'] ?? '';

    switch ($role) {
        case 'mahasiswa':
            header("Location: mahasiswa/beranda.php");
            break;
        case 'dosen':
            header("Location: dosen/dashboard.php");
            break;
        case 'admin':
            header("Location: admin/beranda.php");
            break;
        case 'kps':
            header("Location: kaprodi/dashboard.php");
            break;
        default:
            header("Location: index.php");
            break;
    }
    exit;
}

$error = "";
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi!";
    } else {

        $db = new Database();
        $conn = $db->getConnection();

        $userModel = new UserModel($conn);
        $user = $userModel->findUser($email);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'id' => $user['id'],
                'nama' => $user['nama'],
                'role' => $user['role']
            ];

            switch ($user['role']) {
                case 'mahasiswa':
                    header("Location: mahasiswa/beranda.php");
                    break;
                case 'dosen':
                    header("Location: dosen/dashboard.php");
                    break;
                case 'admin':
                    header("Location: admin/beranda.php");
                    break;
                case 'kps':
                    header("Location: kaprodi/dashboard.php");
                    break;
                default:
                    header("Location: index.php");
            }

            exit;

        } else {
            $error = "Email atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Magang</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        .btn-google {
            background-color: #fff;
            color: #757575;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 500;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .btn-google:hover {
            background-color: #f8f9fa;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn-google img {
            width: 20px;
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #6c757d;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        .divider:not(:empty)::before {
            margin-right: .25em;
        }
        .divider:not(:empty)::after {
            margin-left: .25em;
        }
    </style>
</head>
<body>

<div class="shape shape-1"></div>
<div class="shape shape-2"></div>

<div class="login-card">

    <div class="login-header">
        <div class="login-logo">
            <img src="assets/logoLogin.png" alt="Logo Sistem">
        </div>
        <h4>Selamat Datang</h4>
    </div>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <a href="<?= getGoogleLoginUrl() ?>" class="btn btn-google mb-3 w-100 py-2" style="border-radius: 12px;">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo">
        Masuk dengan Google (SSO)
    </a>

    <div class="divider">ATAU</div>

    <form method="POST">

        <div class="input-group-custom">
            <div class="icon-wrapper"><i class="bi bi-envelope"></i></div>
            <input type="email" name="email" class="form-control" placeholder="Alamat Email" required>
        </div>

        <div class="input-group-custom" style="position: relative;">
            <div class="icon-wrapper"><i class="bi bi-lock"></i></div>
            <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Kata Sandi" required style="padding-right: 40px;">
            <i class="bi bi-eye" id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d; z-index: 10;"></i>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <a href="register.php" class="forgot-password">Belum Punya Akun?</a>
            <!-- <a href="#" class="forgot-password">Lupa Kata Sandi?</a> -->
        </div>

        <button type="submit" class="btn btn-login mb-4 w-100">
            Masuk
        </button>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('passwordInput');

    togglePassword.addEventListener('click', function () {

        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
</script>
</body>
</html>