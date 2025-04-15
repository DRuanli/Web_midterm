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
    <title>Face Recognition Auth - Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Using CDN for face-api.js instead of local file -->
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="dashboard-card">
            <h1>Manage Your Profile</h1>
            <p>Update your face data or account information</p>
            
            <div class="nav-menu">
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="about.php">ML Theory</a>
                <a href="logout.php">Logout</a>
            </div>
            
            <div id="statusMessage" class="status-message"></div>
            
            <div class="user-info">
                <h3>Account Information</h3>
                <form id="profileForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" readonly>
                        <small>Username cannot be changed</small>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <button type="submit" class="btn primary">Update Profile</button>
                </form>
            </div>
            
            <div class="info-section">
                <h3>Update Face Data</h3>
                <p>Update your face data to improve recognition accuracy.</p>
                
                <div class="camera-container">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas" style="display: none;"></canvas>
                </div>
                
                <div id="loadingIndicator" class="loading-indicator">Loading face detection models...</div>
                
                <div class="controls">
                    <button id="captureBtn" class="btn secondary" disabled>Capture New Face Data</button>
                    <button id="updateFaceBtn" class="btn primary" disabled>Update Face</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/face-detection.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const captureBtn = document.getElementById('captureBtn');
            const updateFaceBtn = document.getElementById('updateFaceBtn');
            const profileForm = document.getElementById('profileForm');
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
                        updateFaceBtn.disabled = false;
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
            
            // Update face button
            updateFaceBtn.addEventListener('click', async () => {
                if (!isCaptured) {
                    statusMessage.textContent = 'Please capture your face first.';
                    statusMessage.className = 'status-message error';
                    return;
                }
                
                loadingIndicator.style.display = 'block';
                loadingIndicator.textContent = 'Updating your face data...';
                
                // Convert the Float32Array to regular array for JSON serialization
                const faceDescriptorArray = Array.from(faceDescriptor);
                
                try {
                    const response = await fetch('api/update_face.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            userId: '<?php echo $userId; ?>',
                            faceDescriptor: faceDescriptorArray
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        statusMessage.textContent = 'Face data updated successfully!';
                        statusMessage.className = 'status-message success';
                    } else {
                        statusMessage.textContent = data.message || 'Failed to update face data.';
                        statusMessage.className = 'status-message error';
                    }
                } catch (error) {
                    statusMessage.textContent = `Error updating face data: ${error.message}`;
                    statusMessage.className = 'status-message error';
                }
                
                loadingIndicator.style.display = 'none';
            });
            
            // Profile form submission
            profileForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                loadingIndicator.style.display = 'block';
                loadingIndicator.textContent = 'Updating profile...';
                
                const email = document.getElementById('email').value;
                
                try {
                    const response = await fetch('api/update_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            userId: '<?php echo $userId; ?>',
                            email: email
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        statusMessage.textContent = 'Profile updated successfully!';
                        statusMessage.className = 'status-message success';
                        
                        // Update session email
                        <?php $_SESSION['email'] = 'email'; ?>;
                    } else {
                        statusMessage.textContent = data.message || 'Failed to update profile.';
                        statusMessage.className = 'status-message error';
                    }
                } catch (error) {
                    statusMessage.textContent = `Error updating profile: ${error.message}`;
                    statusMessage.className = 'status-message error';
                }
                
                loadingIndicator.style.display = 'none';
            });
        });
    </script>
</body>
</html>