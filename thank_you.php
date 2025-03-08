<?php
session_start();
include 'includes/db.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT status FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if ($order) {
    if ($order['status'] == 'approved') {
        $_SESSION['has_notification'] = true;
        $_SESSION['notification_message'] = "Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.";
    } elseif ($order['status'] == 'rejected') {
        $_SESSION['has_notification'] = true;
        $_SESSION['notification_message'] = "Proses minuman gagal untuk pengiriman.";
    } else {
        $_SESSION['has_notification'] = true;
        $_SESSION['notification_message'] = "Pembelian sedang dalam kemasan... Tunggu konfirmasi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | Float Smoothies</title>
    <style>
        :root {
            --primary-pink: #ff69b4;
            --secondary-pink: #ff8dc7;
            --light-pink: #ffe6f2;
            --dark-pink: #d44a98;
            --neon-pink: #ff1493;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            background: linear-gradient(135deg, #000000, #1a0011);
            color: white;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
            perspective: 1000px;
        }

        .container {
            position: relative;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2;
        }

        .content {
            background: rgba(255, 105, 180, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            border: 2px solid rgba(255, 105, 180, 0.3);
            box-shadow: 0 0 30px rgba(255, 105, 180, 0.3);
            animation: floatCard 3s ease-in-out infinite;
            transform-style: preserve-3d;
            max-width: 500px;
            width: 90%;
        }

        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary-pink);
            text-shadow: 0 0 10px var(--neon-pink);
            animation: glowText 2s ease-in-out infinite;
        }

        p {
            font-size: 1.2rem;
            color: var(--light-pink);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn {
            background: linear-gradient(45deg, var(--primary-pink), var(--neon-pink));
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: inline-block;
            box-shadow: 0 5px 20px rgba(255, 105, 180, 0.5);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 105, 180, 0.6);
        }

        .btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        .hearts {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .heart {
            position: absolute;
            width: 20px;
            height: 20px;
            background: var(--primary-pink);
            transform: rotate(45deg);
            animation: floatHeart 4s ease-in infinite;
            opacity: 0;
        }

        .heart::before,
        .heart::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: inherit;
        }

        .heart::before {
            left: -10px;
        }

        .heart::after {
            top: -10px;
        }

        .sparkles {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes floatCard {
            0%, 100% { transform: translateY(0) rotateX(2deg); }
            50% { transform: translateY(-20px) rotateX(-2deg); }
        }

        @keyframes glowText {
            0%, 100% { text-shadow: 0 0 10px var(--neon-pink); }
            50% { text-shadow: 0 0 20px var(--neon-pink), 0 0 30px var(--neon-pink); }
        }

        @keyframes shimmer {
            0% { transform: rotate(45deg) translateX(-100%); }
            100% { transform: rotate(45deg) translateX(100%); }
        }

        @keyframes floatHeart {
            0% {
                transform: rotate(45deg) translateY(0) scale(0);
                opacity: 0;
            }
            20% {
                opacity: 0.8;
            }
            100% {
                transform: rotate(45deg) translateY(-100vh) scale(1);
                opacity: 0;
            }
        }

        canvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 0;
        }

        .order-status {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 15px;
            margin: 1rem 0;
            border-left: 4px solid var(--neon-pink);
            animation: statusPulse 2s infinite;
        }

        .audio-control {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

        .audio-btn {
            background: rgba(255, 105, 180, 0.2);
            border: 2px solid var(--primary-pink);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 0 15px rgba(255, 105, 180, 0.3);
        }

        .audio-btn:hover {
            transform: scale(1.1);
            background: rgba(255, 105, 180, 0.3);
        }

        .audio-btn.muted {
            opacity: 0.7;
        }

        @keyframes statusPulse {
            0%, 100% { background: rgba(255, 255, 255, 0.1); }
            50% { background: rgba(255, 105, 180, 0.2); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>🎀 Terima Kasih!</h1>
            <p>Terimakasih telah berbelanja di Float Smoothies! Kami akan memproses pesanan Anda 💕</p>
            
            <div class="order-status">
                <?php
                if ($order) {
                    if ($order['status'] == 'approved') {
                        echo "<p>✨ Pesanan dikonfirmasi! Smoothies Anda sedang dalam perjalanan 🚀</p>";
                    } elseif ($order['status'] == 'rejected') {
                        echo "<p>😔 Mohon maaf, pesanan tidak dapat diproses</p>";
                    } else {
                        echo "<p>🎵 Minuman Anda sedang diracik...</p>";
                    }
                }
                ?>
            </div>
            
            <button class="btn" onclick="window.location.href='index.php?user_id=<?php echo $_SESSION['user_id']; ?>'">
                Kembali ke Beranda ✨
            </button>
        </div>
    </div>

    <div class="hearts" id="hearts"></div>
    <canvas id="fireworksCanvas"></canvas>

    <audio id="fireworkSound" src="sounds/explosion2.mp3"></audio>
    <audio id="backgroundMusic" loop>
        <source src="sounds/explosion2.mp3" type="audio/mp3">
    </audio>
    <audio id="thankYouSound" src="sounds/thank-you-cute.mp3"></audio>

    <!-- Add audio control button -->
    <div class="audio-control">
        <button id="toggleAudio" class="audio-btn">
            🔊
        </button>
    </div>

    <script>
        // Create floating hearts
        function createHeart() {
            const heart = document.createElement('div');
            heart.classList.add('heart');
            heart.style.left = Math.random() * 100 + 'vw';
            heart.style.animationDuration = (Math.random() * 3 + 2) + 's';
            document.getElementById('hearts').appendChild(heart);

            setTimeout(() => {
                heart.remove();
            }, 5000);
        }

        setInterval(createHeart, 300);

        // Enhanced fireworks with pink colors
        const canvas = document.getElementById('fireworksCanvas');
        const ctx = canvas.getContext('2d');

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        class Firework {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.particles = [];
                this.createParticles();
            }

            createParticles() {
                const colors = ['#ff69b4', '#ff8dc7', '#ff1493', '#ff99cc', '#ff007f'];
                for (let i = 0; i < 100; i++) {
                    const angle = Math.random() * 2 * Math.PI;
                    const speed = Math.random() * 5 + 2;
                    this.particles.push({
                        x: this.x,
                        y: this.y,
                        speedX: Math.cos(angle) * speed,
                        speedY: Math.sin(angle) * speed,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        life: Math.random() * 30 + 30,
                        size: Math.random() * 2 + 1
                    });
                }
            }

            update() {
                this.particles.forEach((p, index) => {
                    p.x += p.speedX;
                    p.y += p.speedY;
                    p.speedY += 0.05;
                    p.life--;
                    if (p.life <= 0) {
                        this.particles.splice(index, 1);
                    }
                });
            }

            draw() {
                this.particles.forEach(p => {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.size, 0, 2 * Math.PI);
                    ctx.fillStyle = p.color;
                    ctx.fill();
                });
            }
        }

        const fireworks = [];

        function animate() {
        ctx.fillStyle = 'rgba(0, 0, 0, 0.1)';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        if (Math.random() < 0.03) {
            createFirework(
                Math.random() * canvas.width,
                canvas.height + 10
            );
        }

        fireworks.forEach((firework, index) => {
            firework.update();
            firework.draw();
            if (firework.particles.length === 0) {
                fireworks.splice(index, 1);
            }
        });

        requestAnimationFrame(animate);
    }

    // Start the animation
    animate();

        // Resize canvas when window is resized
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // Audio Management
    const fireworkSound = document.getElementById('fireworkSound');
    const backgroundMusic = document.getElementById('backgroundMusic');
    const thankYouSound = document.getElementById('thankYouSound');
    const toggleAudioBtn = document.getElementById('toggleAudio');
    let isMuted = false;

    // Function to handle audio button click
    toggleAudioBtn.addEventListener('click', () => {
        isMuted = !isMuted;
        toggleAudioBtn.textContent = isMuted ? '🔇' : '🔊';
        toggleAudioBtn.classList.toggle('muted');
        
        backgroundMusic.volume = isMuted ? 0 : 0.3;
        fireworkSound.volume = isMuted ? 0 : 0.4;
        thankYouSound.volume = isMuted ? 0 : 0.5;
    });

    // Play background music on page load
    document.addEventListener('DOMContentLoaded', () => {
        // Set initial volumes
        backgroundMusic.volume = 0.3;
        fireworkSound.volume = 0.4;
        thankYouSound.volume = 0.5;

        // Play background music
        backgroundMusic.play().catch(() => {
            console.log('Autoplay prevented. User needs to interact first.');
        });

        // Play thank you sound
        thankYouSound.play().catch(() => {
            console.log('Autoplay prevented. User needs to interact first.');
        });
    });
    // Update firework creation to include sound
    function createFirework(x, y) {
        fireworks.push(new Firework(x, y));
        if (!isMuted) {
            // Clone and play firework sound
            const soundClone = fireworkSound.cloneNode();
            soundClone.volume = 0.4;
            soundClone.play().catch(() => {});
            // Remove the cloned audio element after it's done playing
            soundClone.onended = () => soundClone.remove();
        }
    }
    </script>
</body>
</html>