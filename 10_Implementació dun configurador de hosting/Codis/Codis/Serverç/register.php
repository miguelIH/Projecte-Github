<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $password]);
        $_SESSION['user'] = $email;
        header("Location: dashboard.php");
    } catch (PDOException $e) {
        echo "Error: aquest correu ja està registrat.";
    }
}
?>

<form method="POST">
    Nom: <input type="text" name="name" required><br>
    Correu electrònic: <input type="email" name="email" required><br>
    Contrasenya: <input type="password" name="password" required><br>
    <input type="submit" value="Registrar-se">
</form>

