<?php
// config.php
$dsn = 'mysql:host=localhost;dbname=tu_base;charset=utf8mb4';
$user = 'tu_usuario';
$pass = 'tu_contraseña';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $db = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
?>