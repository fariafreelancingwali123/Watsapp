<?php
session_start();
require 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    header('Location: login.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$chat_user_id = $_GET['user_id'];

// Fetch the other user's details
$stmt = $conn->prepare("SELECT unique_number, name FROM users WHERE id = ?");
$stmt->execute([$chat_user_id]);
$chat_user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chat_user) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($chat_user['unique_number']); ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .chat-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .chat-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .chat-window {
            border: 1px solid #ccc;
            height: 400px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .chat-message {
            margin: 5px 0;
        }
        .chat-message.self {
            text-align: right;
            color: blue;
        }
        .chat-message.other {
            text-align: left;
            color: green;
        }
        .chat-form {
            display: flex;
            margin-top: 10px;
        }
        .chat-form input[type="text"] {
            flex: 1;
            padding: 10px;
        }
        .chat-form button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            Chat with <?php echo htmlspecialchars($chat_user['name']); ?> (<?php echo htmlspecialchars($chat_user['unique_number']); ?>)
        </div>

        <div class="chat-window" id="chat-window"></div>

        <form class="chat-form" id="chat-form">
            <input type="text" id="message" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            const chatWindow = $('#chat-window');

            // Function to load messages
            function loadMessages() {
                $.get('message.php?user_id=<?php echo $chat_user_id; ?>', function (data) {
                    chatWindow.html(data);
                    chatWindow.scrollTop(chatWindow.prop("scrollHeight"));
                });
            }

            // Load messages initially and refresh every 2 seconds
            loadMessages();
            setInterval(loadMessages, 2000);

            // Handle form submission
            $('#chat-form').on('submit', function (e) {
                e.preventDefault();
                const message = $('#message').val().trim();

                if (message === '') {
                    return;
                }

                $.post('send.php', {
                    message: message,
                    receiver_id: <?php echo $chat_user_id; ?>
                }, function () {
                    $('#message').val('');
                    loadMessages();
                });
            });
        });
    </script>
</body>
</html>
