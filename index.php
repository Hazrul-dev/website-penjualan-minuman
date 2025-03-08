<?php
session_start();
include 'includes/db.php';

// Jika ada user_id di URL, update session agar tetap di akun yang benar
if (isset($_GET['user_id'])) {
    $_SESSION['user_id'] = $_GET['user_id'];
}

// Cek jika ada notifikasi pembayaran
$has_notification = isset($_SESSION['has_notification']) ? $_SESSION['has_notification'] : false;
$notification_message = isset($_SESSION['notification_message']) ? $_SESSION['notification_message'] : '';

// Hapus notifikasi dari session setelah dimuat
unset($_SESSION['has_notification']);
unset($_SESSION['notification_message']);

include 'includes/header.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    die("Selamat Datang di Aplikasi Penjualan Minuman Float Smoothies Medan");
    header('Location: login.php');
    exit();
}

// Ambil status pesanan terbaru
$user_id = $_SESSION['user_id'];
$sql = "SELECT status FROM payments WHERE user_id = ? AND status = 'approved' ORDER BY payment_date DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$latest_payment = $result->fetch_assoc();
$stmt->close();

// Ambil data profil pengguna
$user_id = $_SESSION['user_id'];
$sql = "SELECT user_profiles.profile_picture, users.username 
        FROM user_profiles 
        JOIN users ON user_profiles.user_id = users.id 
        WHERE user_profiles.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

