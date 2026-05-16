<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Magang (SIMMAG)</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #334155;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 28px;
            margin-right: 10px;
        }

        .navbar-brand span {
            color: #2563eb;
        }

        .btn-login-outline {
            color: #2563eb;
            border: 2px solid #2563eb;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 20px;
            transition: 0.2s;
        }

        .btn-login-outline:hover {
            background: #2563eb;
            color: white;
        }

        .btn-register-solid {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-register-solid:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .hero-section {
            padding: 120px 0 80px;
            position: relative;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -1px;
            margin-bottom: 1.5rem;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, #2563eb 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: #64748b;
            margin-bottom: 2.5rem;
            line-height: 1.6;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.5;
            animation: float 10s infinite ease-in-out alternate;
        }
        .shape-1 {
            width: 400px; height: 400px;
            background: #93c5fd;
            top: 50px; left: -100px;
        }
        .shape-2 {
            width: 300px; height: 300px;
            background: #c4b5fd;
            bottom: 50px; right: -50px;
            animation-delay: -5s;
        }

        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            100% { transform: translateY(30px) scale(1.05); }
        }

        .features-section {
            padding: 80px 0;
            background: white;
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 3rem;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 20px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: #e2e8f0;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .feature-desc {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
        }

        .roles-section {
            padding: 80px 0;
            background: #f8fafc;
        }

        .role-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            height: 100%;
        }

        .role-icon {
            font-size: 40px;
            color: #2563eb;
            margin-bottom: 15px;
        }

        .footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 40px 0;
            text-align: center;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .footer-logo img {
            height: 28px;
            margin-right: 10px;
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light navbar-custom fixed-top py-3">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="assets/logo.png" alt="SIMMAG Logo">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link text-dark fw-medium mx-2" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium mx-2" href="#fitur">Fitur Utama</a></li>
                    <li class="nav-item"><a class="nav-link text-dark fw-medium mx-2" href="#pengguna">Pengguna</a></li>
                </ul>
                <div class="d-flex gap-2 mt-3 mt-lg-0">
                    <a href="login.php" class="btn btn-login-outline text-decoration-none">Masuk</a>
                    <a href="register.php" class="btn btn-register-solid text-decoration-none">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center" id="beranda">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="container position-relative z-1">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-4 fw-semibold" style="font-size: 14px;">
                        🚀 Platform Magang Terpadu 2026
                    </span>
                    <h1 class="hero-title">
                        Pantau & Kelola Magang<br>
                        Lebih <span class="highlight">Mudah & Efisien</span>
                    </h1>
                    <p class="hero-subtitle">
                        Sistem Monitoring Magang (SIMMAG) mendigitalkan seluruh proses administrasi magang. Dari pengajuan proposal, logbook harian, hingga penilaian akhir dalam satu platform cerdas.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="register.php" class="btn btn-register-solid btn-lg px-4" style="border-radius: 12px;">Mulai Sekarang <i class="bi bi-arrow-right ms-2"></i></a>
                        <a href="#fitur" class="btn btn-light btn-lg px-4" style="border-radius: 12px; font-weight: 600; border: 1px solid #e2e8f0; color: #475569;">Pelajari Fitur</a>
                    </div>
                </div>
            </div>
            
            <div class="row justify-content-center mt-5 pt-4">
                <div class="col-lg-10">
                    <div class="rounded-4 p-2 shadow-lg" style="background: rgba(255, 255, 255, 0.4); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.8);">
                        <div class="rounded-3 overflow-hidden bg-white shadow-sm" style="border: 1px solid #e2e8f0;">
                            <div class="bg-light p-3 border-bottom d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-danger" style="width: 12px; height: 12px;"></div>
                                <div class="rounded-circle bg-warning" style="width: 12px; height: 12px;"></div>
                                <div class="rounded-circle bg-success" style="width: 12px; height: 12px;"></div>
                            </div>
                            <div class="bg-white text-center">
                                <img src="assets/home.png" alt="Tampilan Dashboard SIMMAG" class="img-fluid w-100" style="display: block;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="fitur">
        <div class="container">
            <h2 class="section-title">Semua yang Anda Butuhkan</h2>
            <p class="section-subtitle">Sistem yang dirancang khusus untuk mempermudah alur birokrasi magang.</p>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon bg-primary-subtle text-primary">
                            <i class="bi bi-file-earmark-arrow-up"></i>
                        </div>
                        <h4 class="feature-title">Pengajuan Online</h4>
                        <p class="feature-desc">Ajukan proposal magang ke program studi secara digital tanpa perlu mencetak dokumen fisik.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon bg-success-subtle text-success">
                            <i class="bi bi-journal-check"></i>
                        </div>
                        <h4 class="feature-title">Logbook Harian</h4>
                        <p class="feature-desc">Catat aktivitas magang harian dan dapatkan persetujuan langsung dari dosen pembimbing.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon bg-warning-subtle text-warning">
                            <i class="bi bi-chat-right-dots"></i>
                        </div>
                        <h4 class="feature-title">Review Interaktif</h4>
                        <p class="feature-desc">Dosen dapat memantau progres dan memberikan revisi atau catatan secara real-time.</p>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon bg-danger-subtle text-danger">
                            <i class="bi bi-clipboard2-data"></i>
                        </div>
                        <h4 class="feature-title">Penilaian Terpusat</h4>
                        <p class="feature-desc">Kirim laporan akhir dan dapatkan nilai akhir yang transparan dan terintegrasi langsung.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="roles-section" id="pengguna">
        <div class="container">
            <h2 class="section-title">Satu Sistem, Multi Peran</h2>
            <p class="section-subtitle">Akses yang disesuaikan untuk kenyamanan setiap pihak yang terlibat.</p>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="role-card">
                        <i class="bi bi-mortarboard role-icon"></i>
                        <h4 class="fw-bold mb-3" style="color:#1e293b;">Mahasiswa</h4>
                        <p class="text-muted" style="font-size:14px; line-height: 1.6;">Fokus pada pembelajaran dan praktik kerja di industri. Biarkan sistem kami yang mengurus segala administrasi, logbook, dan pelaporan akhir Anda.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="role-card">
                        <i class="bi bi-person-video3 role-icon text-success"></i>
                        <h4 class="fw-bold mb-3" style="color:#1e293b;">Dosen Pembimbing</h4>
                        <p class="text-muted" style="font-size:14px; line-height: 1.6;">Pantau progres, review laporan harian, dan berikan penilaian akhir untuk seluruh mahasiswa bimbingan dengan mudah melalui satu dasbor.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="role-card">
                        <i class="bi bi-shield-check role-icon text-warning"></i>
                        <h4 class="fw-bold mb-3" style="color:#1e293b;">Admin & Kaprodi</h4>
                        <p class="text-muted" style="font-size:14px; line-height: 1.6;">Kelola data induk mahasiswa, tetapkan dosen pembimbing, dan pantau penyebaran tempat magang mahasiswa secara komprehensif.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <h4 class="fw-bold text-white footer-logo">
                <img src="assets/logo.png" alt="SIMMAG Logo">
            </h4>
            <p class="mb-4" style="font-size: 14px;">Sistem Cerdas Manajemen Magang Mahasiswa.</p>
            <hr style="border-color: #334155; margin-bottom: 20px;">
            <p class="mb-0" style="font-size: 13px;">&copy; 2026 Sistem Monitoring Magang. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>