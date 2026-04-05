<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$resource = $_GET['resource'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch($resource) {
    case 'dashboard':
        $stats = [];
        $stats['orders_today'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
        $stats['revenue_today'] = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE DATE(created_at) = CURDATE() AND status != 'cancelled'")->fetchColumn();
        $stats['pending_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
        $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        
        // Revenue Chart (Last 7 Days)
        $chartQuery = $pdo->query("
            SELECT DATE(created_at) as date, SUM(total) as revenue 
            FROM orders 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
            AND status != 'cancelled'
            GROUP BY DATE(created_at) 
            ORDER BY date ASC
        ");
        $stats['chart_data'] = $chartQuery->fetchAll();

        // Top Selling Items
        $topItemsQuery = $pdo->query("
            SELECT mi.name, mi.image, mi.price, SUM(oi.quantity) as sold_count, SUM(oi.subtotal) as total_revenue
            FROM order_items oi
            JOIN menu_items mi ON oi.item_id = mi.id
            GROUP BY mi.id
            ORDER BY sold_count DESC
            LIMIT 5
        ");
        $stats['top_items'] = $topItemsQuery->fetchAll();

        jsonResponse(true, $stats);
        break;

    case 'items':
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT m.*, c.name as category_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.id DESC");
            jsonResponse(true, $stmt->fetchAll());
        } elseif ($method === 'POST') {
            $stmt = $pdo->prepare("INSERT INTO menu_items (category_id, name, description, price, is_available) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['category_id'], $data['name'], $data['description'], $data['price'], $data['is_available'] ?? 1]);
            jsonResponse(true, ['id' => $pdo->lastInsertId()], 'Item added');
        } elseif ($method === 'PATCH') {
            $stmt = $pdo->prepare("UPDATE menu_items SET category_id=?, name=?, description=?, price=?, is_available=? WHERE id=?");
            $stmt->execute([$data['category_id'], $data['name'], $data['description'], $data['price'], $data['is_available'], $data['id']]);
            jsonResponse(true, null, 'Item updated');
        } elseif ($method === 'DELETE') {
            $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id=?");
            $stmt->execute([$data['id']]);
            jsonResponse(true, null, 'Item deleted');
        }
        break;

    case 'categories':
        if ($method === 'GET') {
            jsonResponse(true, $pdo->query("SELECT * FROM categories ORDER BY display_order")->fetchAll());
        } elseif ($method === 'POST') {
            $stmt = $pdo->prepare("INSERT INTO categories (name, display_order, icon) VALUES (?, ?, ?)");
            $stmt->execute([$data['name'], $data['display_order'] ?? 0, $data['icon'] ?? '']);
            jsonResponse(true, null, 'Category added');
        } elseif ($method === 'DELETE') {
             $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
             $stmt->execute([$data['id']]);
             jsonResponse(true, null, 'Category deleted');
        }
        break;

    case 'deals':
        if ($method === 'GET') {
            jsonResponse(true, $pdo->query("SELECT * FROM deals ORDER BY id DESC")->fetchAll());
        } elseif ($method === 'POST') {
            $stmt = $pdo->prepare("INSERT INTO deals (name, description, price, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['name'], $data['description'], $data['price'], $data['is_active'] ?? 1]);
            jsonResponse(true, null, 'Deal added');
        } elseif ($method === 'DELETE') {
             $stmt = $pdo->prepare("DELETE FROM deals WHERE id=?");
             $stmt->execute([$data['id']]);
             jsonResponse(true, null, 'Deal deleted');
        }
        break;

    case 'users':
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
            jsonResponse(true, $stmt->fetchAll());
        }
        break;

    default:
        jsonResponse(false, null, 'Resource not found', 404);
}
