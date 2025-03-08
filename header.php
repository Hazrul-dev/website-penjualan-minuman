<?php
include 'includes/db.php';
include_once 'includes/functions.php';

$user_id = $_SESSION['user_id'] ?? 0; // Pastikan user_id tersedia

// Ambil daftar notifikasi
$notifications = getNotifications($user_id, $conn);

// Cek jika ada notifikasi dalam session
$has_notification = hasUnreadNotifications($user_id, $conn) || (isset($_SESSION['has_notification']) && $_SESSION['has_notification']);
$notification_message = isset($_SESSION['notification_message']) ? $_SESSION['notification_message'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>
<body>
<header>
    <div class="container">
        <div class="navbar-logo">
            <img src="assets/float.jpg" alt="FLOAT SMOOTHIES MEDAN">
        </div>
        <h1 class="header-title animate__animated animate__fadeIn">FLOAT SMOOTHIES MEDAN</h1>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="navbar">
                    <div class="navbar-menu">
                        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>
                        <a href="menu.php" class="<?= basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : '' ?>">Menu</a>
                        <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>

                    <!-- Ikon Notifikasi Bell -->
                    <div class="notification-bell" onclick="toggleNotification()">
                        🔔
                        <?php if ($has_notification): ?>
                            <span class="notification-dot"></span>
                        <?php endif; ?>
                    </div>

                    <!-- Panel Notifikasi -->
                    <div id="notification-panel" class="notification-panel">
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $notif): ?>
                                <p><?= htmlspecialchars($notif['message']); ?></p>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Tidak ada notifikasi</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-links">
                    <a href="register.php">Register</a>
                    <a href="login.php">Login</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
    <?php if (isset($_SESSION['notification_message'])): ?>
    <div class="notification-box animate__animated animate__fadeIn">
        <p><?= $_SESSION['notification_message']; ?></p>
        <?php unset($_SESSION['notification_message']); ?>
    </div>
<?php endif; ?>
</header>

<script>
    function toggleNotification() {
        let panel = document.getElementById("notification-panel");
        panel.style.display = (panel.style.display === "block") ? "none" : "block";

        if (panel.style.display === "block") {
            setTimeout(() => {
                panel.style.display = 'none';
            }, 5000);  // Notifikasi hilang otomatis setelah 5 detik
        }
    }

    function checkNotifications() {
    fetch("check_notifications.php")
        .then(response => response.json())
        .then(data => {
            let bell = document.querySelector(".notification-bell");
            let dot = document.querySelector(".notification-dot");

            if (data.has_notification) {
                if (!dot) {
                    let newDot = document.createElement("span");
                    newDot.classList.add("notification-dot");
                    bell.appendChild(newDot);
                }
            } else {
                if (dot) dot.remove();
            }
        });
}

setInterval(checkNotifications, 5000);

document.addEventListener("DOMContentLoaded", function () {
    const bellIcon = document.querySelector(".bell-icon");
    const notificationDropdown = document.querySelector(".notification-panel");

    bellIcon.addEventListener("click", function () {
        notificationDropdown.classList.toggle("show");
    });

    // Menutup dropdown jika klik di luar notifikasi
    document.addEventListener("click", function (event) {
        if (!bellIcon.contains(event.target) && !notificationDropdown.contains(event.target)) {
            notificationDropdown.classList.remove("show");
        }
    });
});

</script>