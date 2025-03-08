<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isAdmin()) {
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
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price_large = $_POST['price_large'];
    $price_medium = $_POST['price_medium'];

    // Handle file upload if a new image is provided
    if ($_FILES['image']['error'] == 0) {
        $target_dir = __DIR__ . "/../uploads/";
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
        // Keep the old image if no new image is uploaded
        $image = $product['image'];
    }

    if (!isset($error)) {
        $sql = "UPDATE products SET name = ?, description = ?, price_large = ?, price_medium = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddsi", $name, $description, $price_large, $price_medium, $image, $product_id);
        $stmt->execute();
        $stmt->close();
        header('Location: manage_products.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - FLOAT SMOOTHIES MEDAN</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/jpeg" href="assets/float.jpg">
</head>
<body>
    <div class="admin-container">
        <h1>Edit Product</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>" required>
            </div>
            <div class="input-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo $product['description']; ?></textarea>
            </div>
            <div class="input-group">
                <label for="price_large">Price (Large)</label>
                <input type="number" id="price_large" name="price_large" step="0.01" value="<?php echo $product['price_large']; ?>" required>
            </div>
            <div class="input-group">
                <label for="price_medium">Price (Medium)</label>
                <input type="number" id="price_medium" name="price_medium" step="0.01" value="<?php echo $product['price_medium']; ?>" required>
            </div>
            <div class="input-group">
                <label for="image">Product Image</label>
                <input type="file" id="image" name="image">
                <small>Current Image: <img src="../uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="50"></small>
            </div>
            <button type="submit" class="btn">Update Product</button>
        </form>
    </div>
</body>
</html>