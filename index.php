<?php
session_start();
require 'db.php';

// Fetch featured jobs
$stmt = $pdo->query("SELECT * FROM jobs WHERE featured = 1 LIMIT 4");
$jobs = $stmt->fetchAll();

// Fetch featured freelancers
$stmt = $pdo->query("SELECT * FROM users WHERE role = 'freelancer' AND featured = 1 LIMIT 4");
$freelancers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelance Marketplace</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        nav {
            background: #fff;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #2a5298;
            font-weight: bold;
        }
        nav a:hover {
            color: #1e3c72;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .job-card, .freelancer-card {
            background: #fff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .job-card:hover, .freelancer-card:hover {
            transform: translateY(-5px);
        }
        .job-card h3, .freelancer-card h3 {
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
        .search-bar {
            margin: 20px 0;
            text-align: center;
        }
        .search-bar input {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-bar select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 0 10px;
            }
            .search-bar input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Freelance Marketplace</h1>
        <p>Connect with top freelancers and clients</p>
    </header>
    <nav>
        <a href="index.php">Home</a>
        <a href="jobs.php">Jobs</a>
        <a href="freelancers.php">Freelancers</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profile</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>
        <?php endif; ?>
    </nav>
    <div class="container">
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search jobs...">
            <select id="category">
                <option value="">All Categories</option>
                <option value="web">Web Development</option>
                <option value="design">Graphic Design</option>
                <option value="writing">Writing</option>
            </select>
            <select id="budget">
                <option value="">All Budgets</option>
                <option value="fixed">Fixed Price</option>
                <option value="hourly">Hourly</option>
            </select>
        </div>
        <h2>Featured Jobs</h2>
        <?php foreach ($jobs as $job): ?>
            <div class="job-card">
                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                <p><?php echo htmlspecialchars($job['description']); ?></p>
                <p>Budget: $<?php echo htmlspecialchars($job['budget']); ?> (<?php echo htmlspecialchars($job['type']); ?>)</p>
                <a href="job.php?id=<?php echo $job['id']; ?>" class="btn">View Job</a>
            </div>
        <?php endforeach; ?>
        <h2>Featured Freelancers</h2>
        <?php foreach ($freelancers as $freelancer): ?>
            <div class="freelancer-card">
                <h3><?php echo htmlspecialchars($freelancer['username']); ?></h3>
                <p>Skills: <?php echo htmlspecialchars($freelancer['skills']); ?></p>
                <a href="freelancer.php?id=<?php echo $freelancer['id']; ?>" class="btn">View Profile</a>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
        document.getElementById('search').addEventListener('input', function() {
            // Implement search functionality
            console.log('Search:', this.value);
        });
        document.getElementById('category').addEventListener('change', function() {
            console.log('Category:', this.value);
        });
        document.getElementById('budget').addEventListener('change', function() {
            console.log('Budget:', this.value);
        });
    </script>
</body>
</html>
