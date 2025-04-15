<?php
// API endpoint to update user profile information
session_start();

// Set headers
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit;
}

// Get request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (empty($data['userId']) || empty($data['email'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

// Verify user ID matches session user
if ($data['userId'] !== $_SESSION['user_id']) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid email format'
    ]);
    exit;
}

// Read users from file
$usersFile = '../data/users.json';
if (!file_exists($usersFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'User database not found'
    ]);
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);
$userFound = false;

// Update user's email
foreach ($users as &$user) {
    // Check if email already exists for another user
    if ($user['email'] === $data['email'] && $user['id'] !== $data['userId']) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already in use by another account'
        ]);
        exit;
    }
    
    // Update the user's email
    if ($user['id'] === $data['userId']) {
        $user['email'] = $data['email'];
        $userFound = true;
        break;
    }
}

if (!$userFound) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit;
}

// Save users to file
if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
    // Update session email
    $_SESSION['email'] = $data['email'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving user data'
    ]);
}
?>