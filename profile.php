<?php
// profile.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

include 'config/database.php';
include 'inc/header.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$user_data = null;
$user_score = 0;

// Ambil data user
$stmt_user = $conn->prepare("SELECT email, created_at FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows > 0) {
    $user_data = $result_user->fetch_assoc();
}
$stmt_user->close();

// Ambil skor user
$stmt_score = $conn->prepare("SELECT score, last_played FROM scores WHERE user_id = ?");
$stmt_score->bind_param("i", $user_id);
$stmt_score->execute();
$result_score = $stmt_score->get_result();
if ($result_score->num_rows > 0) {
    $score_data = $result_score->fetch_assoc();
    $user_score = $score_data['score'];
    $last_played = $score_data['last_played'];
}
$stmt_score->close();

$conn->close();
?>

<div class="card shadow-sm mx-auto">
    <div class="card-header bg-primary text-white text-center">
        <h2>Profil Pengguna: <?php echo htmlspecialchars($username); ?></h2>
    </div>
    <div class="card-body">
        <?php if ($user_data): ?>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
            <p><strong>Bergabung Sejak:</strong> <?php echo date("d-m-Y H:i:s", strtotime($user_data['created_at'])); ?></p>
            <hr>
            <h4>Statistik Game</h4>
            <p><strong>Skor Saat Ini:</strong> <span class="badge bg-success fs-5"><?php echo $user_score; ?></span></p>
            <p><strong>Terakhir Dimainkan:</strong> <?php echo isset($last_played) ? date("d-m-Y H:i:s", strtotime($last_played)) : 'Belum pernah bermain'; ?></p>
            <hr>
            <a href="game.php" class="btn btn-primary">Kembali ke Game</a>
            <a href="auth/logout.php" class="btn btn-danger">Logout</a>
        <?php else: ?>
            <div class="alert alert-danger">Data pengguna tidak ditemukan.</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'inc/footer.php'; ?>