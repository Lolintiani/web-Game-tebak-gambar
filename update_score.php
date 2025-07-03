<?php
// update_score.php
session_start();
include 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk memperbarui skor.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $user_id = $_SESSION['user_id'];
    $points = $data['points'] ?? 0;
    $image_id = $data['image_id'] ?? 0; // Bisa digunakan untuk menandai gambar yang sudah dijawab

    // Pastikan poin adalah angka dan image_id adalah angka
    if (!is_numeric($points) || !is_numeric($image_id)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
        exit();
    }

    // Ambil skor user saat ini
    $stmt_get_score = $conn->prepare("SELECT score FROM scores WHERE user_id = ?");
    $stmt_get_score->bind_param("i", $user_id);
    $stmt_get_score->execute();
    $result_get_score = $stmt_get_score->get_result();
    $current_score = 0;
    if ($result_get_score->num_rows > 0) {
        $current_score = $result_get_score->fetch_assoc()['score'];
    }
    $stmt_get_score->close();

    $new_score = $current_score + $points;

    // Update skor di tabel scores
    $stmt_update_score = $conn->prepare("UPDATE scores SET score = ?, last_played = NOW() WHERE user_id = ?");
    $stmt_update_score->bind_param("ii", $new_score, $user_id);

    if ($stmt_update_score->execute()) {
        echo json_encode(['success' => true, 'new_score' => $new_score, 'message' => 'Skor berhasil diperbarui.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui skor: ' . $stmt_update_score->error]);
    }
    $stmt_update_score->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
}

$conn->close();
?>