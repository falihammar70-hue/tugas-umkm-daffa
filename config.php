<?php
session_start();

$koneksi = mysqli_connect("localhost", "root", "", "db_toko");

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

function redirect($url)
{
    header('Location: ' . $url);
    exit();
}
?>