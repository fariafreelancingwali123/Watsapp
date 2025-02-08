<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

// Fetch logged-in user details
$user_id = $_SESSION['user_id'];
$unique_number = $_SESSION['unique_number'];

// Fetch contacts
$stmt = $conn->prepare("
    SELECT u.id, u.unique_number 
    FROM users u 
    JOIN contacts c ON u.id = c.contact_id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new contact
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_number'])) {
    $search_number = $_POST['search_number'];

    // Search for the user by unique number
    $search_stmt = $conn->prepare("SELECT id FROM users WHERE unique_number = ? AND id != ?");
    $search_stmt->execute([$search_number, $user_id]);
    $found_user = $search_stmt->fetch(PDO::FETCH_ASSOC);

    if ($found_user) {
        // Add the contact
        $contact_id = $found_user['id'];
        $insert_stmt = $conn->prepare("INSERT INTO contacts (user_id, contact_id) VALUES (?, ?)");
        $insert_stmt->execute([$user_id, $contact_id]);
        echo "<p>Contact added successfully!</p>";
        header("Refresh:0"); // Reload the page
    } else {
        echo "<p>No user found with this number.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($unique_number); ?></h1>
    <a href="signout.php">Sign Out</a>

    <h2>Your Contacts</h2>
    <?php if (!empty($contacts)): ?>
        <ul>
            <?php foreach ($contacts as $contact): ?>
                <li>
                    <a href="chat.php?user_id=<?php echo $contact['id']; ?>">
                        <?php echo htmlspecialchars($contact['unique_number']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No contacts found. Use the search bar to add contacts.</p>
    <?php endif; ?>

    <h2>Search and Add Contacts</h2>
    <form method="POST">
        <input type="text" name="search_number" placeholder="Enter unique number" required>
        <button type="submit">Add Contact</button>
    </form>
</body>
</html>
