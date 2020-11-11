<?php
namespace System;
use System\Template;

// interface iStructure {
//     function showHeader($val);
//     function showBody($val);
// }

class MasterAction {

    public function __construct($module, $action) {
        if (method_exists($this, $action)) {
            $str = $this->$action();
        }
    }

    public function getTemplate($pathTemplate, $initVars = array()) {
        $tpl = new Template($pathTemplate, $initVars);
        if (count($initVars) > 0) {
            foreach ($initVars as $id => $var) {
                $tpl->Set($id, $var);
            }
        }
        return $tpl;
    }

    public function getServerModule($module, $panel = true) {
        if (!class_exists($module . 'Business')) {
            $bar = DIRECTORY_SEPARATOR;
            $painel = '';
            include(__CONTEXTPATH__ . strtolower($module) . $bar . 'php' . $bar . 'business.php');
        }
    }

}
