<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all users for the receiver dropdown
$users = [];
try {
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id != ?");
    $stmt->execute([$user_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . htmlspecialchars($e->getMessage());
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $receiver_id = filter_input(INPUT_POST, 'receiver_id', FILTER_VALIDATE_INT);
        $message = trim($_POST['message']);

        // Validate receiver_id and message
        if ($receiver_id && $message) {
            // Verify receiver_id exists in users table
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$receiver_id]);
            if ($stmt->fetch()) {
                $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $receiver_id, $message]);
                echo "<script>window.location.href = 'messages.php';</script>";
                exit;
            } else {
                $error = "Invalid receiver ID. Please select a valid user.";
            }
        } else {
            $error = "Please select a valid receiver and enter a message.";
        }
    } catch (PDOException $e) {
        $error = "Error sending message: " . htmlspecialchars($e->getMessage());
    }
}

// Fetch messages
$messages = [];
try {
    $stmt = $pdo->prepare("
        SELECT m.*, u.username 
        FROM messages m 
        JOIN users u ON m.sender_id = u.id 
        WHERE m.receiver_id = ? OR m.sender_id = ? 
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$user_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching messages: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .container h2 {
            color: #2a5298;
        }
        .form-container, .message {
            padding: 20px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px;
            background: #2a5298;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .form-container button:hover {
            background: #1e3c72;
        }
        .error {
            color: red;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2a5298;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #1e3c72;
        }
        @media (max-width: 768px) {
            .container {
                margin: 0 20px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Messages</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <div class="form-container">
            <form method="POST">
                <?php if (empty($users)): ?>
                    <p>No users available to message.</p>
                <?php else: ?>
                    <select name="receiver_id" required>
                        <option value="">Select Receiver</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <textarea name="message" placeholder="Your message" required></textarea>
                    <button type="submit">Send Message</button>
                <?php endif; ?>
            </form>
        </div>
        <?php if (empty($messages)): ?>
            <p>No messages found.</p>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="message">
                    <p><strong><?php echo htmlspecialchars($message['username']); ?>:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                    <p><small><?php echo htmlspecialchars($message['created_at']); ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="javascript:redirectTo('index.php')" class="btn">Back to Home</a>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
