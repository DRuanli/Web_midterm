## Face Recognition Authentication System

### Project Information
Project Name: Web-based Face Recognition Authentication System
Submitted for: Web Development Midterm Project

### Team Members
- ID: [Your ID Number]
- Full Name: [Your Full Name]
- Class: [Your Class Name/Number]

### Project Description
This project is a web-based authentication system that uses facial recognition technology instead of traditional passwords. The system utilizes browser-based machine learning through TensorFlow.js and Face-API.js to perform face detection and recognition directly in the client's browser, providing enhanced security while maintaining user privacy.

### Features
- User registration with face biometrics
- Login using face recognition
- User profile management
- Face data updating
- Information about the ML technology being used
- Responsive design with dark mode support
- Privacy-focused (biometric data never leaves the user's device)

### Technologies Used
- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP
- **Face Recognition**: TensorFlow.js, Face-API.js
- **Data Storage**: JSON (file-based)
- **Additional Libraries**: SweetAlert2 for notifications, AOS for animations

### Setup Instructions

#### Prerequisites
- PHP 8.0 or higher
- Modern web browser with camera access (Chrome, Firefox, Edge recommended)

#### Installation Steps
1. Clone or extract all project files to a directory on your computer
2. Navigate to the project directory in terminal/command prompt
3. Ensure the `data` directory exists and is writable:
   ```
   mkdir -p data
   chmod 755 data/
   ```
4. No database setup is required as the application uses file-based storage

#### Running the Application
You can run the application using PHP's built-in development server:

```bash
php -S localhost:8000
```

Then open your browser and navigate to http://localhost:8000 to access the application.

#### Directory Structure
```
├── index.php                # Landing page
├── login.php                # Login page
├── register.php             # Registration page
├── dashboard.php            # User dashboard
├── profile.php              # Profile management
├── about.php                # ML theory information
├── logout.php               # Logout functionality
├── api/                     # Backend API endpoints
│   ├── login_user.php
│   ├── register_user.php
│   ├── update_face.php
│   └── update_user.php
├── js/                      # JavaScript files
│   ├── face-detection.js
│   ├── face-recognition.js
|   └── face-api.min.js
├── css/                     # CSS stylesheets
│   └── style.css
└── data/                    # Data storage (created automatically)
    └── users.json
```

### Usage Instructions
1. Start the server using `php -S localhost:8000` from the project directory
2. Open a web browser and navigate to http://localhost:8000
3. New users should click "Register New Account" to create an account:
   - Fill in username and email
   - Allow camera access when prompted
   - Position your face in the frame and capture your face data
   - Complete registration
4. Returning users can click "Login with Face" to authenticate:
   - Allow camera access when prompted
   - Position your face in the frame
   - The system will automatically detect and verify your face
5. Once logged in, you can:
   - View your account information on the dashboard
   - Update your profile information
   - Update your face data for improved recognition
   - Learn about the face recognition technology
   - Logout when finished

### Important Notes
- The face recognition works best in good lighting conditions
- For optimal recognition, try to maintain a similar facial position and expression during login as used during registration
- No face data is sent to any external server; all processing happens in the browser
- The system uses a threshold of 0.6 for face matching (lower values are more strict, higher values are more lenient)
- The data is stored in a JSON file (`data/users.json`); in a production environment, you would want to use a secure database instead

### Troubleshooting
- If camera access is denied, check your browser permissions
- If face detection isn't working, ensure you have good lighting and are facing the camera directly
- Clear your browser cache if you experience unexpected behavior
- The application requires a secure context (HTTPS) in some browsers to access the camera
- If you encounter any PHP errors, make sure you're using PHP 8.0 or higher

### Credits
- Face-API.js - https://justadudewhohacks.github.io/face-api.js/
- TensorFlow.js - https://www.tensorflow.org/js