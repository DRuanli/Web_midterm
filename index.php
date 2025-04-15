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
    <title>Face Recognition Authentication</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
</head>
<body>
    <a href="#main" class="skip-to-content">Skip to content</a>
    <button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle dark mode">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="moon-icon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
    </button>
    
    <div class="container">
        <main id="main" class="welcome-card" data-aos="fade-up">
            <h1>Face Recognition Authentication</h1>
            <p>Welcome to our ML-powered face recognition system. This application demonstrates browser-based machine learning for secure authentication using your face as a biometric identifier.</p>
            
            <div class="button-group">
                <a href="login.php" class="btn primary" data-aos="fade-up" data-aos-delay="100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    Login with Face
                </a>
                <a href="register.php" class="btn secondary" data-aos="fade-up" data-aos-delay="200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                    Register New Account
                </a>
            </div>
            
            <div class="features-container" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feature-icon"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <h3>Secure Authentication</h3>
                    <p>Your face is your password - no need to remember complex credentials.</p>
                </div>
                <div class="feature-card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feature-icon"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                    <h3>Browser-Based AI</h3>
                    <p>Leverages TensorFlow.js to perform face detection directly in your browser.</p>
                </div>
                <div class="feature-card">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feature-icon"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <h3>Privacy-Focused</h3>
                    <p>Your biometric data never leaves your device, ensuring complete privacy.</p>
                </div>
            </div>
            
            <div class="info-section" data-aos="fade-up" data-aos-delay="400">
                <h3>About This Project</h3>
                <p>This project demonstrates the power of modern web technologies for advanced authentication. Learn how machine learning can be implemented directly in web browsers without server-side processing.</p>
                <a href="about.php" class="text-link">Learn more about ML on the Web</a>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize AOS animations
            AOS.init({
                duration: 800,
                easing: 'ease-out',
                once: true
            });
            
            // Dark mode toggle functionality
            const darkModeToggle = document.getElementById('darkModeToggle');
            const moonIcon = darkModeToggle.querySelector('.moon-icon');
            
            // Check for saved theme preference or respect OS theme setting
            const prefersDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const savedTheme = localStorage.getItem('darkMode');
            
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
        /* Additional styles for the enhanced index page */
        .features-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
            margin: 2.5rem 0;
        }
        
        .feature-card {
            flex: 1;
            min-width: 200px;
            padding: 1.5rem;
            background-color: rgba(248, 249, 250, 0.7);
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition-medium);
            border: 1px solid #eee;
        }
        
        .dark-mode .feature-card {
            background-color: rgba(30, 30, 30, 0.7);
            border-color: #333;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow);
        }
        
        .feature-icon {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .dark-mode .feature-icon {
            color: var(--primary-light);
        }
        
        .feature-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        
        .feature-card p {
            font-size: 0.95rem;
            margin-bottom: 0;
            color: var(--text-light);
        }
        
        .dark-mode .feature-card p {
            color: #aaa;
        }
        
        @media (max-width: 768px) {
            .features-container {
                flex-direction: column;
            }
            
            .feature-card {
                min-width: 100%;
            }
        }
    </style>
</body>
</html>