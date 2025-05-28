<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Database configuration - UPDATE THESE SETTINGS FOR YOUR SERVER
$host = 'localhost';
$dbname = 'equiptrack';
$username = 'root'; // Change to your database username
$password = '';     // Change to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
$name = trim($input['name'] ?? '');
$new_username = trim($input['username'] ?? '');
$current_password = $input['current_password'] ?? '';
$new_password = $input['new_password'] ?? '';

if (empty($name) || empty($new_username) || empty($current_password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Validate new password length if provided
if (!empty($new_password) && strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters long']);
    exit;
}

try {
    // Get current user data
    $stmt = $pdo->prepare("SELECT password, username FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Verify current password
    if ($user['password'] !== $current_password) { // Note: In production, use password_hash/password_verify
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Check if new username is already taken (excluding current user)
    if ($new_username !== $user['username']) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
        $stmt->execute([$new_username, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username is already taken']);
            exit;
        }
    }
    
    // Prepare update query
    $update_fields = [];
    $update_values = [];
    
    // Always update name and username
    $update_fields[] = "name = ?";
    $update_values[] = $name;
    
    $update_fields[] = "username = ?";
    $update_values[] = $new_username;
    
    // Update password if provided
    if (!empty($new_password)) {
        $update_fields[] = "password = ?";
        $update_values[] = $new_password; // Note: In production, use password_hash()
    }
    
    $update_values[] = $_SESSION['user_id']; // for WHERE clause
    
    // Execute update
    $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($update_values);
    
    // Update session data
    $_SESSION['name'] = $name;
    $_SESSION['username'] = $new_username;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Account settings updated successfully!',
        'data' => [
            'name' => $name,
            'username' => $new_username
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>