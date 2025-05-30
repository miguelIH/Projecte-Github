<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Credencials Proxmox
$proxmox_host = '192.168.222.200';
$proxmox_user = 'root@pam';
$proxmox_pass = 'P@ssw0rd';
$node = 'pve';

// Obtenir ticket d'autenticació
$login = curl_init("https://$proxmox_host:8006/api2/json/access/ticket");
curl_setopt_array($login, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_POSTFIELDS => http_build_query([
        'username' => $proxmox_user,
        'password' => $proxmox_pass
    ])
]);

$response = curl_exec($login);
curl_close($login);

$data = json_decode($response, true);

if (!isset($data['data']['ticket']) || !isset($data['data']['CSRFPreventionToken'])) {
    echo "<p style='color:red;'>Error d'autenticació amb Proxmox.</p>";
    exit;
}

$ticket = $data['data']['ticket'];
$csrf_token = $data['data']['CSRFPreventionToken'];

// Rebre dades del formulari
$vmid = $_POST['vmid'] ?? '';
$action = $_POST['action'] ?? '';

if (!$vmid || !$action) {
    echo "<p style='color:red;'> Error: Dades del formulari incorrectes.</p>";
    exit;
}

$action_endpoint = '';
$method = 'POST';

switch ($action) {
    case 'start':
        $action_endpoint = "/api2/json/nodes/$node/qemu/$vmid/status/start";
        break;
    case 'stop':
        $action_endpoint = "/api2/json/nodes/$node/qemu/$vmid/status/stop";
        break;
    case 'delete':
        $action_endpoint = "/api2/json/nodes/$node/qemu/$vmid";
        $method = 'DELETE';
        break;
    default:
        echo "<p style='color:red;'>Acció no vàlida.</p>";
        exit;
}

$url = "https://$proxmox_host:8006$action_endpoint";
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_HTTPHEADER => [
        "CSRFPreventionToken: $csrf_token",
        "Cookie: PVEAuthCookie=$ticket"
    ]
]);

$result = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($result === false) {
    echo "<p style='color:red;'>Error CURL: $error</p>";
    exit;
}

// Redirigir a la pàgina de màquines
header("Location: vms.php");
exit;
?>
