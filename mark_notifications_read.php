<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id) {
    // Update notifications to mark as read
    $query = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    // Clear session notification flag
    unset($_SESSION['has_notification']);
}

echo json_encode(['success' => true]);
?>