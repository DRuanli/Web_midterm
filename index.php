<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Recognition Auth - Welcome</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="welcome-card">
            <h1>Face Recognition Authentication</h1>
            <p>Welcome to our ML-powered face recognition system. This application demonstrates browser-based machine learning for secure authentication.</p>
            
            <div class="button-group">
                <a href="login.php" class="btn primary">Login with Face</a>
                <a href="register.php" class="btn secondary">Register New Account</a>
            </div>
            
            <div class="info-section">
                <h3>About This Project</h3>
                <p>This project uses TensorFlow.js to perform face detection and recognition directly in your browser.</p>
                <a href="about.php" class="text-link">Learn more about ML on the Web</a>
            </div>
        </div>
    </div>
</body>
</html>