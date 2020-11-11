<?php

require_once('../system/uteis.php');
set_time_limit(0);

$aquivoNome = $_GET['file'];
$novoNome = !empty($_GET['name']) ? $_GET['name'] : $_GET['file'];
$ext = uteis::getExtensaoArquivo($aquivoNome);

header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="' . uteis::slugify($novoNome) . '.' . $ext);
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($aquivoNome));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');
readfile($aquivoNome);
