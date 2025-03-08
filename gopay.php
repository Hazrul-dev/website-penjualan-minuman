<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = $_GET['order_id'] ?? null;
$delivery_location = $_GET['location'] ?? null;
$phone_number = $_GET['phone'] ?? null;

if (empty($order_id) || empty($delivery_location) || empty($phone_number)) {
    die("Data tidak valid. Pastikan semua field diisi.");
}

// Validasi nomor telepon
if (!preg_match('/^08[0-9]{9,12}$/', $phone_number)) {
    die("Nomor telepon tidak valid. Harus dimulai dengan 08 dan terdiri dari 10-13 digit.");
}

// Daftar kecamatan dan jarak dari toko (dalam km)
$kecamatan_jarak = [
    "Medan Amplas" => 5, "Medan Area" => 3, "Medan Barat" => 7, "Medan Baru" => 4, 
    "Medan Belawan" => 25, "Medan Deli" => 10, "Medan Denai" => 8, "Medan Helvetia" => 6, 
    "Medan Johor" => 5, "Medan Kota" => 2, "Medan Labuhan" => 18, "Medan Maimun" => 3, 
    "Medan Marelan" => 15, "Medan Perjuangan" => 4, "Medan Petisah" => 3, 
    "Medan Polonia" => 6, "Medan Selayang" => 7, "Medan Sunggal" => 9, 
    "Medan Tembung" => 12, "Medan Timur" => 5, "Medan Tuntungan" => 10
];

// Validasi kecamatan
if (!array_key_exists($delivery_location, $kecamatan_jarak)) {
    die("Kecamatan tidak valid. Silakan pilih kecamatan yang tersedia.");
}

// Hitung ongkos kirim (Rp 1000 per km)
$distance = $kecamatan_jarak[$delivery_location];
$delivery_fee = $distance * 1000;

// Cek jika jarak lebih dari 20 km
if ($distance > 20) {
    $error = "Maaf, pengiriman tidak bisa dilakukan karena jarak lebih dari 20 km.";
}

// Ambil total harga pesanan dari database
$order_sql = "SELECT total_price FROM orders WHERE id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();

// Cek apakah order ditemukan
if (!$order) {
    die("Error: Pesanan tidak ditemukan di database.");
}

// Ambil total harga pesanan (jika kosong, anggap 0)
$total_price = isset($order['total_price']) ? $order['total_price'] : 0;

// Hitung total belanja (harga minuman + ongkos kirim)
$total_payment = isset($order['total_price']) ? $order['total_price'] + $delivery_fee : $delivery_fee;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $captcha = $_POST['captcha'];

    // Validasi captcha
    if (!isset($_SESSION['captcha']) || $captcha != $_SESSION['captcha']) {
        $error = "Captcha tidak valid!";
    } else {
        // Ambil total harga dari order
        $order_sql = "SELECT total_price FROM orders WHERE id = ?";
        $order_stmt = $conn->prepare($order_sql);
        $order_stmt->bind_param("i", $order_id);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        $order = $order_result->fetch_assoc();
        $order_stmt->close();

        // Simpan data pembayaran
        $sql = "INSERT INTO payments (order_id, method, amount, phone_number, delivery_location, delivery_fee, user_id, status) 
                VALUES (?, 'Gopay', ?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param(
            "idssdi",
            $order_id,
            $order['total_price'],
            $phone_number,
            $delivery_location,
            $delivery_fee,
            $_SESSION['user_id']
        );

        if ($stmt->execute()) {
            header("Location: thank_you.php");
            exit();
        } else {
            $error = "Gagal menyimpan data pembayaran.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gopay Payment - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }
        .input-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
        }
        .error {
            color: #dc3545;
            text-align: center;
            margin-bottom: 1rem;
        }
        label {
            color: black !important; /* Mengubah warna teks menjadi hitam */
        }
        input[readonly] {
            color: black !important; /* Warna teks untuk input readonly */
            font-weight: bold; /* Opsional: Membuat teks lebih tebal */
        }

    </style>
</head>
<body>
    <div class="payment-container animate__animated animate__fadeIn">
        <h1>Gopay Payment</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php elseif ($distance > 20): ?>
            <p class="error">Maaf, pengiriman tidak bisa dilakukan karena jarak lebih dari 20 km.</p>
        <?php else: ?>
            <form action="" method="POST">
                <div class="input-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?php echo $phone_number; ?>" readonly>
                </div>
                <div class="input-group">
                    <label for="delivery_fee">Ongkos Kirim</label>
                    <input type="text" id="delivery_fee" name="delivery_fee" value="Rp <?php echo number_format($delivery_fee, 2); ?>" readonly>
                </div>
                <div class="input-group">
                    <label for="total_payment">Total Belanja</label>
                    <input type="text" id="total_payment" name="total_payment" value="Rp <?php echo number_format($total_payment, 2); ?>" readonly>
                </div>
                <div class="input-group">
                <label for="captcha" style="color: black;">Captcha</label>
                <input type="text" id="captcha" name="captcha" required style="color: black; font-weight: bold;">
                <img src="captcha.php" alt="Captcha" onclick="this.src='captcha.php?'+Math.random();" style="cursor: pointer;">
                </div>
                <button type="submit" class="btn">Submit</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>