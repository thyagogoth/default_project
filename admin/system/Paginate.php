<?php
namespace System;

class Paginate {

    private $sql;
    private $query;
    private $n_de_resultados;
    private $n_de_paginas;
    private $pagina;
    private $linhas;
    private $n_resultado_pagina;

    function __construct($sql) {
        $this->sql = $sql;
    }

    function query($query, $n_de_resultados, $n_de_paginas, $pagina) {
        $this->query = $query;
        $this->n_de_resultados = $n_de_resultados;
        $this->n_de_paginas = $n_de_paginas;
        $this->pagina = $pagina;
        $this->linhas = $this->sql->rows($this->sql->query($query));
        $pg = $n_de_resultados * $pagina;
        $query .= $this->linhas > $n_de_resultados ? " LIMIT $pg,$n_de_resultados" : '';
        $consulta = $this->sql->query($query);
        $this->n_resultado_pagina = $this->sql->rows();
        return $consulta;
    }

    function paginas() {
        $msg = '';
        $total = sizeof(explode('pg/', $_SERVER['QUERY_STRING']));
        $php_self = explode('/pg/', str_replace('//', '/', $_SERVER['REDIRECT_URL']));
        $php_self = utf8_decode(urldecode($php_self[0]));

        $QUERY_STRING = $_SERVER['QUERY_STRING'];
        $resultados = intval($this->linhas / $this->n_de_resultados);
        if ($resultados > 0) {
            $resultados = (($this->linhas / $this->n_de_resultados) == $resultados) ? $resultados = $resultados - 1 : $resultados;

            $inicio = intval($resultados > $this->n_de_paginas ? ($this->pagina >= ($this->n_de_paginas / 2) ? (($this->pagina + ($this->n_de_paginas / 2)) <= $resultados ? $this->pagina - ($this->n_de_paginas / 2) : $resultados - $this->n_de_paginas) : 0) : 0);
            $final = intval(($inicio + $this->n_de_paginas) <= $resultados ? ($inicio + $this->n_de_paginas) : $resultados);

            $div = $this->linhas / $this->n_de_resultados;
            $div = explode('.', $div);
            $total_geral = ((int) $div[1] < 5) ? floor($this->linhas / $this->n_de_resultados) : round($this->linhas / $this->n_de_resultados);
            $msg .= '<ul class="post-pagination list-inline text-center">';
            $msg .= $this->pagina > 0 ? '<li><a href="' . $php_self . '"><i class="fa fa-arrow-left" aria-hidden="true"></i></a></li>' : '<li><a class="disabled"><i class="fa fa-arrow-left" aria-hidden="true"></i></a></li>&nbsp;';

            for ($i = $inicio; $i <= $final; $i++) {
                $msg .= $i == $this->pagina ? ($this->linhas > $this->n_de_resultados ?
                        "<li class=\"active\"><a>" . ($i + 1) . "</a></li>&nbsp;" : "") : "<li><a href=\"$php_self" . ($total > 1 ?
                        str_replace("/pg/$this -> pagina", "/pg/$i", $QUERY_STRING) : $QUERY_STRING . "/pg/$i") . "\">" . ($i + 1) . "</a></li>&nbsp;";
            }

            $ultima = (($total_geral - 1) > 0) ? ($total_geral - 1) : $total_geral;
            if ($ultima > 1) {
                $ultima++;
            }
            $msg .= $this->pagina < $ultima ? '<li><a href="' . $php_self . '/pg/' . $ultima . '"><i class="fa fa-arrow-right" aria-hidden="true"></i></a></li>' : '<li><a class="disabled"><i class="fa fa-arrow-right" aria-hidden="true"></i></a></li>&nbsp;';
            $msg .= '</ul>';
        }
        return $msg;
    }

    function paginasCentral() {
        $msg = '<nav><ul class="pagination">';
        $total = sizeof(explode('pagina=', $_SERVER['QUERY_STRING']));
        $php_self = str_replace('index.php', '', $_SERVER['REDIRECT_URL']);
        $QUERY_STRING = $_SERVER['QUERY_STRING'];
        $resultados = intval($this->linhas / $this->n_de_resultados);
        $resultados = (($this->linhas / $this->n_de_resultados) == $resultados) ? $resultados = $resultados - 1 : $resultados;
        $inicio = intval($resultados > $this->n_de_paginas ? ($this->pagina >= ($this->n_de_paginas / 2) ? (($this->pagina + ($this->n_de_paginas / 2)) <= $resultados ? $this->pagina - ($this->n_de_paginas / 2) : $resultados - $this->n_de_paginas) : 0) : 0);
        $final = intval(($inicio + $this->n_de_paginas) <= $resultados ? ($inicio + $this->n_de_paginas) : $resultados);
        if ($final > 0) {
            if ($this->pagina > 0) {
                $msg .= '<li>';
                $msg .= '    <a href="' . $php_self . '?' . str_replace('pagina=' . $this->pagina, 'pagina=' . ($this->pagina - 1), $QUERY_STRING) . '">';
                $msg .= '        <i class="mdi mdi-chevron-left"></i>';
                $msg .= '    </a>';
                $msg .= '</li>';
            } else {
                $msg .= '<li class="disabled"><a href="#"><i class="mdi mdi-chevron-left"></i></a></li>';
            }
            for ($i = $inicio; $i <= $final; $i++) {
                $msg .= $i == $this->pagina ? ($this->linhas > $this->n_de_resultados ? '<li class="active"><a href="#1">' . ($i + 1) . '</a></li>' : '') : '<li><a href = "' . $php_self . '?' . ($total > 1 ? str_replace('pagina=' . $this->pagina, 'pagina=' . $i, $QUERY_STRING) : $QUERY_STRING . '&amp;pagina=' . $i) . '">' . ($i + 1) . '</a></li>';
            }
            $msg .= ($this->pagina + 1) <= $resultados ? '<li><a href = "' . $php_self . '?' . ($total > 1 ? str_replace('pagina=' . $this->pagina, 'pagina=' . ($this->pagina + 1), $QUERY_STRING) : $QUERY_STRING . '&amp;pagina=' . ($this->pagina + 1)) . '"><i class="mdi mdi-chevron-right"></i></a>' : '<li class="disabled"><a href="#"><i class = "mdi mdi-chevron-right"></i></a></li>';
        }
        $msg .= '</ul></nav>';

        return $msg;
    }

    function getTotal() {
        return $this->linhas;
    }

    function getInfoPgn() {
        $arrInfo = array('total' => $this->linhas, 'n_resultado_pagina' => $this->n_resultado_pagina, 'paginacao' => $this->paginasCentral());
        return $arrInfo;
    }

}
