<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Recognition Auth - ML Theory</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php if ($isLoggedIn): ?>
        <div class="nav-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="about.php">ML Theory</a>
            <a href="logout.php">Logout</a>
        </div>
        <?php endif; ?>
        
        <div class="theory-section">
            <h1>Machine Learning on the Web</h1>
            <p>This page explains the technology behind our face recognition authentication system and the broader concept of Machine Learning on the Web.</p>
            
            <h2>What is Browser-Based Machine Learning?</h2>
            <p>Browser-based Machine Learning refers to the ability to run ML models directly in web browsers, without requiring server-side processing. This is made possible by technologies like TensorFlow.js, which enable developers to leverage the power of machine learning directly in client-side applications.</p>
            
            <h2>How Face Recognition Works</h2>
            <p>Our face recognition system uses several key technologies:</p>
            <ul>
                <li><strong>TensorFlow.js</strong>: A JavaScript library that brings TensorFlow machine learning to the browser</li>
                <li><strong>Face-API.js</strong>: A JavaScript API for face detection and recognition built on top of TensorFlow.js</li>
                <li><strong>SSD MobileNet</strong>: A single-shot detection model used to identify faces in images</li>
                <li><strong>FaceNet</strong>: A deep learning model that generates face embeddings (descriptors) that represent the features of a face</li>
            </ul>
            
            <h2>The Face Recognition Process</h2>
            <p>The process consists of several stages:</p>
            <ol>
                <li><strong>Face Detection</strong>: Locating faces within the camera feed</li>
                <li><strong>Facial Landmark Detection</strong>: Identifying key points on the face (eyes, nose, mouth)</li>
                <li><strong>Face Alignment</strong>: Normalizing the face based on landmark positions</li>
                <li><strong>Feature Extraction</strong>: Converting the face into a numerical descriptor (a 128-dimensional vector)</li>
                <li><strong>Face Comparison</strong>: Calculating the distance between face descriptors to determine identity</li>
            </ol>
            
            <h2>Advantages of Browser-Based ML</h2>
            <ul>
                <li><strong>Privacy</strong>: Your face data never leaves your device, enhancing privacy and security</li>
                <li><strong>Latency</strong>: No round-trip to the server means faster response times</li>
                <li><strong>Offline Capability</strong>: Can function without an internet connection once models are loaded</li>
                <li><strong>Reduced Server Costs</strong>: Processing happens on the client, reducing server load</li>
            </ul>
            
            <h2>Challenges of Browser-Based ML</h2>
            <ul>
                <li><strong>Model Size</strong>: ML models can be large, resulting in longer initial load times</li>
                <li><strong>Computational Constraints</strong>: Browsers have more limited computational resources compared to servers</li>
                <li><strong>Device Variability</strong>: Performance can vary widely across different devices and browsers</li>
                <li><strong>Battery Consumption</strong>: Running complex ML models can drain mobile device batteries</li>
            </ul>
            
            <h2>Technical Implementation</h2>
            <p>Our implementation uses several key components:</p>
            <ul>
                <li><strong>Model Loading</strong>: Pre-trained models are loaded from the server and cached in the browser</li>
                <li><strong>WebRTC</strong>: Used to access the camera feed for real-time face detection</li>
                <li><strong>Canvas API</strong>: For drawing detection results and processing image data</li>
                <li><strong>Euclidean Distance</strong>: Used to measure similarity between face descriptors</li>
            </ul>
            
            <h2>Future Developments</h2>
            <p>The field of browser-based ML is rapidly evolving, with exciting possibilities:</p>
            <ul>
                <li>Smaller, more efficient models that load faster</li>
                <li>More advanced recognition capabilities beyond faces</li>
                <li>Integration with WebGPU for faster processing</li>
                <li>Federated learning for privacy-preserving model improvements</li>
            </ul>
            
            <?php if (!$isLoggedIn): ?>
            <div class="button-group">
                <a href="index.php" class="btn primary">Back to Home</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>