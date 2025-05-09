<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['user'];

// Accions
if (isset($_GET['action'], $_GET['id'])) {
    $id = $_GET['id'];
    if ($_GET['action'] === 'engegar') {
        $pdo->prepare("UPDATE servers SET status = 'actiu' WHERE id = ? AND user_email = ?")->execute([$id, $email]);
    } elseif ($_GET['action'] === 'aturar') {
        $pdo->prepare("UPDATE servers SET status = 'aturat' WHERE id = ? AND user_email = ?")->execute([$id, $email]);
    }
}

// Llistar
$stmt = $pdo->prepare("SELECT * FROM servers WHERE user_email = ?");
$stmt->execute([$email]);
$servers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Els meus servidors</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Els meus servidors</h2>
    <?php if (empty($servers)): ?>
        <p>No tens cap servidor creat.</p>
    <?php else: ?>
    <table>
        <tr>
            <th>Nom</th><th>CPU</th><th>RAM</th><th>Disc</th><th>SO</th><th>Plataforma</th><th>Estat</th><th>Accions</th>
        </tr>
        <?php foreach ($servers as $srv): ?>
        <tr>
            <td><?= htmlspecialchars($srv['name']) ?></td>
            <td><?= $srv['cpu'] ?> vCPU</td>
            <td><?= $srv['ram'] ?> GB</td>
            <td><?= $srv['disk'] ?> GB</td>
            <td><?= htmlspecialchars($srv['os']) ?></td>
            <td><?= htmlspecialchars($srv['platform']) ?></td>
            <td><?= $srv['status'] ?></td>
            <td>
                <?php if ($srv['status'] === 'aturat'): ?>
                    <a href="?action=engegar&id=<?= $srv['id'] ?>">Engegar</a>
                <?php else: ?>
                    <a href="?action=aturar&id=<?= $srv['id'] ?>">Aturar</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
    <p><a href="dashboard.php">Tornar</a></p>
</div>
</body>
</html>

