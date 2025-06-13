<?php
session_start();
require 'db.php';

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'freelancer'");
$freelancers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .freelancer-card {
            background: #fff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .freelancer-card:hover {
            transform: translateY(-5px);
        }
        .freelancer-card h3 {
            margin: 0 0 10px;
            color: #2a5298;
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
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Freelancers</h2>
        <?php foreach ($freelancers as $freelancer): ?>
            <div class="freelancer-card">
                <h3><?php echo htmlspecialchars($freelancer['username']); ?></h3>
                <p>Skills: <?php echo htmlspecialchars($freelancer['skills']); ?></p>
                <p>Experience: <?php echo htmlspecialchars($freelancer['experience']); ?></p>
                <a href="javascript:redirectTo('freelancer.php?id=<?php echo $freelancer['id']; ?>')" class="btn">View Profile</a>
            </div>
        <?php endforeach; ?>
        <a href="javascript:redirectTo('index.php')" class="btn">Back to Home</a>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
