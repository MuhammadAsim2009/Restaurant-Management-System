<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/response.php';

// Suppress HTML error output to enforce strict JSON responses
ini_set('display_errors', 0);
error_reporting(E_ALL);

$resource = $_GET['resource'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Handle both JSON and Multipart Form Data (for file uploads)
if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') !== false) {
    $data = $_POST;
} else {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
}

try {
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
                $stmt = $pdo->query("SELECT m.*, c.name as category_name FROM menu_items m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.is_featured DESC, m.id DESC");
                jsonResponse(true, $stmt->fetchAll());
            } elseif ($method === 'POST') {
                $id = $data['id'] ?? null;
                $imagePath = $data['existing_image'] ?? '';
                
                // Handle File Upload
                if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['item_image']['tmp_name'];
                    $fileName = $_FILES['item_image']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $newFileName = 'menu_' . uniqid() . '.' . $fileExtension;
                    $uploadDir = __DIR__ . '/../assets/images/menu/';
                    
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imagePath = 'assets/images/menu/' . $newFileName;
                    }
                }

                if ($id) {
                    // UPDATE Existing Item
                    $stmt = $pdo->prepare("UPDATE menu_items SET category_id=?, name=?, description=?, price=?, image=?, is_available=?, is_featured=? WHERE id=?");
                    $stmt->execute([
                        $data['category_id'], 
                        $data['name'], 
                        $data['description'] ?? '', 
                        $data['price'], 
                        $imagePath, 
                        $data['is_available'] ?? 1,
                        $data['is_featured'] ?? 0,
                        $id
                    ]);
                    jsonResponse(true, null, 'Item updated successfully');
                } else {
                    // INSERT New Item
                    $stmt = $pdo->prepare("INSERT INTO menu_items (category_id, name, description, price, image, is_available, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $data['category_id'], 
                        $data['name'], 
                        $data['description'] ?? '', 
                        $data['price'], 
                        $imagePath, 
                        $data['is_available'] ?? 1,
                        $data['is_featured'] ?? 0
                    ]);
                    jsonResponse(true, ['id' => $pdo->lastInsertId()], 'Item added successfully');
                }
            } elseif ($method === 'PATCH') {
                $stmt = $pdo->prepare("UPDATE menu_items SET category_id=?, name=?, description=?, price=?, image=?, is_available=? WHERE id=?");
                $stmt->execute([
                    $data['category_id'], 
                    $data['name'], 
                    $data['description'], 
                    $data['price'], 
                    $data['image'], 
                    $data['is_available'], 
                    $data['id']
                ]);
                jsonResponse(true, null, 'Item updated successfully');
            } elseif ($method === 'DELETE') {
                $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id=?");
                $stmt->execute([$data['id']]);
                jsonResponse(true, null, 'Item deleted successfully');
            }
            break;

        case 'categories':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT c.*, COUNT(m.id) as item_count FROM categories c LEFT JOIN menu_items m ON m.category_id = c.id GROUP BY c.id ORDER BY c.display_order");
                jsonResponse(true, $stmt->fetchAll());
            } elseif ($method === 'POST') {
                $id = $data['id'] ?? null;
                if ($id) {
                    // UPDATE existing category
                    $stmt = $pdo->prepare("UPDATE categories SET name=?, display_order=?, icon=? WHERE id=?");
                    $stmt->execute([$data['name'], $data['display_order'] ?? 0, $data['icon'] ?? '', $id]);
                    jsonResponse(true, null, 'Category updated successfully');
                } else {
                    // INSERT new category
                    $stmt = $pdo->prepare("INSERT INTO categories (name, display_order, icon) VALUES (?, ?, ?)");
                    $stmt->execute([$data['name'], $data['display_order'] ?? 0, $data['icon'] ?? '']);
                    jsonResponse(true, ['id' => $pdo->lastInsertId()], 'Category added successfully');
                }
            } elseif ($method === 'DELETE') {
                 $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
                 $stmt->execute([$data['id']]);
                 jsonResponse(true, null, 'Category deleted successfully');
            }
            break;

        case 'deals':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT * FROM deals ORDER BY id DESC");
                jsonResponse(true, $stmt->fetchAll());
            } elseif ($method === 'POST') {
                $id = $data['id'] ?? null;
                $imagePath = $data['existing_image'] ?? '';
                
                // Handle File Upload
                if (isset($_FILES['deal_image']) && $_FILES['deal_image']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['deal_image']['tmp_name'];
                    $fileName = $_FILES['deal_image']['name'];
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $newFileName = 'deal_' . uniqid() . '.' . $fileExtension;
                    $uploadDir = __DIR__ . '/../assets/images/deals/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $destPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imagePath = 'assets/images/deals/' . $newFileName;
                    }
                }

                // Correct mapping for multiple items in deal (items_json)
                $itemsJson = isset($data['items_json']) ? json_encode($data['items_json']) : null;

                if ($id) {
                    // UPDATE
                    $stmt = $pdo->prepare("UPDATE deals SET name=?, description=?, price=?, original_price=?, image=?, is_active=?, items_json=?, valid_until=? WHERE id=?");
                    $stmt->execute([
                        $data['name'], 
                        $data['description'] ?? '', 
                        $data['price'], 
                        $data['original_price'] ?? null, 
                        $imagePath, 
                        $data['is_active'] ?? 1,
                        $itemsJson,
                        $data['valid_until'] ?? null,
                        $id
                    ]);
                    jsonResponse(true, null, 'Deal updated successfully');
                } else {
                    // INSERT
                    $stmt = $pdo->prepare("INSERT INTO deals (name, description, price, original_price, image, is_active, items_json, valid_until) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $data['name'], 
                        $data['description'] ?? '', 
                        $data['price'], 
                        $data['original_price'] ?? null, 
                        $imagePath, 
                        $data['is_active'] ?? 1,
                        $itemsJson,
                        $data['valid_until'] ?? null
                    ]);
                    jsonResponse(true, ['id' => $pdo->lastInsertId()], 'Deal added successfully');
                }
            } elseif ($method === 'DELETE') {
                 $stmt = $pdo->prepare("DELETE FROM deals WHERE id=?");
                 $stmt->execute([$data['id']]);
                 jsonResponse(true, null, 'Deal deleted successfully');
            }
            break;

        case 'users':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
                jsonResponse(true, $stmt->fetchAll());
            } elseif ($method === 'POST') {
                $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->execute([$data['new_role'], $data['id']]);
                jsonResponse(true, null, 'User role updated to ' . $data['new_role']);
            } elseif ($method === 'DELETE') {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$data['id']]);
                jsonResponse(true, null, 'User deleted successfully');
            }
            break;

        case 'settings':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT * FROM site_settings");
                $settings = $stmt->fetchAll();
                $grouped = [];
                foreach ($settings as $s) {
                    $grouped[$s['group']][] = $s;
                }
                jsonResponse(true, $grouped);
            } elseif ($method === 'POST') {
                $pdo->beginTransaction();
                try {
                    foreach ($data as $key => $value) {
                        $stmt = $pdo->prepare("UPDATE site_settings SET value = ? WHERE `key` = ?");
                        $stmt->execute([$value, $key]);
                    }
                    $pdo->commit();
                    jsonResponse(true, null, 'All settings updated successfully');
                } catch (Exception $e) {
                    $pdo->rollBack();
                    jsonResponse(false, null, 'Failed to update settings: ' . $e->getMessage(), 500);
                }
            }
            break;

        case 'messages':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
                jsonResponse(true, $stmt->fetchAll());
            } elseif ($method === 'POST') {
                // Mark as read
                $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
                $stmt->execute([$data['id']]);
                jsonResponse(true, null, 'Message marked as read');
            } elseif ($method === 'DELETE') {
                $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
                $stmt->execute([$data['id']]);
                jsonResponse(true, null, 'Message deleted successfully');
            }
            break;

        default:
            jsonResponse(false, null, 'Resource not found', 404);
    }
} catch (Throwable $e) {
    jsonResponse(false, null, 'Server Error: ' . $e->getMessage(), 500);
}
