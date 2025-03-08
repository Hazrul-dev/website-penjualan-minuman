<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'];

$sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

header('Location: index.php');
exit();
?>
