<?php
session_start();
require_once('koneksi.php'); // Menggunakan require_once agar lebih aman

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifikasi'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND email = '$email'");
    
    if (mysqli_num_rows($query) === 1) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['reset_id'] = $data['id'];
        
        // Pindah menggunakan header agar lebih halus daripada JavaScript
        header("Location: reset_password.php");
        exit;
    } else {
        echo "<script>alert('Username atau Email tidak cocok!'); window.location='lupa_password.php';</script>";
        exit;
    }
} else {
    // Jika user langsung akses file ini via URL, kembalikan ke login
    header("Location: login.php");
    exit;
}
?>