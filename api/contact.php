<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/response.php';

// Suppress HTML error output to enforce strict JSON responses
ini_set('display_errors', 0);
error_reporting(E_ALL);

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if ($method !== 'POST') {
    jsonResponse(false, null, 'Invalid request method', 405);
}

// Basic Validation
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$message = trim($data['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    jsonResponse(false, null, 'All fields are required', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, null, 'Invalid email address', 400);
}

try {
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);
    
    jsonResponse(true, null, 'Thank you! Your message has been sent successfully.');
} catch (Throwable $e) {
    jsonResponse(false, null, 'Failed to send message: ' . $e->getMessage(), 500);
}
