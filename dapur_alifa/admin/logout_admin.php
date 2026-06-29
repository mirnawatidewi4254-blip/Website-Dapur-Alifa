<?php
session_start();

// =============================================================
// PERBAIKAN: Hanya menghapus session milik Admin saja
// =============================================================
unset($_SESSION['login_admin_status']);
unset($_SESSION['role']);
unset($_SESSION['id_admin']);
unset($_SESSION['admin_nama']);

// Lempar kembali ke halaman login admin
header("Location: login_admin.php");
exit();
?>