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
    <title>Face Recognition Auth - Register</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Using CDN for face-api.js instead of local file -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <h1>Register New Account</h1>
            <p>Create a new account using your face as authentication</p>
            
            <div id="statusMessage" class="status-message"></div>
            
            <form id="registerForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="camera-container">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas" style="display: none;"></canvas>
                </div>
                
                <div id="loadingIndicator" class="loading-indicator">Loading face detection models...</div>
                
                <div class="controls">
                    <button type="button" id="captureBtn" class="btn secondary" disabled>Capture Face</button>
                    <button type="submit" id="registerBtn" class="btn primary" disabled>Register Account</button>
                </div>
            </form>
            
            <div class="info-section">
                <p>Already have an account? <a href="login.php" class="text-link">Login here</a></p>
            </div>
        </div>
    </div>
    
    <script src="js/face-detection.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('captureBtn');
            const registerBtn = document.getElementById('registerBtn');
            const registerForm = document.getElementById('registerForm');
            const statusMessage = document.getElementById('statusMessage');
            const loadingIndicator = document.getElementById('loadingIndicator');
            
            let faceDescriptor = null;
            let isCaptured = false;
            
            // Initialize face detection
            loadingIndicator.style.display = 'block';
            initFaceDetection().then(() => {
                loadingIndicator.style.display = 'none';
                captureBtn.disabled = false;
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
            
            // Capture face button
            captureBtn.addEventListener('click', async () => {
                try {
                    loadingIndicator.style.display = 'block';
                    loadingIndicator.textContent = 'Processing face...';
                    
                    const detection = await detectFace(video);
                    
                    if (detection) {
                        // Draw face rectangle for visual feedback
                        const ctx = canvas.getContext('2d');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        // Get face descriptor for recognition later
                        faceDescriptor = detection.descriptor;
                        
                        statusMessage.textContent = 'Face captured successfully!';
                        statusMessage.className = 'status-message success';
                        registerBtn.disabled = false;
                        isCaptured = true;
                    } else {
                        statusMessage.textContent = 'No face detected. Please position yourself properly.';
                        statusMessage.className = 'status-message error';
                    }
                    
                    loadingIndicator.style.display = 'none';
                } catch (error) {
                    statusMessage.textContent = `Error capturing face: ${error.message}`;
                    statusMessage.className = 'status-message error';
                    loadingIndicator.style.display = 'none';
                }
            });
            
            // Registration form submission
            registerForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                if (!isCaptured) {
                    statusMessage.textContent = 'Please capture your face first.';
                    statusMessage.className = 'status-message error';
                    return;
                }
                
                loadingIndicator.style.display = 'block';
                loadingIndicator.textContent = 'Creating your account...';
                
                const username = document.getElementById('username').value;
                const email = document.getElementById('email').value;
                
                // Convert the Float32Array to regular array for JSON serialization
                const faceDescriptorArray = Array.from(faceDescriptor);
                
                try {
                    const response = await fetch('api/register_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            username,
                            email,
                            faceDescriptor: faceDescriptorArray
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        statusMessage.textContent = 'Registration successful! Redirecting to login...';
                        statusMessage.className = 'status-message success';
                        
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        statusMessage.textContent = data.message || 'Registration failed.';
                        statusMessage.className = 'status-message error';
                    }
                } catch (error) {
                    statusMessage.textContent = `Error during registration: ${error.message}`;
                    statusMessage.className = 'status-message error';
                }
                
                loadingIndicator.style.display = 'none';
            });
        });
    </script>
</body>
</html>