<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Generate unique number
    $stmt = $conn->query("SELECT MAX(id) AS max_id FROM users");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $next_id = $row['max_id'] + 1;
    $unique_number = '+786-1001-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

    $stmt = $conn->prepare("INSERT INTO users (name, password, unique_number) VALUES (?, ?, ?)");
    $stmt->execute([$name, $password, $unique_number]);

    header('Location: login.php');
    exit();
}
?>

<form method="POST">
    <input type="text" name="name" placeholder="Name" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Sign Up</button>
</form>
