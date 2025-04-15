<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info from session
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Recognition Auth - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-card">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>You've successfully logged in using face recognition.</p>
            
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="about.php">ML Theory</a>
                <a href="logout.php">Logout</a>
            </div>
            
            <div class="user-info">
                <h3>Your Account Information</h3>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Account ID:</strong> <?php echo htmlspecialchars($userId); ?></p>
            </div>
            
            <div class="info-section">
                <h3>About Face Recognition</h3>
                <p>Your account is secured using advanced face recognition powered by TensorFlow.js. This technology runs entirely in your browser, ensuring your face data never leaves your device.</p>
                <a href="about.php" class="btn secondary">Learn More About ML on the Web</a>
            </div>
            
            <div class="info-section">
                <h3>Manage Your Face Data</h3>
                <p>You can update your face data or add additional face angles to improve recognition accuracy.</p>
                <a href="profile.php" class="btn primary">Update Face Data</a>
            </div>
        </div>
    </div>
</body>
</html>