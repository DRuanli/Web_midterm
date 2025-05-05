<?php
// API endpoint for user login with face recognition
session_start();

// Set headers
header('Content-Type: application/json');

// Get request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if face descriptor is provided
if (empty($data['faceDescriptor'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No face descriptor provided'
    ]);
    exit;
}

// Read users from file
$usersFile = '../data/users.json';
if (!file_exists($usersFile)) {
    echo json_encode([
        'success' => false,
        'message' => 'No registered users found'
    ]);
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);
if (empty($users)) {
    echo json_encode([
        'success' => false,
        'message' => 'No registered users found'
    ]);
    exit;
}

// Function to calculate Euclidean distance between two face descriptors
function calculateDistance($descriptor1, $descriptor2) {
    if (count($descriptor1) !== count($descriptor2)) {
        return INF;
    }
    
    $sum = 0;
    for ($i = 0; $i < count($descriptor1); $i++) {
        $diff = $descriptor1[$i] - $descriptor2[$i];
        $sum += $diff * $diff;
    }
    
    return sqrt($sum);
}

// Find best match among users
$bestMatch = null;
$bestDistance = INF;
$threshold = 0.6; 

foreach ($users as $user) {
    $distance = calculateDistance($data['faceDescriptor'], $user['faceDescriptor']);
    
    if ($distance < $bestDistance) {
        $bestDistance = $distance;
        $bestMatch = $user;
    }
}

// Check if the best match is within the threshold
if ($bestMatch && $bestDistance <= $threshold) {
    // Set session data
    $_SESSION['user_id'] = $bestMatch['id'];
    $_SESSION['username'] = $bestMatch['username'];
    $_SESSION['email'] = $bestMatch['email'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $bestMatch['id'],
            'username' => $bestMatch['username'],
            'email' => $bestMatch['email']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Face not recognized. Please try again or register.',
        'debug' => [
            'bestDistance' => $bestDistance,
            'threshold' => $threshold
        ]
    ]);
}
?>