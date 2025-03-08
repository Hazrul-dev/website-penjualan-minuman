<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'];
$sql = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

header('Location: manage_products.php');
exit();
?>