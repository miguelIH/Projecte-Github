<?php
// vms.php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$proxmox_host = '192.168.222.200';
$proxmox_user = 'root@pam';
$proxmox_pass = 'P@ssw0rd';
$node = 'pve';

// Obtenir ticket
$login = curl_init("https://$proxmox_host:8006/api2/json/access/ticket");
curl_setopt($login, CURLOPT_POST, true);
curl_setopt($login, CURLOPT_RETURNTRANSFER, true);
curl_setopt($login, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($login, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($login, CURLOPT_POSTFIELDS, http_build_query([
    'username' => $proxmox_user,
    'password' => $proxmox_pass
]));
$response = curl_exec($login);
curl_close($login);

$data = json_decode($response, true);
if (!isset($data['data']['ticket'])) {
    echo "<p class=\"error\">Error d'autenticació amb Proxmox.</p>";
    exit;
}

$ticket = $data['data']['ticket'];
$csrf   = $data['data']['CSRFPreventionToken'];

// Obtenir màquines
$vmList = curl_init("https://$proxmox_host:8006/api2/json/nodes/$node/qemu");
curl_setopt($vmList, CURLOPT_RETURNTRANSFER, true);
curl_setopt($vmList, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($vmList, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($vmList, CURLOPT_HTTPHEADER, [
    "Cookie: PVEAuthCookie=$ticket"
]);
$vmResponse = curl_exec($vmList);
curl_close($vmList);

$vms = json_decode($vmResponse, true);

// Ordenar per ID
if (isset($vms['data'])) {
    usort($vms['data'], function($a, $b) {
        return $a['vmid'] <=> $b['vmid'];
    });
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Llista de màquines</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <!-- Botón Cerrar sesión -->
    <div style="text-align: right;">
      <a href="logout.php" class="button small danger">Cerrar sesión</a>
    </div>

    <h1>Llista de màquines</h1>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Estat</th>
            <th>Accions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($vms['data'])): ?>
            <?php foreach ($vms['data'] as $vm): ?>
              <tr>
                <td><?= htmlspecialchars($vm['vmid']) ?></td>
                <td><?= htmlspecialchars($vm['name']) ?></td>
                <td><?= htmlspecialchars($vm['status']) ?></td>
                <td class="actions">
                  <form action="control.php" method="post" style="display:inline">
                    <input type="hidden" name="action" value="start">
                    <input type="hidden" name="vmid" value="<?= $vm['vmid'] ?>">
                    <input type="submit" value="Start" class="button small">
                  </form>
                  <form action="control.php" method="post" style="display:inline">
                    <input type="hidden" name="action" value="stop">
                    <input type="hidden" name="vmid" value="<?= $vm['vmid'] ?>">
                    <input type="submit" value="Stop" class="button small">
                  </form>
                  <form action="control.php" method="post" style="display:inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="vmid" value="<?= $vm['vmid'] ?>">
                    <input type="submit" value="Delete" class="button small danger">
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="error">No s'han pogut obtenir les màquines.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <p class="link"><a href="menu.php">&#x2190; Tornar al menú</a></p>
  </div>
</body>
</html>
