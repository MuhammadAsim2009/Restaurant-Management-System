<?php
require_once __DIR__ . '/includes/db.php';

$updates = [
    ['icon' => 'pepper-hot',    'name' => 'Matka Fries'],
    ['icon' => 'drumstick-bite','name' => 'Fried Chicken'],
    ['icon' => 'burger',        'name' => 'Burgers'],
    ['icon' => 'wheat-awn',     'name' => 'Wraps'],
    ['icon' => 'glass-water',   'name' => 'Drinks'],
];

foreach ($updates as $u) {
    $stmt = $pdo->prepare("UPDATE categories SET icon = ? WHERE name = ?");
    $stmt->execute([$u['icon'], $u['name']]);
    echo "Updated '{$u['name']}' → {$u['icon']}<br>";
}

// Show result
$cats = $pdo->query("SELECT id, name, icon, display_order FROM categories ORDER BY display_order")->fetchAll();
echo "<hr><pre>" . print_r($cats, true) . "</pre>";

// Self-delete this file for security
unlink(__FILE__);
echo "<b>Done! This file has been deleted.</b>";
?>
