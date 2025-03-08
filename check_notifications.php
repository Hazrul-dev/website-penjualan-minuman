<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? 0;
$has_notification = false;

if ($user_id) {
    // Check for unread notifications
    $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $has_notification = $row['count'] > 0;
}

echo json_encode(['has_notification' => $has_notification]);
?>