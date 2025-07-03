<?php
// auth/login.php
session_start(); // Pastikan ini ada di baris paling atas

if (isset($_SESSION['user_id'])) {
    header("Location: ../game.php"); // Redirect ke game jika sudah login
    exit();
}

include '../config/database.php'; // Path relatif ke database.php
include '../inc/header.php';     // Path relatif ke header.php

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Gunakan prepared statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password yang di-hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $message = "<p class='text-success'>Login berhasil! Anda akan diarahkan...</p>";
            header("Refresh: 2; url=../game.php"); // Redirect setelah 2 detik
        } else {
            $message = "<p class='text-danger'>Username atau password salah.</p>";
        }
    } else {
        $message = "<p class='text-danger'>Username atau password salah.</p>";
    }
    $stmt->close();
}
?>

<div class="card shadow-sm mx-auto mt-5" style="max-width: 450px;">
    <div class="card-header text-center"> <h2 class="mb-0">Login</h2>
    </div>
    <div class="card-body">
        <?php if ($message): ?>
            <div class="alert alert-info text-center" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">Login</button> </form>
        <p class="text-center mt-3">Belum punya akun? <a href="register.php" class="text-secondary fw-bold">Daftar di sini</a></p> </div>
</div>

<?php include '../inc/footer.php'; ?>