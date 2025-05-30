<?php
session_start();
require 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    echo "Omple tots els camps. <a href='index.php'>Torna</a>";
    exit;
}

// Busca l'usuari
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch();

if ($user && $password === $user['password']) {
    // Login correcte
    $_SESSION['user'] = $username;
    header("Location: menu.php");
    exit;
} else {
    echo "Usuari o contrasenya incorrectes. <a href='index.php'>Torna</a>";
    exit;
}
