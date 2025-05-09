<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['email'];
        header("Location: dashboard.php");
    } else {
        echo "Credencials incorrectes.";
    }
}
?>

<form method="POST">
    Correu electrònic: <input type="email" name="email" required><br>
    Contrasenya: <input type="password" name="password" required><br>
    <input type="submit" value="Iniciar sessió">
</form>

