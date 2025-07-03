<?php
// game.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php"); // Redirect jika belum login
    exit();
}

include 'config/database.php';
include 'inc/header.php';

// Fungsi untuk mendapatkan gambar acak yang belum dijawab oleh user (opsional)
// Untuk simplicity, kita ambil gambar acak saja tanpa mempertimbangkan riwayat user
// Jika Anda sudah menambahkan tabel user_image_progress, Anda bisa memodifikasi fungsi ini
// untuk mengambil gambar yang belum dijawab oleh user_id saat ini.
function getRandomImage($conn, $user_id) {
    // Contoh query yang mempertimbangkan gambar yang belum dijawab (membutuhkan tabel user_image_progress)
    // $sql = "SELECT i.id, i.filename, i.answer, i.points
    //         FROM images i
    //         LEFT JOIN user_image_progress uip ON i.id = uip.image_id AND uip.user_id = ?
    //         WHERE uip.image_id IS NULL
    //         ORDER BY RAND() LIMIT 1";
    // $stmt = $conn->prepare($sql);
    // $stmt->bind_param("i", $user_id);
    // $stmt->execute();
    // $result = $stmt->get_result();

    // Untuk saat ini, kita gunakan query acak sederhana seperti sebelumnya:
    $sql = "SELECT id, filename, answer, points FROM images ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

$user_id = $_SESSION['user_id'];
$current_image = getRandomImage($conn, $user_id); // Mengirim user_id jika nanti fungsi getRandomImage diubah

// Ambil skor user saat ini
$stmt_score = $conn->prepare("SELECT score FROM scores WHERE user_id = ?");
$stmt_score->bind_param("i", $user_id);
$stmt_score->execute();
$result_score = $stmt_score->get_result();
$user_score = 0;
if ($result_score->num_rows > 0) {
    $user_score = $result_score->fetch_assoc()['score'];
}
$stmt_score->close();

?>

<div class="card shadow-sm mx-auto">
    <div class="card-header bg-primary text-white text-center">
        <h2>Main Tebak Gambar!</h2>
    </div>
    <div class="card-body text-center">
        <h4 class="mb-3">Skor Anda: <span id="userScoreDisplay" class="badge bg-success"><?php echo $user_score; ?></span></h4>
        <p id="message" class="text-info fs-5"></p>

        <?php if ($current_image): ?>
            <img id="gameImage" src="uploads/<?php echo htmlspecialchars($current_image['filename']); ?>" alt="Tebak Gambar" class="img-fluid rounded shadow-sm">
            <input type="hidden" id="currentImageId" value="<?php echo $current_image['id']; ?>">
            <input type="hidden" id="currentImageAnswer" value="<?php echo htmlspecialchars(strtoupper($current_image['answer'])); ?>">
            <input type="hidden" id="currentImagePoints" value="<?php echo $current_image['points']; ?>">

            <div class="input-group mb-3 mx-auto mt-4" style="max-width: 400px;">
                <input type="text" class="form-control form-control-lg answer-input" id="guessInput" placeholder="Ketik jawaban di sini" aria-label="Ketik jawaban" autocomplete="off">
                <button class="btn btn-primary btn-lg" type="button" id="submitGuessBtn">Tebak!</button>
            </div>
            <button class="btn btn-warning mt-2" id="skipImageBtn">Lewati Gambar Ini (+0 Poin)</button>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                Maaf, belum ada gambar soal yang tersedia. Silakan hubungi admin.
            </div>
        <?php endif; ?>

        <button class="btn btn-info mt-4" id="playAgainBtn" style="display: none;">Main Gambar Baru</button>
    </div>
</div>

<?php include 'inc/footer.php'; ?>