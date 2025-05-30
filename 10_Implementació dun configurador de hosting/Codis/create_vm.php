<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// CONFIGURACIÓ DE PROXMOX
$proxmox_host = 'https://192.168.222.200:8006';
$node         = 'pve';
$username     = 'root@pam';
$password     = 'P@ssw0rd';

// LOGIN
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
$csrf   = $data['data']['CSRFPreventionToken'];
$cookie = "PVEAuthCookie=$ticket";

// OBTENIR LLISTA D'ISOS
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$proxmox_host/api2/json/nodes/$node/storage/local/content");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Cookie: $cookie"]);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$iso_response = curl_exec($ch);
curl_close($ch);

$iso_data = json_decode($iso_response, true);
$iso_list = [];
if (isset($iso_data['data'])) {
    foreach ($iso_data['data'] as $file) {
        if ($file['content'] === 'iso') {
            $iso_list[] = $file['volid'];
        }
    }
}

// Tarifas
$prices = [
    'disk'     => ['20'=>5, '50'=>10, '100'=>15],
    'transfer' => ['500'=>5, '1000'=>10, 'unl'=>20],
    'cpu'      => ['1'=>5, '2'=>10, '4'=>15],
    'ram'      => ['1024'=>5, '2048'=>10, '4096'=>15],
    'os'       => ['Ubuntu/Debian'=>0, 'Windows Server'=>10],
    'platform' => ['Cap'=>0, 'Wordpress'=>5],
];

$cost = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recoger datos
    $vmid     = $_POST['vmid'];
    $name     = $_POST['name'];
    $disk_gb  = $_POST['disk'];
    $transfer = $_POST['transfer'];
    $cpu      = $_POST['cpu'];
    $ram_mb   = $_POST['ram'];
    $iso      = $_POST['iso'];
    $os       = $_POST['os'];
    $platform = $_POST['platform'];

    // Calcular coste
    $cost = $prices['disk'][$disk_gb]
          + $prices['transfer'][$transfer]
          + $prices['cpu'][$cpu]
          + $prices['ram'][$ram_mb]
          + $prices['os'][$os]
          + $prices['platform'][$platform];

    // CREAR MÀQUINA
    $params = http_build_query([
        'vmid'    => $vmid,
        'name'    => $name,
        'memory'  => $ram_mb,
        'cores'   => $cpu,
        'sockets' => 1,
        'ide2'    => "$iso,media=cdrom",
        'ostype'  => in_array($os, ['Ubuntu/Debian']) ? 'l26' : 'win2019',
        'scsihw'  => 'virtio-scsi-pci',
        'scsi0'   => "local-lvm:$disk_gb",
        'net0'    => 'virtio,bridge=vmbr0'
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$proxmox_host/api2/json/nodes/$node/qemu");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Cookie: $cookie",
        "CSRFPreventionToken: $csrf"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200 || $http_code == 202) {
        echo "<p class=\"success\">Màquina $vmid creada correctament.</p>";
    } else {
        echo "<p class=\"error\">Error creant la màquina $vmid.</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <title>Crear nova màquina virtual</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <!-- Cerrar sesión -->
    <div style="text-align: right;">
      <a href="logout.php" class="button small danger">Cerrar sesión</a>
    </div>

    <h2>Crear nova màquina virtual</h2>
    <form method="POST">
      <!-- Campos existentes -->
      <label for="vmid">ID (vmid):</label>
      <input type="number" id="vmid" name="vmid" required>

      <label for="name">Nom:</label>
      <input type="text" id="name" name="name" required>

      <label for="disk">Espai de disc:</label>
      <select id="disk" name="disk" required>
        <?php foreach ($prices['disk'] as $gb => $_): ?>
          <option value="<?= $gb ?>"><?= $gb ?>GB (€<?= $prices['disk'][$gb] ?>/mes)</option>
        <?php endforeach; ?>
      </select>

      <label for="transfer">Transferència mensual:</label>
      <select id="transfer" name="transfer" required>
        <?php foreach ($prices['transfer'] as $k => $_): ?>
          <option value="<?= $k ?>"><?= $k === 'unl' ? 'Il·limitada' : $k.'GB' ?> (€<?= $prices['transfer'][$k] ?>/mes)</option>
        <?php endforeach; ?>
      </select>

      <label for="cpu">CPU:</label>
      <select id="cpu" name="cpu" required>
        <?php foreach ($prices['cpu'] as $cores => $_): ?>
          <option value="<?= $cores ?>"><?= $cores ?> vCPU (€<?= $prices['cpu'][$cores] ?>/mes)</option>
        <?php endforeach; ?>
      </select>

      <label for="ram">RAM:</label>
      <select id="ram" name="ram" required>
        <?php foreach ($prices['ram'] as $mb => $_): ?>
          <option value="<?= $mb ?>"><?= ($mb/1024) ?>GB (€<?= $prices['ram'][$mb] ?>/mes)</option>
        <?php endforeach; ?>
      </select>

      <label for="iso">ISO:</label>
      <select id="iso" name="iso" required>
        <option value="">-- Selecciona una ISO --</option>
        <?php foreach ($iso_list as $iso): ?>
          <option value="<?= htmlspecialchars($iso) ?>"><?= htmlspecialchars($iso) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="os">Sistema Operatiu:</label>
      <select id="os" name="os" required>
        <?php foreach ($prices['os'] as $opt => $_): ?>
          <option value="<?= $opt ?>"><?= $opt ?> (€<?= $prices['os'][$opt] ?>/mes)</option>
        <?php endforeach; ?>
      </select>

      <label for="platform">Plataforma preinstalada:</label>
      <select id="platform" name="platform" required>
        <?php foreach ($prices['platform'] as $opt => $_): ?>
          <option value="<?= $opt ?>"><?= $opt ?> (€<?= $prices['platform'][$opt] ?>/mes)</option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="button">Crear VM</button>
    </form>

    <!-- Mostrar coste estimado si procede -->
    <?php if ($cost !== null): ?>
      <div class="cost-summary">
        <h3>Cost mensual estimat:</h3>
        <p class="total-cost">€<?= $cost ?></p>
      </div>
    <?php endif; ?>

    <p class="link"><a href="vms.php">&#x2190; Tornar al panell</a></p>
  </div>
</body>
</html>
