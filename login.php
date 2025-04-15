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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/style.css">
    <!-- Using CDN for face-api.js instead of local file -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
</head>
<body>
    <a href="#main" class="skip-to-content">Skip to content</a>
    <button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle dark mode">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="moon-icon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
    </button>
    
    <div class="container">
        <main id="main" class="auth-card fade-in-up">
            <h1>Login with Face Recognition</h1>
            <p>Position your face in front of the camera to login</p>
            
            <div id="statusMessage" class="status-message" style="display: none;"></div>
            
            <div class="camera-container">
                <video id="video" autoplay muted playsinline></video>
                <div class="face-guide"></div>
                <div class="detection-status">
                    <div class="scanning-indicator">
                        <div class="scanning-bar"></div>
                    </div>
                    <div class="text">Scanning for face...</div>
                </div>
                
                <div class="face-detection-box" style="display: none;"></div>
            </div>
            
            <div id="loadingIndicator" class="loading-indicator">Loading face recognition models...</div>
            
            <div class="login-progress">
                <div class="progress-bar">
                    <div class="progress-fill"></div>
                </div>
                <div class="progress-text">Initializing face recognition...</div>
            </div>
            
            <div class="controls">
                <button id="loginBtn" class="btn primary pulse-animation" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    Login with Face
                </button>
            </div>
            
            <div class="info-section">
                <div class="tips-container">
                    <h3>Tips for Best Recognition</h3>
                    <div class="tips-grid">
                        <div class="tip-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                            <p>Ensure good lighting</p>
                        </div>
                        <div class="tip-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <p>Look directly at camera</p>
                        </div>
                        <div class="tip-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 16v-2m0-6v2m-6 2h2m8 0h2"></path></svg>
                            <p>Remove obstacles</p>
                        </div>
                        <div class="tip-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            <p>Stay still</p>
                        </div>
                    </div>
                </div>
                <p>Don't have an account? <a href="register.php" class="text-link">Register here</a></p>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="js/face-detection.js"></script>
    <script src="js/face-recognition.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('video');
            const loginBtn = document.getElementById('loginBtn');
            const statusMessage = document.getElementById('statusMessage');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const progressFill = document.querySelector('.progress-fill');
            const progressText = document.querySelector('.progress-text');
            const detectionStatus = document.querySelector('.detection-status');
            const detectionText = document.querySelector('.detection-status .text');
            const faceDetectionBox = document.querySelector('.face-detection-box');
            
            let faceDetected = false;
            let loginInProgress = false;
            
            // Animation frames for face detection
            function updateFaceDetectionAnimation() {
                if (faceDetected) {
                    detectionStatus.classList.add('face-found');
                    detectionText.textContent = 'Face detected!';
                    faceDetectionBox.style.display = 'block';
                } else {
                    detectionStatus.classList.remove('face-found');
                    detectionText.textContent = 'Scanning for face...';
                    faceDetectionBox.style.display = 'none';
                }
            }
            
            // Initialize face detection with progress updates
            loadingIndicator.style.display = 'block';
            
            let progressValue = 0;
            const progressInterval = setInterval(() => {
                if (progressValue < 90) {
                    progressValue += 5;
                    progressFill.style.width = `${progressValue}%`;
                    
                    if (progressValue < 30) {
                        progressText.textContent = 'Loading dependencies...';
                    } else if (progressValue < 60) {
                        progressText.textContent = 'Initializing face detection models...';
                    } else {
                        progressText.textContent = 'Preparing recognition engine...';
                    }
                }
            }, 200);
            
            initFaceDetection().then(() => {
                clearInterval(progressInterval);
                progressValue = 100;
                progressFill.style.width = '100%';
                progressText.textContent = 'Face recognition ready!';
                
                loadingIndicator.style.display = 'none';
                loginBtn.disabled = false;
                
                setTimeout(() => {
                    document.querySelector('.login-progress').classList.add('fade-out');
                    startVideo();
                    startFaceDetection();
                }, 1000);
            }).catch(error => {
                clearInterval(progressInterval);
                showStatus(`Error loading face detection: ${error.message}`, 'error');
                loadingIndicator.style.display = 'none';
                progressText.textContent = 'Error loading face recognition';
                progressFill.style.backgroundColor = 'var(--danger-color)';
            });
            
            // Show status message with animation
            function showStatus(message, type) {
                statusMessage.textContent = message;
                statusMessage.className = `status-message ${type}`;
                statusMessage.style.display = 'block';
                
                // Hide after 5 seconds
                setTimeout(() => {
                    statusMessage.classList.add('fade-out');
                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                        statusMessage.classList.remove('fade-out');
                    }, 300);
                }, 5000);
            }
            
            // Start webcam video capture
            async function startVideo() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: "user" } 
                    });
                    video.srcObject = stream;
                } catch (error) {
                    showStatus(`Error accessing camera: ${error.message}`, 'error');
                }
            }
            
            // Continuous face detection
            async function startFaceDetection() {
                if (loginInProgress) return;
                
                try {
                    const detection = await detectFace(video);
                    
                    if (detection) {
                        faceDetected = true;
                        
                        // Update face detection box position
                        const box = detection.boundingBox;
                        const videoContainer = document.querySelector('.camera-container');
                        const containerWidth = videoContainer.offsetWidth;
                        const containerHeight = videoContainer.offsetHeight;
                        const videoWidth = video.videoWidth;
                        const videoHeight = video.videoHeight;
                        
                        const scaleX = containerWidth / videoWidth;
                        const scaleY = containerHeight / videoHeight;
                        
                        faceDetectionBox.style.left = `${box.x * scaleX}px`;
                        faceDetectionBox.style.top = `${box.y * scaleY}px`;
                        faceDetectionBox.style.width = `${box.width * scaleX}px`;
                        faceDetectionBox.style.height = `${box.height * scaleY}px`;
                    } else {
                        faceDetected = false;
                    }
                    
                    updateFaceDetectionAnimation();
                    
                    // Continue detection
                    requestAnimationFrame(startFaceDetection);
                } catch (error) {
                    console.error('Face detection error:', error);
                    requestAnimationFrame(startFaceDetection);
                }
            }
            
            // Login button click
            loginBtn.addEventListener('click', async () => {
                if (loginInProgress) return;
                loginInProgress = true;
                
                try {
                    loadingIndicator.style.display = 'block';
                    loadingIndicator.textContent = 'Verifying your face...';
                    
                    const detection = await detectFace(video);
                    
                    if (detection) {
                        // Get face descriptor for recognition
                        const faceDescriptor = Array.from(detection.descriptor);
                        
                        // Animated loading for better UX
                        Swal.fire({
                            title: 'Authenticating...',
                            html: 'Comparing face with stored profile',
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            allowOutsideClick: false
                        });
                        
                        // Send to server for verification
                        const response = await fetch('api/login_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ faceDescriptor })
                        });
                        
                        const data = await response.json();
                        
                        Swal.close();
                        
                        if (data.success) {
                            // Success animation
                            Swal.fire({
                                title: 'Welcome Back!',
                                text: `Successfully authenticated as ${data.user.username}`,
                                icon: 'success',
                                timerProgressBar: true,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = 'dashboard.php';
                            });
                        } else {
                            showStatus(data.message || 'Face not recognized. Please try again.', 'error');
                            loginInProgress = false;
                        }
                    } else {
                        showStatus('No face detected. Please position yourself properly in the frame.', 'error');
                        loginInProgress = false;
                    }
                } catch (error) {
                    showStatus(`Error during login: ${error.message}`, 'error');
                    loginInProgress = false;
                }
                
                loadingIndicator.style.display = 'none';
            });
            
            // Dark mode toggle functionality
            const darkModeToggle = document.getElementById('darkModeToggle');
            const moonIcon = darkModeToggle.querySelector('.moon-icon');
            
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('darkMode');
            const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            // Apply dark mode if saved or preferred by OS
            if (savedTheme === 'true' || (savedTheme === null && prefersDarkMode)) {
                document.body.classList.add('dark-mode');
                updateIcon(true);
            }
            
            // Dark mode toggle click handler
            darkModeToggle.addEventListener('click', () => {
                const isDarkMode = document.body.classList.toggle('dark-mode');
                localStorage.setItem('darkMode', isDarkMode);
                updateIcon(isDarkMode);
            });
            
            // Update moon/sun icon based on dark mode state
            function updateIcon(isDarkMode) {
                if (isDarkMode) {
                    moonIcon.innerHTML = '<circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>';
                } else {
                    moonIcon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>';
                }
            }
        });
    </script>
    
    <style>
        /* Additional styles for the enhanced login page */
        .login-progress {
            width: 100%;
            margin: 1rem 0;
            transition: opacity 0.5s ease;
        }
        
        .login-progress.fade-out {
            opacity: 0;
            height: 0;
            margin: 0;
            pointer-events: none;
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }
        
        .dark-mode .progress-bar {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .progress-fill {
            height: 100%;
            width: 0;
            background-color: var(--primary-color);
            transition: width 0.4s ease-out;
        }
        
        .progress-text {
            font-size: 0.85rem;
            color: var(--text-light);
            text-align: center;
        }
        
        .dark-mode .progress-text {
            color: #aaa;
        }
        
        .pulse-animation {
            animation: pulse-button 2s infinite;
        }
        
        @keyframes pulse-button {
            0% {
                box-shadow: 0 0 0 0 rgba(67, 97, 238, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(67, 97, 238, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(67, 97, 238, 0);
            }
        }
        
        .detection-status {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        
        .detection-status.face-found {
            background-color: rgba(6, 214, 160, 0.7);
        }
        
        .detection-status .text {
            font-weight: 600;
            font-size: 0.9rem;
            margin: 5px 0;
        }
        
        .scanning-indicator {
            width: 100%;
            height: 2px;
            background-color: rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        
        .scanning-bar {
            height: 100%;
            width: 30%;
            background-color: white;
            animation: scanning 1.5s infinite ease-in-out;
        }
        
        @keyframes scanning {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(300%); }
        }
        
        .face-detection-box {
            position: absolute;
            border: 2px solid var(--success-color);
            box-shadow: 0 0 0 1px rgba(6, 214, 160, 0.5);
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .tips-container {
            margin-bottom: 1.5rem;
        }
        
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .tip-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background-color: rgba(248, 249, 250, 0.7);
            padding: 1rem;
            border-radius: var(--border-radius);
            transition: var(--transition-medium);
            border: 1px solid #eee;
        }
        
        .dark-mode .tip-item {
            background-color: rgba(30, 30, 30, 0.7);
            border-color: #333;
        }
        
        .tip-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow);
        }
        
        .tip-item svg {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .tip-item p {
            margin: 0;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .dark-mode .tip-item p {
            color: #aaa;
        }
        
        .fade-out {
            animation: fadeOut 0.3s forwards;
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        @media (max-width: 768px) {
            .tips-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</body>
</html>