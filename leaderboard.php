<?php
// leaderboard.php
session_start();
include 'config/database.php';
include 'inc/header.php';

// Ambil data highscore dari database
$sql = "SELECT u.username, s.score FROM users u JOIN scores s ON u.id = s.user_id ORDER BY s.score DESC LIMIT 20";
$result = $conn->query($sql);
?>

<div class="card shadow-sm mx-auto">
    <div class="card-header bg-primary text-white text-center">
        <h2>Papan Peringkat</h2>
    </div>
    <div class="card-body">
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Username</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $rank++; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo $row['score']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">Belum ada skor yang tercatat. Ayo mainkan game!</div>
        <?php endif; ?>
    </div>
</div>

<?php
$conn->close();
include 'inc/footer.php';
?>