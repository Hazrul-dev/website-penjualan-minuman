<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'customer'; // Default role untuk pengguna baru

    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sss", $username, $password, $role);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = $role;
        
            // Tambahkan user ke user_profiles
            $sql_profile = "INSERT INTO user_profiles (user_id, username) VALUES (?, ?)";
            $stmt_profile = $conn->prepare($sql_profile);
        
            if ($stmt_profile) {
                $stmt_profile->bind_param("is", $_SESSION['user_id'], $username);
                
                if (!$stmt_profile->execute()) {
                    echo "Error saat menambahkan user ke user_profiles: " . $stmt_profile->error;
                    exit();
                }
        
                $stmt_profile->close();
            } else {
                echo "Error dalam query user_profiles: " . $conn->error;
                exit();
            }
        
            header('Location: index.php');
            exit();
        } else {
            echo "Error dalam query users: " . $stmt->error;
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/auth.css"> <!-- Gaya khusus untuk autentikasi -->
</head>
<body>
    <div class="auth-container animate__animated animate__fadeIn">
        <h1>Register</h1>
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
            <button type="submit" class="btn">Register</button>
        </form>
        <div class="auth-links">
            <a href="login.php">Sudah punya akun? Login</a>
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