<?php
namespace System;

use System\Sql;
use System\Paginate;

class MasterBusiness {

    static $pgn;

    public static function getConnection() {
        return new Sql(DBHOST, DBUSER, DBPASS, DBNAME);
    }

    private static function transformToArray($consulta) {
        $arr = array();
        while ($val = mysqli_fetch_array($consulta)) {
            array_push($arr, $val);
        }
        return $arr;
    }

    public static function fetchArray($query) {
        $con = self::getConnection();
        $cs = $con->fetch_array($con->query($query));
        $con->close();
        return $cs;
    }

    public static function transformFetchToArray($query) {
        $con = self::getConnection();
        $cs = self::transformToArray($con->query($query));
        $con->close();
        return $cs;
    }

    public static function transformFetchToArrayPgn($query, $pagina = 0, $n_de_resultados = 10, $n_de_paginas = 10) {
        $pagina = empty($pagina) ? 0 : $pagina;
        $con = self::getConnection();
        self::$pgn = new Paginate($con);
        $cs = self::transformToArray(self::$pgn->query($query, $n_de_resultados, $n_de_paginas, $pagina));
        $con->close();
        return $cs;
    }

    public static function create($vars, $tabela) {
        $con = self::getConnection();
        $con->createSQL($vars, $tabela);
        $id = $con->getId();
        $con->close();
        return $id;
    }

    public static function update($vars, $tabela, $primary = 'id') {
        $con = self::getConnection();
        $con->updateSQL($vars, $tabela, $primary);
        $id = $con->affected_rows();
        $con->close();
        return $id;
    }

    public static function remove($valor, $tabela, $primary = 'id') {
        $con = self::getConnection();
        $con->removeSQL($tabela, $primary, $valor);
        $id = $con->affected_rows();
        $con->close();
        return $id;
    }

    public static function query($query) {
        $con = self::getConnection();
        $con->query($query);
        $id = $con->affected_rows();
        $con->close();
        return $id;
    }

    public static function logsql($post, $usuario, $tabela, $busca = '', $mode = 1) {
        if (count($post) > 0) {
            $campo = array();
            $valor_anterior = array();
            $valor_novo = array();

            if (is_array($busca)) {
                if (array_key_exists('data_nasc', $busca)) {
                    if (($busca['data_nasc'] == '0000-00-00') && ($post['data_nasc'] == '')) {
                        $busca['data_nasc'] = '';
                    }
                }
            }

            if (empty($busca)) {
                $aux['tipo'] = 'Adicionar';
                foreach ($post as $id => $var) {
                    $campo[] = $id;
                    $valor_anterior[] = $busca[$id];
                    $valor_novo[] = $var;
                }
            } else {
                if ($busca == 'remove') {
                    /* quando remover */
                } else {
                    $aux['tipo'] = 'Atualizar';
                    foreach ($post as $id => $var) {
                        if (is_numeric($busca[$id])) {
                            $var = (float) $var;
                        }
                        if ($busca[$id] != $var) {
                            $campo[] = $id;
                            $valor_anterior[] = $busca[$id];
                            $valor_novo[] = $var;
                        }
                    }
                }
            }
        }

        if (count($campo) > 0) {
            $con = self::getConnection($mode);
            $aux = array();
            $aux['data'] = date('Y-m-d H:i:s');
            $aux['tabela'] = $tabela;
            $aux['usuario'] = $usuario;
            foreach ($campo as $vl => $value) {
                $aux['pk'] = $post['id'];
                $aux['campo'] = $campo[$vl];
                $aux['valor_anterior'] = $valor_anterior[$vl];
                $aux['valor_novo'] = $valor_novo[$vl];
                $aux['unique_id'] = session_id();
                $con->createSQL($aux, 'sistema_log');
            }
            $con->close();
        }
    }

}
