// js/script.js

document.addEventListener('DOMContentLoaded', () => {
    const guessInput = document.getElementById('guessInput');
    const submitGuessBtn = document.getElementById('submitGuessBtn');
    const skipImageBtn = document.getElementById('skipImageBtn');
    const playAgainBtn = document.getElementById('playAgainBtn');
    const messageDisplay = document.getElementById('message');
    const userScoreDisplay = document.getElementById('userScoreDisplay');

    if (submitGuessBtn) { // Pastikan elemen game ada (hanya di halaman game.php)
        submitGuessBtn.addEventListener('click', checkAnswer);
        guessInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                checkAnswer();
            }
        });
        skipImageBtn.addEventListener('click', skipImage);
        playAgainBtn.addEventListener('click', reloadGamePage); // Reload untuk gambar baru
        guessInput.focus(); // Fokuskan input saat halaman dimuat
    }

    function checkAnswer() {
        const currentAnswer = document.getElementById('currentImageAnswer').value.toUpperCase().trim();
        const guessedAnswer = guessInput.value.toUpperCase().trim();
        const currentImageId = document.getElementById('currentImageId').value;
        const currentImagePoints = parseInt(document.getElementById('currentImagePoints').value);

        if (guessedAnswer === '') {
            showMessage('Masukkan jawaban Anda!', 'danger');
            return;
        }

        if (guessedAnswer === currentAnswer) {
            showMessage(`Benar! Anda mendapatkan ${currentImagePoints} poin.`, 'success');
            updateScore(currentImagePoints, currentImageId);
            disableGameControls();
            playAgainBtn.style.display = 'block';
        } else {
            showMessage('Salah! Coba lagi.', 'danger');
            // Biarkan user coba lagi, atau tambahkan fitur hint/life jika diinginkan
        }
    }

    function skipImage() {
        showMessage('Gambar dilewati. Main gambar baru!', 'info');
        updateScore(0, document.getElementById('currentImageId').value); // Tetap catat sudah melihat gambar ini (opsional)
        disableGameControls();
        playAgainBtn.style.display = 'block';
    }

    function disableGameControls() {
        guessInput.disabled = true;
        submitGuessBtn.disabled = true;
        skipImageBtn.disabled = true;
    }

    function reloadGamePage() {
        window.location.reload(); // Cara sederhana untuk memuat gambar baru
    }

    async function updateScore(points, imageId) {
        try {
            const response = await fetch('update_score.php', { // Buat file ini nanti
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    points: points,
                    image_id: imageId
                })
            });
            const data = await response.json();
            if (data.success) {
                userScoreDisplay.textContent = data.new_score;
                // messageDisplay.textContent += ` (Skor terbaru: ${data.new_score})`;
            } else {
                console.error('Gagal memperbarui skor:', data.message);
                showMessage('Gagal memperbarui skor. ' + data.message, 'danger');
            }
        } catch (error) {
            console.error('Error fetching:', error);
            showMessage('Terjadi kesalahan koneksi saat memperbarui skor.', 'danger');
        }
    }

    function showMessage(msg, type = 'info') {
        messageDisplay.textContent = msg;
        messageDisplay.className = `text-${type} fs-5`; // Bootstrap text color classes
    }
});