<?php
session_start();
include '../includes/db.php';
include '../includes/functions.php';

if (!isAdmin()) {
    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = 'Akses ditolak. Anda tidak memiliki izin untuk mengkonfirmasi pembayaran.';
    header('Location: ../index.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = 'ID pembayaran tidak valid.';
    header('Location: ../index.php');
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

// Update status pembayaran menjadi 'approved'
$sql = "UPDATE payments SET status = 'approved' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $payment_id);

if ($stmt->execute()) {
    // Simpan notifikasi ke database
    $message = "Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.";
    $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
    
    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = $message;
} else {
    $_SESSION['has_notification'] = true;
    $_SESSION['notification_message'] = 'Gagal mengkonfirmasi pembayaran. Silakan coba lagi.';
}

$stmt->close();

// Redirect berdasarkan role
if (isAdmin()) {
    header('Location: ../admin/index.php'); // Redirect ke dashboard admin
} else {
    header('Location: ../index.php'); // Redirect ke halaman user
}
exit();
?>