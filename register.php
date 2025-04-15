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
            <h1>Register New Account</h1>
            <p>Create a new account using your face as authentication</p>
            
            <div id="steps-indicator" class="steps-container">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Account Details</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Face Capture</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-label">Confirmation</div>
                </div>
            </div>
            
            <div id="statusMessage" class="status-message" style="display: none;"></div>
            
            <div id="step1" class="registration-step active">
                <form id="detailsForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required aria-required="true" autocomplete="username">
                        <div class="input-validation" id="username-validation"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required aria-required="true" autocomplete="email">
                        <div class="input-validation" id="email-validation"></div>
                    </div>
                    <button type="button" id="nextToFaceCapture" class="btn primary btn-next">
                        Continue to Face Capture
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>
            </div>
            
            <div id="step2" class="registration-step">
                <h3>Face Capture</h3>
                <p class="instruction-text">Position your face within the guide and look directly at the camera</p>
                
                <div class="camera-container">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas" style="display: none;"></canvas>
                    <div class="face-guide"></div>
                    <div class="camera-overlay">
                        <div class="camera-instructions">
                            <p>For best results:</p>
                            <ul>
                                <li>Ensure good lighting</li>
                                <li>Remove glasses if possible</li>
                                <li>Look directly at camera</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div id="loadingIndicator" class="loading-indicator">Loading face detection models...</div>
                
                <div class="controls">
                    <button type="button" id="backToDetails" class="btn secondary btn-back">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back
                    </button>
                    <button type="button" id="captureBtn" class="btn primary" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>
                        Capture Face
                    </button>
                </div>
            </div>
            
            <div id="step3" class="registration-step">
                <h3>Review & Complete</h3>
                <p>Please verify your information before completing registration</p>
                
                <div class="preview-container">
                    <div class="user-preview">
                        <div class="preview-section">
                            <h4>Account Details</h4>
                            <p><strong>Username:</strong> <span id="preview-username"></span></p>
                            <p><strong>Email:</strong> <span id="preview-email"></span></p>
                        </div>
                        <div class="preview-section">
                            <h4>Face Capture</h4>
                            <div class="face-preview-container">
                                <canvas id="previewCanvas"></canvas>
                                <div class="success-indicator">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    Face detected
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="controls">
                    <button type="button" id="backToFaceCapture" class="btn secondary btn-back">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Back
                    </button>
                    <button type="button" id="registerBtn" class="btn primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                        Complete Registration
                    </button>
                </div>
            </div>
            
            <div class="info-section">
                <p>Already have an account? <a href="login.php" class="text-link">Login here</a></p>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="js/face-detection.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Elements
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const previewCanvas = document.getElementById('previewCanvas');
            const captureBtn = document.getElementById('captureBtn');
            const registerBtn = document.getElementById('registerBtn');
            const statusMessage = document.getElementById('statusMessage');
            const loadingIndicator = document.getElementById('loadingIndicator');
            
            // Step navigation elements
            const stepsIndicator = document.getElementById('steps-indicator');
            const steps = document.querySelectorAll('.registration-step');
            const nextToFaceCapture = document.getElementById('nextToFaceCapture');
            const backToDetails = document.getElementById('backToDetails');
            const backToFaceCapture = document.getElementById('backToFaceCapture');
            
            // Form validation elements
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const usernameValidation = document.getElementById('username-validation');
            const emailValidation = document.getElementById('email-validation');
            
            // Preview elements
            const previewUsername = document.getElementById('preview-username');
            const previewEmail = document.getElementById('preview-email');
            
            let faceDescriptor = null;
            let isCaptured = false;
            
            // Step navigation
            function goToStep(stepNumber) {
                // Update steps indicator
                const stepIndicators = stepsIndicator.querySelectorAll('.step');
                stepIndicators.forEach(step => {
                    if (parseInt(step.dataset.step) <= stepNumber) {
                        step.classList.add('active');
                    } else {
                        step.classList.remove('active');
                    }
                });
                
                // Show active step
                steps.forEach(step => step.classList.remove('active'));
                document.getElementById(`step${stepNumber}`).classList.add('active');
                
                // Special handling for step 2 (face capture)
                if (stepNumber === 2 && !isCaptured) {
                    startVideo();
                }
                
                // Special handling for step 3 (review)
                if (stepNumber === 3) {
                    previewUsername.textContent = username.value;
                    previewEmail.textContent = email.value;
                }
            }
            
            // Input validation
            function validateUsername() {
                if (username.value.length < 3) {
                    usernameValidation.textContent = 'Username must be at least 3 characters';
                    usernameValidation.classList.add('invalid');
                    return false;
                } else {
                    usernameValidation.textContent = 'Valid username';
                    usernameValidation.classList.remove('invalid');
                    usernameValidation.classList.add('valid');
                    return true;
                }
            }
            
            function validateEmail() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    emailValidation.textContent = 'Please enter a valid email address';
                    emailValidation.classList.add('invalid');
                    return false;
                } else {
                    emailValidation.textContent = 'Valid email';
                    emailValidation.classList.remove('invalid');
                    emailValidation.classList.add('valid');
                    return true;
                }
            }
            
            // Initialize face detection
            loadingIndicator.style.display = 'block';
            initFaceDetection().then(() => {
                loadingIndicator.style.display = 'none';
                captureBtn.disabled = false;
            }).catch(error => {
                showStatus(`Error loading face detection: ${error.message}`, 'error');
                loadingIndicator.style.display = 'none';
            });
            
            // Show status message
            function showStatus(message, type) {
                statusMessage.textContent = message;
                statusMessage.className = `status-message ${type}`;
                statusMessage.style.display = 'block';
                
                // Hide after 5 seconds
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 5000);
            }
            
            // Start webcam video capture
            async function startVideo() {
                try {
                    if (!video.srcObject) {
                        loadingIndicator.textContent = 'Accessing camera...';
                        loadingIndicator.style.display = 'block';
                        const stream = await navigator.mediaDevices.getUserMedia({ 
                            video: { facingMode: "user" } 
                        });
                        video.srcObject = stream;
                        loadingIndicator.style.display = 'none';
                    }
                } catch (error) {
                    showStatus(`Error accessing camera: ${error.message}`, 'error');
                    loadingIndicator.style.display = 'none';
                }
            }
            
            // Event Listeners for step navigation
            nextToFaceCapture.addEventListener('click', () => {
                const isUsernameValid = validateUsername();
                const isEmailValid = validateEmail();
                
                if (isUsernameValid && isEmailValid) {
                    goToStep(2);
                }
            });
            
            backToDetails.addEventListener('click', () => {
                goToStep(1);
            });
            
            backToFaceCapture.addEventListener('click', () => {
                goToStep(2);
            });
            
            // Input validation on change/blur
            username.addEventListener('blur', validateUsername);
            email.addEventListener('blur', validateEmail);
            
            // Capture face button
            captureBtn.addEventListener('click', async () => {
                try {
                    loadingIndicator.style.display = 'block';
                    loadingIndicator.textContent = 'Processing face...';
                    
                    const detection = await detectFace(video);
                    
                    if (detection) {
                        // Draw face for visual feedback
                        const videoCtx = canvas.getContext('2d');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        videoCtx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        // Draw face in preview canvas
                        const previewCtx = previewCanvas.getContext('2d');
                        previewCanvas.width = video.videoWidth;
                        previewCanvas.height = video.videoHeight;
                        previewCtx.drawImage(video, 0, 0, previewCanvas.width, previewCanvas.height);
                        
                        // Get face descriptor for recognition later
                        faceDescriptor = detection.descriptor;
                        
                        isCaptured = true;
                        
                        // Success notification
                        Swal.fire({
                            title: 'Face Captured!',
                            text: 'Your face has been successfully captured.',
                            icon: 'success',
                            confirmButtonText: 'Continue',
                            confirmButtonColor: '#4361ee'
                        }).then(() => {
                            goToStep(3);
                        });
                    } else {
                        showStatus('No face detected. Please position yourself properly in the frame.', 'error');
                    }
                    
                    loadingIndicator.style.display = 'none';
                } catch (error) {
                    showStatus(`Error capturing face: ${error.message}`, 'error');
                    loadingIndicator.style.display = 'none';
                }
            });
            
            // Registration button
            registerBtn.addEventListener('click', async () => {
                if (!isCaptured) {
                    showStatus('Please capture your face first.', 'error');
                    return;
                }
                
                loadingIndicator.style.display = 'block';
                loadingIndicator.textContent = 'Creating your account...';
                
                const usernameValue = username.value;
                const emailValue = email.value;
                
                // Convert the Float32Array to regular array for JSON serialization
                const faceDescriptorArray = Array.from(faceDescriptor);
                
                try {
                    const response = await fetch('api/register_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            username: usernameValue,
                            email: emailValue,
                            faceDescriptor: faceDescriptorArray
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        loadingIndicator.style.display = 'none';
                        
                        Swal.fire({
                            title: 'Registration Successful!',
                            text: 'Your account has been created. You will be redirected to login.',
                            icon: 'success',
                            timerProgressBar: true,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'login.php';
                        });
                    } else {
                        showStatus(data.message || 'Registration failed.', 'error');
                        loadingIndicator.style.display = 'none';
                    }
                } catch (error) {
                    showStatus(`Error during registration: ${error.message}`, 'error');
                    loadingIndicator.style.display = 'none';
                }
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
        /* Additional styles for the enhanced registration process */
        .registration-step {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }
        
        .registration-step.active {
            display: block;
        }
        
        .steps-container {
            display: flex;
            justify-content: space-between;
            margin: 1.5rem 0 2rem;
            position: relative;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .steps-container::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 30px;
            right: 30px;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .dark-mode .steps-container::before {
            background-color: #444;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f0f0f0;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            color: #888;
        }
        
        .dark-mode .step-number {
            background-color: #333;
            border-color: #444;
            color: #aaa;
        }
        
        .step.active .step-number {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #888;
        }
        
        .dark-mode .step-label {
            color: #aaa;
        }
        
        .step.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .camera-overlay {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            z-index: 3;
        }
        
        .camera-instructions ul {
            margin: 5px 0 0 15px;
            padding: 0;
        }
        
        .camera-instructions li {
            margin-bottom: 3px;
        }
        
        .instruction-text {
            background-color: rgba(67, 97, 238, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .dark-mode .instruction-text {
            background-color: rgba(67, 97, 238, 0.2);
        }
        
        .input-validation {
            font-size: 0.85rem;
            margin-top: 5px;
            min-height: 20px;
        }
        
        .input-validation.valid {
            color: var(--success-color);
        }
        
        .input-validation.invalid {
            color: var(--danger-color);
        }
        
        .preview-container {
            background-color: rgba(248, 249, 250, 0.5);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border: 1px solid #eee;
        }
        
        .dark-mode .preview-container {
            background-color: rgba(30, 30, 30, 0.5);
            border-color: #333;
        }
        
        .user-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }
        
        .preview-section {
            flex: 1;
            min-width: 200px;
        }
        
        .preview-section h4 {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
            color: var(--primary-color);
        }
        
        .dark-mode .preview-section h4 {
            border-color: #333;
        }
        
        .face-preview-container {
            position: relative;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        #previewCanvas {
            width: 100%;
            display: block;
        }
        
        .success-indicator {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(6, 214, 160, 0.8);
            color: white;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .success-indicator svg {
            width: 16px;
            height: 16px;
            margin-right: 5px;
        }
        
        .btn-next, .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .steps-container {
                max-width: 100%;
            }
            
            .step-label {
                font-size: 0.75rem;
            }
            
            .user-preview {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>