// Ambil riwayat pembelian
$sql = "SELECT orders.id, orders.order_date, orders.total_price, 
               payments.delivery_fee, 
               (orders.total_price + payments.delivery_fee) AS total_payment, 
               payments.method, payments.bank, payments.status
        FROM orders 
        JOIN payments ON orders.id = payments.order_id 
        WHERE orders.user_id = ? 
        ORDER BY orders.order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if (!$stmt) {
    die("Query Error: " . $conn->error); // Menampilkan error jika query salah
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Pengaturan waktu pemesanan
$open_time = "08:00";
$close_time = "22:00";
$current_time = date("H:i");  // Get current time in 24-hour format
$is_open = (strtotime($current_time) >= strtotime($open_time) && strtotime($current_time) <= strtotime($close_time));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Gaya Umum */
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/kede.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .hero {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            text-align: center; /* Agar teks di dalamnya tetap rata tengah */
        }

        .hero-content {
            background: rgba(255, 105, 180, 0.85);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            margin: auto;
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 1s ease-out;
        }


        .profile-picture {
            margin-bottom: 1.5rem;
        }

        .profile-picture img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 0 20px rgba(255, 105, 180, 0.5);
            object-fit: cover;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .status-container {
            margin: 1.5rem 0;
            padding: 1rem;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .status-open {
            background: linear-gradient(135deg, #4caf50, #45a049);
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .status-closed {
            background: linear-gradient(135deg, #f44336, #f4f4f4);
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Opening Hours */
        .opening-hours {
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
            margin: 1.5rem 0;
        }

        .opening-hours p {
            margin: 0;
            font-size: 1rem;
        }

        .opening-hours strong {
            color: #fff;
            font-weight: 600;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #ff69b4, #ff1493);
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 105, 180, 0.6);
            background: linear-gradient(45deg, #ff1493, #ff69b4);
        }

        .about-container {
            background: rgba(255, 255, 255, 0.95);
            margin: 3rem auto;
            padding: 2rem;
            border-radius: 20px;
            max-width: 800px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        .about-container h2 {
            color: #ff1493;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .about-container p {
            line-height: 1.8;
            margin-bottom: 1rem;
        }

        .notification-modal {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255, 105, 180, 0.9); /* Warna pink cerah */
            padding: 20px;
            border-radius: 10px;
            z-index: 1000;
        }

        .purchase-history {
            margin: 40px auto;
            max-width: 800px;
            background: rgba(255, 255, 255, 0.9); /* Latar belakang putih dengan transparansi */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 1.5rem 0;
        }

        .modern-table th,
        .modern-table td {
            padding: 1rem;
            text-align: left;
            border: none;
        }

        .modern-table th {
            background: #ff69b4;
            color: #fff;
            font-weight: 600;
        }

        .modern-table th:first-child {
            border-top-left-radius: 10px;
        }

        .modern-table th:last-child {
            border-top-right-radius: 10px;
        }

        .modern-table tr:nth-child(even) td {
            background: rgba(255, 105, 180, 0.1);
        }

        .modern-table tr:nth-child(odd) td {
            background: #fff;
        }

        .modern-table td {
            color: #333;
            border-bottom: 1px solid rgba(255, 105, 180, 0.2);
        }

        .bell-icon {
            font-size: 24px;
            position: relative;
        }

        .bell-icon .notification-dot {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            border-radius: 50%;
            width: 10px;
            height: 10px;
        }

        .about-container {
            background: rgba(255, 255, 255, 0.9); /* Latar belakang putih dengan transparansi */
            padding: 20px;
            border-radius: 10px;
            margin: 40px auto;
            max-width: 800px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        .animate__animated {
            animation-duration: 1s;
        }

        /* Status Buka/Tutup */
        .status-container {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2em; /* Ukuran font lebih besar */
        }

        /* Efek Blur untuk Background */
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('images/hero-bg.jpg') no-repeat center center/cover;
            filter: blur(5px); /* Efek blur */
            z-index: -1;
        }
        .opening-hours {
            margin-top: 15px;
            font-size: 0.9em;
            color: #fff;
            background: rgba(0, 0, 0, 0.3); /* Latar belakang semi-transparan */
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .opening-hours strong {
            color: #ff69b4; /* Warna pink cerah untuk highlight jam */
        }

        /* Responsive Design */
    @media (max-width: 768px) {
        .hero-content {
            padding: 2rem;
            margin: 1rem;
        }

        .modern-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .about-container {
            margin: 2rem 1rem;
            padding: 1.5rem;
        }

        .hero-content h1 {
            font-size: 2rem;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>
<body>

<div class="hero-content animate__animated animate__fadeIn">
    <?php if ($profile && $profile['profile_picture']): ?>
        <div class="profile-picture">
            <img src="uploads/profiles/<?php echo $profile['profile_picture']; ?>" alt="Profile Picture">
        </div>
    <?php endif; ?>
    <h1>Welcome, <?php echo $profile['username'] ?? 'Guest'; ?>!</h1>
    <p>Order your favorite drinks now!</p>
    <div id="shop-status" class="status-container"></div>
    <div class="opening-hours">
        <p>Toko buka pada pukul <strong>08.00 WIB</strong> sampai dengan <strong>22.00 WIB</strong>.</p>
    </div>
    <a href="menu.php" class="btn animate__animated animate__pulse animate__infinite">View Menu</a>
</div>

    <div class="about-container animate__animated animate__fadeInUp">
        <h2>About Our Drink Sales</h2>
        <p>Situs web kami menawarkan berbagai macam minuman segar, mulai dari minuman klasik hingga kreasi unik. Kami mendapatkan bahan-bahan dari pemasok terbaik untuk memastikan kualitas terbaik bagi pelanggan kami. Dengan 8 pilihan minuman, kami melayani semua selera dan preferensi. Baik Anda ingin smoothie manis atau jus sehat, kami menyediakannya untuk Anda. Misi kami adalah menyediakan pengalaman pemesanan online yang lancar, lengkap dengan pengiriman cepat dan layanan pelanggan yang sangat baik. Bergabunglah dengan kami dalam menjelajahi dunia minuman lezat!</p>
    </div>

    <div class="about-container animate__animated animate__fadeInUp">
        <h2>Riwayat Pembelian</h2>
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $order['id']; ?></td>
                <td>Rp <?php echo number_format($order['total_payment']); ?></td>
                <td><?php echo date('d F Y H:i:s', strtotime($order['order_date'])); ?></td>
                <td><?php echo $order['method']; ?></td> <!-- Tampilkan metode pembayaran -->
                <td><?php echo $order['status']; ?></td>
                <td>
                    <a href="print_receipt.php?order_id=<?php echo $order['id']; ?>" class="btn">Cetak Bon</a>
                </td>
            </tr>
        <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    function toggleNotification() {
        let panel = document.getElementById("notification-panel");
        panel.style.display = (panel.style.display === "block") ? "none" : "block";

        if (panel.style.display === "block") {
            setTimeout(() => {
                panel.style.display = 'none';
            }, 5000);
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

    function updateShopStatus() {
    const now = new Date();
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();
    const currentTime = currentHour * 60 + currentMinute; // Convert to minutes

    const openHour = 8;
    const closeHour = 22;
    const openTime = openHour * 60; // Convert to minutes
    const closeTime = closeHour * 60; // Convert to minutes

    const statusContainer = document.getElementById("shop-status");
    
    if (currentTime >= openTime && currentTime <= closeTime) {
        statusContainer.innerHTML = "Opened";
        statusContainer.className = "status-container status-open";
    } else {
        statusContainer.innerHTML = "Toko Sedang Tutup...Silahkan Datang Kembali Besok:) 🔴";
        statusContainer.className = "status-container status-closed";
    }
}

// Update status setiap detik
setInterval(updateShopStatus, 1000);
updateShopStatus(); // Panggil fungsi pertama kali
</script>

    <?php include 'includes/footer.php'; ?>
    
</body>
</html>