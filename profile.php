<?php
session_start();
include 'includes/db.php';
include_once 'includes/functions.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    die("Error: User ID tidak ditemukan dalam sesi.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $location = $_POST['location'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $profile_picture = null;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/profiles/";
        $imageFileType = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = $new_filename;
        } else {
            $_SESSION['notification_message'] = "Gagal mengunggah gambar profil.";
            header("Location: profile.php");
            exit();
        }
    }

    if ($profile_picture) {
        $sql = "UPDATE user_profiles SET username = ?, profile_picture = ?, location = ?, email = ?, phone = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $profile_picture, $location, $email, $phone, $user_id);
    } else {
        $sql = "UPDATE user_profiles SET username = ?, location = ?, email = ?, phone = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $location, $email, $phone, $user_id);
    }

    if (!$stmt->execute()) {
        die("Error saat update profil: " . $stmt->error);
    }

    if ($stmt->execute()) {
        $_SESSION['notification_message'] = "Profil berhasil diperbarui.";
    } else {
        $_SESSION['notification_message'] = "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
    header("Location: profile.php");
    exit();
}

// Ambil data user
$sql = "SELECT * FROM user_profiles WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Gaya Umum */
        body {
            background: url('images/hero-bg.jpg') no-repeat center center/cover;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            color: #333;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #ff69b4; /* Warna pink cerah */
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease, transform 0.3s ease;
            font-size: 1em;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            background: #ff1493; /* Warna pink lebih gelap */
            transform: scale(1.05);
        }

        .notification {
            background: #4caf50; /* Warna hijau untuk notifikasi sukses */
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        /* Efek Blur untuk Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('images/kede.jpg') no-repeat center center/cover;
            filter: blur(5px); /* Efek blur */
            z-index: -1;
        }
    </style>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div class="profile-container animate__animated animate__fadeIn">
        <div class="profile-box">
            <h1>Profile</h1>
            <?php if (isset($_SESSION['notification_message'])): ?>
                <p class="notification"><?= $_SESSION['notification_message']; unset($_SESSION['notification_message']); ?></p>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="profile-picture">
                    <?php if ($profile && $profile['profile_picture']): ?>
                        <img src="uploads/profiles/<?php echo htmlspecialchars($profile['profile_picture']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <img src="images/default-profile.png" alt="Default Profile Picture">
                    <?php endif; ?>
                    <input type="file" id="profile_picture" name="profile_picture">
                </div>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($profile['location'] ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
                </div>
                <div class="input-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>" required>
                </div>
                <button type="submit" class="btn">Update Profile</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>
</html>