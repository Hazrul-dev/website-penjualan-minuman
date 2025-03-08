<?php
// admin/manage_shop_settings.php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

// Ambil data pengaturan toko
$sql = "SELECT * FROM shop_settings LIMIT 1";
$result = $conn->query($sql);
$shop_settings = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shop_name = $_POST['shop_name'];
    $shop_address = $_POST['shop_address'];
    $bank_bri = $_POST['bank_bri'];
    $bank_mandiri = $_POST['bank_mandiri'];
    $dana = $_POST['dana'];
    $shopeepay = $_POST['shopeepay'];
    $gopay = $_POST['gopay'];
    $ovo = $_POST['ovo'];
    $opening_hours = $_POST['opening_hours'];

    // Handle image upload
    $shop_image = $shop_settings['shop_image'];
    if ($_FILES['shop_image']['error'] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["shop_image"]["name"]);
        move_uploaded_file($_FILES["shop_image"]["tmp_name"], $target_file);
        $shop_image = basename($_FILES["shop_image"]["name"]);
    }

    if ($shop_settings) {
        // Update existing settings
        $sql = "UPDATE shop_settings SET shop_name = ?, shop_address = ?, shop_image = ?, bank_bri = ?, bank_mandiri = ?, dana = ?, shopeepay = ?, gopay = ?, ovo = ?, opening_hours = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssi", $shop_name, $shop_address, $shop_image, $bank_bri, $bank_mandiri, $dana, $shopeepay, $gopay, $ovo, $opening_hours, $shop_settings['id']);
    } else {
        // Insert new settings
        $sql = "INSERT INTO shop_settings (shop_name, shop_address, shop_image, bank_bri, bank_mandiri, dana, shopeepay, gopay, ovo, opening_hours) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $shop_name, $shop_address, $shop_image, $bank_bri, $bank_mandiri, $dana, $shopeepay, $gopay, $ovo, $opening_hours);
    }

    $stmt->execute();
    $stmt->close();

    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = "Pengaturan toko berhasil diperbarui.";

    header("Location: manage_shop_settings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shop Settings - FLOAT SMOOTHIES MEDAN</title>
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <style>
        /* Gaya Umum untuk Semua Halaman Admin */
    body {
        background-color: #1a1a2e;
        color: #eaeaea;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
    }

    header {
            background-color: #002855; /* Warna biru gelap */
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
    }

    header .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    header h1 {
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #ffff;
    }

    /* Navigasi Admin */
    .admin-navigation {
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .admin-navigation .nav-link {
        text-decoration: none;
        color: #007bff;
        font-size: 1rem;
        padding: 10px 20px;
        border: 2px solid transparent;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .admin-navigation .nav-link:hover {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .admin-navigation .nav-link.active {
        background-color: #0056b3;
        color: #fff;
        border-color: #0056b3;
    }

    /* Container Admin */
    .admin-container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
        background-color: #16213e;
        border-radius: 10px;
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.5);
    }

    .admin-container h2 {
        font-size: 24px;
        color: #00d9ff;
        margin-bottom: 20px;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        font-size: 14px;
        margin-bottom: 5px;
        color: #eaeaea;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #2c3e50;
        background-color: #1a1a2e;
        color: #eaeaea;
        font-family: 'Poppins', sans-serif;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #00d9ff;
        outline: none;
    }

    /* Tombol */
    .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #00d9ff;
        color: #fff;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #00b2cc;
    }

    /* Tabel Modern */
    .modern-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .modern-table th,
    .modern-table td {
        text-align: left;
        padding: 12px;
        border: 1px solid #2c3e50;
    }

    .modern-table th {
        background-color: #0f3460;
        color: #fff;
    }

    .modern-table td {
        background-color: #1a1a2e;
    }

    .modern-table tr:hover td {
        background-color: #0f3460;
        color: #fff;
    }

    .modern-table img {
        border-radius: 5px;
        max-width: 100px;
        height: auto;
    }

    /* Stat Card */
    .stats-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .stat-card {
        background-color: #0f3460;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        flex: 1 1 calc(33.333% - 20px);
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-10px);
    }

    .stat-card h3 {
        font-size: 18px;
        color: #00d9ff;
        margin-bottom: 10px;
    }

    .stat-card p {
        font-size: 22px;
        font-weight: bold;
        color: #eaeaea;
    }

    .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
    }

    /* Animasi */
    .animate__animated {
        animation-duration: 0.5s;
    }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Manage Shop Settings</h1>
        <nav class="admin-navigation">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="manage_products.php" class="nav-link">Manage Products</a>
            <a href="manage_shop_settings.php" class="nav-link">Manage Shop Settings</a>
            <a href="../logout.php" class="nav-link">Logout</a>
        </nav>
    </div>
</header>

<div class="admin-container">
    <h2>Shop Settings</h2>
    <form action="manage_shop_settings.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="shop_name">Shop Name</label>
            <input type="text" id="shop_name" name="shop_name" value="<?php echo $shop_settings['shop_name'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="shop_address">Shop Address</label>
            <textarea id="shop_address" name="shop_address" required><?php echo $shop_settings['shop_address'] ?? ''; ?></textarea>
        </div>
        <div class="form-group">
            <label for="shop_image">Shop Image</label>
            <input type="file" id="shop_image" name="shop_image">
            <?php if ($shop_settings && $shop_settings['shop_image']): ?>
                <img src="../uploads/<?php echo $shop_settings['shop_image']; ?>" alt="Shop Image" style="max-width: 200px; margin-top: 10px;">
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="bank_bri">Bank BRI</label>
            <input type="text" id="bank_bri" name="bank_bri" value="<?php echo $shop_settings['bank_bri'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="bank_mandiri">Bank Mandiri</label>
            <input type="text" id="bank_mandiri" name="bank_mandiri" value="<?php echo $shop_settings['bank_mandiri'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="dana">DANA</label>
            <input type="text" id="dana" name="dana" value="<?php echo $shop_settings['dana'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="shopeepay">ShopeePay</label>
            <input type="text" id="shopeepay" name="shopeepay" value="<?php echo $shop_settings['shopeepay'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="gopay">GoPay</label>
            <input type="text" id="gopay" name="gopay" value="<?php echo $shop_settings['gopay'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="ovo">OVO</label>
            <input type="text" id="ovo" name="ovo" value="<?php echo $shop_settings['ovo'] ?? ''; ?>">
        </div>
        <div class="form-group">
            <label for="opening_hours">Opening Hours</label>
            <textarea id="opening_hours" name="opening_hours"><?php echo $shop_settings['opening_hours'] ?? ''; ?></textarea>
        </div>
        <button type="submit" class="btn">Save Settings</button>
    </form>
</div>

<footer>
        <div class="footer">
            <p>&copy; 2025 FLOAT SMOOTHIES MEDAN. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
</body>
</html>