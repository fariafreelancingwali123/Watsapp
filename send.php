<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['message']) || !isset($_POST['receiver_id'])) {
    http_response_code(400);
    exit();
}

$current_user_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = trim($_POST['message']);

if ($message === '') {
    http_response_code(400);
    exit();
}

// Save the message in the database
$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, NOW())");
$stmt->execute([$current_user_id, $receiver_id, $message]);
