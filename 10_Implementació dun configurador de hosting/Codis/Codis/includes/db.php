<?php
$host = 'localhost';
$db = 'hosting_configurator';
$user = 'dma'; // l'usuari que has creat
$pass = '12345'; // la seva contrasenya

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de connexió: " . $e->getMessage());
}
?>

