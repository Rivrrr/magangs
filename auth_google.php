<?php
session_start();
require_once 'config/database.php';
require_once 'config/userchecker.php';
require_once 'config/google_oauth.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Tukar kode dengan access token
    $tokenUrl = "https://oauth2.googleapis.com/token";
    $tokenData = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tokenResponse = curl_exec($ch);
    curl_close($ch);

    $tokenInfo = json_decode($tokenResponse, true);

    if (isset($tokenInfo['access_token'])) {
        $accessToken = $tokenInfo['access_token'];

        // Ambil data user dari Google
        $userInfoUrl = "https://www.googleapis.com/oauth2/v2/userinfo";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);

        $googleUser = json_decode($userInfoResponse, true);

        if (isset($googleUser['email'])) {
            $email = $googleUser['email'];

            $db = new Database();
            $conn = $db->getConnection();
            $userModel = new UserModel($conn);

            // Cari user berdasarkan email
            $user = $userModel->findUser($email);

            if ($user) {
                // User ditemukan, buat session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'role' => $user['role']
                ];

                // Redirect sesuai role
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
                // Email tidak terdaftar
                $_SESSION['login_error'] = "Email SSO ($email) tidak terdaftar dalam sistem. Silakan hubungi admin atau daftar akun baru.";
                header("Location: login.php");
                exit;
            }
        }
    } else {
        $_SESSION['login_error'] = "Gagal mendapatkan token akses dari Google.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['login_error'] = "Akses ditolak atau terjadi kesalahan SSO.";
    header("Location: login.php");
    exit;
}
