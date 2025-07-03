<?php

/**
 * Merekam aktivitas pengguna ke tabel activity_log.
 *
 * @param mysqli $conn Objek koneksi database MySQLi.
 * @param int $user_id ID pengguna yang melakukan aktivitas.
 * @param string $action Deskripsi singkat tentang aktivitas (misalnya, 'Login', 'Update Profile').
 * @param string|null $details Detail lebih lanjut tentang aktivitas (opsional).
 * @return bool True jika aktivitas berhasil dicatat, false jika terjadi kesalahan.
 */
function log_activity($conn, $user_id, $action, $details = null) {
    // Pastikan koneksi database valid
    if (!$conn instanceof mysqli || $conn->connect_error) {
        error_log("log_activity: Koneksi database tidak valid.");
        return false;
    }

    // Persiapkan statement SQL untuk mencatat aktivitas
    // Asumsi tabel activity_log memiliki kolom: id, user_id, action, details, created_at
    // 'created_at' idealnya diatur otomatis oleh database (DEFAULT CURRENT_TIMESTAMP)
    $sql = "INSERT INTO activity_log (user_id, action, details) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Periksa jika persiapan statement gagal
    if ($stmt === false) {
        error_log("log_activity: Gagal mempersiapkan statement SQL: " . $conn->error);
        return false;
    }

    // Bind parameter ke statement
    // "iss" -> i (integer untuk user_id), s (string untuk action), s (string untuk details)
    $bindSuccess = $stmt->bind_param("iss", $user_id, $action, $details);

    // Periksa jika bind_param gagal (jarang terjadi jika tipe cocok, tapi baik untuk berjaga-jaga)
    if ($bindSuccess === false) {
        error_log("log_activity: Gagal mengikat parameter: " . $stmt->error);
        $stmt->close();
        return false;
    }

    // Jalankan statement
    $executeSuccess = $stmt->execute();

    // Periksa jika eksekusi gagal
    if ($executeSuccess === false) {
        error_log("log_activity: Gagal mengeksekusi statement: " . $stmt->error);
        $stmt->close();
        return false;
    }

    // Tutup statement
    $stmt->close();

    // Jika semua berhasil
    return true;
}

// --- Contoh Penggunaan ---
// Pastikan Anda memiliki koneksi $conn yang valid ke database Anda
// $conn = new mysqli("localhost", "username", "password", "database_name");

// if ($conn->connect_error) {
//     die("Koneksi database gagal: " . $conn->connect_error);
// }

// Contoh penggunaan:
// if (log_activity($conn, 1, 'Login', 'Pengguna admin berhasil login dari IP 192.168.1.10')) {
//     echo "Aktivitas berhasil dicatat." . PHP_EOL;
// } else {
//     echo "Gagal mencatat aktivitas." . PHP_EOL;
// }

// if (log_activity($conn, 5, 'Perbarui Profil', 'Email diubah dari user@old.com menjadi user@new.com')) {
//     echo "Aktivitas perbarui profil berhasil dicatat." . PHP_EOL;
// } else {
//     echo "Gagal mencatat aktivitas perbarui profil." . PHP_EOL;
// }

// if (log_activity($conn, 3, 'Hapus Data')) { // Tanpa detail
//     echo "Aktivitas hapus data berhasil dicatat." . PHP_EOL;
// } else {
//     echo "Gagal mencatat aktivitas hapus data." . PHP_EOL;
// }

// $conn->close(); // Jangan lupa menutup koneksi saat tidak lagi diperlukan

?>