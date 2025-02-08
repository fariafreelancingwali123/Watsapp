<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    http_response_code(400);
    exit();
}

$current_user_id = $_SESSION['user_id'];
$chat_user_id = $_GET['user_id'];

// Fetch messages
$stmt = $conn->prepare("
    SELECT sender_id, receiver_id, message, timestamp 
    FROM messages 
    WHERE (sender_id = :current_user AND receiver_id = :chat_user)
       OR (sender_id = :chat_user AND receiver_id = :current_user)
    ORDER BY timestamp ASC
");
$stmt->execute([
    'current_user' => $current_user_id,
    'chat_user' => $chat_user_id
]);
$messages = $stmt->fetchAll();

// Display messages
foreach ($messages as $message) {
    $css_class = $message['sender_id'] == $current_user_id ? 'self' : 'other';
    $sender = $message['sender_id'] == $current_user_id ? 'You' : 'User';
    echo "<div class='chat-message {$css_class}'>
        <strong>{$sender}:</strong> " . htmlspecialchars($message['message']) . "
        <small>" . htmlspecialchars($message['timestamp']) . "</small>
    </div>";
}
