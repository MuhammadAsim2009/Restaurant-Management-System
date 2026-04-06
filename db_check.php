<?php
require_once __DIR__ . '/includes/db.php';
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'deals'");
    if ($stmt->rowCount() > 0) {
        echo "Deals table exists.\n";
        $columns = $pdo->query("DESCRIBE deals")->fetchAll();
        foreach ($columns as $col) {
            echo "Column: " . $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    } else {
        echo "Deals table does NOT exist in " . $dbname . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
