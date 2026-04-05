<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(false, null, 'Invalid request', 400);

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    jsonResponse(false, null, 'No image uploaded or upload error', 400);
}

$file = $_FILES['image'];
$allowed = ['jpg', 'jpeg', 'png', 'webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    jsonResponse(false, null, 'Invalid file type. Only JPG, PNG, WEBP allowed', 400);
}

if ($file['size'] > 5 * 1024 * 1024) {
    jsonResponse(false, null, 'File too large. Max 5MB', 400);
}

$uploadDir = __DIR__ . '/../assets/images/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = uniqid('img_') . '.' . $ext;
$destination = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    $publicPath = 'assets/images/uploads/' . $filename;
    jsonResponse(true, ['path' => $publicPath], 'Image uploaded successfully');
} else {
    jsonResponse(false, null, 'Failed to save image', 500);
}
