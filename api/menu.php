<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/response.php';

// Suppress HTML error output to enforce strict JSON responses
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    $featured = $_GET['featured'] ?? null;
    $dealsOnly = $_GET['deals'] ?? null;

    // Always fetch categories
    $stmtCat = $pdo->query("SELECT * FROM categories ORDER BY display_order");
    $categories = $stmtCat->fetchAll();

    // Fetch Active Deals and resolve item names
    $stmtDeals = $pdo->prepare("SELECT * FROM deals WHERE is_active = 1 AND (valid_until IS NULL OR valid_until >= CURDATE())");
    $stmtDeals->execute();
    $deals = $stmtDeals->fetchAll();

    foreach ($deals as &$deal) {
        if ($deal['items_json']) {
            $itemIds = json_decode($deal['items_json'], true);
            if ($itemIds && is_array($itemIds) && count($itemIds) > 0) {
                $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
                $stmtNames = $pdo->prepare("SELECT name FROM menu_items WHERE id IN ($placeholders)");
                $stmtNames->execute($itemIds);
                $names = $stmtNames->fetchAll(PDO::FETCH_COLUMN);
                $deal['included_items_text'] = implode(', ', $names);
            }
        }
    }
    $activeDeals = $deals;

    if ($dealsOnly) {
        jsonResponse(true, ['deals' => $activeDeals]);
        exit;
    }

    $categoryId = $_GET['category_id'] ?? null;
    $query = "SELECT m.*, c.name as category_name FROM menu_items m JOIN categories c ON m.category_id = c.id WHERE m.is_available = 1";
    $params = [];

    if ($categoryId) {
        $query .= " AND m.category_id = ?";
        $params[] = $categoryId;
    }

    if ($featured) {
        $query .= " AND m.is_featured = 1";
    }

    $query .= " ORDER BY c.display_order, m.id";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    jsonResponse(true, [
        'items' => $items, 
        'categories' => $categories,
        'deals' => $activeDeals
    ]);
} catch (Throwable $e) {
    jsonResponse(false, null, 'Server Error: ' . $e->getMessage(), 500);
}
