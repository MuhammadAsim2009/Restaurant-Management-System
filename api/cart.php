<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/session.php';
require_once '../includes/response.php';

$action = $_GET['action'] ?? '';

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, null, 'Unauthorized', 401);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($action === 'save') {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);
    
    if (!$data || !isset($data['cart'])) {
        jsonResponse(false, null, 'Invalid cart data', 400);
        exit;
    }
    
    try {
        $pdo->beginTransaction();
        
        // 1. Clear existing cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // 2. Insert new cart items
        if (!empty($data['cart'])) {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, item_id, item_type, quantity) VALUES (?, ?, ?, ?)");
            foreach ($data['cart'] as $item) {
                // Determine item type and ID from cartId (if present) or distinct fields
                $type = $item['type'] ?? 'item';
                $id = $item['id'];
                $qty = (int)$item['qty'];
                
                if ($qty > 0) {
                    $stmt->execute([$user_id, $id, $type, $qty]);
                }
            }
        }
        
        $pdo->commit();
        jsonResponse(true, null, 'Cart saved successfully');
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        jsonResponse(false, null, 'Database error: ' . $e->getMessage(), 500);
    }
} else {
    jsonResponse(false, null, 'Invalid action', 400);
}
