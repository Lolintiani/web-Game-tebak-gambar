<?php
// auth/register.php
session_start();
include '../config/database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Konfirmasi password tidak cocok.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah username atau email sudah ada
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $message = "Username atau email sudah terdaftar.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                // Inisialisasi skor untuk pengguna baru
                $new_user_id = $stmt->insert_id;
                $stmt_score = $conn->prepare("INSERT INTO scores (user_id, score) VALUES (?, 0)");
                $stmt_score->bind_param("i", $new_user_id);
                $stmt_score->execute();
                $stmt_score->close();

                $message = "Registrasi berhasil! Silakan <a href='login.php'>Login</a>.";
                echo "<script>alert('Registrasi berhasil! Silakan Login.'); window.location.href='login.php';</script>";
                exit();
            } else {
                $message = "Registrasi gagal: " . $stmt->error;
            }
        }
        $stmt_check->close();
        $stmt->close();
    }
}
$conn->close();
?>

<?php include '../inc/header.php'; ?>

<div class="card shadow-sm mx-auto mt-5" style="max-width: 450px;">
    <div class="card-header bg-primary text-white text-center">
        <h3>Daftar Akun Baru</h3>
    </div>
    <div class="card-body">
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'berhasil') !== false ? 'alert-success' : 'alert-danger'; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>
        <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</div>

<?php include '../inc/footer.php'; ?>