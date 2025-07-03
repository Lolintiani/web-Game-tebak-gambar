<?php
// index.php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: game.php");
    exit();
}
include 'inc/header.php';
?>

<div class="card shadow-sm text-center mx-auto mt-5">
    <div class="card-header"> <h2>Selamat Datang di Game Tebak Gambar!</h2>
    </div>
    <div class="card-body">
        <p class="lead">Uji pengetahuan visualmu dengan menebak gambar-gambar menarik.</p>
        <p>Login atau daftar untuk memulai petualangan menebak gambar!</p>
        <a href="auth/login.php" class="btn btn-primary btn-lg me-2">Login</a>
        <a href="auth/register.php" class="btn btn-success btn-lg">Daftar</a> </div>
</div>

<?php include 'inc/footer.php'; ?>