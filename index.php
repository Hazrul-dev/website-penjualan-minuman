<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

// Ambil data pengaturan toko
$sql_shop_settings = "SELECT * FROM shop_settings LIMIT 1";
$result_shop_settings = $conn->query($sql_shop_settings);
$shop_settings = $result_shop_settings->fetch_assoc();

// Ambil data statistik
$sql_total_orders = "SELECT COUNT(*) as total_orders FROM orders";
$result_total_orders = $conn->query($sql_total_orders);
$total_orders = $result_total_orders->fetch_assoc()['total_orders'];

$sql_total_revenue = "SELECT SUM(orders.total_price + payments.delivery_fee) as total_revenue 
                      FROM orders 
                      JOIN payments ON orders.id = payments.order_id";
$result_total_revenue = $conn->query($sql_total_revenue);
$total_revenue = $result_total_revenue->fetch_assoc()['total_revenue'];

$sql_pending_payments = "SELECT COUNT(*) as pending_payments FROM payments WHERE status = 'pending'";
$result_pending_payments = $conn->query($sql_pending_payments);
$pending_payments = $result_pending_payments->fetch_assoc()['pending_payments'];

if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status']; // 'approved' atau 'rejected'

    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();

    // Set notifikasi di sesi agar bisa digunakan di halaman index.php
    $_SESSION['has_notification'] = true;
    if ($status == 'approved') {
        $_SESSION['notification_message'] = "Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.";
    } else {
        $_SESSION['notification_message'] = "Proses minuman gagal untuk pengiriman.";
    }

    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/print-js@1.6.0/dist/print.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/print-js@1.6.0/dist/print.min.css">
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
        color: #black;
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
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background-color: #0f3460;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.5);
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

    .shop-image {
    text-align: center;
    margin: 20px 0;
    }

    .shop-image img {
        max-width: 300px; /* Sesuaikan ukuran maksimal gambar */
        height: auto;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .shop-image img:hover {
        transform: scale(1.05); /* Efek zoom saat hover */
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.5);
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
        <h1>Admin Dashboard</h1>
        <nav class="admin-navigation">
            <a href="index.php" class="nav-link active">Dashboard</a>
            <a href="manage_products.php" class="nav-link">Manage Products</a>
            <a href="manage_shop_settings.php" class="nav-link">Manage Shop Settings</a>
            <a href="../logout.php" class="nav-link">Logout</a>
        </nav>
    </div>
</header>

<div class="admin-container animate__animated animate__fadeIn">
    <h2>Dashboard Overview</h2>
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <p>Rp <?php echo number_format($total_revenue); ?></p>
        </div>
        <div class="stat-card">
            <h3>Pending Payments</h3>
            <p><?php echo $pending_payments; ?></p>
        </div>
    </div>

    <div class="admin-container animate__animated animate__fadeIn">
    <h2>Shop Information</h2>
    <div class="stats-container">
        <div class="stat-card">
            <h3>Shop Name</h3>
            <p><?php echo $shop_settings['shop_name'] ?? 'N/A'; ?></p>
        </div>
        <div class="stat-card">
            <h3>Shop Address</h3>
            <p><?php echo $shop_settings['shop_address'] ?? 'N/A'; ?></p>
        </div>
        <div class="stat-card">
            <h3>Opening Hours</h3>
            <p><?php echo $shop_settings['opening_hours'] ?? 'N/A'; ?></p>
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <h3>Bank BRI</h3>
            <p><?php echo $shop_settings['bank_bri'] ?? 'N/A'; ?></p>
        </div>
        <div class="stat-card">
            <h3>Bank Mandiri</h3>
            <p><?php echo $shop_settings['bank_mandiri'] ?? 'N/A'; ?></p>
        </div>
        <div class="stat-card">
            <h3>DANA</h3>
            <p><?php echo $shop_settings['dana'] ?? 'N/A'; ?></p>
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <h3>ShopeePay</h3>
            <p><?php echo $shop_settings['shopeepay'] ?? 'N/A'; ?></p>
        </div>
        <div class="stat-card">
            <h3>GoPay</h3>
            <p><?php echo $shop_settings['gopay'] ?? 'N/A'; ?></p>
        </div>
        <div class="stat-card">
            <h3>OVO</h3>
            <p><?php echo $shop_settings['ovo'] ?? 'N/A'; ?></p>
        </div>
    </div>

    <?php if ($shop_settings && $shop_settings['shop_image']): ?>
        <div class="shop-image">
            <img src="../uploads/<?php echo $shop_settings['shop_image']; ?>" alt="Shop Image">
        </div>
    <?php endif; ?>
</div>

    <div class="chart-container">
        <canvas id="ordersChart"></canvas>
    </div>

    <h2>Pending Payments</h2>
    <table class="modern-table">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Customer</th>
                <th>Method</th>
                <th>Amount</th>
                <th>Delivery Location</th>
                <th>Payment Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql_payments = "SELECT payments.id, users.username, payments.method, 
                                    (payments.amount) AS total_payment, 
                                    payments.delivery_location, payments.payment_date 
                            FROM payments 
                            JOIN users ON payments.user_id = users.id 
                            WHERE payments.status = 'pending'";
            $result_payments = $conn->query($sql_payments);
            while($row = $result_payments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['method']; ?></td>
                    <td>Rp <?php echo number_format($row['total_payment']); ?></td>
                    <td><?php echo $row['delivery_location']; ?></td>
                    <td><?php echo $row['payment_date']; ?></td>
                    <td>
                        <a href="approve_payment.php?id=<?php echo $row['id']; ?>" class="btn">Approve</a>
                        <a href="reject_payment.php?id=<?php echo $row['id']; ?>" class="btn">Reject</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    const ctx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [{
                label: 'Total Orders',
                data: [12, 19, 3, 5, 2, 3, 10],
                backgroundColor: 'rgba(0, 255, 136, 0.2)',
                borderColor: 'rgba(0, 255, 136, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2c3e50'
                    }
                },
                x: {
                    grid: {
                        color: '#2c3e50'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#eaeaea'
                    }
                }
            }
        }
    });
    // Fungsi untuk mencetak bon pembayaran
    function printReceipt(paymentId) {
        fetch(`fetch_receipt.php?id=${paymentId}`)
            .then(response => response.text())
            .then(data => {
                printJS({
                    printable: data,
                    type: 'raw-html',
                    style: 'body { font-family: Arial, sans-serif; padding: 20px; } h2 { color: #333; }'
                });
            })
            .catch(error => console.error('Error:', error));
    }
</script>

<footer>
        <div class="footer">
            <p>&copy; 2025 FLOAT SMOOTHIES MEDAN. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
</body>
</html>