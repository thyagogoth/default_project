<?php

$path = __DIR__ . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR;
require_once($path . 'class.m2brimagem.php');
$arquivo = $_GET['src'] ? $_GET['src'] : 'no-photo.png';
$largura = $_GET['x'];
$altura = $_GET['y'];
$new = new m2brimagem($arquivo);
$valida = $new->valida();
if ($valida == 'OK') {
    $new->redimensiona($largura, $altura, 'crop');
    $new->grava();
} else {
    die($valida);
}
exit;