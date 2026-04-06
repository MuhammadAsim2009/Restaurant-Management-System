<?php
/**
 * Helper to emit a standardized JSON response format.
 * Automatically sets the HTTP response code and Content-Type header.
 */
function jsonResponse($success, $data = [], $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    
    $payload = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    
    if ($json === false) {
        $json = json_encode([
            'success' => false,
            'message' => 'Internal JSON Encoding Error: ' . json_last_error_msg(),
            'data' => null
        ]);
    }

    echo $json;
    exit;
}
?>
