<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = $_GET['order_id'];
$sql = "SELECT orders.id, users.username, 
               orders.total_price, 
               (orders.total_price + payments.delivery_fee) AS total_payment, 
               orders.order_date, payments.method, payments.status, 
               payments.delivery_fee, payments.bank
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        JOIN payments ON orders.id = payments.order_id 
        WHERE orders.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

// Cek apakah pembayaran telah disetujui
if (!$order || $order['status'] !== 'approved') {
    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = "Maaf, Receipt tidak bisa dicetak karena pembayaran gagal. Terima kasih :)";
    header("Location: index.php?user_id=" . $_SESSION['user_id']); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Bon - FLOAT SMOOTHIES MEDAN</title>
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            text-align: center;
            padding: 20px;
        }
        .receipt {
            background: #fff;
            padding: 20px;
            width: 400px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
            border: 2px dashed #007bff;
        }
        .logo {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo img {
            width: 100px;
        }
        .receipt h1 {
            text-align: center;
            font-size: 22px;
            color: #007bff;
            margin-bottom: 10px;
        }
        .receipt h2 {
            text-align: center;
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        .receipt p {
            font-size: 14px;
            line-height: 1.5;
            margin: 5px 0;
        }
        .receipt .detail {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }
        .receipt .detail p {
            font-size: 14px;
            margin: 5px 0;
        }
        .receipt .total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            color: #d9534f;
        }
        .thank-you {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-top: 15px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="logo">
            <img src="assets/float.jpg" alt="Logo">
        </div>
        <h1>FLOAT SMOOTHIES MEDAN</h1>
        <h2>Struk Pembayaran</h2>

        <!-- Informasi Pesanan -->
        <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
        <p><strong>Customer:</strong> <?php echo $order['username']; ?></p>
        <p><strong>Order Date:</strong> <?php echo date('d F Y H:i:s', strtotime($order['order_date'])); ?></p>

        <!-- Detail Pembayaran -->
        <div class="detail">
            <p><strong>Drink Fee:</strong> Rp <?php echo number_format($order['total_price']); ?></p>
            <p><strong>Delivery Fee:</strong> Rp <?php echo number_format($order['delivery_fee']); ?></p>
            <p class="total"><strong>Total Pembayaran:</strong> Rp <?php echo number_format($order['total_payment']); ?></p>
        </div>


        <!-- Metode Pembayaran -->
        <div class="detail">
        <p><strong>Payment Method:</strong> 
    <?php 
        if ($order['method'] === 'BANK_BRI') {
            echo 'BRImo';
        } elseif ($order['method'] === 'BANK_MANDIRI') {
            echo 'Livin';
        } else {
            echo $order['method']; 
        }
        ?>
        </p>

                <p><strong>Bank:</strong> <?php echo $order['bank']; ?></p>
        </div>

        <!-- Pesan Terima Kasih -->
        <p class="thank-you">Terima kasih telah berbelanja di FLOAT SMOOTHIES MEDAN! 🍹</p>
    </div>

    <script>
        window.onload = function() {
            window.print(); // Cetak otomatis
        }
    </script>
</body>
</html>