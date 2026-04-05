<?php
/**
 * Helper to emit a standardized JSON response format.
 * Automatically sets the HTTP response code and Content-Type header.
 */
function jsonResponse($success, $data = [], $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>
