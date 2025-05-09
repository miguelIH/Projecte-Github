<?php
// Mostrar errors a pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

// Si s'ha enviat el formulari
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Formulari rebut<br>";
    var_dump($_POST); // DEBUG: Mostra dades del formulari

    $name = $_POST['name'];
    $cpu = $_POST['cpu'];
    $ram = $_POST['ram'];
    $disk = $_POST['disk'];
    $os = $_POST['os'];
    $platform = $_POST['platform'];

    // Calcular preu mensual
    $preu = 0;
    $preu += ($disk == 20) ? 5 : (($disk == 50) ? 10 : 15);
    $preu += ($cpu == 1) ? 5 : (($cpu == 2) ? 10 : 15);
    $preu += ($ram == 1) ? 5 : (($ram == 2) ? 10 : 15);
    if ($platform != "cap") $preu += 5;

    // Inserir servidor a la base de dades
    $stmt = $pdo->prepare("INSERT INTO servers (user_email, name, cpu, ram, disk, os, platform, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'aturat')");
    $stmt->execute([$_SESSION['user'], $name, $cpu, $ram, $disk, $os, $platform]);

    $msg = "Servidor creat correctament. Preu mensual estimat: {$preu} €";

    // Si s'ha seleccionat WordPress, executar Ansible
    if ($platform == "WordPress") {
        echo "Executant playbook WordPress...<br>";
        $user = $_SESSION['user'];

        // Ruta absoluta i amb sudo si cal
	$cmd = escapeshellcmd("sudo ansible-playbook /var/www/html/playbook-wordpress.yml -i /var/www/html/includes/inventory.ini -e \"site_name=$name\" -e \"admin_user=$user\" -e \"admin_email=$user\"");
        $output = shell_exec($cmd);
        $msg .= "<br><strong>WordPress desplegat automàticament!</strong><br><pre>$output</pre>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nou servidor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Configura un nou servidor</h2>
    <?php if ($msg) echo "<p>$msg</p>"; ?>
    <form method="POST">
        Nom del servidor: <input type="text" name="name" required>

        <label>CPU:</label>
        <select name="cpu">
            <option value="1">1 vCPU</option>
            <option value="2">2 vCPU</option>
            <option value="4">4 vCPU</option>
        </select>

        <label>RAM:</label>
        <select name="ram">
            <option value="1">1 GB</option>
            <option value="2">2 GB</option>
            <option value="4">4 GB</option>
        </select>

        <label>Disc:</label>
        <select name="disk">
            <option value="20">20 GB</option>
            <option value="50">50 GB</option>
            <option value="100">100 GB</option>
        </select>

        <label>Sistema Operatiu:</label>
        <select name="os">
            <option value="Ubuntu">Ubuntu</option>
            <option value="Debian">Debian</option>
            <option value="Windows">Windows Server</option>
        </select>

        <label>Plataforma:</label>
        <select name="platform">
            <option value="cap">Cap</option>
            <option value="WordPress">WordPress</option>
            <option value="Nextcloud">Nextcloud</option>
            <option value="PrestaShop">PrestaShop</option>
        </select>

        <input type="submit" value="Crear servidor">
    </form>
    <p><a href="dashboard.php">Tornar</a></p>
</div>
</body>
</html>
