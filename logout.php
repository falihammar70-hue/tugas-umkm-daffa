<?php
//logout.php - Keluar dari sesi
require_once __DIR__ . '/config/koneksi.php';
/**@var mysqli $koneksi */
global $koneksi;
//kosongkan array sesi
$_SESSION = [];

//hapus cookie sesi jika ada 
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000
        , $params["path"], $params["domain"]
        , $params["secure"], $params["httponly"]
    );
}

//hancurkan sesi 
session_destroy();

//mulai sesi baru untuk flash message
session_start();
$_SESSION['flash_success'] = 'Anda berhasil keluar dari akun.';
header("Location: index.php");
exit;

