<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$payment_id = intval($_GET['id']);

// Ambil user_id dari pembayaran
$sql = "SELECT user_id FROM payments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$stmt->bind_result($user_id);
if (!$stmt->fetch()) {
    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = 'Pembayaran tidak ditemukan.';
    header('Location: ../index.php');
    exit();
}
$stmt->close();

// Update status pembayaran menjadi 'rejected'
$sql = "UPDATE payments SET status = 'rejected' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$stmt->close();

// Simpan notifikasi ke database
$message = "Proses minuman gagal untuk pengiriman.";
$sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $message);
$stmt->execute();
$stmt->close();

// Simpan notifikasi di session untuk user
$_SESSION['has_notification'] = true;
$_SESSION['notification_message'] = $message;

// Redirect berdasarkan role
if (isAdmin()) {
    header('Location: ../admin/index.php'); // Redirect ke dashboard admin
} else {
    header('Location: ../index.php'); // Redirect ke halaman user
}
exit();
?>