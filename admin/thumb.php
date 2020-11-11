<?php

$_GET['src'] = isset($_GET['src']) ? $_GET['src'] : '';
$_GET['x'] = isset($_GET['x']) ? $_GET['x'] : '';
$_GET['y'] = isset($_GET['y']) ? $_GET['y'] : '';
$_GET['q'] = isset($_GET['q']) ? $_GET['q'] : '80';
$tipo = isset($_GET['type']) ? $_GET['type'] : 'crop';
$diretorio = '';
$miniatura = '';

$aux = explode('/',$_GET['src']);
$arquivo = array_pop($aux);

foreach ($aux as $dirs) {
    $diretorio .= $dirs . '/';
}

if ($_GET['x'] && $_GET['y']) {
    $miniatura = $diretorio . $_GET['x'] . 'x' . $_GET['y'] . '_' . $tipo . '_' . $arquivo;
} else {
    $miniatura = $diretorio . '256x256_crop_' . $_GET['src'];
}

if (!file_exists($miniatura)) {
    $path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR;
    include_once($path . 'class.m2brimagem.php');

    $arquivo = $_GET['src'] ? $_GET['src'] : 'no-image.png';
    $largura = $_GET['x'];
    $altura = $_GET['y'];
    $qualidade = $_GET['q'] ? $_GET['q'] : 80;

    $arquivo = $_GET['src'];

    $new = new m2brimagem($arquivo);
    $valida = $new->valida();

    $r = isset($_GET['r']) ? $_GET['r'] : 255;
    $g = isset($_GET['g']) ? $_GET['g'] : 255;
    $b = isset($_GET['b']) ? $_GET['b'] : 255;

    if ($valida == 'OK') {
        $new->rgb($r, $g, $b);
        $new->redimensiona($largura, $altura, $tipo);
        $new->grava($miniatura, $qualidade);
        show_img($miniatura);
    } else {
        die($valida);
    }
} else {
    show_img($miniatura);
}

function show_img($img)
{
    $imgInfo = getimagesize($img);
    header('Content-type: ' . $imgInfo['mime']);
    readfile($img);
    exit;
}

exit;
