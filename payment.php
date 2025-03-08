<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';

// Ambil order_id, simpan ke session jika belum ada
$order_id = $_GET['order_id'] ?? $_SESSION['order_id'] ?? null;

if (!$order_id) {
    die("Order ID tidak ditemukan.");
}

if (!isset($_SESSION['order_id']) && $order_id) {
    $_SESSION['order_id'] = $order_id;
}


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
if (!$stmt) {
    die("Query error: " . $conn->error); // Menampilkan error MySQL
}

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();
$conn->close();


// Daftar kecamatan di Medan
$valid_kecamatan = [
    "Medan Amplas", "Medan Area", "Medan Barat", "Medan Baru", "Medan Belawan", 
    "Medan Deli", "Medan Denai", "Medan Helvetia", "Medan Johor", "Medan Kota", 
    "Medan Labuhan", "Medan Maimun", "Medan Marelan", "Medan Perjuangan", 
    "Medan Petisah", "Medan Polonia", "Medan Selayang", "Medan Sunggal", 
    "Medan Tembung", "Medan Timur", "Medan Tuntungan"
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = $_POST['method'];
    $delivery_location = $_POST['delivery_location'];
    $phone_number = $_POST['phone_number'];
    $address_detail = $_POST['address_detail']; // Ambil alamat detail dari form

    // Validasi kecamatan
    if (!in_array($delivery_location, $valid_kecamatan)) {
        die("Kecamatan tidak valid. Silakan pilih kecamatan yang tersedia.");
    }

    // Validasi nomor telepon
    if (!preg_match('/^08[0-9]{9,12}$/', $phone_number)) {
        die("Nomor telepon tidak valid. Harus dimulai dengan 08 dan terdiri dari 10-13 digit.");
    }

    // Validasi alamat detail
    if (empty($address_detail)) {
        die("Alamat detail tidak boleh kosong.");
    }

    // Redirect ke halaman pembayaran yang sesuai
    if ($method == 'BANK_BRI' || $method == 'BANK_MANDIRI') {
        header("Location: bank.php?order_id=$order_id&method=$method&location=" . urlencode($delivery_location) . "&phone=" . urlencode($phone_number) . "&address=" . urlencode($address_detail));
        exit();
    } elseif ($method == 'DANA') {
        header("Location: dana.php?order_id=$order_id&location=" . urlencode($delivery_location) . "&phone=" . urlencode($phone_number) . "&address=" . urlencode($address_detail));
        exit();
    } elseif ($method == 'Gopay') {
        header("Location: gopay.php?order_id=$order_id&location=" . urlencode($delivery_location) . "&phone=" . urlencode($phone_number) . "&address=" . urlencode($address_detail));
        exit();
    } elseif ($method == 'Shopeepay') {
        header("Location: shopeepay.php?order_id=$order_id&location=" . urlencode($delivery_location) . "&phone=" . urlencode($phone_number) . "&address=" . urlencode($address_detail));
        exit();
    } elseif ($method == 'Ovo') {
        header("Location: ovo.php?order_id=$order_id&location=" . urlencode($delivery_location) . "&phone=" . urlencode($phone_number) . "&address=" . urlencode($address_detail));
        exit();
    }
}
// Pastikan total harga terambil dengan benar
$total_price = $order['total_price'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
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
        input, select, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        input:focus, select:focus, textarea:focus {
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
        .payment-methods {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .payment-method {
            flex: 1;
            text-align: center;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .payment-method:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .payment-method img {
            width: 50px;
            height: 50px;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="payment-container animate__animated animate__fadeIn">
        <h1>Payment</h1>
        <p>Total Amount: Rp <?php echo number_format($total_price); ?></p>
    </form>
        <form action="" method="POST">
            <div class="input-group">
                <label for="delivery_location">Delivery Location</label>
                <select id="delivery_location" name="delivery_location" required>
                    <option value="">Pilih Kecamatan</option>
                    <?php foreach ($valid_kecamatan as $kecamatan): ?>
                        <option value="<?php echo $kecamatan; ?>"><?php echo $kecamatan; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label for="address_detail">Alamat Detail (Nama Jalan, No. Rumah, RT/RW)</label>
                <textarea id="address_detail" name="address_detail" rows="3" placeholder="Contoh: Jl. Gatot Subroto No. 12, RT 03/RW 05" required style="color: black;"></textarea>
            </div>
            <div class="input-group">
                <label for="phone_number">Nomor Telepon/WhatsApp</label>
                <input type="text" id="phone_number" name="phone_number" placeholder="081234567890" required style="color: black;">
            </div>
            <div class="input-group">
                <label for="method">Payment Method</label>
                <div class="payment-methods">
                    <div class="payment-method">
                        <input type="radio" id="bri" name="method" value="BANK_BRI" required>
                        <label for="bri"><img src="images/payment/bri.jpg" alt="BRI"> BRI</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="mandiri" name="method" value="BANK_MANDIRI">
                        <label for="mandiri"><img src="images/payment/mandiri.jpg" alt="Mandiri"> Mandiri</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="dana" name="method" value="DANA">
                        <label for="dana"><img src="images/payment/dana.jpg" alt="DANA"> DANA</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="gopay" name="method" value="Gopay">
                        <label for="gopay"><img src="images/payment/gopay.png" alt="Gopay"> Gopay</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="shopeepay" name="method" value="Shopeepay">
                        <label for="shopeepay"><img src="images/payment/shopeepay.jpg" alt="Shopeepay"> Shopeepay</label>
                    </div>
                    <div class="payment-method">
                        <input type="radio" id="ovo" name="method" value="Ovo">
                        <label for="ovo"><img src="images/payment/ovo.jpg" alt="Ovo"> OVO</label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <button type="submit" class="btn">Continue</button>
        </form>
    </div>
</body>
</html>