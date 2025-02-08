<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $contact_id = $_POST['contact_id'];

    // Check if the contact already exists
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE user_id = ? AND contact_id = ?");
    $stmt->execute([$user_id, $contact_id]);
    if ($stmt->rowCount() === 0) {
        // Add contact
        $stmt = $conn->prepare("INSERT INTO contacts (user_id, contact_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $contact_id]);
    }
    header('Location: dashboard.php');
    exit();
}
?>
