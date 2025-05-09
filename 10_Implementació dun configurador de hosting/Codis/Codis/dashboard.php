<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panell de Control</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Benvingut, <?php echo $_SESSION['user']; ?>!</h1>
    <p>
        <a href="profile.php">Perfil</a> |
        <a href="servers.php">Els meus servidors</a> |
        <a href="new-server.php">Crear servidor nou</a> |
        <a href="logout.php">Tancar sessió</a>
    </p>
</div>
</body>
</html>

