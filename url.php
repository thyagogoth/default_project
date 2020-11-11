<?php

if ($_GET) {
    foreach ($_GET as $id => $get) {
        $get = addslashes($get);
        $_GET[$id] = $get;
    }
}

use Arrilot\DotEnv\DotEnv;
DotEnv::load(__DIR__ . '/.env.php');

$module = null;
$action = null;

$gets = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
array_shift($gets);

$module = $gets[0];
$action = !empty($gets[1]) ? $gets[1] : NULL;
if (!is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'controllers/' . $module)) {
    $module = null;
    $action = $gets[1];
}
new Index($action);

?>


