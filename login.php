<?php
session_start();
session_unset(); // Hapus semua variabel sesi
session_destroy(); // Hancurkan sesi lama
session_start(); // Mulai sesi baru
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // Set role

        if ($user['role'] == 'admin') {
            header('Location: admin/index.php');
        } else {
            header('Location: index.php');
        }
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/auth.css"> <!-- Gaya khusus untuk autentikasi -->
</head>
<body>
    <div class="auth-container animate__animated animate__fadeIn">
        <h1>Login</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="input-group">
                <input type="text" id="username" name="username" required placeholder=" ">
                <label for="username">Username</label>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" required placeholder=" ">
                <label for="password">Password</label>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="auth-links">
            <a href="register.php">Register</a> | 
            <a href="forgot_password.php">Lupa Password?</a>
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