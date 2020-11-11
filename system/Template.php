<?php
namespace System;

class Template {

    private $value = "";
    private $set = array();

    function __construct($value) {
        $arr = file($value);
        foreach ($arr as $valor)
            $this->value .= $valor;
    }

    function set($tag, $val) {
        $this->set[$tag] = $val;
    }

    function setarr($arr) {
        if (count($arr) > 0) {
            foreach ($arr as $id => $var) {
                if (!is_numeric($id)) {
                    $this->Set($id, $var);
                }
            }
        }
    }

    function show($bloco, $tipo = 0) {
        if ($bloco != "" && strpos($this->value, "<!-- %") !== false) {
            preg_match("/<!-- %" . $bloco . "% -->(.*?)(<!-- \%|\Z)/ims", $this->value, $arr);
            $nvalue = $arr[1];
        } else {
            $nvalue = $this->value;
        }

        foreach ($this->set as $id => $var) {
            $nvalue = preg_replace("(([{%]{2})(" . $id . ")([%}]{2}))", $var, $nvalue);
        }
        $nvalue = preg_replace('(([{%]{2})(.*?)([%}]{2}))', '', $nvalue);
        switch ($tipo) {
            default:
                echo $nvalue;
                break;
            case 1:
                return $nvalue;
                break;
        }
    }

    function showarr($bloco, $arr) {
        if (count($arr) > 0) {
            foreach ($arr as $id => $var) {
                $this->SetArr($var);
                $this->Show($bloco, 1);
            }
        }
    }

    function setarrselect($arrBanco, $arrPost, $campo, $compare = 'id') {
        $body = '';
        if (count($arrBanco) > 0) {
            foreach ($arrBanco as $arr) {
                $this->setArr($arr);
                $this->Set('selected', '');
                if ( !empty($arrPost) ) {
                    if ($arr[$compare] == $arrPost[strtolower($campo)]) {
                        $this->Set('selected', 'selected="selected"');
                    }
                }
                $body .= $this->Show('loop-' . $campo, 1);
            }
        }
        $body .= $this->Show('end-loop-' . $campo, 1);
        return $body;
    }

    function setcheckbox($arrCampo, $post) {
        if ((is_array($arrCampo)) && (is_array($post))) {
            foreach ($arrCampo as $var) {
                $this->Set($var . '-' . $post[$var], 'checked="checked"');
            }
        }
    }

    function setarrmsgsistema($arrMsg, $op = true) {
        $msg = '';
        if (count($arrMsg) > 0) {
            foreach ($arrMsg as $id => $arr) {
                foreach ($arr as $var) {
                    $msg .= "<p>$var</p>";
                }
            }
        }
        return $msg;
    }

}
