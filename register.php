<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa - Sistem Magang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            opacity: 0.6;
            pointer-events: none;
        }
        .shape-1 {
            width: 400px;
            height: 400px;
            background: #93c5fd;
            top: -100px;
            left: -100px;
        }
        .shape-2 {
            width: 300px;
            height: 300px;
            background: #c4b5fd;
            bottom: -50px;
            right: -50px;
        }

        .register-card {
            width: 100%;
            max-width: 700px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 1);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            position: relative; 
            z-index: 10;
        }

        .logo-box {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo-box img {
            max-height: 70px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            color: #64748b;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .form-control, .form-select {
            background-color: #f8fafc;
            height: 48px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            border-left: none;
            font-size: 14px;
            color: #334155;
        }
        
        .form-select {
            border-left: 1px solid #e2e8f0;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: none;
            background-color: #ffffff;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            background-color: #ffffff;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-register {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: white;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #64748b;
        }

        .login-link a {
            text-decoration: none;
            color: #2563eb;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="shape shape-1"></div>
<div class="shape shape-2"></div>

<div class="register-card">

    <div class="logo-box">
        <img src="assets/logoLogin.png" alt="Logo" onerror="this.src='assets/logo.png'">
    </div>

    <div class="text-center mb-4">
        <h4 class="fw-bold" style="color: #1e293b;">Daftar Akun Mahasiswa</h4>
        <p class="text-muted small">Buat akun untuk mengakses sistem monitoring magang.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 12px; font-size: 14px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 12px; font-size: 14px;">
            <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="proses_register.php" method="POST">
        
        <div class="row g-3">
            
            <div class="col-md-6">
                <label class="form-label">NIM</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
                    <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM" required>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="nama" class="form-control" placeholder="Nama sesuai KTP" required>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Program Studi</label>
                <div class="input-group">
                    <span class="input-group-text border-right"><i class="bi bi-book"></i></span>
                    <select name="prodi" class="form-select border-start-0" required style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <option value="" selected disabled>Pilih Program Studi</option>
                        <option value="TIF">Teknik Informatika</option>
                        <option value="MIF">Manajemen Informatika</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nomor HP / WhatsApp</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                    <input type="text" name="no_hp" class="form-control" placeholder="0812xxxxxx" required>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="email@mahasiswa.ac.id" required>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Kata Sandi</label>
                <div class="input-group position-relative">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="pass1" class="form-control" placeholder="Buat kata sandi" required style="padding-right: 40px;">
                    <i class="bi bi-eye toggle-password" data-target="pass1" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d; z-index: 10;"></i>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Konfirmasi Kata Sandi</label>
                <div class="input-group position-relative">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" name="konfirmasi_password" id="pass2" class="form-control" placeholder="Ulangi kata sandi" required style="padding-right: 40px;">
                    <i class="bi bi-eye toggle-password" data-target="pass2" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #6c757d; z-index: 10;"></i>
                </div>
            </div>

        </div>

        <button type="submit" class="btn-register">
            <i class="bi bi-person-plus-fill me-2"></i> Daftar Sekarang
        </button>

    </form>

    <div class="login-link">
        Sudah punya akun? <a href="login.php">Masuk di sini</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const toggleIcons = document.querySelectorAll('.toggle-password');
    
    toggleIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const inputField = document.getElementById(targetId);
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                this.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                inputField.type = 'password';
                this.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
</script>
</body>
</html>