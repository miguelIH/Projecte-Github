<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="style.css">
  <meta charset="UTF-8">
  <title>Login al panell Proxmox</title>
</head>
<body>
<div class="container">
    <h2>Login al panell Proxmox</h2>
    <form method="POST" action="check_login.php">
        <label>Usuari:</label>
        <input type="text" name="username" required><br><br>

        <label>Contrasenya:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Entrar">
    </form>

    <p>Si no tens compte, <a href="register.php">registra’t aquí</a>.</p>
</div>
</body>
</html>
