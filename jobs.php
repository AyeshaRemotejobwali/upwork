<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db.php';

// Handle job posting
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['role']) && $_SESSION['role'] == 'client') {
    try {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $budget = filter_input(INPUT_POST, 'budget', FILTER_VALIDATE_FLOAT);
        $type = $_POST['type'];
        $category = $_POST['category'];
        $user_id = $_SESSION['user_id'];

        if ($title && $description && $budget && $type && $category) {
            $stmt = $pdo->prepare("INSERT INTO jobs (title, description, budget, type, category, client_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $budget, $type, $category, $user_id]);
            echo "<script>window.location.href = 'jobs.php';</script>";
            exit;
        } else {
            $error = "Please fill in all fields with valid data.";
        }
    } catch (PDOException $e) {
        $error = "Error posting job: " . htmlspecialchars($e->getMessage());
    }
}

// Fetch all jobs
$jobs = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM jobs ORDER BY created_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $debug = "Fetched " . count($jobs) . " jobs from the database.";
} catch (PDOException $e) {
    $error = "Error fetching jobs: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs</title>
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
        .job-card {
            background: #fff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .job-card:hover {
            transform: translateY(-5px);
        }
        .job-card h3 {
            margin: 0 0 10px;
            color: #2a5298;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-container input, .form-container textarea, .form-container select {
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
        .debug {
            color: green;
            text-align: center;
            font-size: 14px;
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
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($debug)): ?>
            <p class="debug"><?php echo htmlspecialchars($debug); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'client'): ?>
            <div class="form-container">
                <h2>Post a Job</h2>
                <form method="POST">
                    <input type="text" name="title" placeholder="Job Title" required>
                    <textarea name="description" placeholder="Job Description" required></textarea>
                    <input type="number" name="budget" placeholder="Budget" step="0.01" required>
                    <select name="type" required>
                        <option value="fixed">Fixed Price</option>
                        <option value="hourly">Hourly</option>
                    </select>
                    <select name="category" required>
                        <option value="web">Web Development</option>
                        <option value="design">Graphic Design</option>
                        <option value="writing">Writing</option>
                    </select>
                    <button type="submit">Post Job</button>
                </form>
            </div>
        <?php endif; ?>
        <h2>All Jobs</h2>
        <?php if (empty($jobs)): ?>
            <p>No jobs available at the moment.</p>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p><?php echo htmlspecialchars($job['description']); ?></p>
                    <p>Budget: $<?php echo htmlspecialchars(number_format($job['budget'], 2)); ?> (<?php echo htmlspecialchars($job['type']); ?>)</p>
                    <p>Category: <?php echo htmlspecialchars($job['category']); ?></p>
                    <a href="javascript:redirectTo('job.php?id=<?php echo $job['id']; ?>')" class="btn">View Job</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
