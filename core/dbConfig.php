<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=car_inventory', 'root', ''); // Replace with actual credentials
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
