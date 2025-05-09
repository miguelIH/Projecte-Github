<?php
session_start();
require 'includes/db.php';

// Comprovem si l’usuari està logat
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Obtenim les dades de l’usuari des de la BD
$email = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

// Si s’envia el formulari
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST["name"];
    $new_password = $_POST["password"];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET name = ?, password = ? WHERE email = ?");
        $update->execute([$new_name, $hashed_password, $email]);
    } else {
        $update = $pdo->prepare("UPDATE users SET name = ? WHERE email = ?");
        $update->execute([$new_name, $email]);
    }

    echo "<p>Dades actualitzades correctament.</p>";
    // Recàrrega per mostrar les dades actualitzades
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
}
?>

<h2>El teu perfil</h2>

<form method="POST">
    Nom: <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>
    Correu electrònic: <input type="email" value="<?php echo $email; ?>" disabled><br>
    Nova contrasenya (opcional): <input type="password" name="password"><br>
    <input type="submit" value="Actualitzar">
</form>

<p><a href="dashboard.php">Tornar al panell</a></p>

