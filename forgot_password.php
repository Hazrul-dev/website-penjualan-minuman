<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    $sql = "UPDATE users SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $username);

    if ($stmt->execute()) {
        $success = "Password berhasil direset. Silakan login dengan password baru.";
    } else {
        $error = "Gagal reset password. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/auth.css"> <!-- Gaya khusus untuk autentikasi -->
</head>
<body>
    <div class="auth-container animate__animated animate__fadeIn">
        <h1>Lupa Password</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="input-group">
                <input type="text" id="username" name="username" required placeholder=" ">
                <label for="username">Username</label>
            </div>
            <div class="input-group">
                <input type="password" id="new_password" name="new_password" required placeholder=" ">
                <label for="new_password">Password Baru</label>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <div class="auth-links">
            <a href="login.php">Kembali ke Login</a>
        </div>
    </div>
    <div class="bg-animation">
    <div style="left: 10%; animation-delay: 0s;"></div>
    <div style="left: 30%; animation-delay: 2s;"></div>
    <div style="left: 50%; animation-delay: 4s;"></div>
    <div style="left: 70%; animation-delay: 6s;"></div>
    <div style="left: 90%; animation-delay: 8s;"></div>
</div>

<div class="logo-container">
<img src="float.jpg" alt="Float Smoothies">
</div>

<div class="welcome-message">
    <div class="welcome-title">Welcome to Float Smoothies Medan</div>
    <div class="welcome-subtitle">Pusat Minuman Terenak Sejagat Raya ✨</div>
</div>

</body>
</html>
