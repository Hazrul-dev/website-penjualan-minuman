<?php
session_start();
include 'includes/db.php';
include 'functions.php';

$order_id = $_POST['order_id'] ?? 0;
$status = $_POST['status'] ?? '';
$user_id = $_POST['user_id'] ?? 0;

if ($status === 'approved') {
    $message = "Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.";
} else if ($status === 'rejected') {
    $message = "Proses minuman gagal untuk pengiriman.";
} else {
    exit("Status tidak valid.");
}

// Simpan notifikasi ke database
addNotification($user_id, $message, $conn);

// Redirect kembali ke halaman admin
header("Location: admin_orders.php");
exit();
?>
