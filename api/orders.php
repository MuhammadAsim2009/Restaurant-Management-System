<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/response.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(!$data || empty($data['items'])) {
        jsonResponse(false, null, 'Invalid order data', 400);
    }
    
    $userId = getCurrentUserId();
    $name = trim($data['name'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');
    $type = $data['type'] ?? 'delivery';
    $payment = $data['payment'] ?? 'cash';
    
    // Calculate totals server-side
    $subtotal = 0;
    foreach($data['items'] as $item) {
        $subtotal += floatval($item['price']) * intval($item['qty']);
    }
    $tax = 0;
    $total = $subtotal + $tax;
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, customer_name, phone, address, order_type, payment_method, subtotal, tax, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $name, $phone, $address, $type, $payment, $subtotal, $tax, $total]);
        $orderId = $pdo->lastInsertId();
        
        foreach($data['items'] as $item) {
            $itemSubtotal = floatval($item['price']) * intval($item['qty']);
            $istmt = $pdo->prepare("INSERT INTO order_items (order_id, item_id, item_name, item_price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $istmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['qty'], $itemSubtotal]);
        }
        
        $pdo->commit();
        jsonResponse(true, ['order_id' => $orderId], 'Order placed successfully');
    } catch(PDOException $e) {
        $pdo->rollBack();
        jsonResponse(false, null, 'Failed to place order: ' . $e->getMessage(), 500);
    }
}

if ($method === 'GET') {
    if (isset($_GET['admin']) && isAdmin()) {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
        jsonResponse(true, $stmt->fetchAll());
    }
    
    if (isset($_GET['my']) && isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([getCurrentUserId()]);
        jsonResponse(true, $stmt->fetchAll());
    }
    
    if(isset($_GET['id'])) {
        $orderId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        
        if(!$order) jsonResponse(false, null, 'Order not found', 404);
        
        if (!isAdmin() && $order['user_id'] !== getCurrentUserId()) {
            jsonResponse(false, null, 'Unauthorized', 403);
        }
        
        $istmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $istmt->execute([$orderId]);
        $order['items'] = $istmt->fetchAll();
        
        jsonResponse(true, $order);
    }
    
    jsonResponse(false, null, 'Invalid request', 400);
}

if ($method === 'PATCH' && isAdmin()) {
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['id']) && isset($data['status'])) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['id']]);
        jsonResponse(true, null, 'Status updated');
    }
    jsonResponse(false, null, 'Invalid parameters', 400);
}
