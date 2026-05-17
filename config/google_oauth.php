<?php
// config/google_oauth.php

// Masukkan Client ID dan Client Secret yang didapatkan dari Google Cloud Console
define('GOOGLE_CLIENT_ID', 'ISI_DENGAN_CLIENT_ID_ANDA');
define('GOOGLE_CLIENT_SECRET', 'ISI_DENGAN_CLIENT_SECRET_ANDA');

// Sesuaikan URL ini dengan environment Anda
// Contoh di lokal: http://localhost/magangs/auth_google.php
define('GOOGLE_REDIRECT_URI', 'http://localhost/magangs/auth_google.php');

function getGoogleLoginUrl() {
    $authUrl = "https://accounts.google.com/o/oauth2/v2/auth";
    $params = [
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'response_type' => 'code',
        'scope' => 'email profile',
        'access_type' => 'online',
        'prompt' => 'select_account'
    ];
    
    return $authUrl . '?' . http_build_query($params);
}
