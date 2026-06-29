<?php
// Menjalankan session
session_start();

// Menghapus semua data session yang tersimpan
session_unset();

// Menghancurkan/mematikan session
session_destroy();

// Mengarahkan user kembali ke halaman login
header("Location: login.php");
exit;
?>