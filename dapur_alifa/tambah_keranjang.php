<?php
session_start();
include('koneksi.php');

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_menu = $_GET['id'];
$id_user = $_SESSION['id_user'];

// Cek apakah item sudah ada di keranjang user
$cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user='$id_user' AND id_menu='$id_menu'");

if (mysqli_num_rows($cek) > 0) {
    mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id_user='$id_user' AND id_menu='$id_menu'");
} else {
    mysqli_query($conn, "INSERT INTO keranjang (id_user, id_menu, jumlah) VALUES ('$id_user', '$id_menu', 1)");
}

header("Location: keranjang.php");
?>