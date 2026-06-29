<?php
session_start();
include('../koneksi.php');

$id = $_GET['id'];
$aksi = $_GET['aksi'];

if ($aksi == 'verif') {
    // Jika diverifikasi, status jadi 'Diproses' agar muncul di Data Pesanan
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan = 'Diproses' WHERE id_pesanan = '$id'");
    echo "<script>alert('Pembayaran Berhasil Diverifikasi!'); window.location='transaksi.php';</script>";
} else {
    // Jika ditolak, status bisa jadi 'Dibatalkan'
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan = 'Dibatalkan' WHERE id_pesanan = '$id'");
    echo "<script>alert('Pembayaran Ditolak!'); window.location='transaksi.php';</script>";
}
?>