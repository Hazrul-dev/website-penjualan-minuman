<?php
session_start();
include 'includes/db.php';
include 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'];

$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];

    // Tentukan harga berdasarkan ukuran
    $price = ($size == "large") ? $product['price_large'] : $product['price_medium'];
    $total_price = $price * $quantity;

    // Insert order into database
    $sql = "INSERT INTO orders (user_id, product_id, size, quantity, total_price, order_date) 
        VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisid", $_SESSION['user_id'], $product_id, $size, $quantity, $total_price);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; // Ambil ID order yang baru dibuat
        $_SESSION['order_id'] = $order_id; // Simpan order_id di session
        $_SESSION['total_price'] = $total_price; // Simpan total harga di session
    
        $stmt->close();
        
        // Redirect ke payment.php dengan order_id
        header("Location: payment.php?order_id=" . $order_id);
        exit();
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Gaya Umum */
        body {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4); /* Gradien pink */
            color: #333; /* Warna teks lebih gelap untuk kontras */
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .order-container {
            background: rgba(255, 255, 255, 0.9); /* Latar belakang semi-transparan */
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
        }

        .order-container h1 {
            font-size: 2rem;
            color: #ff69b4; /* Warna pink cerah */
            margin-bottom: 1.5rem;
        }

        .product-image {
            width: 100%;
            max-width: 300px;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .price-info {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .price-info span {
            color: #ff69b4; /* Warna pink cerah */
            font-weight: bold;
        }

        .input-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: bold;
        }

        .input-group select,
        .input-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            color: #333;
            transition: border-color 0.3s ease;
        }

        .input-group select:focus,
        .input-group input:focus {
            border-color: #ff69b4; /* Warna pink cerah */
            outline: none;
        }

        .size-options {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .size-option {
            flex: 1;
            padding: 1rem;
            border: 2px solid #ff69b4; /* Warna pink cerah */
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .size-option:hover {
            background: #ff69b4; /* Warna pink cerah */
            color: white;
            transform: translateY(-5px);
        }

        .size-option.selected {
            background: #ff69b4; /* Warna pink cerah */
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #ff69b4; /* Warna pink cerah */
            color: white;
            border: none;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            background: #ff1493; /* Warna pink lebih gelap */
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="order-container animate__animated animate__fadeIn">
        <h1>Order <?php echo $product['name']; ?></h1>
        <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-image">
        <div class="price-info">
            Pricelist 😋 
        </div>
                <form action="" method="POST">
                <div class="input-group">
            <label for="size">Pilih Ukuran:</label>
            <div class="size-options">
                <div class="size-option" data-size="large" data-price="<?php echo $product['price_large']; ?>">
                    Large<br>
                    <span>Rp <?php echo number_format($product['price_large']); ?></span>
                </div>
                <div class="size-option" data-size="medium" data-price="<?php echo $product['price_medium']; ?>">
                    Medium<br>
                    <span>Rp <?php echo number_format($product['price_medium']); ?></span>
                </div>
            </div>
            <input type="hidden" id="size" name="size" required>
        </div>
        <div class="input-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
        </div>
        <div class="price-info">
            <strong>Total Harga:</strong> <span id="total-price">Rp 0</span>
        </div>
        <button type="submit" class="btn">Proceed to Payment</button>
                </form>
    </div>

    <script>
    const sizeOptions = document.querySelectorAll('.size-option');
    const sizeInput = document.getElementById('size');
    const quantityInput = document.getElementById('quantity');
    const totalPriceElement = document.getElementById('total-price');

    let selectedPrice = 0;

    sizeOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Hapus class 'selected' dari semua opsi
            sizeOptions.forEach(opt => opt.classList.remove('selected'));
            // Tambahkan class 'selected' ke opsi yang dipilih
            option.classList.add('selected');
            // Set nilai input hidden
            sizeInput.value = option.getAttribute('data-size');
            // Update harga yang dipilih
            selectedPrice = parseFloat(option.getAttribute('data-price'));
            updateTotalPrice();
        });
    });

    quantityInput.addEventListener('input', () => {
        updateTotalPrice();
    });

    function updateTotalPrice() {
        const quantity = parseInt(quantityInput.value) || 0;
        const totalPrice = selectedPrice * quantity;
        totalPriceElement.textContent = `Rp ${totalPrice.toLocaleString()}`;
    }
</script>
</body>
</html>