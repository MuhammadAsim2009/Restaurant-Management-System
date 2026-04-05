<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/response.php';

// Enforce admin privileges
if (!isAdmin()) {
    // Check if it's an API request or page load
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        jsonResponse(false, null, 'Unauthorized access. Admin privileges required.', 403);
    } else {
        // Redirect to customer login for standard web requests
        header('Location: /#login');
        exit;
    }
}
?>
