<?php
$output = shell_exec("whoami");
echo "Usuari PHP: $output";

$test = shell_exec("sudo ansible-playbook --version");
echo "<pre>$test</pre>";

