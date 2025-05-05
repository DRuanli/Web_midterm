<?php
// API endpoint for user registration
header('Content-Type: application/json');

if (!file_exists('../data')) {
    mkdir('../data', 0755, true);
}

// Initialize users file if it doesn't exist
$usersFile = '../data/users.json';
if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([]));
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Validate required fields
    if (empty($data['username']) || empty($data['email']) || empty($data['faceDescriptor'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields'
        ]);
        exit;
    }
    
    // Read existing users
    $users = json_decode(file_get_contents($usersFile), true);
    
    // Check if username or email already exists
    foreach ($users as $user) {
        if ($user['username'] === $data['username']) {
            echo json_encode([
                'success' => false,
                'message' => 'Username already exists'
            ]);
            exit;
        }
        
        if ($user['email'] === $data['email']) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already exists'
            ]);
            exit;
        }
    }
    
    // Create new user
    $newUser = [
        'id' => uniqid(),
        'username' => $data['username'],
        'email' => $data['email'],
        'faceDescriptor' => $data['faceDescriptor'],
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    // Add to users array
    $users[] = $newUser;
    
    // Save users to file
    if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully',
            'userId' => $newUser['id']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error saving user data'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
}
?>