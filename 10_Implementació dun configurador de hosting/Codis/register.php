<?php
session_start();
require 'db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Tots els camps són obligatoris.';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Les contrasenyes no coincideixen.';
    } else {
        // Comprova si ja existeix
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u");
        $stmt->execute(['u' => $username]);
        if ($stmt->fetch()) {
            $errors[] = 'Aquest usuari ja existeix.';
        }
    }

    if (empty($errors)) {
        // Crea l'usuari (guardant contrasenya en pla, seguint l\'estil actual)
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:u, :p)");
        $ok = $stmt->execute([
            'u' => $username,
            'p' => $password
        ]);
        if ($ok) {
            // Registre correcte: torna al login
            header("Location: index.php");
            exit;
        } else {
            $errors[] = 'Error creant l\'usuari. Torna-ho a intentar.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="style.css">
  <meta charset="UTF-8">
  <title>Registrar-se</title>
</head>
<body>
<div class="container">
  <h2>Registrar nou usuari</h2>

  <?php if (!empty($errors)): ?>
    <ul style="color: red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach ?>
    </ul>
  <?php endif ?>

  <form method="POST" action="register.php">
    <label>Usuari:</label>
    <input type="text" name="username" required><br><br>

    <label>Contrasenya:</label>
    <input type="password" name="password" required><br><br>

    <label>Confirmar contrasenya:</label>
    <input type="password" name="password_confirm" required><br><br>

    <input type="submit" value="Registrar">
  </form>

  <p><a href="index.php">Torna al login</a></p>
</div>
</body>
</html>
