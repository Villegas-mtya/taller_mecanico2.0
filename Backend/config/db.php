<?php

$host = "127.0.0.1";
$port = "3307"; // tu puerto
$db   = "taller_db";
$user = "root";
$pass = ""; // en XAMPP normalmente está vacío

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
