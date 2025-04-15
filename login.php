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
    <title>Face Recognition Auth - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Using CDN for face-api.js instead of local file -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <h1>Login with Face Recognition</h1>
            <p>Position your face in front of the camera to login</p>
            
            <div id="statusMessage" class="status-message"></div>
            
            <div class="camera-container">
                <video id="video" autoplay muted playsinline></video>
            </div>
            
            <div id="loadingIndicator" class="loading-indicator">Loading face recognition models...</div>
            
            <div class="controls">
                <button id="loginBtn" class="btn primary" disabled>Login with Face</button>
            </div>
            
            <div class="info-section">
                <p>Don't have an account? <a href="register.php" class="text-link">Register here</a></p>
            </div>
        </div>
    </div>
    
    <script src="js/face-detection.js"></script>
    <script src="js/face-recognition.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('video');
            const loginBtn = document.getElementById('loginBtn');
            const statusMessage = document.getElementById('statusMessage');
            const loadingIndicator = document.getElementById('loadingIndicator');
            
            // Initialize face detection
            loadingIndicator.style.display = 'block';
            initFaceDetection().then(() => {
                loadingIndicator.style.display = 'none';
                loginBtn.disabled = false;
                startVideo();
            }).catch(error => {
                statusMessage.textContent = `Error loading face detection: ${error.message}`;
                statusMessage.className = 'status-message error';
                loadingIndicator.style.display = 'none';
            });
            
            // Start webcam video capture
            async function startVideo() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    video.srcObject = stream;
                } catch (error) {
                    statusMessage.textContent = `Error accessing camera: ${error.message}`;
                    statusMessage.className = 'status-message error';
                }
            }
            
            // Login button click
            loginBtn.addEventListener('click', async () => {
                try {
                    loadingIndicator.style.display = 'block';
                    loadingIndicator.textContent = 'Verifying your face...';
                    
                    const detection = await detectFace(video);
                    
                    if (detection) {
                        // Get face descriptor for recognition
                        const faceDescriptor = Array.from(detection.descriptor);
                        
                        // Send to server for verification
                        const response = await fetch('api/login_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ faceDescriptor })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            statusMessage.textContent = 'Login successful! Redirecting to dashboard...';
                            statusMessage.className = 'status-message success';
                            
                            setTimeout(() => {
                                window.location.href = 'dashboard.php';
                            }, 1500);
                        } else {
                            statusMessage.textContent = data.message || 'Face not recognized.';
                            statusMessage.className = 'status-message error';
                        }
                    } else {
                        statusMessage.textContent = 'No face detected. Please position yourself properly.';
                        statusMessage.className = 'status-message error';
                    }
                } catch (error) {
                    statusMessage.textContent = `Error during login: ${error.message}`;
                    statusMessage.className = 'status-message error';
                }
                
                loadingIndicator.style.display = 'none';
            });
        });
    </script>
</body>
</html>