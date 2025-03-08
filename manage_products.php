<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price_large = $_POST['price_large'];
    $price_medium = $_POST['price_medium'];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is valid
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Generate unique filename
            $new_filename = uniqid() . "." . $imageFileType;
            $target_file = $target_dir . $new_filename;

            // Move uploaded file to target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "File is not an image.";
        }
    } else {
        $error = "No image uploaded.";
    }

    if (!isset($error)) {
        $sql = "INSERT INTO products (name, description, price_large, price_medium, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdds", $name, $description, $price_large, $price_medium, $image);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
    <style>
        /* Futuristic Styling */
        body {
            background-color: #1a1a2e;
            color: #eaeaea;
            font-family: 'Poppins', sans-serif;
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

        /* Styling Admin Navigation */
        .admin-navigation {
            display: flex;
            gap: 15px; /* Menambahkan jarak antar-tab */
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

        nav a {
            margin: 0 10px;
            color: #eaeaea;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }

        nav a:hover {
            color: #00d9ff;
        }

        .admin-container {
            max-width: 900px;
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

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #2c3e50;
            background-color: #1a1a2e;
            color: #eaeaea;
        }

        .input-group input:focus,
        .input-group textarea:focus {
            border-color: #00d9ff;
            outline: none;
        }

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
        }

        .btn:hover {
            background-color: #00b2cc;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .modern-table th,
        .modern-table td {
            text-align: left;
            padding: 10px;
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
        }

        .modern-table .btn {
            padding: 5px 10px;
            font-size: 12px;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Manage Products</h1>
        <nav class="admin-navigation">
            <a href="index.php" class="nav-link">Dashboard</a>
            <a href="manage_products.php" class="nav-link">Manage Products</a>
            <a href="manage_shop_settings.php" class="nav-link">Manage Shop Settings</a>
            <a href="../logout.php" class="nav-link">Logout</a>
        </nav>
    </div>
</header>


    <div class="admin-container animate__animated animate__fadeIn">
        <h2>Add New Product</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="input-group">
            <label for="price_large">Large Price:</label>
            <input type="number" name="price_large" required>

            <label for="price_medium">Medium Price:</label>
            <input type="number" name="price_medium" required>
            </div>
            <div class="input-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" class="btn">Add Product</button>
        </form>

        <h2>Product List</h2>
        <table class="modern-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price (Large)</th>
                    <th>Price (Medium)</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td>Rp <?php echo number_format($row['price_large']); ?></td>
                        <td>Rp <?php echo number_format($row['price_medium']); ?></td>
                        <td><img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="50"></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                            <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
