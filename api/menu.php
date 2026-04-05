<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/response.php';

$categoryId = $_GET['category_id'] ?? null;
$featured = $_GET['featured'] ?? null;
$deals = $_GET['deals'] ?? null;

if ($deals) {
    $stmt = $pdo->prepare("SELECT * FROM deals WHERE is_active = 1 AND (valid_until IS NULL OR valid_until >= CURDATE())");
    $stmt->execute();
    jsonResponse(true, ['deals' => $stmt->fetchAll()]);
}

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

$stmtCat = $pdo->query("SELECT * FROM categories ORDER BY display_order");
$categories = $stmtCat->fetchAll();

jsonResponse(true, ['items' => $items, 'categories' => $categories]);
