<?php
// db.php
$host = 'localhost';
$db   = 'proxmox_panel';
$user = 'dma';
$pass = '12345';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
