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

// if (DEVELOPMENT_MODE == TRUE) {
//     $gets = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
//     array_shift($gets);
//     switch ($gets[1]) {
//         case ROOT:
//             $module = $gets[1];
//             if (!is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'controllers/' . $module)) {
//                 $module = null;
//                 $action = $gets[1];
//             } else {
//                 $module = $module;
//                 $action = isset($gets[2]) ? $gets[2] : null;
//             }
//             if (empty($action)) {
//                 $action = 'login';
//             }
//             break;
//     }
// }

$gets = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
array_shift($gets);
if ($gets[0] == ROOT || $gets[1] == ROOT) {
	$gets = explode("/", str_replace(strrchr(strstr($_SERVER["REQUEST_URI"], ROOT.'/'), "?"), "", strstr($_SERVER["REQUEST_URI"], ROOT.'/')));
    array_shift($gets);

	$module = $gets[0];
    $action = !empty($gets[1]) ? $gets[1] : NULL;

    if (!is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'controllers/' . $module)) {
        $module = null;
        $action = $gets[0];
    }
    new Index($module, $action);
} else {
	$gets = explode("/", str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]));
	new Index($gets[1]);
}

?>


