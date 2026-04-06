<?php
$databases = ['kitchen86', 'kitchen_86'];
foreach ($databases as $db) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$db", 'root', '');
        echo "Successfully connected to: $db\n";
    } catch (Exception $e) {
        echo "Failed to connect to: $db\n";
    }
}
?>
