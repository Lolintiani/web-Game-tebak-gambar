<?php
// FILE: includes/auth_check.php
// Letakkan file ini di folder includes/ Anda
// Digunakan untuk memeriksa apakah pengguna adalah admin yang login

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Jika tidak login atau bukan admin, arahkan ke halaman login admin
    header("Location: /admin/login.php"); // Sesuaikan path jika perlu
    exit();
}

// Jika sudah login dan adalah admin, lanjutkan eksekusi halaman
?>