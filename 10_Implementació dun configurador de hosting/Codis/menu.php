<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// CONFIGURACIÓN DE PROXMOX
$proxmox_host = 'https://192.168.222.200:8006';
$node         = 'pve';
$username     = 'root@pam';
$password     = 'P@ssw0rd';

// LOGIN A PROXMOX
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$proxmox_host/api2/json/access/ticket");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'username' => $username,
    'password' => $password,
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (!isset($data['data'])) {
    die("Error d'autenticació amb Proxmox.");
}

$ticket = $data['data']['ticket'];
$cookie = "PVEAuthCookie=$ticket";

// OBTENIR LA LLISTA DE MÀQUINES
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$proxmox_host/api2/json/nodes/$node/qemu");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Cookie: $cookie"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$vms_response = curl_exec($ch);
curl_close($ch);

$vms = [];
$tmp = json_decode($vms_response, true);
if (isset($tmp['data'])) {
    $vms = $tmp['data'];
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Panell de Control Proxmox</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <!-- Botón Cerrar sesión -->
    <div style="text-align: right; margin-bottom: 15px;">
      <a href="logout.php" class="button small danger">Cerrar sesión</a>
    </div>

    <h1>Panell de control de Proxmox</h1>

    <div class="menu-grid">
      <!-- Crear VM -->
      <div class="card">
        <h2>Crear nova màquina</h2>
        <a href="create_vm.php" class="button">Anar al formulari de creació</a>
      </div>

      <!-- Llistar VMs -->
      <div class="card">
        <h2>Màquines existents</h2>
        <?php if (count($vms) > 0): ?>
          <ul>
            <?php foreach ($vms as $vm): ?>
              <li>VM <?= htmlspecialchars($vm['vmid']) ?> – <?= htmlspecialchars($vm['name']) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="error">No s'han trobat màquines.</p>
        <?php endif; ?>
        <a href="vms.php" class="button">Gestionar màquines</a>
      </div>
    </div>
  </div>
</body>
</html>

