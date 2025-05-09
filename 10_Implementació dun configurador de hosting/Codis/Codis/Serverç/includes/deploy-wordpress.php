<?php
$site = $_POST['site_name'];
$user = $_POST['admin_user'];
$email = $_POST['admin_email'];

// Exemple de com cridar Ansible des de PHP
$cmd = escapeshellcmd("ansible-playbook /ruta/al/playbook-wordpress.yml -e \"site_name=$site\" -e \"admin_user=$user\" -e \"admin_email=$email\"");
$output = shell_exec($cmd);

echo "<pre>$output</pre>";
?>

