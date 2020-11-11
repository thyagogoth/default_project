<?php
// $minify = filter_input(INPUT_GET, 'minify', FILTER_VALIDATE_BOOLEAN);
// if ($minify || ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'dev.ponto')) {
//     /**
//      * Minify CSS
//      */
//     $minCSS = new \MatthiasMullie\Minify\CSS();
//     $cssDir = scandir(dirname(__DIR__, 1) . "/assets/css/");
//     foreach ($cssDir as $cssItem) {
//         $cssFile = dirname(__DIR__, 1) . "/assets/css/" . $cssItem;
//         if (is_file($cssFile) && pathinfo($cssFile)['extension'] == 'css') {
//             $minCSS->add($cssItem);
//         }
//     }
//     $minCSS->minify(dirname(__DIR__, 1) . "/assets/css/style.min.css");

//     /**
//      * Minify JS
//      */
//     $minJS = new \MatthiasMullie\Minify\JS();
//     $jsDir = scandir(dirname(__DIR__, 1) . "/assets/js/");
//     foreach ($jsDir as $jsItem) {
//         if ($jsItem == 'jquery.js' || $jsItem == 'jquery.min.js') {
//             $minJS->add($jsItem);
//         }
//         $jsFile = dirname(__DIR__, 1) . "/assets/js/" . $jsItem;
//         if (is_file($jsFile) && pathinfo($jsFile)['extension'] == 'js') {
//             $minJS->add($jsItem);
//         }
//     }
//     $minJS->minify(dirname(__DIR__, 1) . "/assets/js/scripts.min.css");
// }
