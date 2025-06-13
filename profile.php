<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $portfolio = $_POST['portfolio'];

    $stmt = $pdo->prepare("UPDATE users SET skills = ?, experience = ?, portfolio = ? WHERE id = ?");
    $stmt->execute([$skills, $experience, $portfolio, $user_id]);
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
        .container form {
            display: flex;
            flex-direction: column;
        }
        .container input, .container textarea {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .container button {
            padding: 10px;
            background: #2a5298;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .container button:hover {
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
        <h2><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>
        <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>
        <?php if ($user['role'] == 'freelancer'): ?>
            <form method="POST">
                <input type="text" name="skills" placeholder="Skills" value="<?php echo htmlspecialchars($user['skills']); ?>">
                <textarea name="experience" placeholder="Experience"><?php echo htmlspecialchars($user['experience']); ?></textarea>
                <input type="text" name="portfolio" placeholder="Portfolio URL" value="<?php echo htmlspecialchars($user['portfolio']); ?>">
                <button type="submit">Update Profile</button>
            </form>
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
